<?php

// self::$perm_list[] =
// [
// 	'caller'      => 'add:company',
// 	'title'       => T_("Add new company"),
// 	'desc'        => T_("Add new company"),
// 	'group'       => 'plan_1',
// 	'need_check'  => true,
// 	'need_verify' => true,
// 	'enable'      => true,
// 	'parent'      => null, // 1,5
// ];

/*admin:view*/
self::$perm_list[1] =
[
	'caller' => 'admin:view',
	'title'  => T_("Can view the admin page"),
	'group'  => 'admin',
];

/*admin:add*/
self::$perm_list[2] =
[
	'caller' => 'admin:add',
	'title'  => T_("Can add hourse for all users in admin page"),
	'group'  => 'admin',
	'parent' => 'admin:view',
];

/*admin:edit*/
self::$perm_list[3] =
[
	'caller' => 'admin:edit',
];

/*admin:admin*/
self::$perm_list[4] =
[
	'caller' => 'admin:admin',
];

/*home:view*/
self::$perm_list[5] =
[
	'caller' => 'home:view',
];

/*home:admin*/
self::$perm_list[6] =
[
	'caller' => 'home:admin',
];

/*home:add*/
self::$perm_list[7] =
[
	'caller' => 'home:add',
];

/*remote:view*/
self::$perm_list[8] =
[
	'caller' => 'remote:view',
];

/*secret:view*/
self::$perm_list[9] =
[
	'caller' => 'secret:view',
];


?>