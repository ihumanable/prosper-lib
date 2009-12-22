<?php
/**
 * @package Prosper
 */
namespace Prosper;

require_once 'Token.php';

/**
 * The Query Class that drives Prosper
 */
class Query {
  //Statement Types
  const DELETE_STMT = "DELETE_STMT";
  const INSERT_STMT = "INSERT_STMT";
  const SELECT_STMT = "SELECT_STMT";
  const UPDATE_STMT = "UPDATE_STMT";
  //SQL Modes
  const DB2_MODE       = "DB2Adapter";
  const DBASE_MODE     = "DBaseAdapter";
  const FIREBIRD_MODE  = "FirebirdAdapter";
  const FRONTBASE_MODE = "FrontBaseAdapter";
  const INFORMIX_MODE  = "InformixAdapter";
  const INGRES_MODE    = "IngresAdapter";
  const MAXDB_MODE     = "MaxDBAdapter";
  const MSQL_MODE      = "MSqlAdapter";
  const MSSQL_MODE     = "MSSqlAdapter";
  const MYSQL_MODE     = "MySqlAdapter";
  const MYSQL_OLD_MODE = "MySqlOldAdapter";
  const ORACLE_MODE    = "OracleAdapter";
  const OVRIMOS_MODE   = "OvrimosAdapter";
  const PARADOX_MODE   = "ParadoxAdapter";
  const POSTGRE_MODE   = "PostgreSqlAdapter";
  const SQLITE_MODE    = "SqliteAdapter";
  const SYBASE_MODE    = "SybaseAdapter";
  //Loading Modes
  const LAZY_LOADING   = true;
  const EAGER_LOADING  = false;
  
  private $mode;
  private $sql;
  private static $adapter;
  private static $schema;
  private static $db_mode;
  
  /**
   * Private constructor used by factory methods
   * @param string $sql [optional] SQL to initialize with
   * @return Query New Query object wrapping supplied sql statement
   */
  private function __construct($sql = "", $mode = "") {
    $this->sql = $sql;
    $this->mode = $mode;
  }
  
  /**
   * Set up Prosper Query Engine
   * @param string $db_mode [optional] Database mode, use one of the [DATABASE]_MODE constants.
   * @param string $username [optional] Database username, defaults to nothing
   * @param string $password [optional] Database password, defaults to nothing
   * @param string $hostname [optional] Database hostname	 
   * @param string $schema [optional] Default schema to apply to queries	 
   */
  static function configure($db_mode = MYSQL_MODE, $username = "", $password = "", $hostname = "", $schema = "", $loading = LAZY_LOADING) {
    $adapter = "Prosper\\$db_mode";
    self::$db_mode = $db_mode;
    self::$adapter = new $adapter($username, $password, $hostname, $schema, $loading);
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
   * Properly quotes and scopes a potential table.
   * @param string $table table name 
   * @return string Properly scoped and quoted tablename ex: Query::table('hello') => `schema`.`hello` (for mysql)
   */
  static function table($table) {
    return self::schema('.') . self::quote($table);
  }
  
  /**
   * Retrieves the schema with an optional suffix
   * @param string $append [optional] If the schema exists this text will be appended, most useful for appending a dot (.)
   * @return string Schema with an optional suffix 
   */
  static function schema($append = "") {
    return (self::$schema == "" ? "" : self::$schema . $append);
  }
  
  /**
   * Quotes the given string using the appropriate adapter's quote function.
   * Used with schema object names
   * @param string $str the text to quote
   * @return Properly quoted text
   * @see BaseAdapter::quote($str)
   */	
  static function quote($str) {
    if(strpos($str, '.') === false) {
      return self::$adapter->quote($str);
    } else {
      $parts = explode('.', $str);
      foreach($parts as $part) {
        $safeparts[] = self::$adapter->quote($part);
      }
      return implode('.', $safeparts);
    }
  }
  
  /**
   * Escapes the given string using the appropriate adapter's escape function.
   * Used with values to be serialized
   * @param string $str the text to escape
   * @return string Properly escaped text
   * @see BaseAdapter::escape($str)
   */
  static function escape($str) {
    return self::$adapter->escape(self::deliteral($str)); 
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
   * @return boolean true if literal, false otherwise
   */
  static function is_literal($str) {
    return ($str[0] == "'" && $str[strlen($str) - 1] == "'");
  }
  
  /**
   * Returns the current database mode, see the family of is_[DATABASE] 
   * functions that use this function	 
   * @return string one of the [DATABASE]_MODE constants	 
   */
  static function db_mode() {
    return self::$db_mode;
  }
  
  /**
   * Convenience function to check if the configuration is DB2
   * @return boolean true if configured for DB2, false otherwise
   */
  static function is_db2() {
    return self::db_mode() == DB2_MODE;
  }
  
  /**
   * Convenience function to check if the configuration is Firebird
   * @return boolean true if configured for Firebird, false otherwise
   */
  static function is_firebird() {
    return self::db_mode() == FIREBIRD_MODE;
  }
  
  /**
   * Convenience function to check if the configuration is FrontBase
   * @return boolean true if configured for FrontBase, false otherwise
   */
  static function is_frontbase() {
    return self::db_mode() == FRONTBASE_MODE;
  }
  
  /**
   * Convenience function to check if the configuration is Informix
   * @return boolean true if configured for Informix, false otherwise
   */
  static function is_informix() {
    return self::db_mode() == INFORMIX_MODE;
  }
  
  /**
   * Convenience function to check if the configuration is Ingres
   * @return boolean true if configured for Ingres, false otherwise
   */
  static function is_ingres() {
    return self::db_mode() == INGRES_MODE;
  }
  
  /**
   * Convenience function to check if the configuration is MaxDB
   * @return boolean true if configured for MaxDB, false otherwise
   */
  static function is_maxdb() {
    return self::db_mode() == MAXDB_MODE;
  }
  
  /**
   * Convenience function to check if the configuration is MSql
   * @return boolean true if configured for MSql, false otherwise
   */
  static function is_msql() {
    return self::db_mode() == MSQL_MODE;
  }
  
  /**
   * Convenience function to check if the configuration is MSSql
   * @return boolean true if configured for MSSql, false otherwise
   */
  static function is_mssql() {
    return self::db_mode() == MSSQL_MODE;
  }
  
  /**
   * Convenience function to check if the configuration is MySql
   * @return boolean true if configured for MySql, false otherwise
   */
  static function is_mysql() {
    return self::db_mode() == MYSQL_MODE;
  }
  
  /**
   * Convenience function to check if the configuration is MySql Old
   * @return boolean true if configured for MySql Old, false otherwise
   */
  static function is_mysql_old() {
    return self::db_mode() == MYSQL_OLD_MODE;
  }
  
  /**
   * Convenience function to check if the configuration is Oracle
   * @return boolean true if configured for Oracle, false otherwise
   */
  static function is_oracle() {
    return self::db_mode() == ORACLE_MODE;
  }
  
  /**
   * Convenience function to check if the configuration is Ovrimos
   * @return boolean true if configured for Ovrimos, false otherwise
   */
  static function is_ovrimos() {
    return self::db_mode() == OVRIMOS_MODE;
  }
  
  /**
   * Convenience function to check if the configuration is Paradox
   * @return boolean true if configured for Paradox, false otherwise
   */
  static function is_paradox() {
    return self::db_mode() == PARADOX_MODE;
  }
  
  /**
   * Convenience function to check if the configuration is Postgre
   * @return boolean true if configured for Postgre, false otherwise
   */
  static function is_postgre() {
    return self::db_mode() == POSTGRE_MODE;
  }
  
  /**
   * Convenience function to check if the configuration is Sqlite
   * @return boolean true if configured for Sqlite, false otherwise
   */
  static function is_sqlite() {
    return self::db_mode() == SQLITE_MODE;
  }
  
  /**
   * Convenience function to check if the configuration is Sybase
   * @return boolean true if configured for Sybase, false otherwise
   */
  static function is_sybase() {
    return self::db_mode() == SYBASE_MODE;
  }
  
  static function has_transactions() {
    return self::$adapter->has_transactions();
  }
  
  //Transaction API
  
  /**
   * Begin a database transaction
   */
  static function begin() {
    self::$adapter->begin();
  }
  
  /**
   * Commit the open transaction
   */
  static function commit() {
    self::$adapter->commit();
    self::$adapter->end();
  }
  
  /**
   * Rollback the open transaction
   */
  static function rollback() {
    self::$adapter->rollback();
    self::$adapter->end();
  }
  
  //Reflection API
  
  /**
   * Get an array of all the tables in the current schema
   * Accepts optional table names to retrieve
   * Result is structured as follows
   * @params varargs [optional] table names to retrieve
   * @return array Table data
   */        
  static function tables() {
    return self::$adapter->tables(func_get_args());
  }
  
  //Select Statement Functions
  
  /**
   * Factory function that creates a select statement query object.
   * @param varargs [optional] If nothing is provided assumes 'select *' 
   *      otherwise processes each argument as a column, 
   *       array arguments are processed as any number of aliased columns where column_name => alias 
   * @return Query Select Statement Query Object
   */
  static function select() {
    if(func_num_args() == 0) {
      $columns = "*";
    } else {
      $args = func_get_args();
      foreach($args as $arg) {
        if(is_array($arg)) {
          foreach($arg as $key => $value) {
            $parts[] = self::quote($key) . self::alias($value);
          }
        } else {
          $parts[] = self::quote($arg);
        }
      }
      $columns = implode(', ', $parts);
    }
    return new Query("select $columns", self::SELECT_STMT);
  }
  
  /**
   * Chained function for describing the table to pull from
   * @param string $table table name or subquery
   * @param string $alias [optional] alias
   * @return Query instance for further chaining
   */
  function from($table, $alias = "") {
    if($table instanceof Query) {
      $this->sql .= " from (" . $table . ")" . self :: alias($alias);
    } else {
      $this->sql .= " from " . self::table($table) . self::alias($alias);
    }
    return $this;
  }
  
  /**
   * Chained function for describing a standard left join
   * Convenience function, identical to calling Query::specified_join($table, $alias, "left join");
   * @param string $table table name
   * @param object $alias [optional] alias
   * @return Query instance for further chaining
   */
  function left($table, $alias = "") {
    return $this->specified_join($table, $alias, "left join");
  }
  
  /**
   * Chained function for describing a standard inner join
   * Convenience function, identical to calling Query::specified_join($table, $alias, "inner join");
   * @param string $table table name
   * @param object $alias [optional] alias
   * @return Query instance for further chaining
   */
  function inner($table, $alias = "") {
    return $this->specified_join($table, $alias, "inner join");
  }
  
  /**
   * Chained function for describing a standard outer join
   * Convenience function, identical to calling Query::specified_join($table, $alias, "outer join");
   * @param string $table table name
   * @param object $alias [optional] alias
   * @return Query instance for further chaining
   */
  function outer($table, $alias = "") {
    return $this->specified_join($table, $alias, "outer join");
  }
  
  /**
   * Chained function for describing a standard cartesian join
   * Convenience function, identical to calling Query::specified_join($table, $alias, "join");
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
   * @param varargs [optional] values to use for parameterization
   * @return Query instance for further chaining
   * @see Query::conditional($clause) for implementation details.
   */
  function on($clause) {
    $args = func_get_args();
    array_shift($args);
    return $this->conditional('on', $clause, $args);
  }
  
  /**
   * Chained function for describing the where clause
   * @param object $clause see below
   * @param varargs [optional] values to use for parameterization
   * @return Query instance for further chaining
   * @see Query::conditional($clause) for implementation details.
   */
  function where($clause) {
    $args = func_get_args();
    array_shift($args);
    return $this->conditional('where', $clause, $args);
  }
  
  /**
   * Used by Query::where and Query::on to parse conditional strings, 
   * interpolating parameters, quoting, escaping, and cross-platform 
   * replacements where needed
   * @param string $predicate result predicate, i.e. 'where' or 'on'
   * @param string $clause conditional clause	 	 	 	 
   */
  function conditional($predicate, $clause, $args) {
    $result = " $predicate";
    $named = $args[count($args) - 1];	
    while($token = Token::next($clause)) {
      $result .= $this->process_token($clause, $token, $args, $named);
    }
    $this->sql .= $result;
    return $this;
  }
  
  /**
   * Processes a token
   * @param string $clause sql cluase to process, by reference
   * @param array $token token array
   * @param array $args arguments used for interpolation, by reference
   * @param array $named associative array
   * @return string processed clause
   */
  function process_token(&$clause, $token, &$args, $named) {
    switch($token['type']) {
      case Token::BOOLEAN:
        return " " . (strtolower($token['token']) == 'true' ? self::true_value() : self::false_value());
        break;
      case Token::LITERAL:
        return " " . self::escape($token['token']);
        break;
      case Token::MODULUS:
        $func_args = $this->parse_func_args($clause, $args, $named);
        if(count($func_args) == 2) {
          return self::modulus($func_args[0], $func_args[1]);
        }
        break;
      case Token::NAMED_PARAM:
        return self::parameterize($named[substr($token['token'], 1)]);
        break;
      case Token::PARAMETER:
        return self::parameterize(array_shift($args));
        break;
      case Token::SQL_ENTITY:
        return " " . self::quote($token['token']);
        break;
      default:
        return " " . $token['token'];
        break;
    }
  }
  
  /**
   * Performs safe parameter interpolation
   * @param mixed $value Value to interpolate, can be an array (will be imploded with commas), a subquery, or a literal value.
   * @return string parameterized fragment	 	 
   */
  function parameterize($value) {
    if(is_array($value)) {
      return " " . implode(', ', array_map(array(__CLASS__, "escape"), $value));
    } else if($value instanceof Query) {
      return " $value";
    } else {
      return " " . self::escape($value);
    }
  }
  
  /**
   * Parses sql function arguments into a php array
   * @param string $clause sql clause to process, by reference
   * @param array $args arguments used for interpolation, by reference
   * @param array $named named interpolation arguments
   * @return array arguments
   */
  function parse_func_args(&$clause, &$args, $named) {
    $token = Token::next($clause);	
    if($token['type'] == Token::OPEN_PAREN) {
      $depth = 1;
      $temp = "";
      while($depth > 0) {
        $token = Token::next($clause);
        switch($token['type']) {
          case Token::OPEN_PAREN:
            $depth += 1;
            $temp .= $this->process_token($clause, $token, $args, $named);
            break;
          case Token::CLOSE_PAREN:
            $depth -= 1;
            if($depth == 0) {
              $result[] = $temp;
            }
            $temp .= $this->process_token($clause, $token, $args, $named);
            break;
          case Token::COMMA:
            if($depth == 1) {
              $result[] = $temp;
              $temp = "";
            }
            break;
          default:
            $temp .= $this->process_token($clause, $token, $args, $named);
            break;
        }
      }
      return $result;
    } else {
      //Something is wrong -- return the processed token
      return $this->process_token($clause, $tkn, $args, $named);
    }
  }
  
  /**
   * Chained function for describing an optionally windowed limit
   * @param int $limit maximum number of results to return
   * @param int $offset [optional] where to start, defaults to 0, beginning of set
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
        $cols[] = self::quote($key) . " " . (strtolower($value) == "desc" ? "desc" : "asc");
      }
      $this->sql .= implode(', ', $cols);
    } else {
      $this->sql .= " order by " . self::quote($columns) . " " . (strtolower($dir) == "desc" ? "desc" : "asc");
    }
    return $this;
  }
  
  //Insert Statement Functions	
  
  /**
   * Factory function that creates an insert statement query object.
   * @return Query Insert Statement Query Object
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
   * This function takes an optional variadic argument for filtering the values
   *   Example:
   *     Assume we have a $_POST array where print_r($_POST) =>
   *      array ( 'first_name' => 'Matt',
   *              'last_name' => 'Nowack',
   *              'some_junk' => 'Not wanted' );
   *     ->values('first_name', 'last_name', $_POST) [read: values 'first_name', 'last_name' from $_POST]
   *       is identical to
   *     ->values(array('first_name' => $_POST['first_name'], 'last_name' => $_POST['last_name']))
   * @param varargs $filter [optional] list of elements to pull out of $values	 
   * @param array $values Array of values with column_name => insert_value
   * @return Query instance for further chaining
   */
  function values($values) {
    if(func_num_args() > 1) {
      $filters = func_get_args();
      $values = array_pop($filters);      //Remove $values
      foreach($filters as $filter) {
        if(array_key_exists($filter, $values)) {
          $cols[] = self::quote($filter);
          $vals[] = self::escape($values[$filter]);
        }
      }
    } else {
      foreach($values as $key => $value) {
        $cols[] = self::quote($key);
        $vals[] = self::escape($value);
      }
    }
    
    if(is_array($cols) && is_array($vals)) {
      $this->sql .= ' (' . implode(', ', $cols) . ') values (' . implode(', ', $vals) . ')';
    }
    return $this;
  }
  
  //Update Statement Functions
  
  /**
   * Factory function that creates an update statement query object.
   * @param string $table Table to update
   * @return Query Update Statement Query Object
   */
  static function update($table) {
    return new Query("update " . self::table($table), self::UPDATE_STMT);
  }
  
  /**
   * Chained function for describing the columns and values to set for an update statement
   * This function takes an optional variadic argument for filtering the values
   *   Example:
   *     Assume we have a $_POST array where print_r($_POST) =>
   *      array ( 'id' => 1,
   *              'first_name' => 'Matt',
   *              'last_name' => 'Nowack' )
   *     ->set('first_name', 'last_name', $_POST) [read: set 'first_name', 'last_name' from $_POST]
   *       is identical to
   *     ->set(array('first_name' => $_POST['first_name'], 'last_name' => $_POST['last_name']))
   * @param varargs $filter [optional] list of elements to pull ovt of $values
   * @param array $values Array of values with column_name => update_value
   * @return Query instance for further chaining
   */
  function set($values) {
    if(func_num_args() > 1) {
      $filters = func_get_args();
      $values = array_pop($filters);      //Remove $values
      foreach($filters as $filter) {
        if(array_key_exists($filter, $values)) {
          $arr[$filter] = $values[$filter];
        }
      }
    } else {
      $arr = $values;
    }
    
    $this->sql .= ' set ';
    if(is_array($arr)) {
      foreach($arr as $key => $value) {
        $entries[] = self::quote($key) . "=" . self::escape($value);
      }
      $this->sql .= implode(', ', $entries);
    }
    return $this;
  }
  
  //Delete Statement Functions
  
  /**
   * Factory function that creates a delete statement query object.
   * @return Query Delete Statement Query Object
   */
  static function delete() {
    return new Query("delete", self::DELETE_STMT);
  }
  
  //Utility Functions
  
  /**
   * Factory function to execute arbitrary native sql
   * @param string $sql Sql to create a query out of
   * @param string $mode Type of query, one of Query::DELETE_STMT, Query::INSERT_STMT, Query::SELECT_STMT, or Query::UPDATE_STMT
   * @return Query Native SQL Query Object
   */
  static function native($sql, $mode) {
    return new Query($sql, $mode);
  }
  
  /**
   * Helper function to translate unix timestamp to database timestamp format
   * The inverse of this function is Query::mktime($timestamp)
   * ie. 
   *  $unix = mktime();
   *  $db_time = Query::timestamp($unix);
   *  $unix === Query::mktime($db_time);
   * @param int timestamp [optional] unix timestamp to translate, defaults to current time
   * @return string database timestamp string
   * @see Query::mktime($timestamp)   
   */
  static function timestamp($timestamp = null) {
    if($timestamp === null) {
      $timestamp = mktime();
    }
    return self::$adapter->timestamp($timestamp);
  }
  
  /**
   * Helper function to translate database timestamp to unix timestamp
   * The inverse of this function is Query::timestamp($timestamp)
   * ie.
   *  $db_time = $row['timestamp'];
   *  $unix = Query::mktime($db_time);
   *  $db_time === Query::timestamp($unix);
   * @param string timestamp database timestamp
   * @return int unix timestamp	 	 
   * @see Query::timestamp($timestamp)   
   */
  static function mktime($timestamp) {
    return self::$adapter->mktime($timestamp);
  }
  
  /**
   * Get the platform specific true value
   * @return mixed Boolean True Value ex: 1 or TRUE
   */
  static function true_value() {
    return self::$adapter->true_value();
  }
  
  /**
   * Get the platform specific false value
   * @return mixed Boolean False Value ex: 0 or FALSE
   */
  static function false_value() {
    return self::$adapter->false_value();
  }
  
  /**
   * Get the platform specific modulus value
   * @param string $lhs left hand side snippet
   * @param string $rhs right hand side snippet
   * @return string modulus snippet
   */
  static function modulus($lhs, $rhs) {
    return self::$adapter->modulus($lhs, $rhs);
  }
  
  //System Functions
  
  /**
   * Executes the sql statement and returns the proper result
   * SELECT returns a result array
   * UPDATE returns affected row count
   * INSERT returns last insert id
   * DELETE returns affected row count
   * @return mixed execution result	 	 
   */
  function execute() {
    return self::$adapter->execute($this->sql, $this->mode);
  }
  
  /**
   * Prints the query and the result of the query 
   * @return mixed the same result set as Query::execute()
   */
  function verbose() {
    echo '<pre class="brush: sql">SQL : <br />' . $this->sql . "</pre>";
    if(self::$adapter->prepared) {
      echo '<pre class="brush: php">BINDINGS : <br />'; 
        print_r(self::$adapter->bindings);
      echo '</pre>';
    }
    $result = $this->execute();
    echo '<pre class="brush: php">RESULT : <br />';
      print_r($result);
    echo "</pre>";
    return $result;
  }
  
  /**
   * Automagic toString method, simply prints wrapped sql statement
   * @return string internal sql statement
   */
  function __toString() {
    return $this->sql;
  }
  
}

?>