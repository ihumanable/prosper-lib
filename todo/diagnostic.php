<?php
  namespace Prosper;
  require_once 'config.php';
  
  include_once 'header.php';
  
  echo "<h2>Schema Information</h2>";
  syntax_print(Query::tables());
  
  include_once 'footer.php';
  
?>