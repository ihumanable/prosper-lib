<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * MySql Database Adapter
 */
class MySqlAdapter extends BaseAdapter {
	
	/**
   * @see BaseAdapter::connect()
   */
	function connect() {
		$this->connection = new \mysqli($this->hostname, $this->username, $this->password, $this->schema);
	}
	
	/**
	 * @see BaseAdapter::disconnect()
	 */
	function disconnect() {
		$this->connection()->close();
	}
	
	/**
	 * @see BaseAdapter::platform_execute($sql, $mode) 
	 */
	function platform_execute($sql, $mode) {
		return $this->connection()->query($sql);
	}
	
	/**
	 * @see BaseAdapter::affected_rows($set) 
	 */
	function affected_rows($set) {
		return $this->connection()->affected_rows;
	}
	
	/**
	 * @see BaseAdapter::insert_id($set)
	 */
	function insert_id($set) {
		return $this->connection()->insert_id;
	}
	
	/**
	 * @see BaseAdapter::fetch_assoc($set)
	 */
	function fetch_assoc($set) {
		return $set->fetch_array(MYSQLI_ASSOC);
	}
	
	/**
	 * @see BaseAdapter::free_result($set)
	 */   	
	function free_result($set) {
    if($set instanceof mysqli_result) {
      $set->close();
    }
  }
  
  /**
   * @see BaseAdapter::addslashes($str)
   */     
  function addslashes($str) {
    return $this->connection()->escape_string($str); 
  }
  
  /**
   * @see BaseAdapter::tables($filter)
   */     
  function tables($filter) {
    if($filter == null) {
      $tables = $this->execute("show tables");
      foreach($tables as $table) {
        $filter[] = $table["Tables_in_{$this->schema}"];
      }
    }
    
    foreach($filter as $table) {
      $data = $this->execute("describe $table");
      foreach($data as $column) {
        $name = $column['Field'];
        
        $platform_type = $this->type_array($column['Type']);
        
        $result[$table][$name]['type']           = $this->cross_type($platform_type);
        $result[$table][$name]['platform_type']  = $platform_type;
        $result[$table][$name]['null']           = ($column['Null'] == "YES");
        $result[$table][$name]['pk']             = ($column['Key'] == "PRI");
        $result[$table][$name]['default']        = $column['Default'];
        $result[$table][$name]['auto_increment'] = ($column['Extra'] == 'auto_increment');
      }
    }
    return $result;
  }
	
	/**
	 * @see BaseAdapter::cross_type($platform)
	 */   	
	function cross_type($platform) {
    return $platform;
  }
  
  function platform_type($cross) {
    return $cross;
  }
	
}

?>