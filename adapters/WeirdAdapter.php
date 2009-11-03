<?php
namespace Prosper;

/**
 * This class is to test the various customizable properties of Prosper
 */
class WeirdAdapter extends BaseAdapter {
	function quote($str) {
		return "[@[$str]@]";
	}
	
	function escape($str) {
		return "[~[" . addslashes($str) . "]~]";
	}
	
	function unescape($str) {
		return "[![" . stripslashes($str) . "]!]";
	}
}
?>