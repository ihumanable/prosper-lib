<?php
  use Prosper\Query;
  
  class UpdateTests extends UnitTestCase {
    
    function UpdateTests() {
      $this->UnitTestCase('MySQL - Update Statements');
    }
    
    function setUp() {
      Query::configure(Query::MYSQL_MODE, 'unittest', 'unittest', 'localhost', 'unittest');
    }
    
    function tearDown() {
    
    }
    
    function test_update() {
      $query = Query::update('foo');
      $this->assertEqual($query->sql(), 'update `unittest`.`foo`');
    }

  }

?>

