<?php
namespace Prosper;

class MaxDBAdapter extends BaseAdapter {
	
	/**
   * @see BaseAdapter#connect()
   */
  function connect() {
		$this->connection = new maxdb($this->hostname, $this->username, $this->password, $this->schema);
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
	function platform_execute($sql, $mode) {
		return $this->connection()->query($sql);
	}
	
	/**
	 * @see BaseAdapter#affected_rows($set)
	 */
	function affected_rows($set) {
		return $this->connection()->affected_rows;
	}
	
	/**
	 * @see BaseAdapter#insert_id($set) 
	 */
	function insert_id($set) {
		return $this->connection()->insert_id;
	}
	
	/**
	 * @see BaseAdapter#fetch_assoc($set) 
	 */
	function fetch_assoc($set) {
		return $set->fetch_assoc();
	}
	
	/**
	 * @see BaseAdapter#cleanup($set) 
	 */
	function cleanup($set) {
		if($set instanceof maxdb_result) {
      $set->free();
    }
	}
	
}
?>