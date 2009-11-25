<?php
namespace Prosper;

class FirebirdAdapter extends BaseAdapter {
	
	/**
   * @see BaseAdapter#connect()
   */
	function connect() {
		$database = $this->hostname . ":" . $this->schema;
		$this->connection = ibase_connect($database, $this->username, $this->password);
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		ibase_close($this->connection());
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql, $mode) 
	 */
	function platform_execute($sql, $mode) {
		return ibase_query($this->connection(), $sql);
	}
	
	/**
	 * @see BaseAdapter#affected_rows($set) 
	 */
	function affected_rows($set) {
		return ibase_affected_rows($this->connection());
	}
	
	/**
	 * @see BaseAdapter#fetch_assoc($set) 
	 */
	function fetch_assoc($set) {
		return ibase_fetch_assoc($set);
	}
	
	/**
	 * @see BaseAdapter#cleanup($set) 
	 */
	function cleanup($set) {
		ibase_free_result($set);	
	}
	
}
?>