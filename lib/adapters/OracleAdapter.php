<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * Oracle Database Adapter
 */
class OracleAdapter extends PreparedAdapter {
	
	private $counter = 0;
	
	/**
   * @see BaseAdapter::connect()
   */
	function connect() {
		$conn = "//{$this->hostname}" . ($this->schema != "" ? "/{$this->schema}" : "");
		$this->connection = oci_connect($this->username, $this->password, $conn);
	}
	
	/**
	 * @see BaseAdapter::disconnect()
	 */
	function disconnect() {
		oci_close($this->connection());
	}
	
	/**
	 * @see PreparedAdapter::prepared_execute($sql, $mode)
	 */      	
	function prepared_execute($sql, $mode) {
    $stmt = oci_parse($this->connection(), $sql);
    
    $counter = 0;
    foreach($this->bindings as $key => $binding) {
      $counter++;
      oci_bind_by_name($stmt, "arg$counter", $this->bindings[$key]);              
    }
    
    oci_execute($stmt);
    return $stmt;          
  }
	
	/**
	 * @see PreparedAdapter::standard_execute($sql, $mode)
	 */
	function standard_execute($sql, $mode) {
		$stmt = oci_parse($this->connection(), $sql);
		oci_execute($stmt);
		return $stmt;
	} 
	
	/**
	 * @see BaseAdapter::affected_rows($set) 
	 */
	function affected_rows($set) {
		return oci_num_rows($set);
	}
	
	
	/**
	 * @see BaseAdapter::fetch_assoc($set) 
	 */
	function fetch_assoc($set) {
		return oci_fetch_assoc($set);
	}

	/**
	 * @see BaseAdapter::free_result($set)
	 */
	function free_result($set) {
		oci_free_statment($set);
	}
	
	/**
	 * @see PreparedAdapter::prepare($value)
	 */   	
	function prepare($value) {
    $this->counter++;
    return ":arg{$this->counter}";
  }
	
	/**
	 * @see BaseAdapter::truth()
	 */
	function true_value() {
		return "1";
	}
	
	/**
	 * @see BaseAdapter::falsehood()
	 */
	function false_value() {
		return "0";
	}
	
}