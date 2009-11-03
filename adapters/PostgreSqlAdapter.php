<?php
namespace Prosper;

/**
 * PostgreSql Database Adapter
 */
class PostgreSqlAdapter extends BaseAdapter {
	
	function quote($str) {
		return "\"$str\"";
	}
	
}
?>