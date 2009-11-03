<?php
	namespace Prosper;
	require_once 'Query.php';
	
	$adapters = array('mysql', 'mssql', 'postgre', 'sqlite', 'weird');
	
	foreach($adapters as $adapter) {
		echo "<h2>$adapter test</h2>";
		Query::configure('test', $adapter);
		
		echo Query::select()
					->from('user')
					->left('pref')->on("user.id = pref.user_id")
					->where(Query::conj("a<'1'", "b LIKE '2'", 
							Query::union("c>='3'", "d!='4'")))
					->order('fname', 'asc')
					->limit(30, 10);
		echo "<br />";
		echo Query::insert()->into('user')->values(array('fname' => 'Matt', 'lname' => 'Nowack'));
		echo "<br />";
		echo Query::update('user')->set(array('fname' => 'Matt', 'lname' => 'Nowack'))->where("id='1'");
		echo "<br />";
		echo Query::delete()->from('user')->where("fname LIKE 'Matt%'");
		echo "<br />";
		
		echo "<hr />";
	}
?>