<?php
namespace lib\db;
use \lib\db;

class staff {


	/**
	 * get all avtive users white today tome
	 *
	 * @param      array   $_args  The arguments
	 *
	 * @return     <type>  The all.
	 */
	public static function get_all($_args = [])
	{

		if(!isset($_args['user_id']))
		{
			$condition = " ORDER BY users.id ";
		}
		else
		{
			$condition = " AND users.id = " . $_args['user_id'];
		}

		$date = date("Y-m-d");
		$no_position = T_("Undefined");

		$query =
				"SELECT
					users.id,
					users.user_permission AS `permission`,
					users.user_displayname AS `displayname`,
					IFNULL(users.user_meta,'$no_position') AS meta,
					hours.hour_start
				FROM users
				LEFT JOIN hours
					ON hours.user_id = users.id
					AND hours.hour_date = '$date'
					AND hours.hour_end is null
				WHERE
					users.user_status = 'active'
				$condition
				";
		$users = db::get($query);
		$new   = array_column($users, "id");
		$users = array_combine($new, $users);
		foreach ($users as $key => $value)
		{
			if(isset($value['permission']))
			{
				$users[$key]['permission'] = self::permission_name($value['permission']);
			}
		}

		return $users;
	}


	/**
	 * load permission name from options table
	 *
	 * @param      <type>  $_user_permission  The user permission
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function permission_name($_user_permission)
	{

		if(is_null($_user_permission))
		{
			return null;
		}

		$query =
		"
			SELECT
				option_value AS `permission`
			FROM
				options
			WHERE
				user_id IS NULL AND
				post_id IS NULL AND
				option_cat = 'permissions' AND
				option_key = '$_user_permission'
			LIMIT 1
		";
		$resutl = \lib\db::get($query, 'permission', true);
		if(empty($resutl) || is_array($resutl))
		{
			return null;
		}
		return $resutl;
	}


	/**
	 * get one users times today
	 *
	 * @param      <type>  $_user_id  The user id
	 *
	 * @return     <type>  The one.
	 */
	public static function get_one($_user_id)
	{
		$resutl = self::get_all(['user_id' => $_user_id]);
		return $resutl[$_user_id];
	}


	/**
	 * get start time in today of one users
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get_start($_user_id)
	{
		$date = date("Y-m-d");
		$query = "
			SELECT
				hour_start as 'start'
			FROM
				hours
			WHERE
				hour_date = '$date' AND
				user_id   = $_user_id
			ORDER BY id DESC
			LIMIT 1
			;";
		$start = \lib\db::get($query, "start", true);
		return $start;
	}


	/**
	 * get count on online users
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function enter()
	{
		$date = date("Y-m-d");
		$query = "SELECT
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


	/**
	 * get count on online users
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function live()
	{
		$date = date("Y-m-d");
		$query = "SELECT
				count(id) as total
			FROM
				hours
			WHERE
				hour_date = '$date'
				AND hour_end IS NULL
			LIMIT 1
			;";
		$total = \lib\db::get($query, "total", true);
		// return result as number of live users
		return $total;
	}


	/**
	 * check users status
	 *
	 * @param      <type>   $_user_id  The user identifier
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function check($_user_id)
	{
		$query = "SELECT
					user_status
				FROM users
				WHERE id = $_user_id
				LIMIT 1";

		$check_user = db::get($query, "user_status", true);

		if($check_user != "active")
		{
			return false;
		}
		else
		{
			return true;
		}
	}


	/**
	 * set enter or exit time of users
	 *
	 * @param      <type>          $_args  The arguments
	 *
	 * @return     boolean|string  ( description_of_the_return_value )
	 */
	public static function set_time($_args)
	{
		if(!isset($_args['user_id']) || !isset($_args['minus']) || !isset($_args['plus']))
		{
			return false;
		}

		$user_id = $_args['user_id'];
		$minus   = $_args['minus'];
		$plus    = $_args['plus'];

		// check status of users
		// if users status is not enable return false and make debug error
		if(!self::check($user_id))
		{
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
		elseif(array_key_exists('hour_end', $check_date) && $check_date['hour_end'] == null)
		{
			// set start time
			// $this->start = strtotime("$today ". $check_date['hour_start']);
			$start_time = date("H:i");
			if(isset($check_date['hour_start']))
			{
				$start_time = $check_date['hour_start'];
			}
			$end_time = $time;

			$start_time = strtotime($start_time);
			$end_time   = strtotime($end_time);
			$diff       = $end_time - $start_time;

			if($minus * 60 > $diff)
			{
				$minus = $diff / 60;
			}

			//------- add end time
			$update = "UPDATE hours
						SET hour_end = '$time',
							hour_diff = TIME_TO_SEC(TIMEDIFF(hour_end,hour_start)) / 60,
							hour_plus = IF($plus = 0, NULL, $plus),
							hour_minus = IF($minus = 0, NULL, $minus)
						WHERE
							id = {$check_date['id']} ";

			db::query($update);
			return 'exit';
		}
	}
}
?>