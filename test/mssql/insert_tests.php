<?php
  require_once 'base_test.php';
  use Prosper\Query;
  
  class MSSqlInsertTests extends MSSqlBase {
    
    function MSSqlInsertTests() {
      $this->MSSqlBase('Insert Statements');
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
      $query = $this->insert_into_values_array($this->safe);
      $this->assertEqual($query->sql(), "insert into [unittest].[foo] ([bar], [baz]) values ('$this->safe_result', '2')");
    }
    
    function test_insert_into_values_array_danger() {
      $query = $this->insert_into_values_array($this->danger);
      $this->assertEqual($query->sql(), "insert into [unittest].[foo] ([bar], [baz]) values ('$this->danger_result', '2')");
    }
    
    function test_insert_into_values_array_where_safe() {
      $query = $this->insert_into_values_array($this->safe)->where('zap = ?', $this->safe);
      $this->assertEqual($query->sql(), "insert into [unittest].[foo] ([bar], [baz]) values ('$this->safe_result', '2') where [zap] = '$this->safe_result'");
    }

    function test_insert_into_values_array_where_danger() {
      $query = $this->insert_into_values_array($this->danger)->where('zap = ?', $this->danger);
      $this->assertEqual($query->sql(), "insert into [unittest].[foo] ([bar], [baz]) values ('$this->danger_result', '2') where [zap] = '$this->danger_result'");
    }

    function insert_into_values_sugar($bar) {
      return Query::insert()->into('foo')->values('bar', 'baz', array('bar' => $bar, 'baz' => 2));
    }
    
    function test_insert_into_values_sugar_safe() {
      $query = $this->insert_into_values_sugar($this->safe);
      $this->assertEqual($query->sql(), "insert into [unittest].[foo] ([bar], [baz]) values ('$this->safe_result', '2')");
    }
    
    function test_insert_into_values_sugar_danger() {
      $query = $this->insert_into_values_sugar($this->danger);
      $this->assertEqual($query->sql(), "insert into [unittest].[foo] ([bar], [baz]) values ('$this->danger_result', '2')"); 
    }
    
    function test_insert_into_values_sugar_where_safe() {
      $query = $this->insert_into_values_sugar($this->safe)->where('zap = ?', $this->safe);
      $this->assertEqual($query->sql(), "insert into [unittest].[foo] ([bar], [baz]) values ('$this->safe_result', '2') where [zap] = '$this->safe_result'");
    }
    
    function test_insert_into_values_sugar_where_danger() {
      $query = $this->insert_into_values_sugar($this->danger)->where('zap = ?', $this->danger);
      $this->assertEqual($query->sql(), "insert into [unittest].[foo] ([bar], [baz]) values ('$this->danger_result', '2') where [zap] = '$this->danger_result'");
    }
    
  }

?>

