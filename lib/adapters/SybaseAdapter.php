<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * Sybase Database Adapter
 */
class SybaseAdapter extends BaseAdapter {
  
  /**
   * @see BaseAdapter::connect()
   */
  function connect() {
    $connection = sybase_connect($this->hostname, $this->username, $this->password);
    if($this->schema != "") {
      sybase_select_db($this->schema, $connection);
    }
    return $connection
  }
  
  /**
   * @see BaseAdapter::disconnect()
   */
  function disconnect() {
    sybase_close($this->connection());
  }
  
  /**
   * @see BaseAdapter::platform_execute($sql, $mode)
   */
  function platform_execute($sql, $mode) {
    return sybase_query($sql, $this->connection());
  }
  
  /**
   * @see BaseAdapter::affected_rows($set)
   */
  function affected_rows($set) {
    return sybase_affected_rows($set);
  }
  
  /**
   * @see BaseAdapter::fetch_assoc($set)
   */
  function fetch_assoc($set) {
    return sybase_fetch_assoc($set);
  }
  
  /**
   * @see BaseAdapter::free_result($set)
   */
  function free_result($set) {
    sybase_free_result($set);
  }
  
  /**
   * @see BaseAdapter::addslashes($str)
   */     
  function addslashes($str) {
    return str_replace("'", "''", $str);
  }
  
  
}