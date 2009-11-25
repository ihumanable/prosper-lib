<?php
namespace Prosper;

class DB2Adapter extends BaseAdapter {

  /**
   * @see BaseAdapter#connect()
   */     	
	function connect() {
		$conn = "DRIVER={IBM DB2 ODBC DRIVER};DATABASE={$this->schema};HOSTNAME={$this->hostname};PORT=50000;PROTOCOL=TCPIP;UID={$this->username};PWD={$this->password}";
		$this->connection = db2_connect($conn);
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		db2_close($this->connection());
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql, $mode) 
	 */
	function platform_execute($sql, $mode) {
		return db2_prepare($this->connection(), $sql);
	}
	
	/**
	 * @see BaseAdapter#affected_rows($set) 
	 */
	function affected_rows($set) {
		return db2_num_rows($set);
	}
	
	/**
	 * @see BaseAdapter#insert_id($set) 
	 */
	function insert_id($set) {
		return db2_last_insert_id($this->connection());
	}
	
	/**
	 * @see BaseAdapter#fetch_assoc($set)
	 */
	function fetch_assoc($set) {
		return db2_fetch_assoc($set);
	}
	
	/**
	 * @see BaseAdapter#cleanup($set) 
	 */
	function cleanup($set) {
		db2_free_stmt($set);
	}
	
	
}

?>