<?php
$parts = explode('/', $_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF']);
array_pop($parts);
define('MYSQL_ROOT', implode('/', $parts) . '/mysql/');

class MySqlSuite extends TestSuite {

  function MySqlSuite() {
    $this->TestSuite('MySql Tests');
    
    $this->addFile(MYSQL_ROOT . 'select_tests.php');
    $this->addFile(MYSQL_ROOT . 'insert_tests.php');
    $this->addFile(MYSQL_ROOT . 'update_tests.php');
    $this->addFile(MYSQL_ROOT . 'delete_tests.php');
    
    
    if(array_key_exists('functional', $_REQUEST)) {
      $this->addFile(MYSQL_ROOT . 'functional_tests.php');
    }
  }

}

?>