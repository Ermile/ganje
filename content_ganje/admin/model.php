<?php
namespace content_ganje\admin;
use \lib\db;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{

	public function add($_args){

		$date    = $_args['date'] ? $_args['date'] : date("Y-m-d") ;
		$start   = $_args['start'] ? $_args['start'] : null;
		$end     = $_args['end'] ? $_args['end'] : null;
		$user_id = $_args['user_id'] ? $_args['user_id'] : 0;
		$minus   = $_args['minus'] ? $_args['minus'] : 0;
		$plus    = $_args['plus'] ? $_args['plus'] : 0;

		$query = "
			INSERT INTO
				hours
			SET
				user_id   	  = $user_id,
				hour_date     = '$date',
				hour_start    = '$start',
				hour_end      = '$end',
				hour_diff     = TIME_TO_SEC(TIMEDIFF(hour_end,hour_start)) / 60,
				hour_plus     = '$plus',
				hour_minus    = '$minus',
				hour_total    = (hour_diff + hour_plus - hour_minus),
				hour_status   = 'raw',
				hour_accepted = hour_total";

		$result =  \lib\db::query($query);

		if($result){
			debug::true(T_("Added"));
		}else{
			debug::error(T_("Error in insert"));
		}
	}

	public function post_last(){

		if(utility::post('add')){
			$args = [
						'date'    => utility::post("date"),
						'start'   => utility::post("start"),
						'end'     => utility::post("end"),
						'user_id' => utility::post("user_id"),
						'minus'   => utility::post("minus"),
						'plus'    => utility::post("plus")
					];
			$this->add($args);
		}

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
			'data'   => \lib\db\last::get(),
			'total'  => 98,
			'filter' => 4
		];
		return $result;
	}

}
?>