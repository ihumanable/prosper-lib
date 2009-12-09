<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * DB2 Database Adapter
 */
class DB2Adapter extends BaseAdapter implements IPreparable {

  private $bindings;
  private $prepared;

  /**
   * @see BaseAdapter::connect()
   */     	
	function connect() {
		$conn = "DRIVER={IBM DB2 ODBC DRIVER};DATABASE={$this->schema};HOSTNAME={$this->hostname};PORT=50000;PROTOCOL=TCPIP;UID={$this->username};PWD={$this->password}";
		$this->connection = db2_connect($conn);
	}
	
	/**
	 * @see BaseAdapter::disconnect()
	 */
	function disconnect() {
		db2_close($this->connection());
	}
	
	/**
	 * @see BaseAdapter::platform_execute($sql, $mode) 
	 */
	function platform_execute($sql, $mode) {
		if($this->prepared) {
		  $stmt = db2_prepare($this->connection(), $sql);
		  db2_execute($stmt, $this->bindings);
		  return $stmt;
		} else {
      return db2_exec($this->connection(), $sql);
    }
	}
	
	/**
	 * @see BaseAdapter::affected_rows($set) 
	 */
	function affected_rows($set) {
		return db2_num_rows($set);
	}
	
	/**
	 * @see BaseAdapter::insert_id($set) 
	 */
	function insert_id($set) {
		return db2_last_insert_id($this->connection());
	}
	
	/**
	 * @see BaseAdapter::fetch_assoc($set)
	 */
	function fetch_assoc($set) {
		return db2_fetch_assoc($set);
	}
	
	/**
	 * @see BaseAdapter::free_result($set) 
	 */
	function free_result($set) {
		db2_free_stmt($set);
	}
	
	/**
	 * @see BaseAdapter::addslashes($str)
	 */   	
	function addslashes($str) {
     return db2_escape_string($str);
  }
	
  /**
   * @see IPreparable::prepare($value)
   */              
  function prepare($value) {
    $this->prepared = true;
    $this->bindings[] = $value;
    return '?'; 
  }        
}

?>