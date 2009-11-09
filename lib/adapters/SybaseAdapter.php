<?php
namespace Prosper;

class SybaseAdapter extends BaseAdapter {
	
	/**
	 * Establishes a Sybase Adapter
	 * @param string $username Database username
	 * @param string $password Database password
	 * @param string $hostname Database hostname
	 * @param string $schema Database schema
	 * @return New Adapter Instance
	 */
	function __construct($username, $password, $hostname, $schema) {
		parent::__construct($username, $password, $hostname, $schema);
		$this->connection = sybase_connect($hostname, $username, $password);
		if($schema != "") {
			sybase_select_db($schema, $this->connection);
		}
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		sybase_close($this->connection);
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql, $mode)
	 */
	function platform_execute($sql, $mode) {
		return sybase_query($sql, $this->connection);
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