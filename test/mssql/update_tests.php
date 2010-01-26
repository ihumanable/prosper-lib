<?php
  use Prosper\Query;
  
  class MSSqlUpdateTests extends MSSqlBase {
    
    function MSSqlUpdateTests() {
      $this->MSSqlBase('Update Statements');
    }
    
    function test_update() {
      $query = Query::update('foo');
      $this->assertEqual($query->sql(), 'update [unittest].[foo]');
    }

    function update_set_values_array($bar) {
      return Query::update('foo')->set( array( 'bar' => $bar, 'baz' => 2));
    }
    
    function test_update_set_values_array_safe() {
      $query = $this->update_set_values_array($this->safe);
      $this->assertEqual($query->sql(), "update [unittest].[foo] set [bar] = '$this->safe_result', [baz] = '2'");
    }
    
    function test_update_set_values_array_danger() {
      $query = $this->update_set_values_array($this->danger);
      $this->assertEqual($query->sql(), "update [unittest].[foo] set [bar] = '$this->danger_result', [baz] = '2'");
    }
    
    function test_update_set_values_array_where_safe() {
      $query = $this->update_set_values_array($this->safe)->where('zap = ?', $this->safe);
      $this->assertEqual($query->sql(), "update [unittest].[foo] set [bar] = '$this->safe_result', [baz] = '2' where [zap] = '$this->safe_result'");
    }

    function test_update_set_values_array_where_danger() {
      $query = $this->update_set_values_array($this->danger)->where('zap = ?', $this->danger);
      $this->assertEqual($query->sql(), "update [unittest].[foo] set [bar] = '$this->danger_result', [baz] = '2' where [zap] = '$this->danger_result'");
    }

    function update_set_values_sugar($bar) {
      return Query::update('foo')->set('bar', 'baz', array('bar' => $bar, 'baz' => 2));
    }
    
    function test_update_set_values_sugar_safe() {
      $query = $this->update_set_values_sugar($this->safe);
      $this->assertEqual($query->sql(), "update [unittest].[foo] set [bar] = '$this->safe_result', [baz] = '2'");
    }
    
    function test_update_set_values_sugar_danger() {
      $query = $this->update_set_values_sugar($this->danger);
      $this->assertEqual($query->sql(), "update [unittest].[foo] set [bar] = '$this->danger_result', [baz] = '2'");
    }
    
    function test_update_set_values_sugar_where_safe() {
      $query = $this->update_set_values_sugar($this->safe)->where('zap = ?', $this->safe);
      $this->assertEqual($query->sql(), "update [unittest].[foo] set [bar] = '$this->safe_result', [baz] = '2' where [zap] = '$this->safe_result'");
    }

    function test_update_set_values_sugar_where_danger() {
      $query = $this->update_set_values_sugar($this->danger)->where('zap = ?', $this->danger);
      $this->assertEqual($query->sql(), "update [unittest].[foo] set [bar] = '$this->danger_result', [baz] = '2' where [zap] = '$this->danger_result'");
    }
    
    

  }

?>

