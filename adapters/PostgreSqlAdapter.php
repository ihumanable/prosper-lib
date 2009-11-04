<?php
namespace Prosper;

/**
 * PostgreSql Database Adapter
 */
class PostgreSqlAdapter extends BaseAdapter {
	
	function __construct($username, $password, $hostname, $schema) {
		parent::__construct($username, $password, $hostname, $schema);
		$conn = ($hostname == "" ? "" : "host=$hostname ") .
		        ($schema   == "" ? "" : "dbname=$schema ") .
				($username == "" ? "" : "user=$username ") .
				($password == "" ? "" : "password=$password");
		$this->connection = pg_connect($conn);
	}
	
	function execute($sql) {
		$set =  pg_query($this->connection, $sql);
		if($set) {
			if($row = pg_fetch_assoc($set)) {
				$result[] = $row;
				while($row = $set->fetch_array(MYSQLI_ASSOC)) {
					$result[] = $row;
				}
			} else {
				$result = pg_affected_rows($set);
			}
		}
		return $result;
	}
	
	function quote($str) {
		return "\"$str\"";
	}
	
}
?>