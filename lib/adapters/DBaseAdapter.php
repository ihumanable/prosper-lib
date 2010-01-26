<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * DBase Database Adapter
 */
class DBaseAdapter extends BaseAdapter {
  
  /**
   * @see BaseAdapter::connect()
   */
  function connect() {
    return dbase_open($this->username, 2);
  }
  
  /**
   * @see BaseAdapter::disconnect()
   */
  function disconnect() {
   dbase_close($this->connection());
  }
  
  /**
   * @see BaseAdapter::platform_execute($sql, $mode)
   */
  function platform_execute($sql, $mode) {
    switch($mode) {
      case Query::DELETE_STMT:
        dbase_delete_record($this->connection(), $sql);
        dbase_pack($this->connection());
        break;
      case Query::INSERT_STMT:
        dbase_add_record($this->connection(), $sql);
        break;
      case Query::SELECT_STMT:
        return dbase_get_record_with_names($this->connection(), $sql);
        break;
      case Query::UPDATE_STMT:
        $data = $sql['data'];
        $row = $sql['row'];
        dbase_replace_record($this->connection(), $data, $row);
        break;
    }
  } 
  
  /**
   * @see BaseAdapter::affected_rows($set) 
   */
  function affected_rows($set) {
    return 1;
  }
  
  /**
   * @see BaseAdapter::fetch_assoc($set) 
   */
  function fetch_assoc($set) {
    return $set;
  }

  /**
   * @see BaseAdapter::true_value()
   */
  function true_value() {
    return "T";
  }
  
  /**
   * @see BaseAdapter::false_value()
   */
  function false_value() {
    return "F";
  }
  
}
?>