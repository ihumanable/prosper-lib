<?php
  use Prosper\Query;

  class MSSqlBase extends UnitTestCase {
    
    protected $safe = 1;
    protected $danger = "' DROP TABLES --";
    
    protected $safe_result = 1;
    protected $danger_result = "'' DROP TABLES --";
    
    function MSSqlBase($name = '') {
      $this->UnitTestCase("MSSQL - $name");
    }
    
    function setUp() {
      Query::configure(Query::MSSQL_MODE, 'unittest', 'unittest', 'localhost', 'unittest');
    }
    
    function tearDown() {
    
    }
    
  }

?>