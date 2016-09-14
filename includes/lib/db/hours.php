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
				hour_minus     = IF('$minus' = 0, NULL, '$minus')
				";
		return \lib\db::query($query);
	}


	/**
	 * return last record of hours table
	 * if none param send to this function get last record of all users
	 * you can send ['user' => \d+] to get last record of this users
	 * @param      array   $_args  The arguments
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function last($_args = [])
	{
		if(isset($_args['user']))
		{
			$user = " AND users.id = '" . $_args['user'] . "' ";
		}
		else
		{
			$user = "";
		}

		//--------- repeat to every query
		$query = "
				SELECT
					hours.id 				as 'id',
					hours.hour_date 		as 'date',
					hours.hour_start 		as 'start',
					hours.hour_end 			as 'end',
					hours.hour_end 			as 'end',
					users.user_displayname 	as 'name',
					hours.hour_diff 	 	as 'diff',
					hours.hour_plus 	 	as 'plus',
					hours.hour_minus 	 	as 'minus',
					hours.hour_status		as 'status',
					hours.hour_accepted 	as 'accepted'
				FROM
					hours
				LEFT JOIN users on hours.user_id = users.id
				WHERE
					  (hours.hour_status = 'filter' OR hours.hour_status = 'active')
				$user
				ORDER BY
					hours.id DESC
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
	public static function status($_args)
	{
		$year    = $_args['year'];
		$month   = $_args['month'];
		$day     = $_args['day'];
		$user_id = $_args['user_id'];
		$lang    = $_args['lang'];

		// limit and page
		$page        = isset($_args['page']) ? $_args['page'] : 1;
		$lenght      = isset($_args['lenght']) ? $_args['lenght'] : 10;

		$limit_start = (($page -1) * $lenght) +1;
		$limit_end   = $limit_start + $lenght + 1;

		$limit       = "LIMIT $limit_start , $limit_end ";

		// check user id . if users id is set get add data by this users id and if users id is not set get all users
		if($user_id == null)
		{
			$user_id = "";
		}
		else
		{
			$user_id = " AND user_id = $user_id ";
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

		if(!$year && !$month && !$day)
		{
			if($lang == 'fa')
			{
				$year  = \lib\utility\jdate::date("Y", time(),false);
				$month = \lib\utility\jdate::date("m", time(),false);
				$day   = \lib\utility\jdate::date("d", time(),false);
			}
			else
			{
				$year  = date("Y");
				$month = date("m");
				$day   = date("d");
			}
		}

		// fields of table whit sum function
		$field =
		"
			SUM(hours.hour_diff) 	 	as 'diff',
			SUM(hours.hour_plus)	 	as 'plus',
			SUM(hours.hour_minus)	 	as 'minus',
			SUM(hours.hour_accepted) 	as 'accepted'
		";

		// check year month and day
		if($year && $month && $day)
		{
			// in one day we not use sum function in mysql to show all record of this day


			// get enter and exit on one day
			if($lang == 'fa')
			{
				$date = self::convert_date($year, $month, $day);
			}
			else
			{
				$date = "$year-$month-$day";
			}

			$where = " hours.hour_date = '$date' ";
			$group = "";
		}

		if($year && $month && !$day)
		{
			// get daily count of hours
			if($lang == 'fa')
			{

				list($start_date, $end_date) = \lib\utility\jdate::jalali_month($year, $month);
				$where = " hours.hour_date >= '$start_date' AND hours.hour_date < '$end_date' ";
				$group = " GROUP BY DAY(hours.hour_date), hours.user_id";
			}
			else
			{
				$where = " hours.hour_date LIKE '$year-$month%'	";
				$group = " GROUP BY DAY(hours.hour_date), hours.user_id";
			}
		}

		if($year && !$month && !$day)
		{
			if($lang == 'fa')
			{

				//SELECT
				// 	users.id,
				// 	count(hours.id) 		as 'count',
				// 	users.user_displayname  as 'name',
				// 	hours.hour_start		as 'start',
				// 	hours.hour_end			as 'end',
				// 	hours.hour_date			as 'date',
				// 	SUM(hours.hour_diff)	 	 	as 'diff',
				// 	SUM(hours.hour_plus)		 	as 'plus',
				// 	SUM(hours.hour_minus)		 	as 'minus',
				// 	SUM(hours.hour_accepted)	 	as 'accepted',
				// CASE hours.hour_date
				// 	WHEN hours.hour_date < '2016-03-20' AND hours.hour_date > '2016-04-19' THEN 'فروردین'
				// 	WHEN hours.hour_date < '2016-04-20' AND hours.hour_date > '2016-05-20' THEN 'اردیبهشت'
				// 	WHEN hours.hour_date < '2016-05-21' AND hours.hour_date > '2016-06-20' THEN 'خرداد'
				// 	WHEN hours.hour_date < '2016-06-21' AND hours.hour_date > '2016-07-21' THEN 'تیر'
				// 	WHEN hours.hour_date < '2016-07-22' AND hours.hour_date > '2016-08-21' THEN 'مرداد'
				// 	WHEN hours.hour_date < '2016-08-22' AND hours.hour_date > '2016-09-21' THEN 'شهریور'
				// 	WHEN hours.hour_date < '2016-09-22' AND hours.hour_date > '2016-10-21' THEN 'مهر'
				// 	WHEN hours.hour_date < '2016-10-22' AND hours.hour_date > '2016-11-20' THEN 'آبان'
				// 	WHEN hours.hour_date < '2016-11-21' AND hours.hour_date > '2016-12-20' THEN 'آذر'
				// 	WHEN hours.hour_date < '2016-12-21' AND hours.hour_date > '2017-01-19' THEN 'دی'
				// 	WHEN hours.hour_date < '2017-01-20' AND hours.hour_date > '2017-02-18' THEN 'بهمن'
				// 	WHEN hours.hour_date < '2017-02-19' AND hours.hour_date > '2017-03-19' THEN 'اسفند'
				// END AS 'month'

				// FROM
				// hours
				// INNER JOIN users on hours.user_id = users.id
				// WHERE
				// ...
				$month_query = "CASE hours.hour_date ";
				for ($i=1; $i <= 12 ; $i++) {
					if($i < 10){
						$i = "0". $i;
					}
					$jdate = \lib\utility\jdate::jalali_month($year, $i);
					$month_name = \lib\utility\jdate::date("F", $jdate[0]);
					$month_query .=	"WHEN hours.hour_date < '{$jdate[0]}' AND hours.hour_date > '{$jdate[1]}' THEN '$month_name' \n";
				}
				$field =
				"
					SUM(hours.hour_diff)	 	 	as 'diff',
					SUM(hours.hour_plus)		 	as 'plus',
					SUM(hours.hour_minus)		 	as 'minus',
					SUM(hours.hour_accepted)	 	as 'accepted',
					$month_query  END AS 'jalalimonth'

				";
				list($start_date, $end_date) = \lib\utility\jdate::jalali_year($year);
				$where = " hours.hour_date >= '$start_date' AND hours.hour_date < '$end_date' ";
				$group = " GROUP BY jalalimonth, hours.user_id";
			}
			else
			{
				$where = " hours.hour_date LIKE '$year%'	";
				$group = " GROUP BY MONTH(hours.hour_date), hours.user_id";
			}
		}

		$query =
		"
			SELECT
			 	users.id,
			 	count(hours.id) 		as 'count',
			 	users.user_displayname  as 'name',
			 	hours.hour_start		as 'start',
			 	hours.hour_end			as 'end',
			 	hours.hour_date			as 'date',
				$field
			FROM
				hours
			INNER JOIN users on hours.user_id = users.id
			WHERE
				$where
				$user_id
				$group
				$limit

		";
		$result = \lib\db::get($query);
		return $result;
	}


	/**
	 * get jalali date and return milady date
	 *
	 * @param      boolean  $year   The year
	 * @param      <type>   $month  The month
	 * @param      <type>   $day    The day
	 *
	 * @return     <type>   ( description_of_the_return_value )
	 */
	public static function convert_date($_year = false, $_month = null, $_day = null, $_format = "Y-m-d")
	{
		$time  = \lib\utility\jdate::mktime(0,0,0,$_month,$_day,$_year,true);
		return date($_format, $time);
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
				 SUM(hours.hour_accepted)   as 'accepted',
				 SUM(hours.hour_diff) 	 as 'diff',
				 SUM(hours.hour_plus) 	 as 'plus',
				 SUM(hours.hour_minus) 	 as 'minus'
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


	public static function update($_args)
	{

			$id   = $_args['id'];
			$type = $_args['type'];
			$time = $_args['time'];

			$edit = false;
			$status = "";

			switch ($type)
			{
				case 'edit':
					$edit = true;
					break;

				case 'minus':
					$status = ", hour_status = 'plus', hour_accepted = hour_diff + hour_plus";
					break;

				case 'plus':
					$status = ", hour_status = 'minus', hour_accepted = hour_diff - hour_minus";
					break;

				case 'all':
				case 'disable':
				case 'enable':
					$status = ", hour_status = '$type', hour_accepted = hour_diff + hour_plus - hour_minus";
					break;

				default:
					$status = "";
					break;
			}


			$check = db::get("SELECT * FROM hours WHERE id = $id LIMIT 1 ",null, true);

			if(!$check)
			{
				return false;
			}
			else
			{

				//------- time unchange when updating status
				//-------- update time when time posted
				if($edit)
				{
					$saved_time = $check['hour_start'];
					if($saved_time > $time)
					{
						$temp = $time;
						$time = "'$saved_time'";
						$saved_time = "'$temp'";
					}
				}
				else
				{
					$saved_time = "hour_start";
					$time       = "hour_end";
				}
				$update = "UPDATE hours
							SET
								hour_start = $saved_time,
								hour_end = $time,
								hour_diff = TIME_TO_SEC(TIMEDIFF(hour_end,hour_start)) / 60,
								$status
							WHERE
								id = $id ";
			return db::query($update);
		}
	}
}
?>