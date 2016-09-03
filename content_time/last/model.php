<?php
namespace content_time\last;
use \lib\db;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{


	public function post_last(){

		$id = utility::post("id");

		$type = utility::post("type");

		$time = utility::post("time");

		$edit = false;
		$status = "";

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

			//-------- update time when time posted
			if($edit) {

				$saved_time = $check['hour_start'];

				if($saved_time > $time){
					$temp = $time;
					$time = "'$saved_time'";
					$saved_time = "'$temp'";
				}

			}else{
				$saved_time = "hour_start";
				$time       = "hour_end";
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

			$query = db::query($update);

			if($query){
				debug::true("Saved");
			}else{
				debug::fatal("Can not save change");
			}

		}
	}



	public function lists() {
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
				ORDER BY
					hours.id DESC
				";

		$report = db::get($query);

		return $report;
	}


	public function get_datatable() {
		// creat data for datatable
		$result =  [
			'columns' => [
					'id'    => ['label' => "شماره", 'value' => "id"],
					'name'  => ['label' => "نام", 'value' => "name"],
					'date'  => ['label' => "تاریخ", 'value' => "date"],
					'start' => ['label' => "ساعت ورود", 'value' => "start"],
					'end'   => ['label' => "ساعت خروج", 'value' => "end"],
					'total' => ['label' => "جمع ساعات", 'value' => "total"],
					'plus'  => ['label' => "دور کاری", 'value' => "plus"],
					'minus' => ['label' => "هدر رفته", 'value' => "minus"],
					'diff'  => ['label' => "جمع نهایی", 'value' => "diff"],
					'status'  => ['label' => "وضعیت", 'value' => "status"],
					'accepted'  => ['label' => "ساعات تایید شده", 'value' => "accepted"]
				],
			'data'   => $this->lists(),
			'total'  => 98,
			'filter' => 4
		];
		return $result;
	}

}
?>