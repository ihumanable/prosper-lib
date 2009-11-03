<?php
namespace Prosper;

/**
 * Base Adapter all SQL adapters are based off of
 */
abstract class BaseAdapter {
	
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
		return $sql . " limit $limit offset $offset";
	}
	
}
?>