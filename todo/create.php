<?php
  namespace Prosper;
  require_once 'config.php';
  
  if($_SERVER['REQUEST_METHOD'] == 'POST') {
    Query::insert()
         ->into('todo')
         ->values('title', $_POST)
         ->execute();
    
    header("Location: index.php");
  } else {
    include_once 'header.php';
    
    display_form('create.php', 'create');
    
    include_once 'footer.php';
  }
  
?>