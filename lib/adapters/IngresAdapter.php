<?php
namespace Prosper;

class IngresAdapter extends BaseAdapter {
	
	/**
	 * Establishes a Ingres Adapter
	 * @param string $username Database username
	 * @param string $password Database password
	 * @param string $hostname Database hostname
	 * @param string $schema Database schema
	 * @return New Adapter Instance
	 */
	function __construct($username, $password, $hostname, $schema) {
		parent::__construct($username, $password, $hostname, $schema);
		$this->connection = ingres_connect($hostname, $username, $password);
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		ingres_close($this->connection);
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql, $mode)
	 */
	function platform_execute($sql, $mode) {
		return ingres_query($this->connection, $sql);
	}
	
	/**
	 * @see BaseAdapter#affected_rows($set)
	 */
	function affected_rows($set) {
		return ingrest_num_rows($set);
	}	
	
	/**
	 * @see BaseAdapter#fetch_assoc($set)
	 */
	function fetch_assoc($set) {
		return ingres_fetch_array($set, INGRES_ASSOC);
	}
	
	/**
	 * @see BaseAdapter#cleanup($set)
	 */
	function cleanup($set) { 
		ingres_free_result($set);
	}
	
}
?>