<?php
namespace Prosper;

/**
 * MySql Old Database Adapter
 */
class MySqlOldAdapter extends BaseAdapter {
	
	/**
	 * Creates a MySQL Connection Adapter
	 * @param string $username Database username
	 * @param string $password Database password
	 * @param string $hostname Database hostname
	 * @param string $schema Database schema
	 * @return Adapter Instance
	 */
	function __construct($username, $password, $hostname, $schema) {
		parent::__construct($username, $password, $hostname, $schema);
		$this->connection = mysql_connect($hostname, $username, $password);
		if($schema != "") {
      mysql_select_db($schema, $this->connection);
    }
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		mysql_close($this->connection);
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql, $mode) 
	 */
	protected function platform_execute($sql, $mode) {
		return mysql_query($sql, $this->connection);
	}
	
	/**
	 * @see BaseAdapter#affected_rows($set) 
	 */
	protected function affected_rows($set) {
		return mysql_affected_rows($this->connection);
	}
	
	/**
	 * @see BaseAdapter#insert_id($set)
	 */
	protected function insert_id($set) {
		return mysql_insert_id($this->connection);
	}
	
	/**
	 * @see BaseAdapter#fetch_assoc($set)
	 */
	protected function fetch_assoc($set) {
		return mysql_fetch_assoc($set);
	}
	
	/**
	 * @see BaseAdapter#cleanup($set)
	 */   	
	protected function cleanup($set) {
    mysql_free_result($set);
  }
	
}

?>