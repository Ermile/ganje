<?php
$modules = [];

$modules['admin'] =
[
	'desc' 			=> T_('Manage enter and exit and confirm records'),
	'permissions'	=> ['view', 'add', 'edit', 'admin'],
];
/**
 * register enter and exit
 */
$modules['secret'] =
[
	'desc' 			=> T_('Remove this user from staff list'),
	'permissions'	=> ['view'],
];


$modules['remote'] =
[
	'desc' 			=> T_('Allow enter remote time'),
	'permissions'	=> ['add'],
];


$modules['home'] =
[
	'desc' 			=> T_('Allow enter remote time'),
	'permissions'	=> ['view', 'add', 'admin'],
];

return ["modules" => $modules];
?>