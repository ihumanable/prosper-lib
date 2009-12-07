<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * PostgreSql Database Adapter
 */
class PostgreSqlAdapter extends BaseAdapter {
	
	private $inserted_cols;
	
	/**
   * @see BaseAdapter::connect()
   */
	function connect() {
		$conn = ($this->hostname == "" ? "" : "host={$this->hostname} ") .
		        ($this->schema   == "" ? "" : "dbname={$this->schema} ") .
				($this->username == "" ? "" : "user={$this->username} ") .
				($this->password == "" ? "" : "password={$this->password}");
		$this->connection = pg_connect($conn);
	}
	
	/**
	 * @see BaseAdapter::disconnect()
	 */
	function disconnect() {
		pg_close($this->connection());
	}
	
	/**
	 * @see BaseAdapter::platform_execute($sql, $mode) 
	 */
	function platform_execute($sql, $mode) {
		if($mode == Query::INSERT_STMT) {
			$sql .= " RETURNING *";
			$parse = explode('(', $sql);
			$parse = explode(')', $parse[1]);
			$this->inserted_cols = explode(", ", $parse[0]);
		}
		return pg_query($this->connection(), $sql);
	}
	
	/**
	 * @see BaseAdapter::affected_rows($set) 
	 */
	function affected_rows($set) {
		return pg_affected_rows($set);
	}
	
	/**
	 * @see BaseAdapter::fetch_assoc($set) 
	 */
	function fetch_assoc($set) {
		return pg_fetch_assoc($set);
	}
	
	/**
	 * @see BaseAdapter::insert_id($set) 
	 */
	function insert_id($set) {
		$result = $this->fetch_assoc($set);
		foreach($result as $key => $value) {
			if(!in_array($key, $this->inserted_cols)) {
				return $value;
			}
		}
		return -1;
	}
	
	/**
	 * @see BaseAdapter::free_result($set) 
	 */
	function free_result($set) {
		pg_free_result($set);	
	}
	
	/**
	 * @see BaseAdapter::quote($str) 
	 */
	function quote($str) {
		return "\"$str\"";
	}
	
	/**
	 * @see BaseAdapter::escape($str)
	 */   	
	function escape($str) {
    return "'" . pg_escape_string($this->connection(), $str) . "'"; 
  }
	
}
?>