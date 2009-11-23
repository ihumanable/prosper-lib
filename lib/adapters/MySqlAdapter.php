<?php
namespace Prosper;

/**
 * MySql Database Adapter
 */
class MySqlAdapter extends BaseAdapter {
	
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
		$this->connection = new \mysqli($hostname, $username, $password, $schema);
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
		return $this->connection->affected_rows;
	}
	
	/**
	 * @see BaseAdapter#insert_id($set)
	 */
	protected function insert_id($set) {
		return $this->connection->insert_id;
	}
	
	/**
	 * @see BaseAdapter#fetch_assoc($set)
	 */
	protected function fetch_assoc($set) {
		return $set->fetch_array(MYSQLI_ASSOC);
	}
	
	/**
	 * @see BaseAdapter#cleanup($set)
	 */   	
	protected function cleanup($set) {
    if($set instanceof mysqli_result) {
      $set->close();
    }
  }
	
}

?>