<?php
namespace content\home;
use \lib\utility;
use \lib\debug;
use \lib\db;

class model extends \mvc\model
{

	public $user_id;
	public $plus;
	public $minus;

	public function post_hours(){

		$type = utility::post("type");

		switch ($type) {
			case 'summary':
				$this->summary(utility::post("id"));
				return ;
				break;
			
			default:
				# code...
				break;
		}
		//----------- get value
		$this->user_id = intval(utility::post('userId'));
		$this->plus = (utility::post('plus') == null) ? 0 : intval(utility::post('plus'));
		$this->minus = (utility::post('minus') == null) ? 0 : intval(utility::post('minus'));

		//----------- check users status
		if($this->check_user()){
			//------- set time 
			$this->set_time();
		}
	}

	/*
	* check users status
	*/
	public function check_user(){

		$check_user = db::get("SELECT * FROM users WHERE id = {$this->user_id} LIMIT 1", "user_status", true);

		if($check_user != "active"){
			debug::error(T_("User not found"));
			return false;
		}

		return true;
	}

	public function set_time(){
		
		$date = date("Y-m-d");
		$time = date("H:i:s");

		$query = "SELECT * FROM hours 
					WHERE 
						user_id   = {$this->user_id} AND
						hour_date = '$date' AND
						hour_end is null
					LIMIT 1";

		$check_date = db::get($query, null, true);
		
		if($check_date == null) {

			//----- add firs time in day
			$insert = "INSERT INTO hours
						SET user_id = {$this->user_id},
							hour_date = '$date',
							hour_start = '$time' 
							";
		
			db::query($insert);

			debug::true(T_("Your are Entered"));

		}elseif($check_date['hour_end'] == null){

			//------- add end time
			$update = "UPDATE hours			
						SET hour_end = '$time',
							hour_diff = TIME_TO_SEC(TIMEDIFF(hour_end,hour_start)) / 60,
							hour_plus = {$this->plus},
							hour_minus = {$this->minus},
							hour_total = (hour_diff + hour_plus - hour_minus)  

						WHERE 
							id = {$check_date['id']} ";
					
			db::query($update);

			debug::true(T_("Your are Exited"));			

		}
		
	}


	/*
	* get list of users to show
	*/
	public function post_list(){
		$date = date("Y-m-d");
		db::query("set sql_mode = '' ", false);

		$query = " 
				SELECT 
					u.id,u.user_displayname
				FROM users u 
				WHERE u.user_status = 'active'  ";

		$users = db::get($query);
		return $users;
	}

	public function summary($user_id = false) {
		$date = date("Y-m-d");

		//-------- sql_mode = "" !!!!!!
		// WEEK(hours.hour_date), MONTH(hours.hour_date)
		
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
					WHERE hours.user_id = $user_id and hours.hour_date = '$date'
					GROUP BY 
						hours.user_id,
						hours.hour_date
					";
		$report[] = db::get($daily);
		
		return $report;
	}

}
?>