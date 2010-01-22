<?php
  use Prosper\Query;
  
  class MySqlDeleteTests extends UnitTestCase {
    
    function MySqlDeleteTests() {
      $this->UnitTestCase('MySQL - Delete Statements');
    }
    
    function setUp() {
      Query::configure(Query::MYSQL_MODE, 'unittest', 'unittest', 'localhost', 'unittest');
    }
    
    function tearDown() {
    
    }
    
    function test_delete() {
      $query = Query::delete();
      $this->assertEqual($query->sql(), 'delete');
    }
    
    function test_delete_from() {
      $query = Query::delete()->from('foo');
      $this->assertEqual($query->sql(), 'delete from `unittest`.`foo`');
    }    

    /* Single where clause */
    
    function delete_where_naive_single() {
      $x = 1;
      $query = Query::delete()->from('foo')->where("x = '$x'");
      return $query;
    }
    
    function delete_where_anonymous_single() {
      $x = 1;
      $query = Query::delete()->from('foo')->where('x = ?', $x);
      return $query;
    }
    
    function delete_where_named_single() {
      return Query::delete()->from('foo')->where('x = :x', array('x' => 1));
    }
    
    function assert_where_single($query) {
      $this->assertEqual($query->sql(), 'delete from `unittest`.`foo` where `x` = ?');
    }
    
    function assert_bindings_single($query) {
      $this->assertEqual($query->bindings(), array(1));
    }
    
    function test_delete_where_naive_single_sql() {
      $this->assert_where_single($this->delete_where_naive_single());
    }
    
    function test_delete_where_naive_single_bindings() {
      $this->assert_bindings_single($this->delete_where_naive_single());
    }
    
    function test_delete_where_anonymous_single_sql() {
      $this->assert_where_single($this->delete_where_anonymous_single());
    }
    
    function test_delete_where_anonymous_single_bindings() {
      $this->assert_bindings_single($this->delete_where_anonymous_single());
    }
    
    function test_delete_where_named_single_sql() {
      $this->assert_where_single($this->delete_where_named_single());
    }
    
    function test_delete_where_named_single_bindings() {
      $this->assert_bindings_single($this->delete_where_named_single());
    }
    
    /* ---------- */
    
    
    /* Multiple where clause */
    
    function delete_where_naive_multiple() {
      $x = 1;
      $y = 2;
      $z = 3;
      $query = Query::delete()->from('foo')->where("x = '$x' and y = '$y' and z = '$z'");
      return $query;
    }
    
    function delete_where_anonymous_multiple() {
      $x = 1;
      $y = 2;
      $z = 3;
      $query = Query::delete()->from('foo')->where('x = ? and y = ? and z = ?', $x, $y, $z);
      return $query;
    }
    
    function delete_where_named_multiple() {
      return Query::delete()->from('foo')->where('x = :x and y = :y and z = :z', array('x' => 1, 'y' => 2, 'z' => 3));
    }
    
    function assert_where_multiple($query) {
      $this->assertEqual($query->sql(), 'delete from `unittest`.`foo` where `x` = ? and `y` = ? and `z` = ?');
    }
    
    function assert_bindings_multiple($query) {
      $this->assertEqual($query->bindings(), array(1, 2, 3));
    }
    
    function test_delete_where_naive_multiple_sql() {
      $this->assert_where_multiple($this->delete_where_naive_multiple());
    }
    
    function test_delete_where_naive_multiple_bindings() {
      $this->assert_bindings_multiple($this->delete_where_naive_multiple());
    }
    
    function test_delete_where_anonymous_multiple_sql() {
      $this->assert_where_multiple($this->delete_where_anonymous_multiple());
    }
    
    function test_delete_where_anonymous_multiple_bindings() {
      $this->assert_bindings_multiple($this->delete_where_anonymous_multiple());
    }
    
    function test_delete_where_named_multiple_sql() {
      $this->assert_where_multiple($this->delete_where_named_multiple());
    }
    
    function test_delete_where_named_multiple_bindings() {
      $this->assert_bindings_multiple($this->delete_where_named_multiple());
    }
    
  }

?>

