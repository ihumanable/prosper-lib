<?php
  use Prosper\Query;
  
  class MSSqlUpdateTests extends UnitTestCase {
    
    function MSSqlUpdateTests() {
      $this->UnitTestCase('MSSQL - Update Statements');
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
    
    function test_update() {
      $query = Query::update('foo');
      $this->assertEqual($query->sql(), 'update [unittest].[foo]');
    }

    function update_set_values_array($bar) {
      return Query::update('foo')->set( array( 'bar' => $bar, 'baz' => 2));
    }
    
    function test_update_set_values_array_safe() {
      $query = $this->update_set_values_array($this->safe_value());
      $this->assertEqual($query->sql(), "update [unittest].[foo] set [bar] = '1', [baz] = '2'");
    }
    
    function test_update_set_values_array_danger() {
      $query = $this->update_set_values_array($this->danger_value());
      $this->assertEqual($query->sql(), "update [unittest].[foo] set [bar] = ''' DROP TABLES --', [baz] = '2'");
    }
    
    function test_update_set_values_array_where_safe() {
      $query = $this->update_set_values_array($this->safe_value())->where('zap = ?', $this->safe_value());
      $this->assertEqual($query->sql(), "update [unittest].[foo] set [bar] = '1', [baz] = '2' where [zap] = '1'");
    }

    function test_update_set_values_array_where_danger() {
      $query = $this->update_set_values_array($this->danger_value())->where('zap = ?', $this->danger_value());
      $this->assertEqual($query->sql(), "update [unittest].[foo] set [bar] = ''' DROP TABLES --', [baz] = '2' where [zap] = ''' DROP TABLES --'");
    }

    function update_set_values_sugar($bar) {
      return Query::update('foo')->set('bar', 'baz', array('bar' => $bar, 'baz' => 2));
    }
    
    function test_update_set_values_sugar_safe() {
      $query = $this->update_set_values_sugar($this->safe_value());
      $this->assertEqual($query->sql(), "update [unittest].[foo] set [bar] = '1', [baz] = '2'");
    }
    
    function test_update_set_values_sugar_danger() {
      $query = $this->update_set_values_sugar($this->danger_value());
      $this->assertEqual($query->sql(), "update [unittest].[foo] set [bar] = ''' DROP TABLES --', [baz] = '2'");
    }
    
    function test_update_set_values_sugar_where_safe() {
      $query = $this->update_set_values_sugar($this->safe_value())->where('zap = ?', $this->safe_value());
      $this->assertEqual($query->sql(), "update [unittest].[foo] set [bar] = '1', [baz] = '2' where [zap] = '1'");
    }

    function test_update_set_values_sugar_where_danger() {
      $query = $this->update_set_values_sugar($this->danger_value())->where('zap = ?', $this->danger_value());
      $this->assertEqual($query->sql(), "update [unittest].[foo] set [bar] = ''' DROP TABLES --', [baz] = '2' where [zap] = ''' DROP TABLES --'");
    }
    
    

  }

?>

