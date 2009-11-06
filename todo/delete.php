<?php
	namespace Prosper;
	require_once 'config.php';
	
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		//Delete the post and redirect
		Query::delete()->from('blog')->where("id = '{$_POST['id']}'")->execute();
		header("Location: index.php");
	} else {
		include_once 'header.php';
		
		$posts = Query::select()->from('blog')->where("id = '{$_GET['id']}'")->execute();
		$post = $posts[0];
		
		echo "<h1>Delete Post</h1>";
		
		echo "<form class='post' action='delete.php' method='post'>";
			echo "Are you sure you want to delete '{$post['title']}'?<br /><br />";
			echo "<input type=\"hidden\" name=\"id\" value=\"{$post['id']}\" />";
			echo "<input type=\"submit\" value=\"Confirm\" />";
			echo "<button onclick=\"window.location.href='index.php'; return false;\">Cancel</button>";
		echo "</form>";
		
		
		include_once 'footer.php';
	}
	
	
	
	
?>