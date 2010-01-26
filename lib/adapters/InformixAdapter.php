<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * Informix Database Adapter
 */
class InformixAdapter extends BaseAdapter {
  
  /**
   * @see BaseAdapter::connect()
   */
  function connect() {  
    $database = ($this->schema != "") ? $this->schema . "@" . $this->hostname ; $this->hostname;
    return ifx_connect($database, $this->username, $this->password);
  }
  
  /**
   * @see BaseAdapter::disconnect()
   */
  function disconnect() {
    ifx_close($this->connection());
  }
  
  /**
   * @see BaseAdapter::platform_execute($sql, $mode) 
   */
  function platform_execute($sql, $mode) {
    return ifx_query($sql, $this->connection());
  }
  
  /**
   * @see BaseAdapter::affected_rows($set) 
   */
  function affected_rows($set) {
    return ifx_affected_rows($set);
  }
  
  /**
   * @see BaseAdapter::fetch_assoc($set) 
   */
  function fetch_assoc($set) {
    return ifx_fetch_Row($set, "NEXT");
  }
  
  /**
   * @see BaseAdapter::free_result($set) 
   */
  function free_result($set) {
    ifx_free_result($set);
  }
  
} 

?>