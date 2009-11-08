<?php
namespace Prosper;

class MSqlAdapter extends BaseAdapter {
	
	/**
 	 * Establishes an MSql Adapter 
	 * @param string $username Database username
	 * @param string $password Database password
	 * @param string $hostname Database hostname
	 * @param string $schema Database schema
	 * @return New Adapter Instance
	 */
	function __construct($username, $password, $hostname, $schema) {
		parent::__construct($username, $password, $hostname, $schema);
		$this->connection = msql_connect($hostname, $username, $password);
		if($schema != "") {
			msql_select_db($schema, $this->connection);
		}
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		msql_close($this->connection);
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql)
	 */
	function platform_execute($sql) {
		return msql_query($sql, $this->connection);
	}
	
	/**
	 * @see BaseAdapter#affected_rows($set)
	 */
	function affected_rows($set) {
		return msql_affected_rows($set);
	}
	
	/**
	 * @see BaseAdapter#fetch_assoc($set)
	 */
	function fetch_assoc($set) {
		return msql_fetch_array($set, MSQL_ASSOC);
	}
	
	/**
	 * @see BaseAdapter#cleanup($set)
	 */
	function cleanup($set) {
		msql_free_result($set);
	} 
}

