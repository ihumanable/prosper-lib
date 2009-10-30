<?php
	namespace Prosper;
	require_once 'Query.php';
	
	$adapters = array('mysql', 'mssql', 'postgre', 'sqlite');
	
	foreach($adapters as $adapter) {
		echo "<h2>$adapter test</h2>";
		Query::configure('test', $adapter);
		
		echo Query::select()->from('user')->where(Query::conj('a<1', 'b LIKE 2', Query::union('c>=3', 'd!=4')));
		echo "<br />";
		echo Query::insert()->into('user')->values(array('fname' => 'Matt', 'lname' => 'Nowack'));
		
		echo "<hr />";
	}
?>