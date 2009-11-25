<?php
namespace Prosper;

class SybaseAdapter extends BaseAdapter {
	
	/**
   * @see BaseAdapter#connect()
   */
	function connect() {
		$this->connection = sybase_connect($this->hostname, $this->username, $this->password);
		if($this->schema != "") {
			sybase_select_db($this->schema, $this->connection);
		}
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		sybase_close($this->connection());
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql, $mode)
	 */
	function platform_execute($sql, $mode) {
		return sybase_query($sql, $this->connection());
	}
	
	/**
	 * @see BaseAdapter#affected_rows($set)
	 */
	function affected_rows($set) {
		return sybase_affected_rows($set);
	}
	
	/**
	 * @see BaseAdapter#fetch_assoc($set)
	 */
	function fetch_assoc($set) {
		return sybase_fetch_assoc($set);
	}
	
	/**
	 * @see BaseAdapter#cleanup($set)
	 */
	function cleanup($set) {
		sybase_free_result($set);
	}
	
}