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
    display_form('delete.php', 'delete', $todo['title'], $todo['id'], false);
    
    include_once 'footer.php';
  }
  
?>