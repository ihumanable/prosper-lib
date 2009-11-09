<?php
namespace Prosper;

class MaxDBAdapter extends BaseAdapter {
	
	/**
	 * Establishes a MaxDB Adapter
	 * @param string $username Database username
	 * @param string $password Database password
	 * @param string $hostname Database hostname
	 * @param string $schema Database schema
	 * @return New Adapter Instance
	 */
	function __construct($username, $password, $hostname, $schema) {
		parent::__construct($username, $password, $hostname, $schema);
		$this->connection = new maxdb($hostname, $username, $password, $schema);
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		$this->connection->close();
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql, $mode)
	 */
	function platform_execute($sql, $mode) {
		return $this->connection->query($sql);
	}
	
	/**
	 * @see BaseAdapter#affected_rows($set)
	 */
	function affected_rows($set) {
		return $this->connection->affected_rows;
	}
	
	/**
	 * @see BaseAdapter#insert_id($set) 
	 */
	function insert_id($set) {
		return $this->connection->insert_id;
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
		$set->free();
	}
	
}
?>