<?php
  /**
   * @package Prosper
   */
   
  //Base Adapter that all other adapters are based off of
	require_once 'BaseAdapter.php';
	//Optional interface for prepared statement supporting adapters
  require_once 'IPreparable.php';    
  //Query is the engine that is prosper
  require_once '/../Query.php';
   
?>