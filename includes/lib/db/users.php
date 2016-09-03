<?php
namespace lib\db;
use \lib\db;

class users {

	public static function get(){
		$date = date("Y-m-d");
		$no_position = T_("Undefined");
		$query =
				"SELECT
					users.id,
					users.user_displayname as displayname,
					IFNULL(users.user_meta,'$no_position') as meta,

					hours.hour_start
				FROM users
				LEFT JOIN hours
					ON hours.user_id = users.id
					AND hours.hour_date = DATE(NOW())
					AND hours.hour_end is null
				ORDER BY users.id
				";
		$users = db::get($query);
		$new   = array_column($users, "id");
		$users = array_combine($new, $users);

		return $users;
	}
}

?>