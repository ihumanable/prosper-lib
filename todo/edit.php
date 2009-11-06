<?php
	namespace Prosper;
	require_once 'config.php';
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		Query::update('blog')->set(array('title' => $_POST['title'], 'content' => $_POST['content']))->where("id = '{$_POST['id']}'")->execute();
		
		header("Location: index.php");
	} else {
		include_once 'header.php';
		
		$posts = Query::select()->from('blog')->where("id = '{$_GET['id']}'")->execute();
		$post = $posts[0];
		
		echo '<h1>Edit Post</h1>';
		
		echo '<form class="post" action="edit.php" method="post">';
			echo "<label for=\"title\">Title</label><input type=\"text\" name=\"title\" value=\"{$post['title']}\" /><br />";
			echo "<label for=\"content\">Content</label><textarea name=\"content\" rows=\"3\" cols=\"40\">{$post['content']}</textarea><br />";
			echo "<input type=\"hidden\" name=\"id\" value=\"{$post['id']}\" />";
			echo "<input type=\"submit\" value=\"Edit\" />";
			echo "<button onclick=\"window.location.href='index.php'; return false;\">Cancel</button>";			
		echo '</form>';
		
		
		include_once 'footer.php';
	}


?>