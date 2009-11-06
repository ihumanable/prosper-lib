<?php
	namespace Prosper;
	require_once '../lib/Query.php';

	Query::configure('mysql', 'localhost', 'root', 'xamppdevpwd', 'test');

	function time_ago($timestamp) {
		$spans = array ( "year"   => 31536000,
		                 "month"  =>  2592000,
										 "week"   =>   604000,
										 "day"    =>    86400,
										 "hour"   =>     3600,
										 "minute" =>       60,
										 "second" =>        1	 );
					
		
		$now = mktime();
		$diff = $now - $timestamp;
		
		foreach($spans as $key => $value) {
			if($diff > $value) {
				$label = $key;
				$span = $value;
				break;
			}
		}
		
		$amt = floor($diff / $span);	
		
		return $amt . " " . $label . ($amt > 1 ? "s" : "") . " ago";
	}

?>