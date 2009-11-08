<?php
namespace Prosper;

class FirebirdAdapter extends BaseAdapter {
	
	/**
	 * Establishes a Firebird / InterBase Adapter
	 * @param string $username Database username
	 * @param string $password Database password
	 * @param string $hostname Database hostname
	 * @param string $schema Database schema
	 * @return New Adapter Instance
	 */
	function __construct($username, $password, $hostname, $schema) { 
		parent::__construct($username, $password, $hostname, $schema);
		$database = $hostname . ":" . $schema;
		$this->connection = ibase_connect($database, $username, $password);
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		ibase_close($this->connection);
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql) 
	 */
	function platform_execute($sql) {
		return ibase_query($this->connection, $sql);
	}
	
	/**
	 * @see BaseAdapter#affected_rows($set) 
	 */
	function affected_rows($set) {
		return ibase_affected_rows($this->connection);
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