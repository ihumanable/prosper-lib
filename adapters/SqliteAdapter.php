<?php
namespace Prosper;

class SqliteAdapter implements BaseAdapter {
	function quote($str) {
		return "`$str`";
	}
}
?>