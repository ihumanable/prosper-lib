<?php
namespace Prosper;

class FrontBaseAdapter extends BaseAdapter {
	
	/**
	 * Establishes a connection
	 * @param string $username Database username
	 * @param string $password Database password
	 * @param string $hostname Database hostname
	 * @param string $schema Database schema
	 * @return New Adapter Instance
	 */
	function __construct($username, $password, $hostname, $schema) {
		parent::__construct($username, $passwor, $hostname, $schema);
		$this->connection = fbsql_connect($hostname, $username, $password);
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql)
	 */
	function platform_execute($sql) {
		return fbsql_query($sql, $this->connection);
	}
	
	/**
	 * @see BaseAdapter#affected_rows($set) 
	 */
	function affected_rows($set) {
		return fbsql_affected_rows($this->connection);
	}
	
	/**
	 * @see BaseAdapter#insert_id($set) 
	 */
	function insert_id($set) {
		return fbsql_insert_id($this->connection);
	}
	
	/**
	 * @see BaseAdapter#fetch_assoc($set) 
	 */
	function fetch_assoc($set) {
		return fbsql_fetch_assoc($set);
	}
	
	/**
	 * @see BaseAdapter#cleanup($set) 
	 */
	function cleanup($set) {
		fbsql_free_result($set);
	}
	
}
?>