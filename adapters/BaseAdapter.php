<?php
namespace Prosper;

interface BaseAdapter {
	function quote($str);
	function escape($str);
	function unescape($str);
}
?>