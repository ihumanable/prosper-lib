<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * Sqlite Database Adapter
 */
class SqliteAdapter extends BaseAdapter {
  
  /**
   * @see BaseAdapter::connect()
   */
  function connect() {
    return new \SQLite3($this->username);
  }
  
  /**
   * @see BaseAdapter::disconnect()
   */
  function disconnect() {
    $this->connection()->close();
  }
  
  /**
   * @see BaseAdapter::platform_execute($sql, $mode)
   */
  function platform_execute($sql, $mode) {
    return $this->connection()->query($sql);
  } 
  
  /**
   * @see BaseAdapter::affected_rows($set) 
   */
  function affected_rows($set) {
    return $this->connection()->changes();
  }
  
  /**
   * @see BaseAdapter::insert_id($set) 
   */
  function insert_id($set) {
    return $this->connection()->lastInsertRowID();
  }
  
  /**
   * @see BaseAdapter::fetch_assoc($set) 
   */
  function fetch_assoc($set) {
    return $set->fetchArray(SQLITE3_ASSOC);
  }

  /**
   * @see BaseAdapter::free_result($set)
   */     
  function free_result($set) {
    if($set instanceof SQLite3Result) {
      $set->finalize();
    }
  }

  /**
   * @see BaseAdapter::addslashes($str)
   */     
  function addslashes($str) {
    return $this->connection()->escapeString($str);
  }

  /**
   * @see BaseAdapter::true_value()
   */
  function true_value() {
    return "1";
  }
  
  /**
   * @see BaseAdapter::false_value()
   */
  function false_value() {
    return "0";
  }
  
}
?>