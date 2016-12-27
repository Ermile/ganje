<?php
$modules = [];

$modules['admin'] =
[
	'desc' 			=> T_('Manage enter and exit and confirm records'),
	'permissions'	=> ['view', 'add', 'edit', 'delete', 'admin'],
];
/**
 * register enter and exit
 */
$modules['intro'] =
[
	'desc' 			=> T_('Allow to enter and exit staff'),
	'permissions'	=> ['admin'],
];


$modules['remote'] =
[
	'desc' 			=> T_('Allow enter remote time'),
	'permissions'	=> ['admin'],
];

return ["modules" => $modules];
?>