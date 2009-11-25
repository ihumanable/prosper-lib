<?php
namespace Prosper;

/**
 * MySql Database Adapter
 */
class MySqlAdapter extends BaseAdapter {
	
	/**
   * @see BaseAdapter#connect()
   */
	function connect() {
		$this->connection = new \mysqli($this->hostname, $this->username, $this->password, $this->schema);
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		$this->connection()->close();
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql, $mode) 
	 */
	protected function platform_execute($sql, $mode) {
		return $this->connection()->query($sql);
	}
	
	/**
	 * @see BaseAdapter#affected_rows($set) 
	 */
	protected function affected_rows($set) {
		return $this->connection()->affected_rows;
	}
	
	/**
	 * @see BaseAdapter#insert_id($set)
	 */
	protected function insert_id($set) {
		return $this->connection()->insert_id;
	}
	
	/**
	 * @see BaseAdapter#fetch_assoc($set)
	 */
	protected function fetch_assoc($set) {
		return $set->fetch_array(MYSQLI_ASSOC);
	}
	
	/**
	 * @see BaseAdapter#cleanup($set)
	 */   	
	protected function cleanup($set) {
    if($set instanceof mysqli_result) {
      $set->close();
    }
  }
	
}

?>