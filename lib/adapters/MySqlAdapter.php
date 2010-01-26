<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * MySql Database Adapter
 */
class MySqlAdapter extends PreparedAdapter {
  
  private $types = "";
  
  /**
   * @see BaseAdapter::connect()
   */
  function connect() {
    return new \mysqli($this->hostname, $this->username, $this->password, $this->schema);
  }
  
  /**
   * @see BaseAdapter::disconnect()
   */
  function disconnect() {
    $this->connection()->close();
  }
  
  /**
   * @see BaseAdapter::has_transactions()
   */     
  function has_transactions() {
    return true;
  }
  
  /**
   * @see BaseAdapter::begin()
   */   	
  function begin() {
    $this->connection()->autocommit(false);
  }
  
  /**
   * @see BaseAdapter::commit()
   */     
  function commit() {
    $this->connection()->commit();
  }
  
  /**
   * @see BaseAdapter::rollback()
   */     	
  function rollback() {          
    $this->connection()->rollback();
  }
  
  /**
   * @see BaseAdapter::end()
   */     
  function end() {
    $this->connection()->autocommit(true);
  }
  
  /**
   * @see PreparedAdapter::prepared_execute($sql, $mode) 
   */
  function prepared_execute($sql, $mode) {
    $stmt = $this->connection()->prepare($sql);
    $arguments = array(&$this->types);    
    foreach($this->bindings as $key => $binding) {  
      $arguments[] = &$this->bindings[$key];
    }
    call_user_func_array(array($stmt, 'bind_param'), $arguments);
    $stmt->execute();  
    return $stmt;
  }
  
  /**
   * @see PreparedAdapter::standard_execute($sql, $mode)
   */     
  function standard_execute($sql, $mode) {
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
   * Clean up needs to clear the type string, this isn't performed by default
   * @see PreparedAdapter::cleanup()
   */
  function cleanup() {
    parent::cleanup();
    $this->types = "";
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
   * @see PreparedAdapter::prepare($value)
   */     
  function prepare($value) {
    if(is_bool($value) || is_int($value)) {
      $this->types .= 'i';  //Technically bools are ints too
    } else if(is_double($value)) {
      $this->types .= 'd';  //Double
    } else if(is_string($value)) {
      $this->types .= 's';  //String
    } else {
      $this->types .= 'b';  //Blob
    }
    
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