<?php
namespace content_ganje\home;
use \lib\utility;
use \lib\debug;
use \lib\db;
use \lib\telegram\tg as bot;

class model extends \mvc\model
{

	public $user_id;
	public $plus;
	public $minus;
	public $lang = 'fa';

	public function post_hours(){

		$type   = utility::post("type");
		$result = null;

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
			$result = $this->set_time();
		}

		// send class name for absent on present
		debug::property('result', $result);
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

		return 'true';
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


		$displayname = \lib\db\users::get_one($this->user_id);
		$displayname = preg_replace("/\s/", "_", T_($displayname['displayname']));

		$telegram_date = date("Y_m_d");
		if($this->lang == 'fa'){
			$telegram_date =  \lib\utility\jdate::date("Y_m_d");
		}


		if($check_date == null) {

			$enter_msg = "#" . T_("Enter") . "\n";
			$enter_msg .= "#" . $displayname. "\n";
			$enter_msg .= T_("Registered"). "\n";
			$enter_msg .= "#" .  $telegram_date . "\n";


			//----- add firs time in day
			$insert = "INSERT INTO hours
						SET user_id = {$this->user_id},
							hour_date = '$date',
							hour_start = '$time'
							";

			db::query($insert);

			$tg = self::send_telegram($enter_msg);
			debug::true(T_("Enter was registered."). ' '. T_("Have a good time."));
			return 'enter';

		}elseif($check_date['hour_end'] == null){


			$exit_msg = "#". T_("Exit"). "\n";
			$exit_msg .= "#". $displayname. "\n";
			$exit_msg .= T_("in"). ' '. $time. "\n";
			$exit_msg .= "#".  $telegram_date. "\n";
			$exit_msg .= "📊 \n";
			$exit_msg .= T_("from"). " ". $check_date['hour_start']. ' '. T_("to"). ' '. $time. "\n" ;

			if($this->minus > 0){
				$exit_msg .= "😡 ". T_("minus"). ": ". $this->minus. "\n";
			}

			if($this->plus > 0 ){
				$exit_msg .= "😘 ". T_("plus"). ": ". $this->plus. "\n";
			}

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

			$tg = self::send_telegram($exit_msg);
			self::list_online();
			debug::true(T_("Bye Bye;)"));
			return 'exit';

		}

	}


	/*
	* get list of users to show
	*/
	public function post_list(){

		return array('list' => \lib\db\users::get_all(), 'summary' => $this->summary());

	}

	public function summary() {

		$date = date("Y-m-d");

		$report = array();

		//--------- repeat to every query
		$field = "users.id,users.user_displayname as displayname,
				 ROUND(SUM(hours.hour_total) /60)   as 'total',
				 SUM(hours.hour_diff) 	 as 'diff',
				 SUM(hours.hour_plus) 	 as 'plus',
				 SUM(hours.hour_minus) 	 as 'minus'
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
		";

		if($this->lang == 'fa'){

			$jalali_month = \lib\utility\jdate::date("m",false, false);
			$jalali_year = \lib\utility\jdate::date("Y",false, false);

			list($start_date, $end_date) = \lib\db\date::convert_month($jalali_year, $jalali_month);

			$start_week = date("Y-m-d", strtotime("last Saturday", time()));
			$end_week = date("Y-m-d", strtotime("Saturday", time()));

			$qry .= "
			UNION
				SELECT $field,
				'week' as type
				$join
				AND (hours.hour_date >= '$start_week' AND hours.hour_date < '$end_week')
				GROUP BY hours.user_id
			UNION
			SELECT $field,
			'month' as type
			$join
			AND (hours.hour_date >= '$start_date' AND hours.hour_date < '$end_date')
			GROUP BY hours.user_id";

		}else{
			$qry .= "
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
				GROUP BY hours.user_id";
		}

		$report = db::get($qry);

		$return  = array();
		foreach ($report as $key => $value) {
			$id = $value['id'];
			if(!isset($return[$id])) {

				$return[$id] = [];
				$return[$id]['id'] = $id;
				$return[$id]['name'] = $value['displayname'];
			}

			$return[$id][$value['type']]['diff'] = $value['diff'];
			$return[$id][$value['type']]['plus'] = $value['plus'];
			$return[$id][$value['type']]['minus'] = $value['minus'];
			$return[$id][$value['type']]['total'] = $value['total'];

		}
		return $return;
	}


	/**
	 * send message from telegram to admin
	 * @param  [type] $text [description]
	 * @return [type]       [description]
	 */
	public static function send_telegram($_text)
	{
			bot::$api_key   = '164997863:AAFC3nUcujDzpGq-9ZgzAbZKbCJpnd0FWFY';
			bot::$name      = 'saloos_bot';

		$msg =
		[
			'method'       => 'sendMessage',
			'text'         => $_text,
			'chat_id'      => '46898544',

		];
		$result = bot::sendResponse($msg);
		return $result;
	}

	public static function list_online(){
		$list = \lib\db\users::get_all();
		$msg = "🔬 \n";
		foreach ($list as $key => $value) {
		 	if($value['hour_start'] == null) {
		 		$msg .= "😴 " . T_($value['displayname']) . "\n";
		 	}else{
		 		$msg .= "❤ " . T_($value['displayname']) . "\n";
		 	}
		}
		self::send_telegram($msg);
	}
}
?>