<?php
$modules = [];
$modules['home'] = [
	'desc' 			=> T_('Register enter and exit'),
	'permissions'	=> ['view', 'edit'],
	];

$modules['admin'] = [
	'desc' 			=> T_('Manage enter and exit and confirm records'),
	'permissions'	=> ['view', 'add', 'edit', 'delete'],
	];



return ["modules" => $modules];
?>