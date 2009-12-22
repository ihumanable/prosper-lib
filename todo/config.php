<?php
  //Use Prosper's Query
  use Prosper\Query;

  //Standard pathing definition
  define('PROSPER_PATH', '../lib/');
  
  //Require Prosper
  require_once PROSPER_PATH . 'adapters/_common_.php';
  require_once PROSPER_PATH . 'Query.php';
  
  //Go and get the utility functions
  require_once 'utility.php';
  
  //Configure prosper to connect to the database (uncomment the appropriate line and fill in with proper credentials)
  
  //MS-SQL Sample
    //Query::configure(Query::MSSQL_MODE, 'sa', 'sa', 'LOCALHOST\SQLEXPRESS', 'test.dbo');
  
  //MySql Sample
    Query::configure(Query::MYSQL_MODE, 'phpuser', 'phppwd', 'localhost', 'test');
  

?>