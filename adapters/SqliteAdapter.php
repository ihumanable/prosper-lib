<?php
namespace Prosper;

/**
 * Sqlite Database Adapter
 */
class SqliteAdapter extends BaseAdapter {
	function __construct($username, $password, $hostname, $schema) {
		parent::__construct($username, $password, $hostname, $schema);
		$this->connection = new \SQLite3($username);
	}
	
	function execute($sql) {
		$set =  $this->connection->query($sql);
		if($set instanceof \SQLite3Result) {
			while($row = $set->fetchArray(SQLITE3_ASSOC)) {
				$result[] = $row;
			}
		} else {
			$result = $this->connection->changes();
		}
		return $result;
	}
}
?>