<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * MaxDB Database Adapter
 */
class MaxDBAdapter extends BaseAdapter {
	
	/**
   * @see BaseAdapter::connect()
   */
  function connect() {
		$this->connection = new maxdb($this->hostname, $this->username, $this->password, $this->schema);
	}
	
	/**
	 * @see BaseAdapter::disconnect()
	 */
	function disconnect() {
		$this->connection()->close();
	}
	
	/**
	 * @see BaseAdapter::platform_execute($sql, $mode)
	 */
	function platform_execute($sql, $mode) {
		return $this->connection()->query($sql);
	}
	
	/**
	 * @see BaseAdapter::affected_rows($set)
	 */
	function affected_rows($set) {
		return $this->connection()->affected_rows;
	}
	
	/**
	 * @see BaseAdapter::insert_id($set) 
	 */
	function insert_id($set) {
		return $this->connection()->insert_id;
	}
	
	/**
	 * @see BaseAdapter::fetch_assoc($set) 
	 */
	function fetch_assoc($set) {
		return $set->fetch_assoc();
	}
	
	/**
	 * @see BaseAdapter::free_result($set) 
	 */
	function free_result($set) {
		if($set instanceof maxdb_result) {
      $set->free();
    }
	}
	
	/**
	 * @see BaseAdapter::escape($str)
	 */   	
	function escape($str) {
    return "'" . $this->connection()->real_escape_string($str) . "'";
  }
	
}
?>