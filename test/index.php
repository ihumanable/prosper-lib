<?php

  require_once 'config.php';
  
  $modes = array( Prosper\Query::DB2_MODE,
                  Prosper\Query::DBASE_MODE,
                  Prosper\Query::FIREBIRD_MODE,
                  Prosper\Query::FRONTBASE_MODE,
                  Prosper\Query::INFORMIX_MODE,
                  Prosper\Query::INGRES_MODE,
                  Prosper\Query::MAXDB_MODE,
                  Prosper\Query::MSQL_MODE,
                  Prosper\Query::MSSQL_MODE,
                  Prosper\Query::MYSQL_MODE,
                  Prosper\Query::MYSQL_OLD_MODE,
                  Prosper\Query::ORACLE_MODE,
                  Prosper\Query::OVRIMOS_MODE,
                  Prosper\Query::PARADOX_MODE,
                  Prosper\Query::POSTGRE_MODE,
                  Prosper\Query::SQLITE_MODE,
                  Prosper\Query::SYBASE_MODE     );
                  
  $name = "Robert'); DROP TABLE Students;--";
  
  echo "<fieldset>";
    echo '<legend>Testing ::select()->from("students")->where("name = ?", $name);  ($name = ' . $name . ')</legend>';
    foreach($modes as $mode) {
      Prosper\Query::configure($mode, "username", "password", "localhost", "schema");
      $sql = Prosper\Query::select()->from("students")->where("name = ?", $name);
      echo $mode;
      echo "<pre>";
      echo $sql;
      echo "</pre>";
    }
  echo "</fieldset>";

?>