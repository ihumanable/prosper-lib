<?php
	namespace Prosper;
	require_once 'lib/Query.php';
	
	//Let's configure the connection
	Query::configure('mysql', 'localhost', 'root', 'xamppdevpwd', 'test');
	
	//Ok pull out all of the blog posts, newest first
	$posts = Query::select()->from('blog')->order('timestamp')->execute();
	
	echo '<div id="content">';
	echo "<h1>Blog Posts</h1>";
	
	foreach($posts as $post) {
		echo '<div class="post">';
			echo "<h2>#{$post['id']} - {$post['title']} @ {$post['timestamp']}</h2>";
			echo "<p>{$post['content']}</p>";
		echo '</div>';	
		
	}	
	
	echo "</div>";
	
?>