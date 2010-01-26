<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * Firebird (interbase) Database Adapter
 */
class FirebirdAdapter extends PreparedAdapter {
  
  private $transaction = null;
  
  /**
   * @see BaseAdapter::connect()
   */
  function connect() {
    $database = $this->hostname . ":" . $this->schema;
    return ibase_connect($database, $this->username, $this->password);
  }
  
  /**
   * @see BaseAdapter::disconnect()
   */
  function disconnect() {
    ibase_close($this->connection());
  }
  
  /**
   * Gets the appropriate link for prepare and query
   * @return Transaction if there is one, if not the result of connection
   * @see BaseAdapter::connection()
   */
  private function link() {
    if($this->transaction) {
      return $this->transaction;
    } else {
      return $this->connection();
    }
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
    $this->transaction = ibase_trans( IBASE_DEFAULT, $this->connection() );
  }
  
  /**
   * @see BaseAdapter::commit()
   */
  function commit() {
    ibase_commit($this->link());
  }
  
  /**
   * @see BaseAdapter::rollback()
   */
  function rollback() {
    ibase_rollback($this->link());
  }
  
  /**
   * @see PreparedAdapter::prepared_execute($sql, $mode) 
   */
  function prepared_execute($sql, $mode) {
    array_unshift($this->bindings, ibase_prepare($this->link(), $sql));
    return call_user_func_array('ibase_execute', $this->bindings);
  }
  
  /**
   * @see PreparedAdapter::standard_execute($sql, $mode)
   */     
  function standard_execute($sql, $mode) {
    return ibase_query($this->link(), $sql);
  }
  
  /**
   * @see BaseAdapter::affected_rows($set) 
   */
  function affected_rows($set) {
    return ibase_affected_rows($this->connection());
  }
  
  /**
   * @see BaseAdapter::fetch_assoc($set) 
   */
  function fetch_assoc($set) {
    return ibase_fetch_assoc($set);
  }
  
  /**
   * @see BaseAdapter::free_result($set) 
   */
  function free_result($set) {
    ibase_free_result($set);  
  }
  
}
?>