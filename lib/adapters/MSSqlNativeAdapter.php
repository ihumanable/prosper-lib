<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * Microsoft SQL Server Database Adapter
 */
class MSSqlNativeAdapter extends PreparedAdapter {
  
  /**
   * @see BaseAdapter::connect()
   */
  function connect() {
    
    $info = array ( "UID" => $this->username, "PWD" => $this->password );
    if($this->schema != "") {
      if(strpos($this->schema, '.') !== false) {
        $parts = explode('.', $this->schema);
        $schema = $parts[0];
      } else {
        $schema = $this->schema;
      }
      $info["Database"] = $schema;
    }
    
    return sqlsrv_connect($this->hostname, $info);
  }
  
  /**
   * @see BaseAdapter::disconnect()
   */
  function disconnect() {
    sqlsrv_close($this->connection());
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
    sqlsrv_begin_transation($this->connection());
  }

  /**
   * @see BaseAdapter::commit()
   */     
  function commit() {
    sqlsrv_commit($this->connection());
  }
  
  /**
   * @see BaseAdapter::rollback()
   */     
  function rollback() {
    sqlsrv_rollback($this->connection());
  }
  
  /**
   * @see PreparedAdapter::prepared_execute($sql, $mode) 
   */
  function prepared_execute($sql, $mode) {    
    foreach($this->bindings as $key => $binding) {  
      $arguments[] = &$this->bindings[$key];
    }
    $stmt = sqlsrv_prepare($this->connection(), $sql, $arguments);
    sqlsrv_execute($stmt);
    return $stmt; 
  }
  
  /**
   * @see PreparedAdapter::standard_execute($sql, $mode)
   */     
  function standard_execute($sql, $mode) {
    return sqlsrv_query($this->connection(), $sql);
  }
  
  /**
   * @see BaseAdapter::affected_rows($set) 
   */
  function affected_rows($set) {
    return sqlsrv_rows_affected($set);
  }
  
  /**
   * @see BaseAdapter::insert_id($set) 
   */
  function insert_id($set) {
    $result = sqlsrv_query($this->connection(), "select SCOPE_IDENTITY() AS last_insert_id");
    $arr = $this->fetch_assoc($result);
    $retval = $arr['last_insert_id'];
    sqlsrv_free_stmt($result);
    return $retval;
  }
  
  /**
   * @see BaseAdapter::fetch_assoc($set)
   */
  function fetch_assoc($set) {
    return sqlsrv_fetch_array($set, SQLSRV_FETCH_ASSOC);
  }
  
  /**
   * @see BaseAdapter::free_result($set) 
   */
  function free_result($set) {
    sqlsrv_free_stmt($set);
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
      
      $orderpos = strripos($sql, "order by");
      
      if ($orderpos === false) {
        $order = "SELECT 1)) AS row, ";
      } else {
        // I think this accounts for multiple ORDER BY directions, but I could be wrong. It definitely needs tested.
        $order = substr($sql, $orderpos);
      }
      
      $sql = substr($sql, 0, $pos) . " ROW_NUMBER() OVER (ORDER BY (" . $order . substr($sql, $pos);
    }
    
    $sql = "SELECT * FROM (" . $sql . ") WHERE row >= " . $offset . " AND row <= " . ($limit + $offset);
    
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