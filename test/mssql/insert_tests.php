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
    
    function safe_value() {
      return 1;
    }
    
    function danger_value() {
      return "' DROP TABLES --";
    }
    
    function test_insert() {
      $query = Query::insert();
      $this->assertEqual($query->sql(), 'insert');
    }
    
    function test_insert_into() {
      $query = Query::insert()->into('foo');
      $this->assertEqual($query->sql(), 'insert into [unittest].[foo]');
    }
    
    function insert_into_values_array($bar) {
      return Query::insert()->into('foo')->values( array( 'bar' => $bar, 'baz' => 2));
    }
    
    function test_insert_into_values_array_safe() {
      $query = $this->insert_into_values_array($this->safe_value());
      $this->assertEqual($query->sql(), "insert into [unittest].[foo] ([bar], [baz]) values ('1', '2')");
    }
    
    function test_insert_into_values_array_danger() {
      $query = $this->insert_into_values_array($this->danger_value());
      $this->assertEqual($query->sql(), "insert into [unittest].[foo] ([bar], [baz]) values (''' DROP TABLES --', '2')");
    }
    
    function test_insert_into_values_array_where_safe() {
      $query = $this->insert_into_values_array($this->safe_value())->where('zap = ?', $this->safe_value());
      $this->assertEqual($query->sql(), "insert into [unittest].[foo] ([bar], [baz]) values ('1', '2') where [zap] = '1'");
    }

    function test_insert_into_values_array_where_danger() {
      $query = $this->insert_into_values_array($this->danger_value())->where('zap = ?', $this->danger_value());
      $this->assertEqual($query->sql(), "insert into [unittest].[foo] ([bar], [baz]) values (''' DROP TABLES --', '2') where [zap] = ''' DROP TABLES --'");
    }

    function insert_into_values_sugar($bar) {
      return Query::insert()->into('foo')->values('bar', 'baz', array('bar' => $bar, 'baz' => 2));
    }
    
    function test_insert_into_values_sugar_safe() {
      $query = $this->insert_into_values_sugar($this->safe_value());
      $this->assertEqual($query->sql(), "insert into [unittest].[foo] ([bar], [baz]) values ('1', '2')");
    }
    
    function test_insert_into_values_sugar_danger() {
      $query = $this->insert_into_values_sugar($this->danger_value());
      $this->assertEqual($query->sql(), "insert into [unittest].[foo] ([bar], [baz]) values (''' DROP TABLES --', '2')"); 
    }
    
    function test_insert_into_values_sugar_where_safe() {
      $query = $this->insert_into_values_sugar($this->safe_value())->where('zap = ?', $this->safe_value());
      $this->assertEqual($query->sql(), "insert into [unittest].[foo] ([bar], [baz]) values ('1', '2') where [zap] = '1'");
    }
    
    function test_insert_into_values_sugar_where_danger() {
      $query = $this->insert_into_values_sugar($this->danger_value())->where('zap = ?', $this->danger_value());
      $this->assertEqual($query->sql(), "insert into [unittest].[foo] ([bar], [baz]) values (''' DROP TABLES --', '2') where [zap] = ''' DROP TABLES --'");
    }
    
  }

?>

