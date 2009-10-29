<?php
namespace Prosper;

class MySqlAdapter implements BaseAdapter {
	function quote($str) {
		return "`$str`";
	}
}

?>