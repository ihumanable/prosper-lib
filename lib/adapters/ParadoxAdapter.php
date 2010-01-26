<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * Paradox Database Adapter
 */
class ParadoxAdapter extends BaseAdapter {
  
  private $file_handle;
  
  /**
   * @see BaseAdapter::connect()
   */
  function connect() {
    $this->file_handle = fopen($this->username, "rw");
    $connection = new paradox_db();
    $connection->open_fp($this->file_handle);
    return $connection;
  }
  
  /**
   * @see BaseAdapter::disconnect()
   */
  function disconnect() {
    $this->connection()->close();
    fclose($this->file_handle);
  }
  
  /**
   * This function is experimental, as is the adapter.  
   * It can only be used with the non-portable native function
   * @see Query#native($sql)
   * @see BaseAdapter::platform_execute($sql, $mode) 
   */
  function platform_execute($sql, $mode) {
    switch($mode) {
      case Query::DELETE_STMT:
        return px_delete_record($this->connection(), $sql);
        break;
      case Query::INSERT_STMT:
        return px_insert_record($this->connection(), $sql);
        break;
      case Query::SELECT_STMT:
        return px_retrieve_record($this->connection(), $sql);
        break;
      case Query::UPDATE_STMT:
        $data = $sql['data'];
        $row = $sql['row'];
        return px_update_record($this->connection(), $data, $row);
        break;
    }
  }
  
  /**
   * @see BaseAdapter::fetch_assoc($set) 
   */
  function fetch_assoc($set) {
    return $set;
  }
  
}