<?php
namespace content_time\edit;
use \lib\db;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{


	public function get_edit($o){
		$id = $o->match->url[0][1];
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
				WHERE
					hours.id = $id
				LIMIT 1
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