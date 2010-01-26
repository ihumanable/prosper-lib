<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * MaxDB Database Adapter
 * The MaxDB Vender Specific Database Extensino (VSDX) is a modified version of 
 * the MySQLi VSDX, this adapter is experimental but uses identical methodology 
 * as the well test MySqlAdapter   
 */
class MaxDBAdapter extends PreparedAdapter {
  
  /**
   * @see BaseAdapter::connect()
   */
  function connect() {
    return new maxdb($this->hostname, $this->username, $this->password, $this->schema);
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
    if($set instanceof maxdb_result) {
      $set->free();
    }
  }
  
  /**
   * @see BaseAdapter::addslashes($str)
   */     
  function addslashes($str) {
    return $this->connection()->real_escape_string($str);
  }
  
  /**
   * @see PreparedAdapter::platform_prepare($value)
   */     
  function platform_prepare($value) {
    
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
  
}
?>