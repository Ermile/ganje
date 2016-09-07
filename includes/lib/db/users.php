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


	/*
	* check users status
	*/
	public static function check($_user_id)
	{
		$query = "SELECT
					user_status
				FROM users
				WHERE id = $_user_id
				LIMIT 1";

		$check_user = db::get($query, "user_status", true);

		if($check_user != "active"){
			return false;
		}else{
			return true;
		}
	}




	/**
	 * set time of enter or exit
	 */
	public static function set_time($_args)
	{

		$user_id = $_args['user_id'];
		$minus   = $_args['minus'];
		$plus    = $_args['plus'];

		// check status of users
		// if users status is not enable return false and make debug error
		if(!self::check($user_id)){
			return false;
		}

		$today = date("Y-m-d");
		$time  = date("H:i");

		$query = "SELECT * FROM hours
					WHERE
						user_id   = $user_id AND
						hour_date = '$today' AND
						hour_end IS NULL
					LIMIT 1";

		$check_date = db::get($query, null, true);

		if($check_date == null)
		{
			//----- add firs time in day
			$insert = "INSERT INTO hours
						SET user_id = $user_id,
							hour_date = '$today',
							hour_start = '$time'
							";

			db::query($insert);
			return 'enter';

		}
		elseif($check_date['hour_end'] == null)
		{
			// set start time
			// $this->start = strtotime("$today ". $check_date['hour_start']);
			//------- add end time
			$update = "UPDATE hours
						SET hour_end = '$time',
							hour_diff = TIME_TO_SEC(TIMEDIFF(hour_end,hour_start)) / 60,
							hour_plus = $plus,
							hour_minus = $minus,
							hour_total = (hour_diff + hour_plus - hour_minus),
							hour_status = 'raw',
							hour_accepted = hour_total

						WHERE
							id = {$check_date['id']} ";

			db::query($update);
			return 'exit';
		}
	}

}
?>