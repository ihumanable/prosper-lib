<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * MySql Old Database Adapter
 */
class MySqlOldAdapter extends BaseAdapter {
  
  /**
   * @see BaseAdapter::connect()
   */
  function connect() {
    $connection = mysql_connect($this->hostname, $this->username, $this->password);
    if($this->schema != "") {
      mysql_select_db($this->schema, $connection);
    }
    return $connection;
  }
  
  /**
   * @see BaseAdapter::disconnect()
   */
  function disconnect() {
    mysql_close($this->connection());
  }
  
  /**
   * @see BaseAdapter::platform_execute($sql, $mode) 
   */
  function platform_execute($sql, $mode) {
    return mysql_query($sql, $this->connection());
  }
  
  /**
   * @see BaseAdapter::affected_rows($set) 
   */
  function affected_rows($set) {
    return mysql_affected_rows($this->connection());
  }
  
  /**
   * @see BaseAdapter::insert_id($set)
   */
  function insert_id($set) {
    return mysql_insert_id($this->connection());
  }
  
  /**
   * @see BaseAdapter::fetch_assoc($set)
   */
  function fetch_assoc($set) {
    return mysql_fetch_assoc($set);
  }
  
  /**
   * @see BaseAdapter::free_result($set)
   */     
  function free_result($set) {
    mysql_free_result($set);
  }
  
  /**
   * @see BaseAdapter::addslashes($str)
   */     
  function addslashes($str) {
    return mysql_real_escape_string($str, $this->connection());
  }
  
}

?>