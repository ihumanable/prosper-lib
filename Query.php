<?php
namespace Prosper;

require_once 'adapters/all.php';

class Query {
	private $sql;
	private static $adapter;
	private static $schema;
	
	/**
	 * Private constructor used by factory methods
	 * @param string $sql [optional] SQL to initialize with
	 * @return New Query object wrapping supplied sql statement
	 */
	private function __construct($sql = "") {
		$this->sql = $sql;
	}
	
	/**
	 * Set up Prosper Query Engine
	 * @param string $schema [optional] Default schema to apply to queries
	 * @param string $type [optional] Database type, mysql, mssql, pgsql (postgre), and sqlite accepted, defaults to mysql.
	 * @return null
	 */
	static function configure($schema = "", $type = "mysql") {
		switch(trim(strtolower($type))) {
			case 'mysql':
				self::$adapter = new MySqlAdapter();
				break;
			case 'mssql':
				self::$adapter = new MSSqlAdapter();
				break;
			case 'pgsql':
			case 'postgre':
				self::$adapter = new PostgreSqlAdapter();
				break;
			case 'sqlite':
				self::$adapter = new SqliteAdapter();
				break;
			case 'weird':
				self::$adapter = new WeirdAdapter();
				break;
			default:
				self::$adapter = new MySqlAdapter();
				break;
		}		
		self::$schema = ($schema == "" ? "" : self::quote($schema));
	} 

	
	//Common formatting functions

	/**
	 * Creates an alias string if alias is not empty
	 * @param string $alias alias to create 
	 * @return string template is " as self::quote($alias)"
	 */
	static function alias($alias) {
		return ($alias == "" ? "" : " as " . self::quote($alias));
	}
	
	/**
	 * Properly quotes a potential column, allowing for compound table.column format
	 * @param string $col column name, simple or compound
	 * @return Properly quoted column
	 */
	static function column($col) {
		if(strpos($col, '.') === false) {
			$column = self::quote($col);
		} else {
			$parts = explode('.', $col);
			foreach($parts as $part) {
				$safeparts[] = self::quote($part);
			}
			$column = implode('.', $safeparts);
		}
		
		return $column;
		 
	}
	
	/**
	 * Properly quotes and scopes a potential table.
	 * @param string $table table name 
	 * @return Properly scoped and quoted tablename ex: Query::table('hello') => `schema`.`hello` (for mysql)
	 */
	static function table($table) {
		return self::schema('.') . self::quote($table);
	}
	
	/**
	 * Retrieves the schema with an optional suffix
	 * @param object $append [optional] If the schema exists this text will be appended, most useful for appending a dot (.)
	 * @return 
	 */
	static function schema($append = "") {
		return (self::$schema == "" ? "" : self::$schema . $append);
	}
	
	/**
	 * Quotes the given string using the appropriate adapter's quote function.
	 * Used with schema object names
	 * @param string $str the text to quote
	 * @return Properly quoted text
	 * @see BaseAdapter#quote($str)
	 */	
	static function quote($str) {
		return self::$adapter->quote($str);
	}
	
	/**
	 * Escapes the given string using the appropriate adapter's escape function.
	 * Used with values to be serialized
	 * @param string $str the text to escape
	 * @return Properly escaped text
	 * @see BaseAdapter#escape($str)
	 * @todo implement this properly
	 */
	static function escape($str) {
		return self::$adapter->escape(self::deliteral($str)); 
	}
	
	/**
	 * Unescapes the given string using the approriate adapter's unescape function
	 * Used with values to be deserialized
	 * @param string $str the text to unescape
	 * @return Properly unescaped text
	 * @see BaseAdapter#unescape($str)
	 * @todo implement this properly
	 */
	static function unescape($str) {
		return $str;
	}
	
	/**
	 * Makes a value safe, whether it is a literal, symbol, or sql element
	 * @param string $str value to make safe
	 * @return Safe string
	 */
	static function safe($str) {
		$str = trim($str);
		if(self::is_literal($str)) {
			return self::escape($str);
		} else if(self::is_symbol($str)) {
			return $str;
		} else {
			return self::column($str);
		}
	}

	
	/**
	 * Removes the literal quotes from a string, if called on a non-literal simply returns string
	 * @param string $str String to deliteral
	 * @return String with quotes removed
	 */
	static function deliteral($str) {
		return (self::is_literal($str) ? substr($str, 1, strlen($str) - 2) : $str);
	}
	
	
	
	//Common testing functions
	
	/**
	 * Determines if a string is a literal value, literal values are surrounded by single quotes
	 * @param string $str String to check
	 * @return true if literal, false otherwise
	 */
	static function is_literal($str) {
		return ($str[0] == "'" && $str[strlen($str) - 1] == "'");
	}
	
	/**
	 * Determines if a string is a symbol value, legal symbol values are '?' and anything beginning with colon ':'
	 * @param string $str String to check
	 * @return true if symbol, false otherwise
	 */
	static function is_symbol($str) {
		return ($str == "?" || $str[0] == ":");
	}
	
	
	
	//Select Statement Functions
	
	/**
	 * Factory function that creates a select statement query object.
	 * @param varargs [optional] If nothing is provided assumes 'select *' 
	 * 			otherwise processes each argument as a column, 
	 * 			array arguments are processed as any number of aliased columns where column_name => alias 
	 * @return Select Statement Query Object
	 */
	static function select() {
		if(func_num_args() == 0) {
			$columns = "*";
		} else {
			$args = func_get_args();
			foreach($args as $arg) {
				if(is_array($arg)) {
					foreach($arg as $key => $value) {
						$parts[] = self::column($key) . self::alias($value);
					}
				} else {
					$parts[] = self::column($arg);
				}
			}
			$columns = implode(', ', $parts);
		}
		return new Query("select $columns");
	}
	
	/**
	 * Chained function for describing the table to pull from
	 * @param string $table table name
	 * @param string $alias [optional] alias
	 * @return Query instance for further chaining
	 */
	function from($table, $alias = "") {
		$this->sql .= " from " . self::table($table) . self::alias($alias);
		return $this;
	}
	
	/**
	 * Chained function for describing a standard cartesian join
	 * @param object $table table name
	 * @param object $alias [optional] alias
	 * @return Query instance for further chaining
	 */
	function join($table, $alias = "") {
		$this->sql .= " join " . self::table($table) . self::alias($alias);
		return $this;
	}

	/**
	 * Chained function for describing the on clause
	 * @param object $clause see below
	 * @return Query instance for further chaining
	 * @see Query#conditional($clause) for implementation details.
	 */	
	function on($clause) {
		return $this->conditional('on', $clause);
	}

	/**
	 * Chained function for describing the where clause
	 * @param object $clause see below
	 * @return Query instance for further chaining
	 * @see Query#conditional($clause) for implementation details.
	 */	
	function where($clause) {
		return $this->conditional('where', $clause);
	}

	function limit($count, $start = 0) {
		$this->sql = self::$adapter->limit($this->sql, $count, $start);
		return $this;
	}

	/**
	 * Chained function for describing the conditional
	 * @param string $predicate the sql keyword preceding the full conditional clause
	 * @param object $clause if a string than treated as a simple codition (i.e. x > 3) 
	 * 						 if a query instance treated as a complex logic operation
	 * @return Query instance for further chaining
	 */
	function conditional($predicate, $clause) {
		if($clause instanceof Query) {
			$this->sql .= " $predicate $clause";
		} else {
			$this->sql .= " $predicate " . self::comparison($clause);
		}
		return $this;
	}
	
	/**
	 * Alias for notClause
	 * @see Query#notClause($clause)
	 */
	static function not($clause) {
		return self::notClause($clause);
	}
	
	/**
	 * Alias for andClause
	 * @see Query#andClause(...)
	 */
	static function conj() {
		return call_user_func_array(array(__CLASS__, "andClause"), func_get_args());
	}
	
	/**
	 * Alias for orClause
	 * @see Query#orClause(...) 
	 */
	static function union() {
		return call_user_func_array(array(__CLASS__, "orClause"), func_get_args());
	}
	
	/**
	 * Produces the logical "not" conjunction 
	 * @param object $clause if Query then evaluated, otherwise treated as a comparison expresssion
	 * @return Query object containing the appropriate sql snippet
	 */
	static function notClause($clause) {
		if($clause instanceof Query) {
			$sql = " NOT($clause)";
		} else {
			$sql = " NOT(" . self::comparison($clause) . ")";
		} 
		return new Query($sql);
	}
	
	/**
	 * Produces the "and" part of a coditional conjunction
	 * @return Query object containing the appropriate sql snippet
	 */
	static function andClause() {
		return self::conjunction(' AND ', func_get_args());
	}
	
	/**
	 * Produces the "or" part of a conditional conjunction
	 * @return Query object containing the appropriate sql snippet
	 */
	static function orClause() {
		return self::conjunction(' OR ', func_get_args());
	}
	
	/**
	 * Generic conjunction generation function
	 * @param string $glue Text to join conjunctions together with
	 * @param array $args Arguments to join together
	 * @return Query object containing the appropriate sql snippet
	 */
	static function conjunction($glue, $args) {
		foreach($args as $arg) {
			if($arg instanceof Query) {
				$parts[] = "($arg)";
			} else {
				$parts[] = self::comparison($arg);
			}
		}
		$sql .= implode($glue, $parts);
		
		return new Query($sql);
	}
	
	/**
	 * Properly escapes a comparison expression.
	 * @param string $expression Generic comparison expression
	 * @return string The properly escaped comparison expression
	 * @example Query::parseComparison("a<1") => '`a` < "1"' (for mysql)
	 */
	static function comparison($expression) {
		$operators = array('<=', ">=", "!=", "<>", "<", ">", "=", "LIKE");
		foreach($operators as $op) {
			if(stripos($expression, $op) !== false) {
				$parse = explode($op, $expression);
				return self::safe($parse[0]) . ' ' . $op . ' ' . self::safe($parse[1]);
			}
		}
	}


	//Insert Statement Functions	
	
	/**
	 * Factory function that creates an insert statement query object.
	 * @return Insert Statement Query Object
	 */
	static function insert() {
		return new Query("insert");
	}
	
	/**
	 * Chained function for describing the table to insert into
	 * @param string $table tablename
	 * @return Query instance for further chaining
	 */
	function into($table) {
		$this->sql .= ' into ' . self::table($table);
		return $this;
	}
	
	/**
	 * Chained function for describing the columns and values for an insert statement
	 * @param array $arr Array of values with column_name => insert_value
	 * @return Query instance for further chaining
	 */
	function values($arr) {
		foreach($arr as $key => $value) {
			$columns[] = self::quote($key);
			$values[] = self::escape($value);
		}
		$this->sql .= ' (' . implode(', ', $columns) . ') values (' . implode(', ', $values) . ')';
		return $this;
	}
	

	
	//Update Statement Functions
	
	/**
	 * Factory function that creates an update statement query object.
	 * @param string $table Table to update
	 * @return Update Statement Query Object
	 */
	static function update($table) {
		return new Query("update " . self::table($table));
	}
	
	/**
	 * Chained function for describing the columns and values to set for an update statement
	 * @param array $arr Array of values with column_name => update_value
	 * @return Query instance for further chaining
	 */
	function set($arr) {
		$this->sql .= ' set ';
		foreach($arr as $key => $value) {
			$entries[] = self::quote($key) . "=" . self::escape($value);
		}
		$this->sql .= implode(', ', $entries);
		return $this;
	}
	
	
	
	//Delete Statement Functions
	
	/**
	 * Factory function that creates a delete statement query object.
	 * @return Delete Statement Query Object
	 */
	static function delete() {
		return new Query("delete");
	}
	


	//Utility Functions
	
	/**
	 * Factory function to execute arbitrary native sql
	 * @param string $sql Sql to create a query out of
	 * @return Native SQL Query Object
	 */
	static function native($sql) {
		return new Query($sql);
	}

	
	//System Functions
	
	/**
	 * Automagic toString method, simply prints wrapped sql statement
	 * @return Wrapped Sql Statement
	 */
	function __toString() {
		return $this->sql;
	}
	
	
}


?>