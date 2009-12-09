<?php
  namespace Prosper;
  require_once 'config.php';
  
  include_once 'header.php';
  
  Query::select()->from('todo')->where('title = ?', 'Matt')->execute();
  
  include_once 'footer.php';
  
?>