<?php
namespace Prosper;

class OracleAdapter extends BaseAdapter {
	
	/**
   * @see BaseAdapter#connect()
   */
	function connect() {
		$conn = "//{$this->hostname}" . ($this->schema != "" ? "/{$this->schema}" : "");
		$this->connection = oci_connect($this->username, $this->password, $conn);
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		oci_close($this->connection());
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql, $mode)
	 */
	protected function platform_execute($sql, $mode) {
		$stmt = oci_parse($this->connection(), $sql);
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
	function true_value() {
		return "1";
	}
	
	/**
	 * @see BaseAdapter#falsehood()
	 */
	function false_value() {
		return "0";
	}
	
}