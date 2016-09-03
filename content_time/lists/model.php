<?php
namespace content_time\lists;
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
			list($year,$month,$day) = \lib\db\date::convert($year,$month,$day);
		}


		$start  = (utility::post("start") == null) ? 0  : utility::post("start"); // start limit
		$end    = (utility::post("end")   == null) ? 10 : utility::post("end")  ; // end limit


		$q = array();
		$q[] = $user   != null ? "users.user_id = $user" : null;
		$q[] = $day    != null ? "DAY(hours.hour_date) = $day" : null;
		$q[] = $week   != null ? "WEEKOFYEAR(hours.hour_date)=WEEKOFYEAR($week)" : null;
		$q[] = $month  != null ? "YEAR(hours.hour_date) = '$year' AND MONTH(hours.hour_date) = MONTH($month)" : null;
		$q[] = $year   != null ? "YEAR(hours.hour_date) = '$year'" : null;

		$condition = implode(" AND ", array_filter($q));

		$WHERE = "WHERE";

		if(!$user AND !$day AND !$week AND !$year) $WHERE = null;

		//--------- repeat to every query
		$query = "
				SELECT
					users.id as id,
					users.user_displayname as name,
					sum(hours.hour_total) as total,
					sum(hours.hour_diff) as diff,
					sum(hours.hour_plus) as plus,
					sum(hours.hour_minus) as minus
				FROM hours
				INNER JOIN users on hours.user_id = users.id
				$WHERE $condition
				GROUP BY
					hours.user_id
				";
				// LIMIT $start,$end
				// var_dump($query);exit();

		$report = db::get($query);
		return $report;
		debug::msg("report", $report);

		$this->_processor(['force_json'=>true, 'not_redirect'=>true]);
		return $report;

	}


	public function get_datatable(){

		$datatable = $this->get_data();
		return $datatable;
		// $datatable = $this->model()->datatable();

		// echo(json_encode($datatable, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE));

		debug::property('draw'           , $datatable['draw']);
		debug::property('data'           , $datatable['data']);
		// debug::property('columns'        , $datatable['columns']);
		debug::property('recordsTotal'   , $datatable['total']);
		debug::property('recordsFiltered', $datatable['filter']);
		// $this->model()->_processor(object(array("force_json" => true, "force_stop" => true)));
		// $this->_processor(array("force_json" => true, "force_stop" => true));


	}


	public function get_data() {
		return [
			'columns' => [
					'id'    => ['label' => "id", 'value' => "id"],
					'name'  => ['label' => "name", 'value' => "name"],
					'total' => ['label' => "total", 'value' => "total"],
					'diff'  => ['label' => "diff", 'value' => "diff"],
					'plus'  => ['label' => "plus", 'value' => "plus"],
					'minus' => ['label' => "minus", 'value' => "minus"]
				],
			'data' => $this->post_lists(),
			'total' => 98,
			'filter' => 4
		];
	}

}
?>