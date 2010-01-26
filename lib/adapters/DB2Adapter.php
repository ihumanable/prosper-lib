<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * DB2 Database Adapter
 */
class DB2Adapter extends PreparedAdapter {

  /**
   * @see BaseAdapter::connect()
   */
  function connect() {
    $conn = "DRIVER={IBM DB2 ODBC DRIVER};DATABASE={$this->schema};HOSTNAME={$this->hostname};PORT=50000;PROTOCOL=TCPIP;UID={$this->username};PWD={$this->password}";
    return db2_connect($conn);
  }
  
  /**
   * @see BaseAdapter::disconnect()
   */
  function disconnect() {
    db2_close($this->connection());
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
    db2_autocommit($this->connection(), DB2_AUTOCOMMIT_OFF);
  }
  
  /**
   * @see BaseAdapter::commit()
   */
  function commit() {
    db2_commit($this->connection());
  }
  
  /**
   * @see BaseAdapter::rollback()
   */
  function rollback() {
    db2_rollback($this->connection());
  }
  
  /**
   * @see BaseAdapter::end()
   */
  function end() {
    db2_autocommit($this->connection(), DB2_AUTOCOMMIT_ON);
  }
  
  /**
   * @see PreparedAdapter::prepared_execute($sql, $mode) 
   */
  function prepared_execute($sql, $mode) {
    $stmt = db2_prepare($this->connection(), $sql);
    db2_execute($stmt, $this->bindings);
    return $stmt;
  }
  
  /**
   * @see PreparedAdapter::standard_execute($sql, $mode)
   */     
  function standard_execute($sql, $mode) {
      return db2_exec($this->connection(), $sql);
  }
  
  /**
   * @see BaseAdapter::affected_rows($set) 
   */
  function affected_rows($set) {
    return db2_num_rows($set);
  }
  
  /**
   * @see BaseAdapter::insert_id($set) 
   */
  function insert_id($set) {
    return db2_last_insert_id($this->connection());
  }
  
  /**
   * @see BaseAdapter::fetch_assoc($set)
   */
  function fetch_assoc($set) {
    return db2_fetch_assoc($set);
  }
  
  /**
   * @see BaseAdapter::free_result($set) 
   */
  function free_result($set) {
    db2_free_stmt($set);
  }
  
  /**
   * @see BaseAdapter::addslashes($str)
   */     
  function addslashes($str) {
     return db2_escape_string($str);
  }
  
}

?>