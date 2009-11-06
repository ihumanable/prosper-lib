<?php
	namespace Prosper;
	require_once 'config.php';
	
	include_once 'header.php';
?>

	<div id="create">
		<a href="create.php">
			<img src="img/create.png" alt="create todo"/> <div>create todo</div>
		</a>
	</div>
	
<?php
	
	//Ok pull out all of the todo items, stable order
	$todos = Query::select()
								->from('todo')
								->order('id')
								->execute();
	
	foreach($todos as $todo) {
?>

	<div class="todo">
		<div class="item">#<?php echo $todo['id'] . " - " . $todo['title'] . " @ " . time_ago(Query::mktime($todo['timestamp'])); ?> </div>
		<div class="controls"><a href="edit.php?id=<?php echo $todo['id']; ?>">edit</a>  |  <a href="delete.php?id=<?php echo $todo['id']; ?>">delete</a></div>
	</div>	

<?php
	}
	
	include_once 'footer.php';
	
?>