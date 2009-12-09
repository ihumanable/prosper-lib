<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * Firebird (interbase) Database Adapter
 */
class FirebirdAdapter extends PreparedAdapter {
	
	/**
   * @see BaseAdapter::connect()
   */
	function connect() {
		$database = $this->hostname . ":" . $this->schema;
		$this->connection = ibase_connect($database, $this->username, $this->password);
	}
	
	/**
	 * @see BaseAdapter::disconnect()
	 */
	function disconnect() {
		ibase_close($this->connection());
	}
	
	/**
	 * @see PreparedAdapter::prepared_execute($sql, $mode) 
	 */
	function prepared_execute($sql, $mode) {
    array_unshift($this->bindings, ibase_prepare($this->connection(), $sql));
    return call_user_func_array('ibase_execute', $this->bindings);       
  }
  
  /**
   * @see PreparedAdapter::standard_execute($sql, $mode)
   */     
  function standard_execute($sql, $mode) {
    return ibase_query($this->connection(), $sql);
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