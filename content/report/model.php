<?php
namespace content\report;
use \lib\db;
use \lib\debug;

class model extends \mvc\model
{
	public function report(){
		$daily = "SELECT 
					hours.user_id,
					users.user_displayname,
					hours.hour_date, 
					sum(hours.hour_total) as total ,
					sum(hours.hour_diff) as diff,
					sum(hours.hour_plus) as plus,
					sum(hours.hour_minus) as minus
					FROM hours 
					INNER JOIN users on hours.user_id = users.id
					GROUP BY 
						hours.user_id,
						hours.hour_date
					ORDER BY hours.hour_date DESC
					";

		$month = "SELECT 
					hours.user_id,
					users.user_displayname,
					hours.hour_date, 
					sum(hours.hour_total) as total ,
					sum(hours.hour_diff) as diff,
					sum(hours.hour_plus) as plus,
					sum(hours.hour_minus) as minus
					FROM hours 
					INNER JOIN users on hours.user_id = users.id
					GROUP BY 
						hours.user_id,
						MONTH(hours.hour_date)
					ORDER BY hours.hour_date DESC
					";
		$report[] = db::get($daily);
		$report[] = db::get($month);


		return $report;
	}

}
?>