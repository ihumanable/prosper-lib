<?php
  use Prosper\Query;
  
  class MSSqlDeleteTests extends UnitTestCase {
    
    function MSSqlDeleteTests() {
      $this->UnitTestCase('MSSQL - Delete Statements');
    }
    
    function setUp() {
      Query::configure(Query::MSSQL_MODE, 'unittest', 'unittest', 'localhost', 'unittest');
    }
    
    function tearDown() {
    
    }
    
    function test_delete() {
      $query = Query::delete();
      $this->assertEqual($query->sql(), 'delete');
    }
    
    function test_delete_from() {
      $query = Query::delete()->from('foo');
      $this->assertEqual($query->sql(), 'delete from [unittest].[foo]');
    }    

    /* Single where clause */
    
    function delete_where_anonymous_single($x) {
      $query = Query::delete()->from('foo')->where('x = ?', $x);
      return $query;
    }
    
    function delete_where_named_single($x) {
      return Query::delete()->from('foo')->where('x = :x', array('x' => $x));
    }
    
    function safe_value() {
      return 1;
    }
    
    function danger_value() {
      return "' DROP TABLES --";
    }
    
    function assert_single($query) {
      $this->assertEqual($query->sql(), "delete from [unittest].[foo] where [x] = '1'");
    }
    
    function assert_single_escaped($query) {
      $this->assertEqual($query->sql(), "delete from [unittest].[foo] where [x] = ''' DROP TABLES --'");
    }
    
    function test_delete_where_anonymous_single() {
      $this->assert_single($this->delete_where_anonymous_single($this->safe_value()));
    }
    
    function test_delete_where_anonymous_single_escaped() {
      $this->assert_single_escaped($this->delete_where_anonymous_single($this->danger_value()));
    }
    
    function test_delete_where_named_single_sql() {
      $this->assert_single($this->delete_where_named_single($this->safe_value()));
    }
    
    function test_delete_where_named_single_bindings() {
      $this->assert_single_escaped($this->delete_where_named_single($this->danger_value()));
    }
    
    /* ---------- */
    
    
    /* Multiple where clause */
    
    function delete_where_anonymous_multiple($x) {
      $y = 2;
      $z = 3;
      $query = Query::delete()->from('foo')->where('x = ? and y = ? and z = ?', $x, $y, $z);
      return $query;
    }
    
    function delete_where_named_multiple($x) {
      return Query::delete()->from('foo')->where('x = :x and y = :y and z = :z', array('x' => $x, 'y' => 2, 'z' => 3));
    }
    
    function assert_multiple($query) {
      $this->assertEqual($query->sql(), "delete from [unittest].[foo] where [x] = '1' and [y] = '2' and [z] = '3'");
    }
    
    function assert_multiple_escaped($query) {
      $this->assertEqual($query->sql(), "delete from [unittest].[foo] where [x] = ''' DROP TABLES --' and [y] = '2' and [z] = '3'");
    }
    
    function test_delete_where_anonymous_multiple() {
      $this->assert_multiple($this->delete_where_anonymous_multiple($this->safe_value()));
    }
    
    function test_delete_where_anonymous_multiple_escaped() {
      $this->assert_multiple_escaped($this->delete_where_anonymous_multiple($this->danger_value()));
    }
    
    function test_delete_where_named_multiple() {
      $this->assert_multiple($this->delete_where_named_multiple($this->safe_value()));
    }
    
    function test_delete_where_named_multiple_escaped() {
      $this->assert_multiple_escaped($this->delete_where_named_multiple($this->danger_value()));
    }
    
  }

?>

