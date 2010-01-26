<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * Microsoft SQL Server Database Adapter
 */
class MSSqlAdapter extends BaseAdapter {
  
  /**
   * @see BaseAdapter::connect()
   */
  function connect() {
    $connection = mssql_connect($this->hostname, $this->username, $this->password);
    if($this->schema != "") {
      if(strpos($this->schema, '.') !== false) {
        $parts = explode('.', $this->schema);
        $schema = $parts[0];
      } else {
        $schema = $this->schema;
      }
      
      mssql_select_db($schema, $connection);
    }
    return $connection;
  }
  
  /**
   * @see BaseAdapter::disconnect()
   */
  function disconnect() {
    mssql_close($this->connection());
  }
  
  /**
   * @see BaseAdapter::platform_execute($sql, $mode) 
   */
  function platform_execute($sql, $mode) {
    return mssql_query($sql, $this->connection());
  }
  
  /**
   * @see BaseAdapter::affected_rows($set) 
   */
  function affected_rows($set) {
    return mssql_rows_affected($this->connection());
  }
  
  /**
   * @see BaseAdapter::insert_id($set) 
   */
  function insert_id($set) {
    $result = mssql_query("select SCOPE_IDENTITY() AS last_insert_id", $this->connection());
    $arr = $this->fetch_assoc($result);
    $retval = $arr['last_insert_id'];
    mssql_free_result($result);
    return $retval;
  }
  
  /**
   * @see BaseAdapter::fetch_assoc($set)
   */
  function fetch_assoc($set) {
    return mssql_fetch_assoc($set);
  }
  
  /**
   * @see BaseAdapter::free_result($set) 
   */
  function free_result($set) {
    mssql_free_result($set);
  }
  
  /**
   * @see BaseAdapter::query($str) 
   */
  function quote($str) {
    return "[$str]";
  }  
  
  /**
   * @see BaseAdapter::addslashes($str)
   */     
  function addslashes($str) {
    return str_replace("'", "''", $str);
  }
  
  /**
   * Microsoft T-SQL sucks and can't easily do limit offset like every other 
   * reasonable rdbms, this creates a complex windowing function to do something
   * that everyone else has built-in.
   * @param string $sql sql statement
   * @param int $limit how many records to return
   * @param int $offset where to start returning 
   * @return string modified sql statement
   */
  function limit($sql, $limit, $offset) {
    $pos = strripos($sql, "select");
    // TODO: Modify to use ORDER BY from $sql
    if ($pos !== false) {
      $pos += 6;
      
      if($offset) {
        
        $orderpos = strripos($sql, "order by");
        
        if ($orderpos === false) {
          $order = "SELECT 1)) AS row, ";
        } else {
          // I think this accounts for multiple ORDER BY directions, but I could be wrong. It definitely needs tested.
          $order = substr($sql, $orderpos);
        }
        
        $sql = substr($sql, 0, $pos) . " ROW_NUMBER() OVER (ORDER BY (" . $order . substr($sql, $pos);
        $sql = "SELECT * FROM (" . $sql . ") WHERE row >= " . $offset . " AND row <= " . ($limit + $offset);  
      } else {
        $sql = substr($sql, 0, $pos) . " top $limit " . substr($sql, $pos + 1);
      }
      
    }
    
    
    return $sql;
  }

  /**
   * @see BaseAdapter::true_value()
   */
  function true_value() {
    return "1";
  }
  
  /**
   * @see BaseAdapter::false_value()
   */
  function false_value() {
    return "0";
  }
}

?>