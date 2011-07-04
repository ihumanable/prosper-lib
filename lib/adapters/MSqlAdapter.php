<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * MSQL Database Adapter
 */
class MSqlAdapter extends BaseAdapter {
  
  /**
   * @see BaseAdapter::connect()
   */
  function connect() {
    $connection = msql_connect($this->hostname, $this->username, $this->password);
    if($this->schema != "") {
      msql_select_db($this->schema, $connection);
    }
    return $connection;
  }
  
  /**
   * @see BaseAdapter::disconnect()
   */
  function disconnect() {
    msql_close($this->connection());
  }
  
  /**
   * @see BaseAdapter::platform_execute($sql, $mode)
   */
  function platform_execute($sql, $mode) {
    return msql_query($sql, $this->connection());
  }
  
  /**
   * @see BaseAdapter::affected_rows($set)
   */
  function affected_rows($set) {
    return msql_affected_rows($set);
  }
  
  /**
   * @see BaseAdapter::fetch_assoc($set)
   */
  function fetch_assoc($set) {
    return msql_fetch_array($set, MSQL_ASSOC);
  }
  
  /**
   * @see BaseAdapter::free_result($set)
   */
  function free_result($set) {
    msql_free_result($set);
  } 
}

