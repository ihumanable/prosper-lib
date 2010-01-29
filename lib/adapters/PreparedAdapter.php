<?php

/**
 * @package Prosper
 */ 
namespace Prosper;

/**
 * The PreparedAdapter class should be extended by any adapter that supports
 * prepared statements
 */  
abstract class PreparedAdapter extends BaseAdapter {
  public $prepared;
  public $bindings;
  
  /**
   * Initializes the prepared statement adapter and then passes things off to BaseAdapter  
   * @see BaseAdapter::__construct($username, $password, $hostname, $schema, $lazy = true)
   */     
  function __construct($username, $password, $hostname, $schema, $lazy = true) {
    $this->prepared = false;
    parent::__construct($username, $password, $hostname, $schema, $lazy);
  }
  
  /**
   * Binds a value to a prepared statement and returns the query placeholder
   * @param mixed $value The value to bind
   * @return string query placeholder
   */          
  function escape($value) {
    if(is_bool($value)) {
      $this->bindings[] = ($value ? $this->true_value() : $this->false_value());
    } else {
      $this->bindings[] = $value;
    }
    $this->prepared = true;
    return $this->prepare($value);
  }
  
  /**
   * If any special processing is needed for the platform then this function
   * should be overriden
   * @param mixed $value The value being bound
   * @return string query placeholder, default implementation is '?'
   */              
  function prepare($value) {
      return '?';
  }
  
  /**
   * Override the Base behavior and dispatch to the correct function based off
   * of whether or not we are functioning on a prepared statement     
   * @see BaseAdapter::platform_execute($sql, $mode)
   */     
  function platform_execute($sql, $mode) {
    if($this->prepared) {
      return $this->prepared_execute($sql, $mode);
    } else {
      return $this->standard_execute($sql, $mode);
    }
  }
  
  /**
   * Prepare and execute a statement for the given sql
   * @param string $sql SQL to execute 
   * @param string $mode Query type
   * @return mixed Platform specific result set for prepared statements
   */         
  function prepared_execute($sql, $mode) {
  
  }
  
  /**
   * Execute ad-hoc SQL
   * @param string $sql SQL to execute 
   * @param string $mode Query type
   * @return mixed Platform specific result set
   */        
  function standard_execute($sql, $mode) {
  
  }
  
  /**
   * Used to rebind a statement
   * @param array bindings to rebind with
   * @return nothing      
   */     
  function rebind($bindings) {
    $this->cleanup();
    foreach($bindings as $binding) {
      $this->escape($binding);
    }    
  }
  
  /**
   * Cleans up any prepared state
   * @see BaseAdapter::cleanup()
   */
  function cleanup() {
    $this->prepared = false;
    $this->bindings = array();
  }
}

?>