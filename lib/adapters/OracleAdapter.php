<?php
namespace Prosper;

class OracleAdapter extends BaseAdapter {
	
	/**
	 * Creates an Oracle Connection Adapter
	 * @param string $username Database username
	 * @param string $password Database password
	 * @param string $hostname Database hostname
	 * @param string $schema Database schema
	 * @return Adapter Instance
	 */
	function __construct($username, $password, $hostname, $schema) {
		parent::__construct($username, $password, $hostname, $schema);
		$conn = "//$hostname/$schema";
		$this->connection = oci_connect($username, $password, $conn);
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		oci_close($this->connection);
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql, $mode)
	 */
	protected function platform_execute($sql, $mode) {
		$stmt = oci_parse($this->connection, $sql);
		oci_execute($stmt);
		return $stmt;
	} 
	
	/**
	 * @see BaseAdapter#affected_rows($set) 
	 */
	protected function affected_rows($set) {
		return oci_num_rows($set);
	}
	
	
	/**
	 * @see BaseAdapter#fetch_assoc($set) 
	 */
	protected function fetch_assoc($set) {
		return oci_fetch_assoc($set);
	}

	/**
	 * @see BaseAdapter#cleanup($set)
	 */
	protected function cleanup($set) {
		oci_free_statment($set);
	}
	
	/**
	 * @see BaseAdapter#truth()
	 */
	function truth() {
		return "1";
	}
	
	/**
	 * @see BaseAdapter#falsehood()
	 */
	function falsehood() {
		return "0";
	}
	
}