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
	 * Executes a sql statement, the base implementation does nothing
	 * @param string $sql statement to execute
	 * @param string $params [optional] parameters
	 * @return nothing
	 */
	function execute($sql, $params = null) {
		
	}
	
}
?>