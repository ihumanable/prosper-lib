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
    
    function test_update() {
      $query = Query::update('foo');
      $this->assertEqual($query->sql(), 'update [unittest].[foo]');
    }

    function update_set_values_array() {
      return Query::update('foo')->set( array( 'bar' => 1, 'baz' => 2));
    }
    
    function test_update_set_values_array_sql() {
      $query = $this->update_set_values_array();
      $this->assertEqual($query->sql(), 'update [unittest].[foo] set [bar] = ?, [baz] = ?');
    }
    
    function test_update_set_values_array_bindings() {
      $query = $this->update_set_values_array();
      $this->assertEqual($query->bindings(), array(1, 2));
    }
    
    function test_update_set_values_array_where_sql() {
      $query = $this->update_set_values_array()->where('zap = ?', 3);
      $this->assertEqual($query->sql(), 'update [unittest].[foo] set [bar] = ?, [baz] = ? where [zap] = ?');
    }

    function test_update_set_values_array_where_bindings() {
      $query = $this->update_set_values_array()->where('zap = ?', 3);
      $this->assertEqual($query->bindings(), array(1, 2, 3));
    }

    function update_set_values_sugar() {
      return Query::update('foo')->set('bar', 'baz', array('bar' => 1, 'baz' => 2));
    }
    
    function test_update_set_values_sugar_sql() {
      $query = $this->update_set_values_sugar();
      $this->assertEqual($query->sql(), 'update [unittest].[foo] set [bar] = ?, [baz] = ?');
    }
    
    function test_update_set_values_sugar_bindings() {
      $query = $this->update_set_values_sugar();
      $this->assertEqual($query->bindings(), array(1, 2));
    }
    
    function test_update_set_values_sugar_where_sql() {
      $query = $this->update_set_values_sugar()->where('zap = ?', 3);
      $this->assertEqual($query->sql(), 'update [unittest].[foo] set [bar] = ?, [baz] = ? where [zap] = ?');
    }
    
    function test_update_set_values_sugar_where_bindings() {
      $query = $this->update_set_values_sugar()->where('zap = ?', 3);
      $this->assertEqual($query->bindings(), array(1, 2, 3));
    }
    
    

  }

?>

