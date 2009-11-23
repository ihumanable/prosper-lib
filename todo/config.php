<?php
	namespace Prosper;
	require_once '../lib/adapters/_all_.php';
	require_once '../lib/Query.php';

	Query::configure(Query::MYSQL_MODE, 'root', 'xamppdevpwd', 'localhost', 'test');

	/**
	 * Simple function that translates a timestamp from the past into a friendlier
	 * number of somethings ago (ex: 15 minutes ago, 1 hour ago)
	 * @param int timestamp unix timestamp to convert
	 * @return string nicely formatted label	 	 
	 */	 	  	
	function time_ago($timestamp) {
		$spans = array ( "year"   => 31536000,
		                 "month"  =>  2592000,
										 "week"   =>   604000,
										 "day"    =>    86400,
										 "hour"   =>     3600,
										 "minute" =>       60,
										 "second" =>        1	 );
					
		$diff = mktime() - $timestamp - 24;
		$label = "second";
		$span = 1;
		foreach($spans as $key => $value) {
			if($diff > $value) {
				$label = $key;
				$span = $value;
				break;
			}
		}
		
		$amt = floor($diff / $span);	
		
		return $amt . " " . $label . ($amt != 1 ? "s" : "") . " ago";
	}

?>