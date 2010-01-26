<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * PostgreSql Database Adapter
 */
class PostgreSqlAdapter extends PreparedAdapter {
  
  private $inserted_cols;
  private $counter = 0;
  
  /**
   * @see BaseAdapter::connect()
   */
  function connect() {
    $conn = ($this->hostname == "" ? "" : "host={$this->hostname} ") .
            ($this->schema   == "" ? "" : "dbname={$this->schema} ") .
        ($this->username == "" ? "" : "user={$this->username} ") .
        ($this->password == "" ? "" : "password={$this->password}");
    return pg_connect($conn);
  }
  
  /**
   * @see BaseAdapter::disconnect()
   */
  function disconnect() {
    pg_close($this->connection());
  }
  
  /**
   * @see PreparedAdapter::platform_execute($sql, $mode)
   */              
  function platform_execute($sql, $mode) {
    if($mode == Query::INSERT_STMT) {
      $sql .= " RETURNING *";
      $parse = explode('(', $sql);
      $parse = explode(')', $parse[1]);
      $this->inserted_cols = explode(", ", $parse[0]);
    }
    parent::platform_execute($sql, $mode);
  }
  
  /**
   * @see PreparedAdapter::prepared_execute($sql, $mode)
   */     
  function prepared_execute($sql, $mode) {
    pg_prepare($this->connection(), "", $sql);
    return pg_execute($this->connection(), "", $this->bindings);
  } 
  
  /**
   * @see Prepared::standard_execute($sql, $mode) 
   */
  function standard_execute($sql, $mode) {
    return pg_query($this->connection(), $sql);
  }
  
  /**
   * @see BaseAdapter::affected_rows($set) 
   */
  function affected_rows($set) {
    return pg_affected_rows($set);
  }
  
  /**
   * @see BaseAdapter::fetch_assoc($set) 
   */
  function fetch_assoc($set) {
    return pg_fetch_assoc($set);
  }
  
  /**
   * @see BaseAdapter::insert_id($set) 
   */
  function insert_id($set) {
    $result = $this->fetch_assoc($set);
    foreach($result as $key => $value) {
      if(!in_array($key, $this->inserted_cols)) {
        return $value;
      }
    }
    return -1;
  }
  
  /**
   * @see PreparedAdapter::prepare($value)
   */     
  function prepare($value) {
    return "\$$counter";
  }
  
  /**
   * @see BaseAdapter::free_result($set) 
   */
  function free_result($set) {
    pg_free_result($set);  
  }
  
  /**
   * @see BaseAdapter::quote($str) 
   */
  function quote($str) {
    return "\"$str\"";
  }
  
  /**
   * @see BaseAdapter::addslashes($str)
   */     
  function addslashes($str) {
    return pg_escape_string($this->connection(), $str); 
  }
  
}
?>