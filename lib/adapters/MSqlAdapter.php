<?php
namespace Prosper;

class MSqlAdapter extends BaseAdapter {
  
  /**
   * @see BaseAdapter#connect()
   */
  function connect() {
    $this->connection = msql_connect($this->hostname, $this->username, $this->password);
		if($this->schema != "") {
			msql_select_db($this->schema, $this->connection);
		}
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		msql_close($this->connection());
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql, $mode)
	 */
	function platform_execute($sql, $mode) {
		return msql_query($sql, $this->connection());
	}
	
	/**
	 * @see BaseAdapter#affected_rows($set)
	 */
	function affected_rows($set) {
		return msql_affected_rows($set);
	}
	
	/**
	 * @see BaseAdapter#fetch_assoc($set)
	 */
	function fetch_assoc($set) {
		return msql_fetch_array($set, MSQL_ASSOC);
	}
	
	/**
	 * @see BaseAdapter#cleanup($set)
	 */
	function cleanup($set) {
		msql_free_result($set);
	} 
}

