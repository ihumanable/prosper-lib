<?php
	namespace Prosper;
	require_once 'Query.php';
	
	$adapters = array('mysql', 'mssql', 'postgre', 'sqlite');
	
	foreach($adapters as $adapter) {
		echo "<h2>$adapter test</h2>";
		Query::configure('test', $adapter);
		
		echo "Select All:  <br />";
		echo Query::select()->from('user');
		
		echo "<br /><br />Select Columns:  <br />";
		echo Query::select('fname', 'lname')->from('user');
		
		echo "<br /><br />Select Alias Columns: <br />";
		echo Query::select(array('fname' => 'First Name', 'lname' => 'Last Name'))->from('user', 'u');
		
		echo "<br /><br />Select Alias Table Columns: <br />";
		echo Query::select(array('user.fname' => 'First Name', 'pref.email' => 'Email Preference'))->from('sys_user', 'user');
		
		echo "<hr />";
	}
?>