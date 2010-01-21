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
    
    function test_insert_into_values_array_sql() {
      $query = Query::insert()->into('foo')->values( array( 'bar' => 1, 'baz' => 2 ));
      $this->assertEqual($query->sql(), 'insert into `unittest`.`foo` (`bar`, `baz`) values (?, ?)');
    }
    
    function test_insert_into_values_array_bindings() {
      $query = Query::insert()->into('foo')->values( array( 'bar' => 1, 'baz' => 2 ));
      $this->assertEqual($query->bindings(), array(1, 2));
    }
    
    function test_insert_into_values_sugar_sql() {
      $values = array('bar' => 1, 'baz' => 2);
      $query = Query::insert()->into('foo')->values('bar', 'baz', $values);
      $this->assertEqual($query->sql(), 'insert into `unittest`.`foo` (`bar`, `baz`) values (?, ?)');
    }
    
    function test_insert_into_values_sugar_bindings() {
      $values = array('bar' => 1, 'baz' => 2);
      $query = Query::insert()->into('foo')->values('bar', 'baz', $values);
      $this->assertEqual($query->bindings(), array(1, 2));
    }
    
    
  }

?>

