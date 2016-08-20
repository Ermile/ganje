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
				$this->summary();
				return ;
				break;

			default:
				# code...
				break;
		}
		//----------- get value
		$this->user_id = utility::post('userId');
		$this->plus    = (utility::post('plus') == null) ? 0 : utility::post('plus');
		$this->minus   = (utility::post('minus') == null) ? 0 : utility::post('minus');

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
		$query = "
				SELECT
					user_status
				FROM users
				WHERE id = {$this->user_id}
				LIMIT 1";

		$check_user = db::get($query, "user_status", true);

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
							hour_total = (hour_diff + hour_plus - hour_minus),
							hour_status = 'raw',
							hour_accepted = hour_total

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
		$no_position = T_("Undefined");
		$query =
				"SELECT
					u.id,
					u.user_displayname,
					IFNULL(options.option_value,'$no_position') as position,
					hours.hour_start
				FROM users u
				LEFT JOIN hours
					ON hours.user_id = u.id
					AND hours.hour_date = DATE(NOW())
					AND hours.hour_end is null
				LEFT JOIN options ON
					options.user_id = u.id
					AND options.option_cat = 'user_meta'
					AND options.option_key = 'position'
				WHERE u.user_status = 'active'
				ORDER BY u.id
				";

		$users = db::get($query);
		$new   = array_column($users, "id");
		$users = array_combine($new, $users);

		return array('list' => $users, 'summary' => $this->summary());
	}

	public function summary() {
		$date = date("Y-m-d");

		//-------- sql_mode = "" => SET GLOBAL sql_mode = '';

		$report = array();

		//--------- repeat to every query
		$field = "users.id,users.user_displayname,
				  sum(hours.hour_total) as total,
				  sum(hours.hour_diff) as diff,
				  sum(hours.hour_plus) as plus,
				  sum(hours.hour_minus) as minus
				";
		$join =	"FROM hours
				  INNER JOIN users on hours.user_id = users.id
				  WHERE 1 ";


		$qry =
		"SELECT $field,
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


		// var_dump($report);exit();
		$return  = array();
		foreach ($report as $key => $value) {
			$id = $value['id'];
			if(!isset($return[$id])) {

				$return[$id] = [];
				$return[$id]['id'] = $id;
				$return[$id]['name'] = $value['user_displayname'];
			}

			$return[$id][$value['type']]['diff'] = $value['diff'];
			$return[$id][$value['type']]['plus'] = $value['plus'];
			$return[$id][$value['type']]['minus'] = $value['minus'];
			$return[$id][$value['type']]['total'] = $value['total'];

		}
		return $return;
		//-------- return json
		// debug::msg("data", $report);
		// $this->_processor(['force_json'=>true, 'not_redirect'=>true]);
	}

}
?>