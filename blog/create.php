<?php
	namespace Prosper;
	require_once 'config.php';
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		Query::insert()->into('blog')->values(array('title' => $_POST['title'], 'content' => $_POST['content']))->execute();
		
		header("Location: index.php");
	} else {
		include_once 'header.php';
		
		echo '<h1>Create Post</h1>';
		
		echo '<form class="post" action="create.php" method="post">';
			echo "<label for=\"title\">Title</label><input type=\"text\" name=\"title\" value=\"\" /><br />";
			echo "<label for=\"content\">Content</label><textarea name=\"content\" rows=\"3\" cols=\"40\"></textarea><br />";
			echo "<input type=\"submit\" value=\"Create\" />";
			echo "<button onclick=\"window.location.href='index.php'; return false;\">Cancel</button>";			
		echo '</form>';
		
		
		include_once 'footer.php';
	}


?>