<?php
  require_once 'base_test.php';
  use Prosper\Query;
  
  class MSSqlSelectTests extends MSSqlBase {
    
    function MSSqlSelectTests() {
      $this->MSSqlBase('Select Statements');
    }
    
    /* Basic Tests */
    
    function test_select() {
      $query = Query::select();
      $this->assertEqual($query->sql(), 'select *');
    }
    
    function test_select_columns() {
      $query = Query::select('foo', 'bar', 'baz');
      $this->assertEqual($query->sql(), 'select [foo], [bar], [baz]');
    }
    
    function test_select_columns_alias() {
      $query = Query::select(array('foo' => 'f', 'bar' => 'b', 'baz' => 'z'));
      $this->assertEqual($query->sql(), 'select [foo] as [f], [bar] as [b], [baz] as [z]');
    }
    
    function test_select_from() {
      $query = Query::select()->from('foo');
      $this->assertEqual($query->sql(), 'select * from [unittest].[foo]');
    }
    
    /* ---------- */
    
  
    
    /* Single where clauses */
    
    function select_where_anonymous_single($x) {
      $query = Query::select()->from('foo')->where('x = ?', $x);
      return $query;
    }
    
    function select_where_named_single($x) {
      return Query::select()->from('foo')->where('x = :x', array('x' => $x));
    }
    
    function assert_single_safe($query) {
      $this->assertEqual($query->sql(), "select * from [unittest].[foo] where [x] = '$this->safe_result'");
    }
    
    function assert_single_danger($query) {
      $this->assertEqual($query->sql(), "select * from [unittest].[foo] where [x] = '$this->danger_result'");
    }
    
    function test_select_where_anonymous_single_safe() {
      $this->assert_single_safe($this->select_where_anonymous_single($this->safe));
    }
    
    function test_select_where_anonymous_single_danger() {
      $this->assert_single_danger($this->select_where_anonymous_single($this->danger));
    }
    
    function test_select_where_named_single_safe() {
      $this->assert_single_safe($this->select_where_named_single($this->safe));
    }
    
    function test_select_where_named_single_danger() {
      $this->assert_single_danger($this->select_where_named_single($this->danger));
    }
    
    /* ---------- */
    
    
    /* Mulitple where clauses */
     
    function select_where_anonymous_multiple($x) {
      $y = 2;
      $z = 3;
      $query = Query::select()->from('foo')->where('x = ? and y = ? and z = ?', $x, $y, $z);
      return $query;
    }
    
    function select_where_named_multiple($x) {
      return Query::select()->from('foo')->where('x = :x and y = :y and z = :z', array('x' => $x, 'y' => 2, 'z' => 3));
    }
    
    function assert_multiple_safe($query) {
      $this->assertEqual($query->sql(), "select * from [unittest].[foo] where [x] = '$this->safe_result' and [y] = '2' and [z] = '3'");
    }
    
    function assert_multiple_danger($query) {
      $this->assertEqual($query->sql(), "select * from [unittest].[foo] where [x] = '$this->danger_result' and [y] = '2' and [z] = '3'");
    }
    
    function test_select_where_anonymous_multiple_safe() {
      $this->assert_multiple_safe($this->select_where_anonymous_multiple($this->safe));
    }
    
    function test_select_where_anonymous_multiple_danger() {
      $this->assert_multiple_danger($this->select_where_anonymous_multiple($this->danger));
    }
    
    function test_select_where_named_multiple_safe() {
      $this->assert_multiple_safe($this->select_where_named_multiple($this->safe));
    }
    
    function test_select_where_named_multiple_danger() {
      $this->assert_multiple_danger($this->select_where_named_multiple($this->danger));
    }
    
    /* ---------- */
    
    
    /* Joins */
    
    function test_select_join() {
      $query = Query::select()->from('foo')->join('bar');
      $this->assertEqual($query->sql(), 'select * from [unittest].[foo] join [unittest].[bar]');
    }
    
    function test_select_join_on() {
      $query = Query::select()->from('foo')->join('bar')->on('foo.bar_id = bar.id');
      $this->assertEqual(
        $query->sql(), 
        'select * from [unittest].[foo] join [unittest].[bar] on [foo].[bar_id] = [bar].[id]'
        );
    }
    
    function test_select_join_on_alias() {
      $query = Query::select()->from('foo', 'f')->join('bar', 'b')->on('f.bar_id = b.id');
      $this->assertEqual(
        $query->sql(),
        'select * from [unittest].[foo] as [f] join [unittest].[bar] as [b] on [f].[bar_id] = [b].[id]'
        );
    }
    
    function test_select_left_join() {
      $query = Query::select()->from('foo')->left('bar');
      $this->assertEqual($query->sql(), 'select * from [unittest].[foo] left join [unittest].[bar]');
    }
    
    function test_select_outer_join() {
      $query = Query::select()->from('foo')->outer('bar');
      $this->assertEqual($query->sql(), 'select * from [unittest].[foo] outer join [unittest].[bar]');
    }
    
    function test_select_inner_join() {
      $query = Query::select()->from('foo')->inner('bar');
      $this->assertEqual($query->sql(), 'select * from [unittest].[foo] inner join [unittest].[bar]');
    }
    
    function test_select_specified_join() {
      $query = Query::select()->from('foo')->specified_join('bar', '', 'specified join');
      $this->assertEqual($query->sql(), 'select * from [unittest].[foo] specified join [unittest].[bar]');
    }
    
    function test_select_specified_join_alias() {
      $query = Query::select()->from('foo')->specified_join('bar', 'b', 'specified join');
      $this->assertEqual($query->sql(), 'select * from [unittest].[foo] specified join [unittest].[bar] as [b]');
    }
    
    /* ---------- */
    
    
    
    /* Group and having */
    
    function test_select_group() {
      $query = Query::select()->from('foo')->group('bar');
      $this->assertEqual($query->sql(), 'select * from [unittest].[foo] group by [bar]');
    }
    
    function test_select_group_having_safe() {
      $query = Query::select()->from('foo')->group('bar')->having('baz = ?', $this->safe);
      $this->assertEqual($query->sql(), "select * from [unittest].[foo] group by [bar] having [baz] = '$this->safe_result'");
    }
    
    function test_select_group_having_danger() {
      $query = Query::select()->from('foo')->group('bar')->having('baz = ?', $this->danger);
      $this->assertEqual($query->sql(), "select * from [unittest].[foo] group by [bar] having [baz] = '$this->danger_result'");
    }
    
    /* ---------- */
    
    
    /* Limits and Offsets */
    
    function test_select_limit() {
      $query = Query::select()->from('foo')->limit(10);
      $this->assertEqual($query->sql(), 'select top 10 * from [unittest].[foo]');
    }
    
    /*
      TODO: Figure out the best way to write this test
    function test_select_limit_offset() {
      
    }
    */
    
    /* ---------- */
    
    
    
    /* Order By */
    
    function test_select_order() {
      $query = Query::select()->from('foo')->order('bar');
      $this->assertEqual($query->sql(), 'select * from [unittest].[foo] order by [bar] asc');
    }
    
    function test_select_order_desc() {
      $query = Query::select()->from('foo')->order('bar', 'desc');
      $this->assertEqual($query->sql(), 'select * from [unittest].[foo] order by [bar] desc');
    }
    
    function test_select_order_nonsense() {
      $query = Query::select()->from('foo')->order('bar', 'monkey');  //Should default to 'asc' if invalid
      $this->assertEqual($query->sql(), 'select * from [unittest].[foo] order by [bar] asc');
    }
    
    function test_select_order_mulitple() {
      $query = Query::select()->from('foo')->order(array('bar' => 'asc', 'baz' => 'desc'));
      $this->assertEqual($query->sql(), 'select * from [unittest].[foo] order by [bar] asc, [baz] desc');
    }
    
    /* ---------- */
    
  }

?>

