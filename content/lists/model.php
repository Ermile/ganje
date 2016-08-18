<?php
namespace content\lists;
use \lib\db;
use \lib\utility;
use \lib\debug;

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

		$WHERE = true;

		$q = array();
		$q[] = $user   != null ? "users.user_id = $user" : null;
		$q[] = $day    != null ? "DAY(hours.hour_date) = $day" : null;
		$q[] = $week   != null ? "WEEKOFYEAR(hours.hour_date)=WEEKOFYEAR($week)" : null;
		$q[] = $month  != null ? "YEAR(hours.hour_date) = YEAR($year) AND MONTH(hours.hour_date) = MONTH($month)" : null;
		$q[] = $year   != null ? "YEAR(hours.hour_date) = $year" : null;

		$condition = implode(" AND ", array_filter($q)); 

		if(!$user AND !$day AND !$week AND !$year) $WHERE = false;

		if ($WHERE) {
			//--------- repeat to every query 
			$query = "SELECT 
					  sum(hours.hour_total) as total,
					  sum(hours.hour_diff) as diff,
					  sum(hours.hour_plus) as plus,
					  sum(hours.hour_minus) as minus
					  FROM hours 
					  INNER JOIN users on hours.user_id = users.id
					  WHERE $condition
						GROUP BY 
							hours.user_id,
							hours.hour_date ";

			$report = db::get($query);
		
			return $report;
		}

	}

}
?>