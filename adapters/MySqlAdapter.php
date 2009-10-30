<?php
namespace Prosper;

class MySqlAdapter implements BaseAdapter {
	function quote($str) {
		return "`$str`";
	}
	
	function escape($str) {
		return "'" . addslashes($str) . "'";
	}
	
	function unescape($str) {
		return stripslashes($str);
	}
	
}

?>