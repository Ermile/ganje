<?php
namespace content_ganje\status;
use \lib\db;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{


	public function post_u($o){

		if(empty(utility::post())){
			return $this->get_u();
		}

			$args = [
					'user'   => $this->login("id") ,
					'day'    => utility::post("day")  ,
					'week'   => utility::post("week") ,
					'month'  => utility::post("month"),
					'year'   => utility::post("year") ,
					'lang'   => 'fa',
					'start'  => utility::post("start"),
					'end'    => utility::post("end")
					];

		$result =  \lib\db\summary::get($args);
		return $result;

	}

	public function get_u(){

		$id = $this->login("id");

		$date = date("Y-m-d");

		$report = array();

		//--------- repeat to every query
		$field = "
				 users.id,
				 users.user_displayname as displayname,
				 SEC_TO_TIME(SUM(hours.hour_total) * 60 ) as 'total',
				 SEC_TO_TIME(SUM(hours.hour_diff)  * 60 ) as 'diff',
				 SEC_TO_TIME(SUM(hours.hour_plus)  * 60 ) as 'plus',
				 SEC_TO_TIME(SUM(hours.hour_minus) * 60 ) as 'minus'
				";

		$join =	"FROM hours
				  INNER JOIN users on hours.user_id = users.id
				  WHERE hours.user_id = $id ";


		$query =
		"SELECT $field,
			'daily' as type
			$join
			AND hours.hour_date = '$date'
			GROUP BY
				hours.hour_date

		UNION
			SELECT $field,
			'week' as type
			$join
			AND WEEKOFYEAR(hours.hour_date)=WEEKOFYEAR(NOW())

		UNION
		SELECT $field,
			'month' as type
			$join
			AND YEAR(hours.hour_date) = YEAR(NOW()) AND MONTH(hours.hour_date)=MONTH(NOW())

		";


		$check = db::get($query,null, true);

		return $check;

	}
}
?>