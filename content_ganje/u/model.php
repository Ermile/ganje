<?php
namespace content_ganje\u;
use \lib\db;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{


	public function get_u($o){
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


	public function post_edit($o){

		$id = $o->match->url[0][1];

		$time = utility::post("time");

		$type = utility::post("type");

		$edit = false;

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
			debug::error(T_("id not found"));
		}else{

			//------- time unchange when updating status
			$saved_time = "hour_start";
			$time       = "hour_end";

			//-------- update time when time posted
			if($check['hour_end'] == null AND $edit) {

				$saved_time = $check['hour_start'];

				if($saved_time > $time){
					$temp = $time;
					$time = "'$saved_time'";
					$saved_time = "'$temp'";
				}

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

			db::query($update);

			debug::true("Saved");

		}
	}

}
?>