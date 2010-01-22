<?php
$parts = explode('/', $_SERVER['DOCUMENT_ROOT'] . $_SERVER['PHP_SELF']);
array_pop($parts);
define('MSSQL_ROOT', implode('/', $parts) . '/mssql/');

class MSSqlSuite extends TestSuite {

  function MSSqlSuite() {
    $this->TestSuite('MSSql Tests');
    $this->addFile(MSSQL_ROOT . 'select_tests.php');
    $this->addFile(MSSQL_ROOT . 'insert_tests.php');
    $this->addFile(MSSQL_ROOT . 'update_tests.php');
    $this->addFile(MSSQL_ROOT . 'delete_tests.php');
  }

}

?>