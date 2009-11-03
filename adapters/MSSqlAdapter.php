<?php
namespace Prosper;

/**
 * Microsoft SQL Server Database Adapter
 */
class MSSqlAdapter extends BaseAdapter {
	function quote($str) {
		return "[$str]";
	}	
	
	/**
	 * Microsoft T-SQL sucks and can't easily do limit offset like every other 
	 * reasonable rdbms, this creates a complex windowing function to do something
	 * that everyone else has built-in.
	 * @param string $sql sql statement
	 * @param int $limit how many records to return
	 * @param int $offset where to start returning 
	 * @return modified sql statement
	 */
	function limit($sql, $limit, $offset) {
		if($offset == 0) {
			$pos = strripos($sql, "select");
			if($pos !== false) {
				$pos += 6;
				$sql = substr($sql, 0, $pos) . " top $limit " . substr($sql, $pos);
			}
		} else {
			$orderpos = strripos($sql, "order by");
			$pos = strripos($sql, "select");
			
			
			if($orderpos === false) {
				$dir = $opdir = "";
			} else {
				$order = substr($sql, $orderpos);
				if(strpos($order, "desc") !== false) {
					$order = substr($order, 0, strlen($order) - 4);
					$dir = "$order desc";
					$opdir = "$order asc";
				} else {
					$order = substr($order, 0, strlen($order) - 3);
					$dir = "$order asc";
					$opdir = "$order desc";
				}
			}
					
			$sql = substr($sql, 0, $pos) .
				   "(select * from (" .
						"select top $limit * from (" . 
							"select top " . ($limit + $offset) . substr($sql, $pos + 6) . ")" . 
						" $opdir)" .
			       " $dir)"; 
		}
		return $sql;
	}
}

?>