<?php
  namespace Prosper;
  require_once 'config.php';
  
  if($_SERVER['REQUEST_METHOD'] == 'POST') {
    Query::update('todo')
         ->set('title', $_POST)
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
    display_form('edit.php', 'edit', $todo['title'], $todo['id']);
    
    include_once 'footer.php';
  }
  
?>