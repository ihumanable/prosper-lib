<?php
  use Prosper\Query;
  
  class InsertTests extends UnitTestCase {
    
    function InsertTests() {
      $this->UnitTestCase('MySQL - Insert Statements');
    }
    
    function setUp() {
      Query::configure(Query::MYSQL_MODE, 'unittest', 'unittest', 'localhost', 'unittest');
    }
    
    function tearDown() {
    
    }
    
    function test_insert() {
      $query = Query::insert();
      $this->assertEqual($query->sql(), 'insert');
    }
    
    function test_insert_into() {
      $query = Query::insert()->into('foo');
      $this->assertEqual($query->sql(), 'insert into `unittest`.`foo`');
    }
    
    
  }

?>

