<?php
namespace lib\db;
use \lib\db;

class hours {


	/**
	 * insert new record in hours table
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function insert($_args)
	{

		$date    = $_args['date'] 	 ? $_args['date'] 	 : date("Y-m-d") ;
		$start   = $_args['start'] 	 ? $_args['start'] 	 : null;
		$end     = $_args['end'] 	 ? $_args['end'] 	 : null;
		$user_id = $_args['user_id'] ? $_args['user_id'] : 0;
		$minus   = $_args['minus'] 	 ? $_args['minus'] 	 : 0;
		$plus    = $_args['plus'] 	 ? $_args['plus'] 	 : 0;

		if(!$user_id || !$start || !$end)
		{
			\lib\debug::error(T_("user id, start time and end time is require"));
		}

		$query = "
			INSERT INTO
				hours
			SET
				user_id   	  = $user_id,
				hour_date     = '$date',
				hour_start    = '$start',
				hour_end      = '$end',
				hour_diff     = TIME_TO_SEC(TIMEDIFF(hour_end,hour_start)) / 60,
				hour_plus     = IF('$plus' = 0, NULL, '$plus'),
				hour_minus    = IF('$minus' = 0, NULL, '$minus')
				";
		return \lib\db::query($query);
	}


	/**
	 * update hours record by id
	 * get record id and status and end time
	 *
	 * @param      <type>   $_args  The arguments
	 *
	 * @return     boolean  ( description_of_the_return_value )
	 */
	public static function update($_args)
	{

		if(!isset($_args['id']))
		{
			\lib\debug::error(T_("record id not found"));
			$id = 0;
		}
		else
		{
			$id = $_args['id'];
		}


		if(isset($_args['status']))
		{
			$status = " hours.hour_status = '" . $_args['status'] . "' ";
		}
		else
		{
			$status = "";
		}

		if(isset($_args['time']))
		{

			$check = db::get("SELECT id FROM hours WHERE id = $id LIMIT 1 ",null, true);

			if(!$check)
			{
				return false;
			}
			else
			{
				$time = $_args['time'];
				$saved_time = $check['hour_start'];
				if($saved_time > $time)
				{
					$temp       = $time;
					$time       = "'$saved_time'";
					$saved_time = "'$temp'";
				}
				else
				{
					$saved_time = "hour_start";
					$time       = "hour_end";
				}
				$time_query =
				"
						hour_start = $saved_time,
						hour_end   = $time
				";
			}
		}
		else
		{
			$time_query = "";
		}

		if($status && $time_query)
		{
			$time_query = $time_query . ",";
		}

		if(!$status && !$time_query)
		{
			return false;
		}

		$query =
		"
			UPDATE
				hours
			SET
				$time_query
				$status
			WHERE
				id = $id
		";
		return db::query($query);
	}


	/**
	 * return last record of hours table
	 * if none param send to this function get last record of all users
	 * you can send ['user' => \d+] to get last record of this users
	 * @param      array   $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	private static function get_last_time($_args = [])
	{
		if(isset($_args['user_id']))
		{
			$user = " users.id = '" . $_args['user_id'] . "' ";
			$user_displayname = "";
		}
		else
		{
			$user = "";
			$user_displayname = "users.user_displayname 	as 'name',";
		}

		if(isset($_args['date']))
		{
			$date = " hours.hour_date = '". $_args['date']. "'";
		}
		else
		{
			$date = "";
		}

		// pagenation
		$count_record =
		"
			SELECT
				COUNT(id) AS 'count'
			FROM
				hours
			WHERE
				(hours.hour_status = 'filter' OR hours.hour_status = 'active')
		";

		list($limit_start, $length) = \lib\db::pagenation($count_record, 10);
		$limit = " LIMIT $limit_start, $length ";

		if($date || $user)
		{
			$where = " WHERE ";
		}
		else
		{
			$where = "";
		}

		if($date && $user)
		{
			$user = " AND " . $user;
		}

		//--------- repeat to every query
		$query = "
				SELECT
					hours.id 						AS 'id',
					hours.hour_date 				AS 'date',
					$user_displayname
					hours.hour_start 				AS 'start',
					hours.hour_end 					AS 'end',
					hours.hour_end 					AS 'end',
					IFNULL(hours.hour_diff,0) 	 	AS 'diff',
					IFNULL(hours.hour_plus,0) 	 	AS 'plus',
					IFNULL(hours.hour_minus,0) 	 	AS 'minus',
					hours.hour_status				AS 'status',
					IFNULL(hours.hour_accepted,0) 	AS 'accepted'
				FROM
					hours
				LEFT JOIN users on hours.user_id = users.id
				$where
				$date
				$user
				ORDER BY
					hours.id DESC
				$limit
				";
		$report = db::get($query);
		return $report;
	}


	/**
	 * get sum of hours table
	 * total hour of work in month, week, day
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function sum($_args)
	{
		$user  = isset($_args['user'])  ? $_args['user']  : null;
		$day   = isset($_args['day'])   ? $_args['day']   : null;
		$week  = isset($_args['week'])  ? $_args['week']  : null;
		$month = isset($_args['month']) ? $_args['month'] : null;
		$year  = isset($_args['year'])  ? $_args['year']  : null;
		$lang  = isset($_args['lang'])  ? $_args['lang']  : null;

		if($month == '0')
		{
			$month = null;
		}

		$q         = [];
		$q['user'] = $user != null ? "users.id = $user" : null;

		if($user)
		{
			$USER = "";
		}
		else
		{
			$USER = " GROUP BY	hours.user_id ";
		}

		if($lang == "fa")
		{
			if($year && $month)
			{
				list($start_date, $end_date)  = \lib\utility\jdate::jalali_month($year, $month);
				$q['month'] = " hours.hour_date > '$start_date' AND hours.hour_date < '$end_date' ";

			}
			elseif($year && !$month)
			{
				list($start_date, $end_date)  = \lib\utility\jdate::jalali_year($year);
				$q['month'] = " hours.hour_date > '$start_date' AND hours.hour_date < '$end_date' ";
			}
		}
		else
		{
			$q['month'] = $month  != null ? "YEAR(hours.hour_date) = '$year' AND MONTH(hours.hour_date) = '$month' " : null;
			$q['year']  = $year   != null ? "YEAR(hours.hour_date) = '$year' " : null;
			$q['week']  = $week   != null ? "WEEKOFYEAR(hours.hour_date)=WEEKOFYEAR('$week')" : null;
		}

		$condition = ' AND '. implode(" AND ", array_filter($q));

		$start  = (isset($_args["start"])) ? $_args["start"] : 0; // start limit
		$end    = (isset($_args["end"]))   ? $_args["end"]   : 10; // end limit

		//--------- repeat to every query
		$no_position = T_("Undefined");

		$query = "
				SELECT
					users.id as id,
					users.user_displayname as name,
					TRIM(BOTH '".'"'."' FROM IFNULL(users.user_meta, '$no_position')) as meta,
					sum(hours.hour_diff) as diff,
					sum(hours.hour_plus) as plus,
					sum(hours.hour_minus) as minus,
					sum(hours.hour_accepted) as accepted
				FROM
					hours
				INNER JOIN users on hours.user_id = users.id
				WHERE
					  (hours.hour_status = 'filter' OR hours.hour_status = 'active')
				$condition
				$USER
				LIMIT $start,$end
				";
		$report = db::get($query);
		return $report;
	}


	/**
	 * status of users
	 *
	 * @param      <type>  $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function get($_args)
	{
		$year    = $_args['year'];
		$month   = $_args['month'];
		$day     = $_args['day'];
		$user_id = $_args['user_id'];
		$lang    = $_args['lang'];

		// check user id . if users id is set get add data by this users id and if users id is not set get all users
		if($user_id == null)
		{
			$user_id_query = "";
		}
		else
		{
			$user_id_query = " AND user_id = $user_id ";
		}

		// set year = false to use in IF syntax
		if($year == '0000')
		{
			$year = false;
		}

		if($month == '00')
		{
			$month = false;
		}

		if($day == '00')
		{
			$day = false;
		}

		// other mode that need to change to new mode
		if(!$year && $month && $day)
		{
			$year = date("Y");
		}

		if(!$year && !$month && $day)
		{
			$year  = date("Y");
			$month = date("m");
		}

		if(!$year && $month && !$day)
		{
			$year = date("Y");
		}

		if($year && !$month && $day)
		{
			$month = date("m");
		}

		if(!$year && !$month && !$day && !$user_id)
		{
			return self::get_last_time();
		}

		if(!$year && !$month && !$day && $user_id)
		{
			return self::get_last_time(['user_id' => $user_id]);
		}

		if($year && $month && $day)
		{
			if($lang == 'fa')
			{
				$jdate = \lib\utility\jdate::toGregorian($year,$month, $day);
				$jdate = join($jdate, "-");
				$date = $jdate;
			}
			else
			{
				$date = "$year-$month-$day";
			}

			if($user_id)
			{
				return self::get_last_time(['user_id' => $user_id, 'date' => $date]);
			}
			else
			{
				return self::get_last_time(['date' => $date]);
			}
		}

		// fields of table whit sum function
		$sum_fields =
		"
			SUM(IFNULL(hours.hour_diff,0))		AS 'diff',
			SUM(IFNULL(hours.hour_plus,0))		AS 'plus',
			SUM(IFNULL(hours.hour_minus,0))		AS 'minus',
			SUM(IFNULL(hours.hour_accepted,0))	AS 'accepted'
		";
		$field =
		"
			count(hours.hour_date) 				as 'count',
			$sum_fields
		";

		if($year && $month && !$day)
		{
			// get daily count of hours
			if($lang == 'fa')
			{
				$day_query = "(CASE ";
				for ($i=1; $i <= 31 ; $i++) {
					if($i < 10){
						$i = "0". $i;
					}
					$jdate = \lib\utility\jdate::toGregorian($year,$month, $i);
					$jdate = join($jdate, "-");
					$day_query .=	" WHEN hours.hour_date = '{$jdate}' THEN '$i' \n";
				}
				list($start_date, $end_date) = \lib\utility\jdate::jalali_month($year, $month);
				$where = " hours.hour_date >= '$start_date' AND hours.hour_date <= '$end_date' ";
				$group = " GROUP BY day , users.user_displayname";
				$field =
				"
			 		COUNT(hours.hour_date)	AS 'count',
			 		$day_query END) 		AS 'day',
					$sum_fields
				";
			}
			else
			{
				$where = " hours.hour_date LIKE '$year-$month%'	";
				$group = " GROUP BY DAY(hours.hour_date), hours.user_id";
				$field =
				"
			 		COUNT(hours.id)			AS 'count',
					DAY(hours.hour_date)	AS 'day',
					$sum_fields
				";
			}

			if(!$user_id)
			{
				$field =" users.user_displayname	AS 'name', " . $field;
				$group .= ", users.user_displayname ";
			}
		}

		if($year && !$month && !$day)
		{
			if($lang == 'fa')
			{
				$month_query = "(CASE ";
				for ($i=1; $i <= 12 ; $i++) {
					if($i < 10){
						$i = "0". $i;
					}
					$jdate = \lib\utility\jdate::jalali_month($year, $i);
					$month_name = \lib\utility\jdate::date("F", $jdate[0]);
					$month_query .=	" WHEN hours.hour_date >= '{$jdate[0]}' AND hours.hour_date <= '{$jdate[1]}' THEN '$month_name' \n";
				}
				list($start_date, $end_date) = \lib\utility\jdate::jalali_year($year);
				$where = " hours.hour_date >= '$start_date' AND hours.hour_date <= '$end_date' ";
				$group = " GROUP BY month, hours.user_id ";
			}
			else
			{
				$where = " hours.hour_date LIKE '$year%' ";
				$group = " GROUP BY MONTH(hours.hour_date), hours.user_id";
			}

			$field =
			"
				$month_query  END) 					AS 'month',
		 		COUNT(DATE(hours.hour_date)) 		AS 'count',
				SUM(hours.hour_diff)	 			AS 'diff',
				SUM(IFNULL(hours.hour_plus,0))		AS 'plus',
				SUM(IFNULL(hours.hour_minus,0))		AS 'minus',
				SUM(hours.hour_accepted)	 		AS 'accepted'
			";
			if(!$user_id)
			{
				$field =" users.user_displayname	AS 'name', " . $field;
				$group .= ", users.user_displayname ";
			}
		}
		// pagenation
		$count_record = "SELECT COUNT(id) AS 'count' FROM hours WHERE $where ";
		list($limit_start, $length) = \lib\db::pagenation($count_record, 10);
		$limit = " LIMIT $limit_start, $length ";

		$query =
		"	SELECT
				$field
			FROM
				hours
			INNER JOIN users on hours.user_id = users.id
			WHERE
				$where
				$user_id_query
				$group
				$limit
		";
		$result = \lib\db::get($query);
		return $result;
	}


	/**
	 * [summary description]
	 * @return [type] [description]
	 */
	public static function summary()
	{

		$today  = date("Y-m-d");
		$report = [];

		//--------- repeat to every query
		$field = "users.id,users.user_displayname as displayname,
				 SUM(IFNULL(hours.hour_accepted,0))   	as 'accepted',
				 SUM(IFNULL(hours.hour_diff,0)) 		as 'diff',
				 SUM(IFNULL(hours.hour_plus,0)) 	 	as 'plus',
				 SUM(IFNULL(hours.hour_minus,0)) 	 	as 'minus'
				";

		$join =	"FROM hours
				  INNER JOIN users on hours.user_id = users.id
				  WHERE
				  (hours.hour_status = 'filter' OR hours.hour_status = 'active') ";

		$qry = "SELECT $field,
			'daily' as type
			$join
			AND hours.hour_date = '$today'
			GROUP BY
				hours.user_id,
				hours.hour_date
		";

		if(substr(\lib\router::get_storage('language'), 0, 2) === 'fa')
		{
			$jalali_month = \lib\utility\jdate::date("m",false, false);
			$jalali_year  = \lib\utility\jdate::date("Y",false, false);

			list($start_date, $end_date) = \lib\utility\jdate::jalali_month($jalali_year, $jalali_month);

			$start_week = date("Y-m-d", strtotime("last Saturday", time()));
			$end_week   = date("Y-m-d", strtotime("Saturday", time()));

			$qry .= "
			UNION
				SELECT $field,
				'week' as type
				$join
				AND (hours.hour_date >= '$start_week' AND hours.hour_date < '$end_week')
				GROUP BY hours.user_id
			UNION
			SELECT $field,
			'month' as type
			$join
			AND (hours.hour_date >= '$start_date' AND hours.hour_date < '$end_date')
			GROUP BY hours.user_id";

		}
		else
		{
			$qry .= "
			UNION
				SELECT $field,
				'week' as type
				$join
				AND WEEKOFYEAR(hours.hour_date)=WEEKOFYEAR(NOW())
				GROUP BY hours.user_id
			UNION
				SELECT $field,
				'month' as type
				$join
				AND YEAR(hours.hour_date) = YEAR(NOW()) AND MONTH(hours.hour_date)=MONTH(NOW())
				GROUP BY hours.user_id";
		}

		$report = db::get($qry);
		$return = array();
		foreach ($report as $key => $value)
		{
			$id = $value['id'];
			if(!isset($return[$id]))
			{
				$return[$id]         = [];
				$return[$id]['id']   = $id;
				$return[$id]['name'] = $value['displayname'];
			}

			$return[$id][$value['type']]['diff']  = $value['diff'];
			$return[$id][$value['type']]['plus']  = $value['plus'];
			$return[$id][$value['type']]['minus'] = $value['minus'];
			$return[$id][$value['type']]['total'] = $value['accepted'];
		}
		return $return;
	}
}
?>