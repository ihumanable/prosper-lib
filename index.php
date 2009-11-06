<?php
	namespace Prosper;
	require_once 'lib/Query.php';
	
	//Let's configure the connection
	Query::configure('mysql', 'localhost', 'root', 'xamppdevpwd', 'test');
	
	//Ok pull out all of the blog posts, newest first
	$posts = Query::select()->from('blog')->order('timestamp')->execute();
	
	echo '<div style="width: 600px; margin-left: auto; margin-right: auto">';
	echo "<h1>Blog Posts</h1>";
	
	foreach($posts as $post) {
		echo '<div style="border: 1px solid black; padding: 10px; float: left; clear: both; width: 600px; margin-bottom: 20px">';
			echo "<h2>#{$post['id']} - {$post['title']} @ {$post['timestamp']}</h2>";
			echo "<p>{$post['content']}</p>";
		echo '</div>';	
		
	}	
	
	echo "</div>";
	
?>