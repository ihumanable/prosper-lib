<?php
  use Prosper\Query;
  
  class MySqlFunctionalTests extends UnitTestCase {
    
    private $record = array('bar' => 1, 'baz' => 2, 'zap' => 3);   
    
    function MySqlFunctionalTests() {
      $this->UnitTestCase('MySQL - Functional Test');
    }
    
    function setUp() {
      Query::configure(Query::MYSQL_MODE, 'unittest', 'unittest', 'localhost', 'unittest');
      //Start with a clean slate
      Query::delete()->from('foo')->execute();
    }
    
    function tearDown() {
      //Clean out anything the test might have inserted
      Query::delete()->from('foo')->execute();
    }
    
    function populateRow($values) {
      Query::insert()->into('foo')->values( $values )->execute(); 
    }
    
    function test_insert_single() { 
      $this->populateRow($this->record);
      $result = Query::select()->from('foo')->execute();
      $this->assertEqual($result[0], $this->record);
    }
    
    function test_insert_multiple() {
      for($i = 0; $i < 3; ++$i) {
        $this->populateRow($this->record);
      }
      $result = Query::select()->from('foo')->execute();
      $this->assertEqual(3, count($result));
    }
    
    function test_update_single() {
      $this->populateRow($this->record);
      Query::update('foo')->set( array('bar' => 5) )->execute();
      $result = Query::select()->from('foo')->execute();
      $expected = $this->record;
      $expected['bar'] = 5;
      $this->assertEqual($result[0], $expected);
    }
    
    function test_update_multiple() {
      for($i = 0; $i < 3; ++$i) {
        $this->populateRow($this->record);
      }
      Query::update('foo')->set( array('bar' => 5) )->execute();
      $result = Query::select()->from('foo')->execute();
      $expected = $this->record;
      $expected['bar'] = 5;
      $this->assertEqual($result, array($expected, $expected, $expected));
    }
    
    function test_update_targeted() {
      $this->populateRow($this->record);
      $different = $this->record;
      $different['bar'] = 7;
      $this->populateRow($different);
      Query::update('foo')->set( array('baz' => 5) )->where('bar = ?', 7)->execute();
      $result = Query::select()->from('foo')->order('bar')->execute();
      
      $expected = $different;
      $expected['baz'] = 5;
      $this->assertEqual($result, array($this->record, $expected));
    }
    
    function test_delete() {
      $this->populateRow($this->record);
      Query::delete()->from('foo')->execute();
      $result = Query::select()->from('foo')->execute();
      $this->assertEqual($result, array());
    }
    
    function test_delete_targeted() {
      $this->populateRow($this->record);
      $different = $this->record;
      $different['bar'] = 7;
      $this->populateRow($different);
      Query::delete()->from('foo')->where('bar = ?', 7)->execute();
      $result = Query::select()->from('foo')->execute();
      $this->assertEqual($result, array($this->record));
    }
    
    function test_limit() {
      for($i = 0; $i < 100; ++$i) {
        $this->populateRow($this->record);   
      } 
      $result = Query::select()->from('foo')->limit(10)->execute();
      $this->assertEqual(10, count($result));
    }
    
    function test_limit_offset() {
      for($i = 0; $i < 100; ++$i) {
        $record = $this->record;
        $record['bar'] = ($i < 10 ? "0$i" : $i);
        if($i == 20) {
          $first = $record;
        } else if($i == 21) {
          $second = $record;
        } else if($i == 22) {
          $third = $record;
        }
        $this->populateRow($record);
      }
      $result = Query::select()->from('foo')->order('bar')->limit(3, 20)->execute();
      $this->assertEqual($result, array($first, $second, $third));
    }
    
    function test_rollback() {
      Query::begin(); {
        $this->populateRow($this->record);
      } Query::rollback();
      $result = Query::select()->from('foo')->execute();
      $this->assertEqual($result, array());
    }
    
    function test_commit() {
      Query::begin(); {
        $this->populateRow($this->record);
      } Query::commit();
      $result = Query::select()->from('foo')->execute();
      $this->assertEqual($result, array($this->record));
    }
    
    function test_concurrent() {
      $different = $this->record;
      $different['bar'] = 5;

      $this->populateRow($different);
      $this->populateRow($this->record);
      
      $q1 = Query::update('foo')->set(array('baz' => 10));
      $q2 = Query::update('foo')->set(array('baz' => 20));
      
      $q1->where('bar = ?', 1)->execute();
      $q2->where('bar = ?', 5)->execute();
      
      $result = Query::select()->from('foo')->order('bar')->execute();
      
      $this->assertEqual($result, array( array( 'bar' => 1, 'baz' => 10, 'zap' => 3 ),
                                         array( 'bar' => 5, 'baz' => 20, 'zap' => 3 ) ));      
    }
    
    function test_repeatable() {
      $this->populateRow($this->record);
      
      $query = Query::select()->from('foo');
      
      $result1 = $query->execute();
      $result2 = $query->execute();
      
      $this->assertEqual($result1, $result2);
    }
    
    function test_repeatable_prepared() {
      $this->populateRow($this->record);
      $different = $this->record;
      $different['bar'] = 5;
      $this->populateRow($different);
      
      $query = Query::select()->from('foo')->where('bar = ?', 5);
      
      $result1 = $query->execute();
      $result2 = $query->execute();
      
      $this->assertEqual($result1, $result2);
    }
    
    function test_rebinding() {
      $this->populateRow($this->record);
      
      $query = Query::select()->from('foo')->where('bar = ?', 5);
      
      $result1 = $query->execute();
      
      $query->rebind(1);
      $result2 = $query->execute();
      
      $this->assertEqual($result1, array());
      $this->assertEqual($result2, array($this->record));
    }
    
    function test_iterable() {
      $this->populateRow($this->record);
      $this->populateRow($this->record);
      $this->populateRow($this->record);
      
      $query = Query::select()->from('foo');
      
      foreach($query as $row) {
        $this->assertEqual($row, $this->record); 
      }
    }
    
  }

?>

