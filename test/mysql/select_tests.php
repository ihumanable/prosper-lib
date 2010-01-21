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
    
    function test_select_columns_alias() {
      $query = Query::select(array('foo' => 'f', 'bar' => 'b', 'baz' => 'z'));
      $this->assertEqual($query->sql(), 'select `foo` as `f`, `bar` as `b`, `baz` as `z`');
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
    
    function test_select_outer_join() {
      $query = Query::select()->from('foo')->outer('bar');
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo` outer join `unittest`.`bar`');
    }
    
    function test_select_inner_join() {
      $query = Query::select()->from('foo')->inner('bar');
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo` inner join `unittest`.`bar`');
    }
    
    function test_select_specified_join() {
      $query = Query::select()->from('foo')->specified_join('bar', '', 'specified join');
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo` specified join `unittest`.`bar`');
    }
    
    function test_select_specified_join_alias() {
      $query = Query::select()->from('foo')->specified_join('bar', 'b', 'specified join');
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo` specified join `unittest`.`bar` as `b`');
    }
    
    /* ---------- */
    
    
    
    /* Group and having */
    
    function test_select_group() {
      $query = Query::select()->from('foo')->group('bar');
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo` group by `bar`');
    }
    
    function test_select_group_having_sql() {
      $query = Query::select()->from('foo')->group('bar')->having('baz = ?', 1);
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo` group by `bar` having `baz` = ?');
    }
    
    function test_select_group_having_bindings() {
      $query = Query::select()->from('foo')->group('bar')->having('baz = ?', 1);
      $this->assertEqual($query->bindings(), array(1));
    }
    
    /* ---------- */
    
    
    /* Limits and Offsets */
    
    function test_select_limit() {
      $query = Query::select()->from('foo')->limit(10);
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo` limit 10');
    }
    
    function test_select_limit_offset() {
      $query = Query::select()->from('foo')->limit(10, 10);
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo` limit 10 offset 10');
    }
    
    /* ---------- */
    
    
    
    /* Order By */
    
    function test_select_order() {
      $query = Query::select()->from('foo')->order('bar');
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo` order by `bar` asc');
    }
    
    function test_select_order_desc() {
      $query = Query::select()->from('foo')->order('bar', 'desc');
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo` order by `bar` desc');
    }
    
    function test_select_order_nonsense() {
      $query = Query::select()->from('foo')->order('bar', 'monkey');  //Should default to 'asc' if invalid
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo` order by `bar` asc');
    }
    
    function test_select_order_mulitple() {
      $query = Query::select()->from('foo')->order(array('bar' => 'asc', 'baz' => 'desc'));
      $this->assertEqual($query->sql(), 'select * from `unittest`.`foo` order by `bar` asc, `baz` desc');
    }
    
    /* ---------- */
    
  }

?>

