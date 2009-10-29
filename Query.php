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
			default:
				self::$adapter = new MySqlAdapter();
				break;
		}		
		self::$schema = ($schema == "" ? "" : self::quote($schema));
	} 

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
		return $str;
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
			$columns = implode(',', $parts);
		}
		return new Query("select $columns");
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
				return self::column(trim($parse[0])) . ' ' . $op . ' ' . self::escape(trim($parse[1]));
			}
		}
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
	}
	
	/**
	 * Chained function for describing the where conditions
	 * @param object $clause if a string than treated as a simple codition (i.e. x > 3) 
	 * 						 if a query instance treated as a complex logic operation
	 * @return Query instance for further chaining
	 */
	function where($clause) {
		if($clause instanceof Query) {
			$this->sql .= " where $clause";
		} else {
			$this->sql .= ' where ' . self::comparison($clause);
		}
		return $this;
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