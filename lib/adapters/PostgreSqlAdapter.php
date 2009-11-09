<?php
namespace Prosper;

/**
 * PostgreSql Database Adapter
 */
class PostgreSqlAdapter extends BaseAdapter {
	
	private $inserted_cols;
	
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
	 * @see BaseAdapter#platform_execute($sql, $mode) 
	 */
	protected function platform_execute($sql, $mode) {
		if($mode == Query::INSERT_STMT) {
			$sql .= " RETURNING *";
			$parse = explode('(', $sql);
			$parse = explode(')', $parse[1]);
			$this->inserted_cols = explode(", ", $parse[0]);
		}
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
	 * @see BaseAdapter#insert_id($set) 
	 */
	protected function insert_id($set) {
		$result = $this->fetch_assoc($set);
		foreach($result as $key => $value) {
			if(!in_array($key, $this->inserted_cols)) {
				return $value;
			}
		}
		return -1;
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