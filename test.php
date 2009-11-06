<?php
	namespace Prosper;
	require_once 'lib/Query.php';
	
	Query::configure('mysql', 'localhost', 'root', 'xamppdevpwd', 'test');
	
	$query = Query::select()->from('user');
	
	echo $query;
	echo "<pre>";
	print_r($query->execute());
	echo "</pre>";
	
	$query = Query::select()->from('user')->where("firstname like 'm%'");
	
	echo $query;
	echo "<pre>";
	print_r($query->execute());
	echo "</pre>";
	
	$query = Query::insert()->into('user')->values(array('firstname' => 'Mike', 'lastname' => "O'Malley"));
	
	echo $query;
	echo "<pre>";
	print_r($query->execute());
	echo "</pre>";
	
	$query = Query::delete()->from('user')->where("firstname = 'insert'");
	
	echo $query;
	echo "<pre>";
	print_r($query->execute());
	echo "</pre>";
	
?>