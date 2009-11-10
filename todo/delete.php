<?php
	namespace Prosper;
	require_once 'config.php';
	
	
	if($_SERVER['REQUEST_METHOD'] == 'POST') {
		//Delete the todo and redirect
		Query::delete()
				 ->from('todo')
				 ->where("id = :id", $_POST)
				 ->execute();
		header("Location: index.php");
	} else {
		include_once 'header.php';
		
		$todos = Query::select()
									->from('todo')
									->where("id = :id", $_GET)
									->execute();
									
		$todo = $todos[0];
?>

		<h3>delete todo</h3>
		
		<form class='todo' action='delete.php' method='post'>
			<input type="hidden" name="id" value="<?php echo $todo['id']; ?>" />
			<div class="item">
				Delete "<?php echo $todo['title']; ?>" ?
			</div>
			<div class="controls">
				<input type="submit" value="Confirm" />
				<button onclick="window.location.href='index.php'; return false;">Cancel</button>
			</div>
		</form>

<?php

		include_once 'footer.php';
	}
		
?>