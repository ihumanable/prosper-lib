<?php
namespace Prosper;

/**
 * Sqlite Database Adapter
 */
class SqliteAdapter extends BaseAdapter {
	/**
	 * Creates a Sqlite Connection Adapter
	 * @param string $username Database username
	 * @param string $password Database password
	 * @param string $hostname Database hostname
	 * @param string $schema Database schema
	 * @return Adapter Instance
	 */
	function __construct($username, $password, $hostname, $schema) {
		parent::__construct($username, $password, $hostname, $schema);
		$this->connection = new \SQLite3($username);
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
	protected function platform_execute($sql, $mode) {
		return $this->connection->query($sql);
	} 
	
	/**
	 * @see BaseAdapter#affected_rows($set) 
	 */
	protected function affected_rows($set) {
		return $this->connection->changes();
	}
	
	/**
	 * @see BaseAdapter#insert_id($set) 
	 */
	protected function insert_id($set) {
		return $this->connection->lastInsertRowID();
	}
	
	/**
	 * @see BaseAdapter#fetch_assoc($set) 
	 */
	protected function fetch_assoc($set) {
		return $set->fetchArray(SQLITE3_ASSOC);
	}

}
?>