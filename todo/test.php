<?php
namespace Prosper;

require_once 'config.php';

echo Query::select()
			->from('user')
			->where("mod(age, mod(monkey, '2')) = 0");