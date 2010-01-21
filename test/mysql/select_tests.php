<?php
  use Prosper\Query;
  
  class SelectTests extends UnitTestCase {
    
    function SelectTests() {
      $this->UnitTestCase('MySQL - Select Statements');
    }
    
    function setUp() {
      Query::configure(Query::MYSQL_MODE, 'unittest', 'unittest', 'localhost', 'unittest');
    }
    
    function tearDown() {
    
    }
    
    /* Basic Tests */
    
      function test_select() {
        $query = Query::select();
        $this->assertEqual($query->sql(), 'select *');
      }
      
      function test_select_columns() {
        $query = Query::select('foo', 'bar', 'baz');
        $this->assertEqual($query->sql(), 'select `foo`, `bar`, `baz`');
      }
      
      function test_select_from() {
        $query = Query::select()->from('foo');
        $this->assertEqual($query->sql(), 'select * from `unittest`.`foo`');
      }
    
    /* ---------- */
    
    
    
    /* Single where clauses */
    
    function select_where_naive_single() {
      $x = 1;
      $query = Query::select()->from('foo')->where("x = '$x'");
      return $query;
    }
    
    function select_where_anonymous_single() {
      $x = 1;
      $query = Query::select()->from('foo')->where('x = ?', $x);
      return $query;
    }
    
    function select_where_named_single() {
      return Query::select()->from('foo')->where('x = :x', array('x' => 1));
    }
    
    function assert_where_single($query) {
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo` where `x` = ?');
    }
    
    function assert_bindings_single($query) {
      $this->assertEqual($query->bindings(), array(1));
    }
    
    function test_select_where_naive_single_sql() {
      $this->assert_where_single($this->select_where_naive_single());
    }
    
    function test_select_where_naive_single_bindings() {
      $this->assert_bindings_single($this->select_where_naive_single());
    }
    
    function test_select_where_anonymous_single_sql() {
      $this->assert_where_single($this->select_where_anonymous_single());
    }
    
    function test_select_where_anonymous_single_bindings() {
      $this->assert_bindings_single($this->select_where_anonymous_single());
    }
    
    function test_select_where_named_single_sql() {
      $this->assert_where_single($this->select_where_named_single());
    }
    
    function test_select_where_named_single_bindings() {
      $this->assert_bindings_single($this->select_where_named_single());
    }
    
    /* ---------- */
    
    
    /* Mulitple where clauses */
     
    function select_where_naive_multiple() {
      $x = 1;
      $y = 2;
      $z = 3;
      $query = Query::select()->from('foo')->where("x = '$x' and y = '$y' and z = '$z'");
      return $query;
    }
    
    function select_where_anonymous_multiple() {
      $x = 1;
      $y = 2;
      $z = 3;
      $query = Query::select()->from('foo')->where('x = ? and y = ? and z = ?', $x, $y, $z);
      return $query;
    }
    
    function select_where_named_multiple() {
      return Query::select()->from('foo')->where('x = :x and y = :y and z = :z', array('x' => 1, 'y' => 2, 'z' => 3));
    }
    
    function assert_where_multiple($query) {
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo` where `x` = ? and `y` = ? and `z` = ?');
    }
    
    function assert_bindings_multiple($query) {
      $this->assertEqual($query->bindings(), array(1, 2, 3));
    }
    
    function test_select_where_naive_multiple_sql() {
      $this->assert_where_multiple($this->select_where_naive_multiple());
    }
    
    function test_select_where_naive_multiple_bindings() {
      $this->assert_bindings_multiple($this->select_where_naive_multiple());
    }
    
    function test_select_where_anonymous_multiple_sql() {
      $this->assert_where_multiple($this->select_where_anonymous_multiple());
    }
    
    function test_select_where_anonymous_multiple_bindings() {
      $this->assert_bindings_multiple($this->select_where_anonymous_multiple());
    }
    
    function test_select_where_named_multiple_sql() {
      $this->assert_where_multiple($this->select_where_named_multiple());
    }
    
    function test_select_where_named_multiple_bindings() {
      $this->assert_bindings_multiple($this->select_where_named_multiple());
    }
    
    /* ---------- */
    
    
    /* Joins */
    
    function test_select_join() {
      $query = Query::select()->from('foo')->join('bar');
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo` join `unittest`.`bar`');
    }
    
    function test_select_join_on() {
      $query = Query::select()->from('foo')->join('bar')->on('foo.bar_id = bar.id');
      $this->assertEqual(
        $query->sql(), 
        'select * from `unittest`.`foo` join `unittest`.`bar` on `foo`.`bar_id` = `bar`.`id`'
        );
    }
    
    function test_select_join_on_alias() {
      $query = Query::select()->from('foo', 'f')->join('bar', 'b')->on('f.bar_id = b.id');
      $this->assertEqual(
        $query->sql(),
        'select * from `unittest`.`foo` as `f` join `unittest`.`bar` as `b` on `f`.`bar_id` = `b`.`id`'
        );
    }
    
    function test_select_left_join() {
      $query = Query::select()->from('foo')->left('bar');
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo` left join `unittest`.`bar`');
    }
    
    
  }

?>

