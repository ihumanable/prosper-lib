<?php
namespace Prosper;

class PostgreSqlAdapter implements BaseAdapter {
	function quote($str) {
		return "\"$str\"";
	}
}
?>