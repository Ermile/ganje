<?php
namespace content\lists;
use \lib\db;
use \lib\utility;
use \lib\debug;
use \lib\utility\jdate;

class model extends \mvc\model
{
	public function list(){
		$query = "SELECT

				  hours.*,
				  users.user_displayname

				  FROM hours

				  INNER JOIN users on users.id = hours.user_id

				  order by
				  hours.hour_date  DESC ,
				  hours.hour_end   ASC,
				  hours.hour_start DESC
				  LIMIT 30 ";

		$list = db::get($query);

		return $list;
	}

	public function post_lists() {

		$user   = utility::post("user") ;
		$day    = utility::post("day")  ;
		$week   = utility::post("week") ;
		$month  = utility::post("month");
		$year   = utility::post("year") ;
		$lang   = utility::post("lang")	;

		if($lang == "fa") {
			list($year,$month,$day) = $this->convert_date($year,$month,$day);
		}


		$start  = (utility::post("start") == null) ? 0  : utility::post("start"); // start limit
		$end    = (utility::post("end")   == null) ? 10 : utility::post("end")  ; // end limit


		$q = array();
		$q[] = $user   != null ? "users.user_id = $user" : null;
		$q[] = $day    != null ? "DAY(hours.hour_date) = $day" : null;
		$q[] = $week   != null ? "WEEKOFYEAR(hours.hour_date)=WEEKOFYEAR($week)" : null;
		$q[] = $month  != null ? "YEAR(hours.hour_date) = YEAR($year) AND MONTH(hours.hour_date) = MONTH($month)" : null;
		$q[] = $year   != null ? "YEAR(hours.hour_date) = $year" : null;

		$condition = implode(" AND ", array_filter($q));

		$WHERE = "WHERE";

		if(!$user AND !$day AND !$week AND !$year) $WHERE = null;

		//--------- repeat to every query
		$query = "SELECT
				  users.id,
				  users.user_displayname,
				  sum(hours.hour_total) as total,
				  sum(hours.hour_diff) as diff,
				  sum(hours.hour_plus) as plus,
				  sum(hours.hour_minus) as minus
				  FROM hours
				  INNER JOIN users on hours.user_id = users.id
				  $WHERE $condition
					GROUP BY
						hours.user_id,
						hours.hour_date
				  LIMIT $start,$end
				";

		$report = db::get($query);

		return $report;

	}

	public function convert_date ($year = false, $month = null, $day = null){

		//----- current jalali year
		if (!$year) $year = jdate::date("Y",false,false);

		$time = jdate::mktime(0,0,0,$month,$day,$year,true);

		$day = ($day == null) ? null : date("d", $time);
		$month = ($month == null) ? ($day == null) ? null : $day = null : date("m", $time);
		$year = date("Y", $time);
		return [$year,$month,$day];
	}

}
?>