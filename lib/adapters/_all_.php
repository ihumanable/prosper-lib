<?php
    
  //Base Adapter that all other adapters are based off of
  require_once 'BaseAdapter.php';
  
  //Derived Platform Specific Adapters
  require_once 'DB2Adapter.php';
  require_once 'FirebirdAdapter.php';
  require_once 'FrontBaseAdapter.php';
  require_once 'InformixAdapter.php';
  require_once 'IngresAdapter.php';
  require_once 'MaxDBAdapter.php';
  require_once 'MSqlAdapter.php';
  require_once 'MSSqlAdapter.php';
  require_once 'MySqlOldAdapter.php';
  require_once 'MySqlAdapter.php';
  require_once 'OracleAdapter.php';
  require_once 'OvrimosAdapter.php';
  require_once 'ParadoxAdapter.php';
  require_once 'PostgreSqlAdapter.php';
  require_once 'SqliteAdapter.php';
  require_once 'SybaseAdapter.php';
  
?>