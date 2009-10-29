<?php
namespace Prosper;

require_once 'adapters/all.php';

class Query {
	private $sql;
	private static $adapter;
	private static $schema;
	
	private function __construct($sql = "") {
		$this->sql = $sql;
	}
	
	static function configure($schema = "", $type = "mysql") {
		switch(trim(strtolower($type))) {
			case 'mysql':
				self::$adapter = new MySqlAdapter();
				break;
			case 'mssql':
				self::$adapter = new MSSqlAdapter();
				break;
			case 'pgsql':
			case 'postgre':
				self::$adapter = new PostgreSqlAdapter();
				break;
			case 'sqlite':
				self::$adapter = new SqliteAdapter();
				break;
			default:
				self::$adapter = new MySqlAdapter();
				break;
		}		
		self::$schema = ($schema == "" ? "" : self::quote($schema));
	} 
	
	static function select() {
		if(func_num_args() == 0) {
			$columns = "*";
		} else {
			$args = func_get_args();
			foreach($args as $arg) {
				if(is_array($arg)) {
					foreach($arg as $key => $value) {
						$parts[] = self::column($key) . self::alias($value);
					}
				} else {
					$parts[] = self::column($arg);
				}
			}
			$columns = implode(',', $parts);
		}
		return new Query("select $columns");
	}
	
	function from($table, $alias = "") {
		$this->sql .= " from " . self::table($table) . self::alias($alias);
		return $this;
	}
	
	function join($table, $alias = "") {
		$this->sql .= " join " . self::table($table) . self::alias($alias);
	}
	
	
	function __toString() {
		return $this->sql;
	}
	
	static function alias($alias) {
		return ($alias == "" ? "" : " as " . self::quote($alias));
	}
	
	static function column($col) {
		if(strpos($col, '.') === false) {
			$column = self::quote($col);
		} else {
			$parts = explode('.', $col);
			foreach($parts as $part) {
				$safeparts[] = self::quote($part);
			}
			$column = implode('.', $safeparts);
		}
		
		return $column;
		 
	}
	
	static function table($table) {
		return self::schema('.') . self::quote($table);
	}
	
	static function schema($append = "") {
		return (self::$schema == "" ? "" : self::$schema . $append);
	}
		
	static function quote($str) {
		return self::$adapter->quote($str);
	}
	
}


?>