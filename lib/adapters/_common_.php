<?php

  //Base Adapter that all other adapters are based off of
	require_once 'BaseAdapter.php';
	
	//Most common database adapters
	require_once 'MySqlAdapter.php';
	require_once 'MSSqlAdapter.php';
	require_once 'PostgreSqlAdapter.php';
	require_once 'SqliteAdapter.php';
?>