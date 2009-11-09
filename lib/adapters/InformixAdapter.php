<?php
namespace Prosper;

class InformixAdapter extends BaseAdapter {
	
	/**
	 * Establishes an Informix Adapter
	 * @param string $username Database username
	 * @param string $password Database password
	 * @param string $hostname Database hostname
	 * @param string $schema Database schema
	 * @return New Adapter Instance
	 */
	function __construct($username, $password, $hostname, $schema) {
		parent::__construct($username, $password, $hostname, $schema);
		$database = $schema . "@" . $hostname;
		$this->connection = ifx_connect($database, $username, $password);
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		ifx_close($this->connection);
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql, $mode) 
	 */
	function platform_execute($sql, $mode) {
		return ifx_query($sql, $this->connection);
	}
	
	/**
	 * @see BaseAdapter#affected_rows($set) 
	 */
	function affected_rows($set) {
		return ifx_affected_rows($set);
	}
	
	/**
	 * @see BaseAdapter#fetch_assoc($set) 
	 */
	function fetch_assoc($set) {
		return ifx_fetch_Row($set, "NEXT");
	}
	
	/**
	 * @see BaseAdapter#cleanup($set)
	 * @param object $set
	 * @return 
	 */
	function cleanup($set) {
		ifx_free_result($set);
	}
	
} 

?>