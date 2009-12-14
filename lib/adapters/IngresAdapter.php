<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * Ingres Database Adapter
 */
class IngresAdapter extends PreparedAdapter {
	
	/**
   * @see BaseAdapter::connect()
   */
	function connect() {
		$this->connection = ingres_connect($this->hostname, $this->username, $this->password);
	}
	
	/**
	 * @see BaseAdapter::disconnect()
	 */
	function disconnect() {
		ingres_close($this->connection());
	}
	
	/**
	 * @see PreparedAdapter::prepared_execute($sql, $mode)
	 */
	function platform_execute($sql, $mode) {
		return ingres_query($this->connection(), $sql, $this->bindings);
	}
	
	/**
	 * @see PreparedAdapter::standard_execute($sql, $mode)
	 */   	
	function standard_execute($sql, $mode) {
    return ingres_query($this->connection(), $sql);
	}
	
	/**
	 * @see BaseAdapter::affected_rows($set)
	 */
	function affected_rows($set) {
		return ingrest_num_rows($set);
	}	
	
	/**
	 * @see BaseAdapter::fetch_assoc($set)
	 */
	function fetch_assoc($set) {
		return ingres_fetch_array($set, INGRES_ASSOC);
	}
	
	/**
	 * @see BaseAdapter::free_result($set)
	 */
	function free_result($set) { 
		ingres_free_result($set);
	}
	
	/**
	 * @see BaseAdapter::addslashes($str)
	 */   	
	function addslashes($str) {
    return ingres_escape_string($this->connection(), $str);
  }
}
?>