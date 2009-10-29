<?php
namespace Prosper;

class Prosper {
	private $sql = "";
	static $adapter;
	static $schema;
	
	static function configure($schema = "", $type = "mysql");
		switch(trim(strtolower($type))) {
			case 'mysql':
				self::$adapter = new MySQLAdapter();
				break;
			default:
				self::$adapter = new MySQLAdapter();
				break;
		}		
		self::$schema = $schema;
	} 
	
	static function select() {
		if(func_num_args == 0) {
			$columns = "*";
		} else {
			$args = func_get_args();
			foreach($args as $arg) {
				if(is_array($arg)) {
					foreach($arg as $key => $value) {
						$parts[] = self::$adapter->quote($key) . " AS " . self::$adapter->quote($value);
					}
				} else {
					$parts[] = self::$adapter->quote($key);
				}
			}
			$columns = implode(',', $parts);
		}
		return "select $columns";
	}
	
}


?>