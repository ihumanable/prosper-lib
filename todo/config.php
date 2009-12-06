<?php
  //Standard pathing definition
  define('PROSPER_PATH', '../lib/');
  
  //Require Prosper
  require_once PROSPER_PATH . 'adapters/_common_.php';
  require_once PROSPER_PATH . 'Query.php';
  
  //Go and get the utility functions
  require_once 'utility.php';
  
  //Configure prosper to connect to the database
  
  //MS-SQL Sample
    //Prosper\Query::configure(Prosper\Query::MSSQL_MODE, 'sa', 'sa', 'SERVER\SQLEXPRESS', 'test.dbo');
  
  //MySql Sample
    //Prosper\Query::configure(Prosper\Query::MYSQL_MODE, 'root', 'xamppdevpwd', 'localhost', 'test');
  

?>