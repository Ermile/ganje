<?php
namespace lib\db;
use \lib\db;

class users {

	public static function get_all($_args = []){

		if(!isset($_args['user_id'])){
			$condition = " ORDER BY users.id ";
		}else{
			$condition = " WHERE users.id = " . $_args['user_id'];
		}

		$date = date("Y-m-d");
		$no_position = T_("Undefined");
		// $query_new =
		// 		"SELECT
		// 			users.id,
		// 			users.user_displayname as displayname,
		// 			TRIM(BOTH '".'"'."' FROM IFNULL(JSON_EXTRACT(users.user_meta,'$.position'), '$no_position')) as meta,
		// 			hours.hour_start
		// 		FROM users
		// 		LEFT JOIN hours
		// 			ON hours.user_id = users.id
		// 			AND hours.hour_date = DATE(NOW())
		// 			AND hours.hour_end is null
		// 		$condition
		// 		";

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
				$condition
				";
		$users = db::get($query);
		$new   = array_column($users, "id");
		$users = array_combine($new, $users);

		return $users;
	}


	public static function get_one($_user_id) {
		$resutl = self::get_all(['user_id' => $_user_id]);
		return $resutl[$_user_id];
	}


	public static function live(){
		$date = date("Y-m-d");
		$query = "
			SELECT
				count(id) as total
			FROM
				hours
			WHERE
				hour_date = '$date'
			LIMIT 1
			;";
		$total = \lib\db::get($query, "total", true);
		// return result as number of live users
		return $total;
	}
}

?>