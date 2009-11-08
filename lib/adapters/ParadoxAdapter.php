<?php
namespace Prosper;

class ParadoxAdapter extends BaseAdapter {
	
	private $file_handle;
	
	/**
	 * Establishes a Paradox Adapter
	 * @param string $username Database username
	 * @param string $password Database password
	 * @param string $hostname Database hostname
	 * @param string $schema Database schema
	 * @return New Adapter Instance
	 */
	function __construct($username, $password, $hostname, $schema) {
		parent::__construct($username, $password, $hostname, $schema);
		$this->file_handle = fopen($username, "rw");
		$this->connection = new paradox_db();
		$this->connection->open_fp($this->file_handle);
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		$this->connection->close();
		fclose($this->file_handle);
	}
	
	
}