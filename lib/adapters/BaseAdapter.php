<?php
/**
 * @package Prosper
 */ 
namespace Prosper;

/**
 * Base Adapter all SQL adapters are based off of
 */
abstract class BaseAdapter {
  
  static protected $connection;
  protected $username;
  protected $password;
  protected $hostname;
  protected $schema;
  
  /**
   * Creates a new adapter instance
   * @param string $username Database username
   * @param string $password Database password
   * @param string $hostname Database hostname
   * @param string $schema Database schema
   * @param bool $lazy [optional] Lazy loading, defaults to true
   * @return New Adapter Instance
   */
  function __construct($username, $password, $hostname, $schema, $lazy = true) {
    $this->username = $username;
    $this->password = $password;
    $this->hostname = $hostname;
    $this->schema = $schema;
    
    if(!$lazy) {
      $this->connection();
    }
  }
  
  /**
   * Destroys an adapter instance
   */
  function __destruct() {
    //TODO: Figure out if we should close the connection here, I don't think so
  }
  
  /**
   * Get the connection, lazy loads connection if necessary
   * @return mixed $connection The connection to the database
   */
  function connection() {
    if(self::$connection == null) {
      self::$connection = $this->connect();
    }
    return self::$connection;
  }
  
  /**
   * Create a connection to the database backend
   */
  abstract function connect();
  
  /**
   * Destroy the connection to the database backend
   */
  abstract function disconnect();
  
  /**
   * Quotes a database object, uses the backtick by default
   * @param string $str string to quote
   * @return quoted string
   */
  function quote($str) {
    return "`$str`";
  }
  
  /**
   * Escapes a value, adds slashes and surrounds with single quotes
   * @param string $str string to escape
   * @return string escaped string or placeholder for prepared statement
   */
  function escape($str) {
    return "'" . $this->addslashes($str) . "'";
  }
  
  /**
   * Wrapper for the adapters add slashes function, defaults to using php's 
   * addslashes built-in
   * @param string $str string to add slashes too
   * @return string escaped string
   */           
  function addslashes($str) {
    return addslashes($str);  
  }
  
  /**
   * Since various databases support a wide variety of limit syntaxes the 
   * adapter is responsible for writing the limit syntax.
   * @param string $sql sql statment
   * @param int $limit how many to limit the result to
   * @param int $offset where to start at
   * @return sql statement with embedded limit statement
   */
  function limit($sql, $limit, $offset) {
    return $sql . " limit $limit" . ($offset !== 0 ? " offset $offset" : "");
  }
  
  /**
   * Determine if an adapter supports transactions, defaults to false
   * @return bool true if supports transactions, false otherwise
   */
  function has_transactions() {
    return false;
  }
  
  /**
   * Begins a transaction, if the adapter doesn't support transactions, this is a no-op
   */
  function begin() {
    
  }
  
  /**
   * Commits a transaction, if the adapter doesn't support transactions, this is a no-op
   */
  function commit() {
    
  }
  
  /**
   * Rolls back a transaction, if the adapter doesn't support transactions, this is a no-op
   */
  function rollback() {
    
  }
  
  /**
   * Turns transactions off
   * If the adapter doesn't support transactions, this is a no-op
   * @see BaseAdapter::begin()
   * @see BaseAdapeter::commit()
   * @see BaseAdapter::rollback()
   */ 
   function end() {
    
  }
  
  /**
   * Executes a sql statement, the base implementation drives the query results using
   * several derivitive functions
   * @see BaseAdapter::platform_execute($sql)
   * @see BaseAdapter::affected_rows($set)
   * @see BaseAdapter::last_id($set)
   * @see BaseAdapter::fetch_assoc($set)
   * @param string $sql statement to execute
   * @param string $mode [optional] statement type, defaults to Query::SELECT_STMT
   * @return mixed Number of rows affected if update or delete, insert id for insert statements, result set for select
   */
  function execute($sql, $mode = Query::SELECT_STMT) {
    $set = $this->platform_execute($sql, $mode);
    
    switch($mode) {
      case Query::DELETE_STMT:
        $result = $this->affected_rows($set);
        break;
      case Query::INSERT_STMT:
        $result = $this->insert_id($set);
        break;
      case Query::SELECT_STMT:
        $result = array();
        if($set) {
          while($row = $this->fetch_assoc($set)) {
            $result[] = $row;
          }
          $this->free_result($set);
        }
        break;
      case Query::UPDATE_STMT:
        $result = $this->affected_rows($set);
        break;
    }
    return $result;
  }
  
  /**
   * Platform specific execution wrapper
   * @param string $sql SQL to execute 
   * @param string $mode Query type
   * @return mixed Platform specific result set
   */
  function platform_execute($sql, $mode) {
    
  }
  
  /**
   * Get the number of affected rows for a platform specific result object 
   * @param mixed $set Platform specific result set
   * @return int number of rows affected
   */
  function affected_rows($set) {
    
  }
  
  /**
   * Used to rebind the query
   * @params array Array of new bindings
   * @return nothing
   */           
  function rebind($bindings) {
  
  }
  
  /**
   * Retrieve the last insert id for a statement
   * @param mixed $set Platform specific result set
   * @return int inserted id
   */
  function insert_id($set) {
    
  }
  
  /**
   * Retrieve an associative array from a platform specific result set
   * @param mixed $set Platform specific result set
   * @return array row as associative array
   */ 
  function fetch_assoc($set) {
    
  }
  
  /**
   * Do optional memory cleanup, not necessary but will allow for long running scripts
   * @param mixed $set Platform specific result set
   * @return nothing
   */
  function free_result($set) {
    
  }
  
  /**
   * Retrieve table information from the current schema
   * @param array $filter [optional] Filter of table names to return
   * @return array Table data
   */
  function tables($filter = null) {
    
  }
  
  /**
   * Translates a platform specific type into a cross platform type
   * The inverse of this function is the BaseAdapter::platform_type($type, $length) function   
   * @param array $platform The platform specific type array from BaseAdapter::type_array($raw)
   * @return array A cross platform type array created with BaseAdapter::type_array($raw)
   * @see BaseAdapter::platform_type($type, $length)
   * @see BaseAdapter::type_array($raw)      
   */
  function cross_type($platform) {
    
  }
  
  /**
   * Translates a cross platform type into the best possible platform type
   * The inverse of this function is the BaseAdapter::cross_type($type, $length) function
   * @param array $cross The cross platform type array from BaseAdapter::type_array($raw)
   * @return array A platform specific type created with BaseAdapter::type_array($raw)
   * @see BaseAdapter::platform_type($type, $length)
   * @see BaseAdapter::type_array($raw)   	 
   */
  function platform_type($cross) {
    
  }
  
  /**
   * Translates a raw data representation to an array of components	
   * Example:
   * type_array('int(11)')   => array ( raw    => 'int(11)',
   *                                    type   => 'int'    ,
   *                                    length => 11        )
   *                                    	 
   * type_array('timestamp') => array ( raw    => 'timestamp', 
   *                                    type   => 'timestamp',
   *                                    length => null        )
   * @param string $raw The raw string with the length
   * @param string $split [optional] The character that indicates a length is coming up, defaults to '('	 
   * @return array A type array
   */
  function type_array($raw, $split = '(') {
    $result['raw'] = $raw;
    
    if(strpos($raw, $split) !== false) {
      $parts = explode($split, $raw);
      $result['type']   = $parts[0];
      $result['length'] = substr($parts[1], 0, -1);
    } else {
      $result['type']   = $raw;
      $result['length'] = null;
    }
    
    return $result;
  }
  
  /**
   * Return database specific timestamp from unix timestamp
   * Default implementation is the sensible MySQL Timestamp	 
   */
  function timestamp($timestamp) {
    return date ("Y-m-d H:i:s", $timestamp); 
  }
  
  /**
   * Return unix timestamp from database specific timestamp
   * Default implementation is the sensible MySQL Timestamp
   */
  function mktime($timestamp) {
    return strtotime($timestamp);
  }
  
  /**
   * Platform specific true value
   * @return mixed truth value
   */
  function true_value() {
    return "TRUE";
  }
  
  /**
   * Platform specific false value
   * @return mixed false value
   */
  function false_value() {
    return "FALSE";
  }
  
  /**
   * Platform specific modulus function
   * @param string $lhs left hand side snippet
   * @param string $rhs right hand side snippet
   * @return string modulus snippet
   */
  function modulus($lhs, $rhs) {
    return " ($lhs % $rhs) ";
  }
}
?>