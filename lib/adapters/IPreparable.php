<?php
/**
 * @package Prosper
 */ 
namespace Prosper;

/**
 * The IPreparable interface should be implemented by any adapter that supports
 * prepared statements
 */  
interface IPreparable {
  
  /**
   * Binds a value to a prepared statement and returns the query placeholder
   * @param mixed $value The value to bind
   * @return string query placeholder
   */           
  function prepare($value);  
}

?>