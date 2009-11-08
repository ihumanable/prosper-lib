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
	
	function platform_execute($sql) {
		return ingres_query($this->connection, $sql);
	}
	
	function affected_rows($set) {
		return ingrest_num_rows($set);
	}	
	
	function fetch_assoc($set) {
		return ingres_fetch_array($set, INGRES_ASSOC);
	}
	
	function cleanup($set) { 
		ingres_free_result($set);
	}
	
}
?>