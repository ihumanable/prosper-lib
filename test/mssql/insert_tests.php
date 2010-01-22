<?php
  use Prosper\Query;
  
  class MSSqlInsertTests extends UnitTestCase {
    
    function MSSqlInsertTests() {
      $this->UnitTestCase('MSSQL - Insert Statements');
    }
    
    function setUp() {
      Query::configure(Query::MSSQL_MODE, 'unittest', 'unittest', 'localhost', 'unittest');
    }
    
    function tearDown() {
    
    }
    
    function test_insert() {
      $query = Query::insert();
      $this->assertEqual($query->sql(), 'insert');
    }
    
    function test_insert_into() {
      $query = Query::insert()->into('foo');
      $this->assertEqual($query->sql(), 'insert into [unittest].[foo]');
    }
    
    function insert_into_values_array() {
      return Query::insert()->into('foo')->values( array( 'bar' => 1, 'baz' => 2));
    }
    
    function test_insert_into_values_array_sql() {
      $query = $this->insert_into_values_array();
      $this->assertEqual($query->sql(), 'insert into [unittest].[foo] ([bar], [baz]) values (?, ?)');
    }
    
    function test_insert_into_values_array_bindings() {
      $query = $this->insert_into_values_array();
      $this->assertEqual($query->bindings(), array(1, 2));
    }
    
    function test_insert_into_values_array_where_sql() {
      $query = $this->insert_into_values_array()->where('zap = ?', 3);
      $this->assertEqual($query->sql(), 'insert into [unittest].[foo] ([bar], [baz]) values (?, ?) where [zap] = ?');
    }

    function test_insert_into_values_array_where_bindings() {
      $query = $this->insert_into_values_array()->where('zap = ?', 3);
      $this->assertEqual($query->bindings(), array(1, 2, 3));
    }

    function insert_into_values_sugar() {
      return Query::insert()->into('foo')->values('bar', 'baz', array('bar' => 1, 'baz' => 2));
    }
    
    function test_insert_into_values_sugar_sql() {
      $query = $this->insert_into_values_sugar();
      $this->assertEqual($query->sql(), 'insert into [unittest].[foo] ([bar], [baz]) values (?, ?)');
    }
    
    function test_insert_into_values_sugar_bindings() {
      $query = $this->insert_into_values_sugar();
      $this->assertEqual($query->bindings(), array(1, 2));
    }
    
    function test_insert_into_values_sugar_where_sql() {
      $query = $this->insert_into_values_sugar()->where('zap = ?', 3);
      $this->assertEqual($query->sql(), 'insert into [unittest].[foo] ([bar], [baz]) values (?, ?) where [zap] = ?');
    }
    
    function test_insert_into_values_sugar_where_bindings() {
      $query = $this->insert_into_values_sugar()->where('zap = ?', 3);
      $this->assertEqual($query->bindings(), array(1, 2, 3));
    }
    
  }

?>

