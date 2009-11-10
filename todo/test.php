<?php
namespace Prosper;

require_once 'config.php';

echo Query::select()
			->from('foo')
			->where("parameter = ? OR firstname = :firstname AND lastname = :lastname", "blerg",  
					array(":lastname" => "Matt", 
						  ":firstname" => "O'Malley"));


?>