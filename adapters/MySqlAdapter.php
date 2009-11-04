<?php
namespace Prosper;

/**
 * MySql Database Adapter
 */
class MySqlAdapter extends BaseAdapter {
	
	function __construct($hostname, $username, $password, $schema) {
		parent::__construct($hostname, $username, $password, $schema);
		$this->connection = new \mysqli($hostname, $username, $password, $schema);
	}
	
	function execute($sql) {
		$set =  $this->connection->query($sql);
		if($set instanceof \MySQLi_Result) {
			while($row = $set->fetch_array(MYSQLI_ASSOC)) {
				$result[] = $row;
			}
		} else {
			$result = $this->connection->affected_rows;
		}
		return $result;
	}
}

?>