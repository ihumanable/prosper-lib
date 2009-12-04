<?php
/**
 * @package Prosper
 */
namespace Prosper;

/**
 * Ovrimos Database Adapter
 */
class OvrimosAdapter extends BaseAdapter {
	
	/**
   * @see BaseAdapter::connect()
   */
	function connect() {
		$this->connection = ovrimos_connect($this->hostname, $this->schema, $this->username, $this->password);
	}
	
	/**
	 * @see BaseAdapter::disconnect()
	 */
	function disconnect() {
		ovrimos_close($this->connection());
	}
	
	/**
	 * @see BaseAdapter::platform_execute($sql, $mode)
	 */
	function platform_execute($sql, $mode) {
		return ovrimos_exec($this->connection(), $sql);
	}
	
	/**
	 * @see BaseAdapter::affected_rows($set)
	 */
	function affected_rows($set) {
		return ovrimos_num_rows($set);
	}
	
	/**
	 * @see BaseAdapter::fetch_assoc($set)
	 */
	function fetch_assoc($set) {
		if(ovrimos_fetch_row($set)) {
			$limit = ovrimos_num_fields($set);
			for($i = 0; $i < $limit; ++$i) {
				$result[ovrimos_field_name($set, $i)] = ovrimos_result($set, $i);
			}
		}
		return $result;
	}
	
	/**
	 * @see BaseAdapter::free_result($set)
	 */
	function free_result($set) {
		ovrimos_free_result($set);
	}
	
}