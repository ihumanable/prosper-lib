<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * MySql Database Adapter
 */
class MySqlAdapter extends BaseAdapter implements IPreparable {
	
	private $bindings;
	private $prepared = false; 
	
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
		if($this->prepared) {
      $stmt = $this->connection()->prepare($sql);
      foreach($this->bindings as $binding) {
        $type .= $binding['type'];
        $values[] = $binding['value'];
      }
      $arguments = array(&$type);
      foreach($values as $k => $v) {
        $arguments[] = &$values[$k];
      }
      call_user_func_array(array($stmt, 'bind_param'), $arguments);
      $stmt->execute();
      return $stmt;
    } else {
      return $this->connection()->query($sql);
    }
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
		if($this->prepared) {
      $meta = $set->result_metadata();
      while($field = $meta->fetch_field()) {
        $params[] = &$row[$field->name];    
      }
      call_user_func_array(array($set, 'bind_result'), $params);
      
      if($set->fetch()) {
        foreach($row as $key => $value) {
          $result[$key] = $value;
        }
        return $result;
      } else {
        return false;
      }
    } else {
      return $set->fetch_array(MYSQLI_ASSOC);
    }
	}
	
	/**
	 * @see BaseAdapter::free_result($set)
	 */   	
	function free_result($set) {
    if(is_object($set)) {
      $set->close();
    } 
  }
 
  /**
   * @see BaseAdapter::true_value()
   */      
  function true_value() {
    return 1;
  }
  
  /**
   * @see BaseAdapter::false_value()
   */     
  function false_value() {
    return 0;
  }
  
  /**
   * @see BaseAdapter::addslashes($str)
   */     
  function addslashes($str) {
    return $this->connection()->escape_string($str); 
  }
  
  /**
   * @see IPreparable::prepare($value)
   */     
  function prepare($value) {
    $this->prepared = true;
    
    if(is_bool($value)) {
      $binding['value'] = ($value ? $this->true_value() : $this->false_value());
      $binding['type'] = 'i';  //Technically bools are ints
    } else {
      $binding['value'] = $value;
      if(is_int($value)) {
        $binding['type'] = 'i';  //Integer
      } else if(is_double($value)) {
        $binding['type'] = 'd';  //Double
      } else if(is_string($value)) {
        $binding['type'] = 's';  //String
      } else {
        $binding['type'] = 'b';  //Blob
      }
    }
    
    $this->bindings[] = $binding;
    
    return '?';
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
  
  /**
   * @see BaseAdapter::platform_type($cross)
   */     
  function platform_type($cross) {
    return $cross;
  }
	
}

?>