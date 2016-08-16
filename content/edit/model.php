<?php
namespace content\edit;
use \lib\db;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{
	public function post_edit(){

		$id = intval(utility::post("id"));
		$time = utility::post("time");


		$check = db::get("SELECT * FROM hours WHERE id = $id LIMIT 1 ",null, true);

		if($check['hour_end'] == null) {

			$saved_time = $check['hour_start'];

			if($saved_time > $time){
				$temp = $time;
				$time = $saved_time;
				$saved_time = $temp;
			}

			$update = "UPDATE hours			
						SET hour_start = '$saved_time',
							hour_end = '$time',
							hour_diff = TIME_TO_SEC(TIMEDIFF(hour_end,hour_start)) / 60,
							hour_total = (hour_diff + hour_plus - hour_minus)  

						WHERE 
							id = $id ";

					
			db::query($update);

			debug::true("Saved");

		}else{
			debug::error(T_("All Time Saved! Can not edit"));
		}
	}

}
?>