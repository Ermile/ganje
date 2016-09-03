<?php
namespace lib\db;
use \lib\db;

class last {


	public static function get($_args = []) {
		if(isset($_args['user'])){
			$user = " WHERE users.id = '" . $_args['user'] . "' ";
		}else{
			$user = "";
		}

		//--------- repeat to every query
		$query = "
				SELECT
					hours.id 									as id,
					hours.hour_date 							as 'date',
					hours.hour_start 							as start,
					hours.hour_end 								as 'end',
					hours.hour_end 								as 'end',
					users.user_displayname 						as name,
					SEC_TO_TIME(hours.hour_total 	* 60)	 	as total,
					SEC_TO_TIME(hours.hour_diff 	* 60) 		as diff,
					SEC_TO_TIME(hours.hour_plus 	* 60) 		as plus,
					SEC_TO_TIME(hours.hour_minus 	* 60) 		as minus,
					hours.hour_status							as 'status',
					SEC_TO_TIME(hours.hour_accepted * 60)	 	as 'accepted'
				FROM
					hours
				LEFT JOIN users on hours.user_id = users.id
				$user
				ORDER BY
					hours.id DESC
				";

		$report = db::get($query);

		return $report;
	}

}



?>