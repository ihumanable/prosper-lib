<?php
namespace Prosper;

/**
 * Base Adapter all SQL adapters are based off of
 */
abstract class BaseAdapter {
	
	protected $connection;
	
	/**
	 * Establishes a connection
	 * @param string $username Database username
	 * @param string $password Database password
	 * @param string $hostname Database hostname
	 * @param string $schema Database schema
	 * @return New Adapter Instance
	 */
	function __construct($username, $password, $hostname, $schema) {
		$this->connection = null;
	}
	
	/**
	 * Quotes a database object, uses the backtick by default
	 * @param string $str string to quote
	 * @return quoted string
	 */
	function quote($str) {
		return "`$str`";
	}
	
	/**
	 * Escapes a value, adds slashes and surrounds with single quotes
	 * @param string $str string to escape
	 * @return escaped string
	 */
	function escape($str) {
		return "'" . addslashes($str) . "'";
	}
	
	/**
	 * Unescapes a value out of the database, strips slashes
	 * @param string $str string to unescape
	 * @return unescaped string
	 */
	function unescape($str) {
		return stripslashes($str);
	}
	
	/**
	 * Since various databases support a wide variety of limit syntaxes the adapter
	 * is responsible for writing the limit syntax.
	 * @param string $sql sql statment
	 * @param int $limit how many to limit the result to
	 * @param int $offset where to start at
	 * @return sql statement with embedded limit statement
	 */
	function limit($sql, $limit, $offset) {
		return $sql . " limit $limit" . ($offset !== 0 ? " offset $offset" : "");
	}
	
	/**
	 * Executes a sql statement, the base implementation drives the query results using
	 * several derivitive functions
	 * @see BaseAdapter#platform_execute($sql)
	 * @see BaseAdapter#affected_rows($set)
	 * @see BaseAdapter#last_id($set)
	 * @see BaseAdapter#fetch_assoc($set)
	 * @param string $sql statement to execute
	 * @param string $mode statement type
	 * @return mixed Number of rows affected if update or delete, insert id for insert statements, result set for select
	 */
	function execute($sql, $mode) {
		$set = $this->platform_execute($sql);
		
		switch($mode) {
			case Query::DELETE_STMT:
				$result = $this->affected_rows($set);
				break;
			case Query::INSERT_STMT:
				$result = $this->insert_id($set);
				break;
			case Query::SELECT_STMT:
				while($row = $this->fetch_assoc($set)) {
					$result[] = $row;
				}
				break;
			case Query::UPDATE_STMT:
				$result = $this->affected_rows($set);
				break;
		}		
		$this->cleanup($set);
		return $result;		
	}
	
	/**
	 * Platform specific execution wrapper
	 * @param string $sql SQL to execute 
	 * @return mixed Platform specific result set
	 */
	protected function platform_execute($sql) {
		
	}
	
	/**
	 * Get the number of affected rows for a platform specific result object 
	 * @param mixed $set Platform specific result set
	 * @return int number of rows affected
	 */
	protected function affected_rows($set) {
		
	}
	
	/**
	 * Retrieve the last insert id for a statement
	 * @param mixed $set Platform specific result set
	 * @return int inserted id
	 */
	protected function insert_id($set) {
		
	}
	
	/**
	 * Retrieve an associative array from a platform specific result set
	 * @param mixed $set Platform specific result set
	 * @return array row as associative array
	 */ 
	protected function fetch_assoc($set) {
		
	} 
	
	/**
	 * Do optional memory cleanup, not necessary but will allow for long running scripts
	 * @param mixed $set Platform specific result set
	 * @return nothing
	 */
	protected function cleanup($set) {
		
	}
	
	/**
	 * Return database specific timestamp from unix timestamp
	 * Default implementation is the sensible MySQL Timestamp	 
	 */	 	
	function timestamp($timestamp) {
		return date ("Y-m-d H:i:s", $timestamp); 
	}
	
	/**
	 * Return unix timestamp from database specific timestamp
	 * Default implementation is the sensible MySQL Timestamp
	 */	 	 	
	function mktime($timestamp) {
		return strtotime($timestamp);
	}
	
}
?>