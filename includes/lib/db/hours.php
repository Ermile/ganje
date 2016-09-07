<?php
namespace lib\db;
use \lib\db;

class hours {

	public static function insert($_args){

		$date    = $_args['date'] ? $_args['date'] : date("Y-m-d") ;
		$start   = $_args['start'] ? $_args['start'] : null;
		$end     = $_args['end'] ? $_args['end'] : null;
		$user_id = $_args['user_id'] ? $_args['user_id'] : 0;
		$minus   = $_args['minus'] ? $_args['minus'] : 0;
		$plus    = $_args['plus'] ? $_args['plus'] : 0;

		$query = "
			INSERT INTO
				hours
			SET
				user_id   	  = $user_id,
				hour_date     = '$date',
				hour_start    = '$start',
				hour_end      = '$end',
				hour_diff     = TIME_TO_SEC(TIMEDIFF(hour_end,hour_start)) / 60,
				hour_plus     = '$plus',
				hour_minus    = '$minus',
				hour_total    = (hour_diff + hour_plus - hour_minus),
				hour_accepted = hour_total,
				hour_status = IF (hour_total < 5, 'disable', 'raw')
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
	public static function last($_args = []) {
		if(isset($_args['user'])){
			$user = " users.id = '" . $_args['user'] . "' ";
		}else{
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
					hours.hour_total 		as 'total',
					hours.hour_diff 	 	as 'diff',
					hours.hour_plus 	 	as 'plus',
					hours.hour_minus 	 	as 'minus',
					hours.hour_status		as 'status',
					hours.hour_accepted 	as 'accepted'
				FROM
					hours
				LEFT JOIN users on hours.user_id = users.id
				WHERE
					hour_status != 'disable'
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
	public static function sum($_args){
		$user  = isset($_args['user'])  ? $_args['user']  : null;
		$day   = isset($_args['day'])   ? $_args['day']   : null;
		$week  = isset($_args['week'])  ? $_args['week']  : null;
		$month = isset($_args['month']) ? $_args['month'] : null;
		$year  = isset($_args['year'])  ? $_args['year']  : null;
		$lang  = isset($_args['lang'])  ? $_args['lang']  : null;

		if($month == '0') {
			$month = null;
		}

		$q = array();

		$q['user']  = $user   != null ? "users.id = $user" : null;

		if($user){
			$USER = "";
		}else{
			$USER = " GROUP BY	hours.user_id ";
		}

		if($lang == "fa") {
			if($year && $month){
				list($start_date, $end_date)  = \lib\utility\jdate::jalali_month($year, $month);
				$q['month'] = " hours.hour_date > '$start_date' AND hours.hour_date < '$end_date' ";

			}elseif($year && !$month){
				list($start_date, $end_date)  = \lib\utility\jdate::jalali_year($year);
				$q['month'] = " hours.hour_date > '$start_date' AND hours.hour_date < '$end_date' ";
			}
		}else{
			$q['month'] = $month  != null ? "YEAR(hours.hour_date) = '$year' AND MONTH(hours.hour_date) = '$month' " : null;
			$q['year']  = $year   != null ? "YEAR(hours.hour_date) = '$year' " : null;
			$q['week']  = $week   != null ? "WEEKOFYEAR(hours.hour_date)=WEEKOFYEAR('$week')" : null;
		}

		$condition = implode(" AND ", array_filter($q));

		$start  = (isset($_args["start"])) ? $_args["start"] : 0; // start limit
		$end    = (isset($_args["end"]))   ? $_args["end"]   : 10; // end limit

		//--------- repeat to every query
		$no_position = T_("Undefined");

		$query = "
				SELECT
					users.id as id,
					users.user_displayname as name,
					TRIM(BOTH '".'"'."' FROM IFNULL(JSON_EXTRACT(users.user_meta,'$.position'), '$no_position')) as meta,
					sum(hours.hour_total) as total,
					sum(hours.hour_diff) as diff,
					sum(hours.hour_plus) as plus,
					sum(hours.hour_minus) as minus,
					sum(hours.hour_accepted) as accepted
				FROM
					hours
				INNER JOIN users on hours.user_id = users.id
				WHERE
					hours.hour_status != 'disable'
				$condition
				$USER
				LIMIT $start,$end
				";

		$report = db::get($query);
		return $report;
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
				 SUM(hours.hour_total)   as 'total',
				 SUM(hours.hour_diff) 	 as 'diff',
				 SUM(hours.hour_plus) 	 as 'plus',
				 SUM(hours.hour_minus) 	 as 'minus'
				";

		$join =	"FROM hours
				  INNER JOIN users on hours.user_id = users.id
				  WHERE
				  hours.hour_status != 'disable' ";

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
			$return[$id][$value['type']]['total'] = $value['total'];
		}
		return $return;
	}


	public static function update($_args){

			$id   = $_args['id'];
			$type = $_args['type'];
			$time = $_args['time'];

			$edit = false;
			$status = "";

			switch ($type) {
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

			if(!$check) {
				return false;
			}else{

				//------- time unchange when updating status

				//-------- update time when time posted
				if($edit) {

					$saved_time = $check['hour_start'];

					if($saved_time > $time){
						$temp = $time;
						$time = "'$saved_time'";
						$saved_time = "'$temp'";
					}

				}else{

					$saved_time = "hour_start";
					$time       = "hour_end";
				}

				$update = "UPDATE hours
							SET
								hour_start = $saved_time,
								hour_end = $time,
								hour_diff = TIME_TO_SEC(TIMEDIFF(hour_end,hour_start)) / 60,
								hour_total = (hour_diff + hour_plus - hour_minus)
								$status
							WHERE
								id = $id ";

				return db::query($update);

		}
	}
}
?>