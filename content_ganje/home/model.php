<?php
namespace content_ganje\home;
use \lib\utility;
use \lib\debug;
use \lib\db;
use \lib\utility\jdate;
use \lib\telegram\tg as bot;

class model extends \mvc\model
{

	public $user_id;
	public $start;
	public $plus;
	public $minus;


	public function post_hours()
	{
		$type   = utility::post("type");
		$result = null;

		switch ($type)
		{
			case 'summary':
				$this->summary();
				return ;
				break;

			default:
				break;
		}
		//----------- get value
		$this->user_id = utility::post('userId');
		$this->plus    = (utility::post('plus') == null) ? 0 : utility::post('plus');
		$this->minus   = (utility::post('minus') == null) ? 0 : utility::post('minus');

		//----------- check users status
		if($this->check_user())
		{
			//------- set time
			$result = $this->set_time();
		}

		// send class name for absent on present
		debug::property('result', $result);
	}


	/*
	* check users status
	*/
	public function check_user()
	{
		$query = "SELECT
					user_status
				FROM users
				WHERE id = {$this->user_id}
				LIMIT 1";

		$check_user = db::get($query, "user_status", true);

		if($check_user != "active")
		{
			debug::error(T_("User not found"));
			return false;
		}

		return 'true';
	}


	/**
	 * set time of enter or exit
	 */
	public function set_time()
	{
		$today = date("Y-m-d");
		$time  = date("H:i");
		$query = "SELECT * FROM hours
					WHERE
						user_id   = {$this->user_id} AND
						hour_date = '$today' AND
						hour_end is null
					LIMIT 1";

		$check_date = db::get($query, null, true);

		if($check_date == null)
		{
			//----- add firs time in day
			$insert = "INSERT INTO hours
						SET user_id = {$this->user_id},
							hour_date = '$today',
							hour_start = '$time'
							";

			db::query($insert);

			// send message from telegram
			self::generate_telegram_text('enter');
			debug::true(T_("Enter was registered."). ' '. T_("Have a good time."));
			return 'enter';

		}
		elseif($check_date['hour_end'] == null)
		{
			// set start time
			$this->start = strtotime("$today ". $check_date['hour_start']);
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
			self::generate_telegram_text('exit');
			debug::warn(T_("Bye Bye;)"));
			return 'exit';
		}
	}


	/*
	* get list of users to show
	*/
	public function post_list()
	{
		return array('list' => \lib\db\users::get_all(), 'summary' => $this->summary());
	}


	/**
	 * [summary description]
	 * @return [type] [description]
	 */
	public function summary()
	{

		$today  = date("Y-m-d");
		$report = [];

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

		$qry = "SELECT $field,
			'daily' as type
			$join
			AND hours.hour_date = '$today'
			GROUP BY
				hours.user_id,
				hours.hour_date
		";

		if(substr(\lib\router::get_storage('language'), 0, 2) === 'fa')
		{
			$jalali_month = jdate::date("m",false, false);
			$jalali_year  = jdate::date("Y",false, false);

			list($start_date, $end_date) = \lib\db\date::convert_month($jalali_year, $jalali_month);

			$start_week = date("Y-m-d", strtotime("last Saturday", time()));
			$end_week   = date("Y-m-d", strtotime("Saturday", time()));

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

		}
		else
		{
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
		$return = array();
		foreach ($report as $key => $value)
		{
			$id = $value['id'];
			if(!isset($return[$id]))
			{
				$return[$id]         = [];
				$return[$id]['id']   = $id;
				$return[$id]['name'] = $value['displayname'];
			}

			$return[$id][$value['type']]['diff']  = $value['diff'];
			$return[$id][$value['type']]['plus']  = $value['plus'];
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


	/**
	 * generate telegram messages and call send function to send messages directly
	 * @param  [type] $_type [description]
	 * @return [type]        [description]
	 */
	public function generate_telegram_text($_type)
	{
		$msg      = '';
		$date_now = \lib\utility::date("l j F Y", false, 'default');
		$time_now = \lib\utility::date("H:i", false, 'default');
		$name     = \lib\db\users::get_one($this->user_id);
		$name     = "*". T_($name['displayname']). "*";

		switch ($_type)
		{
			case 'enter':
				// if this person is first one in this day send current date
				if(\lib\db\users::check_first())
				{
					$tg = self::send_telegram($date_now);
				}
				$msg = "✅ $name $time_now";
				break;


			case 'exit':
				$msg        = "💤 $name\n";
				$total      = floor(abs(strtotime('now') - $this->start) / 60);
				var_dump($total);
				if($total < 1)
				{
					// exit from switch and show message
					$msg .= "🚷 /:";
					break;
				}
				$time_start = \lib\utility::date('H:i', $this->start, 'default');

				$msg        .= $time_start. ' '. T_('to'). ' '. $time_now;

				// add minus and plus if exist
				if($this->plus > 0 )
				{
					$msg .= "\n➕ ". $this->plus;
				}
				if($this->minus > 0)
				{
					$msg .= "\n➖ ". $this->minus;
				}
				$total = floor($total / 60). ':'. floor($total % 60);
				$msg .= "\n🕗 ". $total;
				break;


			default:
				break;
		}

		// send telegram message
		$tg = self::send_telegram($msg);
	}


	private static function list_online()
	{
		$list = \lib\db\users::get_all();
		$msg  = "";
		foreach ($list as $key => $value) {
		 	if($value['hour_start'] == null) {
		 		$msg .= "💤 " . T_($value['displayname']) . "\n";
		 	}else{
		 		$msg .= "❤ " . T_($value['displayname']) . "\n";
		 	}
		}
		$msg .= "‌";
		self::send_telegram($msg);
	}
}
?>