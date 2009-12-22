<?php

  /**
   * @package Prosper
   */     

  //The base classes
  require_once '_base_.php';
  
  //Most common database adapters
  require_once 'MySqlAdapter.php';
  require_once 'MSSqlAdapter.php';
  require_once 'PostgreSqlAdapter.php';
  require_once 'SqliteAdapter.php';
?>