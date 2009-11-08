<?php
namespace Prosper;

/**
 * PostgreSql Database Adapter
 */
class PostgreSqlAdapter extends BaseAdapter {
	
	/**
	 * Creates a PostgreSQL Connection Adapter
	 * @param string $username Database username
	 * @param string $password Database password
	 * @param string $hostname Database hostname
	 * @param string $schema Database schema
	 * @return Adapter Instance
	 */
	function __construct($username, $password, $hostname, $schema) {
		parent::__construct($username, $password, $hostname, $schema);
		$conn = ($hostname == "" ? "" : "host=$hostname ") .
		        ($schema   == "" ? "" : "dbname=$schema ") .
				($username == "" ? "" : "user=$username ") .
				($password == "" ? "" : "password=$password");
		$this->connection = pg_connect($conn);
	}
	
	/**
	 * Clean up, destroy the connection
	 */
	function __destruct() {
		pg_close($this->connection);
	}
	
	/**
	 * @see BaseAdapter#platform_execute($sql) 
	 */
	protected function platform_execute($sql) {
		return pg_query($this->connection, $sql);
	}
	
	/**
	 * @see BaseAdapter#affected_rows($set) 
	 */
	protected function affected_rows($set) {
		return pg_affected_rows($set);
	}
	
	/**
	 * @see BaseAdapter#fetch_assoc($set) 
	 */
	protected function fetch_assoc($set) {
		return pg_fetch_assoc($set);
	}
	
	/**
	 * @see BaseAdapter#cleanup($set) 
	 */
	protected function cleanup($set) {
		pg_free_result($set);	
	}
	
	/**
	 * @see BaseAdapter#quote($str) 
	 */
	function quote($str) {
		return "\"$str\"";
	}
	
}
?>