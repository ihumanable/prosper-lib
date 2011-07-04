<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * FrontBase Database Adapter
 */ 
class FrontBaseAdapter extends BaseAdapter {
  
  /**
   * @see BaseAdapter::connect()
   */
  function connect() {
    $connection = fbsql_connect($this->hostname, $this->username, $this->password);
    if($this->schema != "") {
      fbsql_select_db($this->schema, $connection);
    }
    return $connection;
  }
  
  /**
   * @see BaseAdapter::disconnect()
   */
  function disconnect() {
    fbsql_close($this->connection());
  }
  
  /**
   * @see BaseAdapter::has_transactions()
   */     
  function has_transactions() {
    return true;
  }
  
  /**
   * @see BaseAdapter::begin()
   */
  function begin() {
    fbsql_autocommit($this->connection(), false);
  }
  
  /**
   * @see BaseAdapter::commit()
   */
  function commit() {
    fbsql_commit($this->connection());
  }
  
  /**
   * @see BaseAdapter::rollback()
   */
  function rollback() {
    fbsql_rollback($this->connection());
  }
  
  /**
   * @see BaseAdapter::end()
   */     
  function end() {
    fbsql_autocommit($this->connectiono(), true);
  }
  
  /**
   * @see BaseAdapter::platform_execute($sql, $mode)
   */
  function platform_execute($sql, $mode) {
    return fbsql_query($sql, $this->connection());
  }
  
  /**
   * @see BaseAdapter::affected_rows($set) 
   */
  function affected_rows($set) {
    return fbsql_affected_rows($this->connection());
  }
  
  /**
   * @see BaseAdapter::insert_id($set) 
   */
  function insert_id($set) {
    return fbsql_insert_id($this->connection());
  }
  
  /**
   * @see BaseAdapter::fetch_assoc($set) 
   */
  function fetch_assoc($set) {
    return fbsql_fetch_assoc($set);
  }
  
  /**
   * @see BaseAdapter::free_result($set) 
   */
  function free_result($set) {
    fbsql_free_result($set);
  }
  
}
?>