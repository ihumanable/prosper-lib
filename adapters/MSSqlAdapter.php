<?php
namespace Prosper;

class MSSqlAdapter implements BaseAdapter {
	function quote($str) {
		return "[$str]";
	}	
}

?>