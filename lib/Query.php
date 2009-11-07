<?php
namespace Prosper;

require_once 'adapters/all.php';

class Query {
	const DELETE_STMT = "DELETE_STMT";
	const INSERT_STMT = "INSERT_STMT";
	const SELECT_STMT = "SELECT_STMT";
	const UPDATE_STMT = "UPDATE_STMT";
	
	private $mode;
	private $sql;
	private static $adapter;
	private static $schema;
	
	/**
	 * Private constructor used by factory methods
	 * @param string $sql [optional] SQL to initialize with
	 * @return New Query object wrapping supplied sql statement
	 */
	private function __construct($sql = "", $mode = "") {
		$this->sql = $sql;
		$this->mode = $mode;
	}
	
	/**
	 * Set up Prosper Query Engine
	 * @param string $type [optional] Database type, mysql, mssql, pgsql (postgre), and sqlite accepted, defaults to mysql.
	 * @param string $username [optional] Database username, defaults to nothing
	 * @param string $password [optional] Database password, defaults to nothing
	 * @param string $schema [optional] Default schema to apply to queries
	 * @return null
	 */
	static function configure($type = "mysql", $hostname = "", $username = "", $password = "", $schema = "") {
		$adapter = "Prosper\\";
		switch(trim(strtolower($type))) {
			case 'mysql':
				$adapter .= "MySqlAdapter";
				break;
			case 'mssql':
				$adapter .= "MSSqlAdapter";
				break;
			case 'pgsql':
			case 'postgre':
				$adapter .= "PostgreSqlAdapter";
				break;
			case 'sqlite':
				$adapter .= "SqliteAdapter";
				break;
			case 'weird':
				$adapter .= "WeirdAdapter";
				break;
			default:
				$adapter .= "MySqlAdapter";
				break;
		}		
		self::$adapter = new $adapter($hostname, $username, $password, $schema);
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
		return new Query("select $columns", self::SELECT_STMT);
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
	 * Chained function for describing a standard left join
	 * Convenience function, identical to calling Query#specified_join($table, $alias, "left join");
	 * @param string $table table name
	 * @param object $alias [optional] alias
	 * @return Query instance for further chaining
	 */
	function left($table, $alias = "") {
		return $this->specified_join($table, $alias, "left join");
	}
	
	/**
	 * Chained function for describing a standard inner join
	 * Convenience function, identical to calling Query#specified_join($table, $alias, "inner join");
	 * @param string $table table name
	 * @param object $alias [optional] alias
	 * @return Query instance for further chaining
	 */
	function inner($table, $alias = "") {
		return $this->specified_join($table, $alias, "inner join");
	}
	
	/**
	 * Chained function for describing a standard outer join
	 * Convenience function, identical to calling Query#specified_join($table, $alias, "outer join");
	 * @param string $table table name
	 * @param object $alias [optional] alias
	 * @return Query instance for further chaining
	 */
	function outer($table, $alias = "") {
		return $this->specified_join($table, $alias, "outer join");
	}
	
	/**
	 * Chained function for describing a standard cartesian join
	 * Convenience function, identical to calling Query#specified_join($table, $alias, "join");
	 * @param string $table table name
	 * @param string $alias [optional] alias
	 * @return Query instance for further chaining
	 */
	function join($table, $alias = "") {
		return $this->specified_join($table, $alias);
	}

	/**
	 * Helper function and extensibility function, allows for arbitrary join types.
	 * @warning use of this function directly greatly reduces backend portability.
	 * @param string $table table name
	 * @param string $alias [optional] alias
	 * @param string $type [optional] join clause
	 * @return Query instance for further chaining
	 */
	function specified_join($table, $alias = "", $type= "join") {
		$this->sql .= " $type " . self::table($table) . self::alias($alias);
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

	/**
	 * Chained function for describing an optionally windowed limit
	 * @param int $limit maximum number of results to return
	 * @param int $start [optional] where to start, defaults to 0, beginning of set
	 * @return Query instance for further chaining
	 */
	function limit($limit, $offset = 0) {
		$this->sql = self::$adapter->limit($this->sql, $limit, $offset);
		return $this;
	}
	
	/**
	 * Chained function for describing an ordering
	 * @param array or string $columns array for multiple ordering column_name => direction or string for the column name
	 * @param string $dir [optional] if $columns is a string, "asc" for ascending ordering "desc" for descending, defaults to "asc" 
	 * @return Query instance for further chaining
	 */
	function order($columns, $dir = "asc") {
		if(is_array($columns)) {
			$this->sql .= " order by ";
			foreach($columns as $key => $value) {
				$cols[] = self::column($key) . " " . (strtolower($value) == "desc" ? "desc" : "asc");
			}
			$this->sql .= implode(', ', $cols);
		} else {
			$this->sql .= " order by " . self::column($columns) . " " . (strtolower($dir) == "desc" ? "desc" : "asc");
		}
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
			$sql = " not($clause)";
		} else {
			$sql = " not(" . self::comparison($clause) . ")";
		} 
		return new Query($sql);
	}
	
	/**
	 * Produces the "and" part of a coditional conjunction
	 * @return Query object containing the appropriate sql snippet
	 */
	static function andClause() {
		return self::conjunction(' and ', func_get_args());
	}
	
	/**
	 * Produces the "or" part of a conditional conjunction
	 * @return Query object containing the appropriate sql snippet
	 */
	static function orClause() {
		return self::conjunction(' or ', func_get_args());
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
				if($op == "LIKE") {
					$pos = stripos($expression, $op);
					$parse[0] = substr($expression, 0, $pos);
					$parse[1] = substr($expression, $pos + 4);					
				} else { 
					$parse = explode($op, $expression);
				}
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
		return new Query("insert", self::INSERT_STMT);
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
		return new Query("update " . self::table($table), self::UPDATE_STMT);
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
		return new Query("delete", self::DELETE_STMT);
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

	/**
	 * Helper function to translate unix timestamp to database timestamp format
	 * The inverse of this function is mktime
	 * ie. 
	 *	$unix = mktime();
	 *  $db_time = Query::timestamp($unix);
	 * 	$unix === Query::mktime($db_time);  
	 *	 	 	 	 
	 * @param int timestamp [optional] unix timestamp to translate, defaults to current time
	 * @return string database timestamp string
	 */	 	 	 	
	static function timestamp($timestamp = null) {
		if($timestamp === null) {
			$timestamp = mktime();
		}
		return self::$adapter->timestamp($timestamp);
	}
	
	/**
	 * Helper function to translate database timestamp to unix timestamp
	 * The invers of this function is timestamp
	 * ie.
	 *  $db_time = $row['timestamp'];
	 *  $unix = Query::mktime($db_time);
	 *  $db_time === Query::timestamp($unix);  
	 *	 	 
	 * @param string timestamp database timestamp
	 * @return int unix timestamp	 	 
	 */	 
	static function mktime($timestamp) {
		return self::$adapter->mktime($timestamp);
	}
	
	//System Functions
	
	/**
	 * Executes the sql statement and returns the proper result
	 * SELECT returns a result array
	 * UPDATE returns affected row count
	 * INSERT returns last insert id
	 * DELETE returns affected row count	 	 
	 */	 	
	function execute() {
		return self::$adapter->execute($this->sql, $this->mode);
	}

	/**
	 * Prints the query and the result of the query 
	 * @param boolean $output [optional] defaults to true, if true automatically echoes result	 
	 */	 	
	function verbose($output = true) {
		$result =  '<pre class="brush: sql">' . $this->sql . "</pre>";
		$result .= '<pre class="brush: php">' . print_r($this->execute(), true) . "</pre>";
		if($output) {
			echo $result;
		} 
		return $result;
	}
	
	/**
	 * Automagic toString method, simply prints wrapped sql statement
	 * @return Wrapped Sql Statement
	 */
	function __toString() {
		return $this->sql;
	}

}


?>