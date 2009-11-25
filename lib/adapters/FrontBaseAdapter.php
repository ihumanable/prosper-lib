<?php
namespace Prosper;

class FrontBaseAdapter extends BaseAdapter {
	
	/**
   * @see BaseAdapter#connect()
   */
	function connect() {
		$this->connection = fbsql_connect($this->hostname, $this->username, $this->password);
		if($this->schema != "") {
			fbsql_select_db($this->schema, $this->connection);
		}
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		fbsql_close($this->connection());
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql, $mode)
	 */
	function platform_execute($sql, $mode) {
		return fbsql_query($sql, $this->connection());
	}
	
	/**
	 * @see BaseAdapter#affected_rows($set) 
	 */
	function affected_rows($set) {
		return fbsql_affected_rows($this->connection());
	}
	
	/**
	 * @see BaseAdapter#insert_id($set) 
	 */
	function insert_id($set) {
		return fbsql_insert_id($this->connection());
	}
	
	/**
	 * @see BaseAdapter#fetch_assoc($set) 
	 */
	function fetch_assoc($set) {
		return fbsql_fetch_assoc($set);
	}
	
	/**
	 * @see BaseAdapter#cleanup($set) 
	 */
	function cleanup($set) {
		fbsql_free_result($set);
	}
	
}
?>