<?php
	namespace Prosper;
	require_once 'config.php';
	
	include_once 'header.php';
	
	
	//Ok pull out all of the blog posts, newest first
	$posts = Query::select()->from('blog')->order('timestamp')->execute();
	
	echo "<h1>Blog Posts</h1>";
	
	foreach($posts as $post) {
		echo '<div class="post">';
			echo "<h2>#{$post['id']} - {$post['title']} @ {$post['timestamp']}</h2>";
			echo "<p>{$post['content']}</p>";
			echo "<a href=\"edit.php?id={$post['id']}\">edit</a>  |  <a href=\"delete.php?id={$post['id']}\">delete</a>";
		echo '</div>';	
	}	
	
	echo "<div style=\"text-align: right; float: left; clear: both; width: 600px\"><a href=\"create.php\">new post</a></div>";
	
	include_once 'footer.php';
	
?>