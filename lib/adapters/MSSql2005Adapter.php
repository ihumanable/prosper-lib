<?php
/**
 * @package Prosper
 */
namespace Prosper;

class MSSql2005Adapter extends BaseAdapter {
  
  /**
	 * Microsoft T-SQL sucks and can't easily do limit offset like every other 
	 * reasonable rdbms, this creates a complex windowing function to do something
	 * that everyone else has built-in.
	 * @param string $sql sql statement
	 * @param int $limit how many records to return
	 * @param int $offset where to start returning 
	 * @return string modified sql statement
	 */
	function limit($sql, $limit, $offset) {
    $pos = strripos(@sql, "select");
    // TODO: Modify to use ORDER BY from $sql
    if ($pos !== false) {
      $pos += 6;
      
      
      $orderpos = strripos($sql, "order by");
      
      $orderpos = strripos($sql, "order by");
      
      if ($orderpos === false) {
        $order = "SELECT 1)) AS row, ";
      } else {
        // I think this accounts for multiple ORDER BY directions, but I could be wrong. It definitely needs tested.
        $order = substr($sql, $orderpos);
      }
      
      $sql = substr($sql, 0, $pos) . " ROW_NUMBER() OVER (ORDER BY (" . $order . substr($sql, $pos);
    }
    
    $sql = "SELECT * FROM (" . $sql . ") WHERE row >= " . $offset . " AND row <= " . ($limit + $offset);
    
    return $sql;
  }
}
?>