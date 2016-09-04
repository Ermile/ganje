<?php
namespace content_ganje\summary;
use \lib\db;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{


	public function get_u(){

		$date = date("Y-m-d");

		$report = array();

		//--------- repeat to every query
		$field = "users.id,users.user_displayname as displayname,
				 SUM(hours.hour_total) as 'total',
				 SUM(hours.hour_diff) as 'diff',
				 SUM(hours.hour_plus) as 'plus',
				 SUM(hours.hour_minus) as 'minus'
				";

		$join =	"FROM hours
				  INNER JOIN users on hours.user_id = users.id
				  WHERE 1 ";

		$qry ="
		SELECT $field,
			'daily' as type
			$join
			AND hours.hour_date = '$date'
			GROUP BY
				hours.user_id,
				hours.hour_date

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
			GROUP BY hours.user_id

		";
		$report = db::get($qry);

		return ['users' => \lib\db\users::get_all(), 'summary' => $report];
	}

	/**
	 * export data to csv
	 */
	public function post_export(){
		$arg = [
				'lang' => 'fa',
				'month' => utility::post('month'),
				'year' => utility::post('year')
				];

		$data = \lib\db\summary::get($arg);
		\lib\db\export::csv(['data' => $data]);
	}
}
?>