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
					hours.hour_total 		 					as total,
					hours.hour_diff 	 						as diff,
					hours.hour_plus 	 						as plus,
					hours.hour_minus 	 						as minus,
					hours.hour_status							as 'status',
					hours.hour_accepted 	 					as 'accepted'
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