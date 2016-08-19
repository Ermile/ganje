<?php
namespace content\edit;
use \lib\db;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{
	public function post_edit(){

		$id = utility::post("id");

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