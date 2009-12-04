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
    $this->connection = mssql_connect($this->hostname, $this->username, $this->password);
    if($schema != "") {
			mssql_select_db($this->schema, $this->connection);
		}
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
		$result = mssql_query("select SCOPE_IDENTITY AS last_insert_id", $this->connection());
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
	 * Microsoft T-SQL sucks and can't easily do limit offset like every other 
	 * reasonable rdbms, this creates a complex windowing function to do something
	 * that everyone else has built-in.
	 * @param string $sql sql statement
	 * @param int $limit how many records to return
	 * @param int $offset where to start returning 
	 * @return string modified sql statement
	 */
	function limit($sql, $limit, $offset) {
		if($offset == 0) {
			$pos = strripos($sql, "select");
			if($pos !== false) {
				$pos += 6;
				$sql = substr($sql, 0, $pos) . " top $limit " . substr($sql, $pos);
			}
		} else {
			$orderpos = strripos($sql, "order by");
			$pos = strripos($sql, "select");
			
			
			if($orderpos === false) {
				$dir = $opdir = "";
			} else {
				$order = substr($sql, $orderpos);
				if(strpos($order, "desc") !== false) {
					$order = substr($order, 0, strlen($order) - 4);
					$dir = "$order desc";
					$opdir = "$order asc";
				} else {
					$order = substr($order, 0, strlen($order) - 3);
					$dir = "$order asc";
					$opdir = "$order desc";
				}
			}
					
			$sql = substr($sql, 0, $pos) .
				   "(select * from (" .
						"select top $limit * from (" . 
							"select top " . ($limit + $offset) . substr($sql, $pos + 6) . ")" . 
						" $opdir)" .
			       " $dir)"; 
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