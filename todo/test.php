<?php
namespace Prosper;

require_once 'config.php';

echo Query::select()->from('foo')->where("firstname = ? and monkey.last_name <> ? or(principia like ?)");


?>