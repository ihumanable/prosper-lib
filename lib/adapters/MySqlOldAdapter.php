<?php
namespace Prosper;

/**
 * MySql Old Database Adapter
 */
class MySqlOldAdapter extends BaseAdapter {
	
	/**
   * @see BaseAdapter#connect()
   */
	function connect() {
		$this->connection = mysql_connect($this->hostname, $this->username, $this->password);
		if($this->schema != "") {
      mysql_select_db($this->schema, $this->connection);
    }
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		mysql_close($this->connection());
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql, $mode) 
	 */
	protected function platform_execute($sql, $mode) {
		return mysql_query($sql, $this->connection());
	}
	
	/**
	 * @see BaseAdapter#affected_rows($set) 
	 */
	protected function affected_rows($set) {
		return mysql_affected_rows($this->connection());
	}
	
	/**
	 * @see BaseAdapter#insert_id($set)
	 */
	protected function insert_id($set) {
		return mysql_insert_id($this->connection());
	}
	
	/**
	 * @see BaseAdapter#fetch_assoc($set)
	 */
	protected function fetch_assoc($set) {
		return mysql_fetch_assoc($set);
	}
	
	/**
	 * @see BaseAdapter#cleanup($set)
	 */   	
	protected function cleanup($set) {
    mysql_free_result($set);
  }
	
}

?>