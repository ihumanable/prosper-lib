<?php
  use Prosper\Query;
  
  class SelectTests extends UnitTestCase {
    
    function SelectTests() {
      $this->UnitTestCase('Select Statements');
    }
    
    function setUp() {
      Query::configure(Query::MYSQL_MODE, 'unittest', 'unittest', 'localhost', 'unittest');
    }
    
    function tearDown() {
    
    }
    
    function test_select() {
      $query = Query::select();
      $this->assertEqual($query->sql(), 'select *');
    }
    
    function test_select_from() {
      $query = Query::select()->from('foo');
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo`');
    }
    
    function select_where_naive() {
      $x = 1;
      $query = Query::select()->from('foo')->where("x = '$x'");
      return $query;
    }
    
    function select_where_anonymous() {
      $x = 1;
      $query = Query::select()->from('foo')->where('x = ?', $x);
      return $query;
    }
    
    function select_where_named() {
      return Query::select()->from('foo')->where('x = :x', array('x' => 1));
    }
    
    function assert_sql($query) {
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo` where `x` = ?');
    }
    
    function assert_bindings($query) {
      $this->assertEqual($query->bindings(), array(1));
    }
    
    function test_select_where_naive_sql() {
      $this->assert_sql($this->select_where_naive());
    }
    
    function test_select_where_naive_bindings() {
      $this->assert_bindings($this->select_where_naive());
    }
    
    function test_select_where_anonymous_sql() {
      $this->assert_sql($this->select_where_anonymous());
    }
    
    function test_select_where_anonymous_bindings() {
      $this->assert_bindings($this->select_where_anonymous());
    }
    
    function test_select_where_named_sql() {
      $this->assert_sql($this->select_where_named());
    }
    
    function test_select_where_named_bindings() {
      $this->assert_bindings($this->select_where_named());
    }
  }

?>

