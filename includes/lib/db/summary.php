<?php
namespace lib\db;
use \lib\db;

class summary {

	public static function get($_args){
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
				list($start_date, $end_date)  = date::convert_month($year, $month);
				$q['month'] = " hours.hour_date > '$start_date' AND hours.hour_date < '$end_date' ";

			}elseif($year && !$month){
				list($start_date, $end_date)  = date::convert_year($year);
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

		$WHERE = "WHERE";

		//--------- repeat to every query
		$no_position = T_("Undefined");

		$query = "
				SELECT
					users.id as id,
					users.user_displayname as name,
					TRIM(BOTH '".'"'."' FROM IFNULL(JSON_EXTRACT(users.user_meta,'$.position'), '$no_position')) as meta,
					SEC_TO_TIME(sum(hours.hour_total) * 60 ) as total,
					SEC_TO_TIME(sum(hours.hour_diff) * 60 ) as diff,
					SEC_TO_TIME(sum(hours.hour_plus) * 60 ) as plus,
					SEC_TO_TIME(sum(hours.hour_minus) * 60 ) as minus
				FROM hours
				INNER JOIN users on hours.user_id = users.id
				$WHERE $condition
				$USER
				LIMIT $start,$end
				";

		$report = db::get($query);
		return $report;
	}
}
?>

