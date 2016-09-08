<?php
namespace content_ganje\home;
use \lib\utility;
use \lib\debug;
use \lib\db;
use \lib\telegram\tg as bot;

class model extends \mvc\model
{
	public $user_id;
	public $user_name;
	public $start;
	public $plus;
	public $minus;


	public function post_hours()
	{
		$result        = null;
		$this->user_id = utility::post('userId');
		//----------- get value
		$arg =
		[
			'user_id' => utility::post('userId'),
			'plus'    => intval(utility::post('plus')),
			'minus'   => intval(utility::post('minus'))
		];

		$result = \lib\db\users::set_time($arg);

		switch ($result)
		{
			case false:
				debug::error(T_("User not found"));
				break;

			case 'enter':
				$msg_notify = T_("Dear :name;", ['name'=> $this->user_name])."<br />". T_('Your enter was registered.').' '. T_("Have a good time.");
				debug::true($msg_notify);
				// send message from telegram
				self::generate_telegram_text('enter');
				break;

			case 'exit':
				$msg_notify = T_("Bye Bye :name ;)", ['name'=> $this->user_name]);
				debug::warn($msg_notify);
				self::generate_telegram_text('exit');
				break;

			default:
				debug::warn(':|');
				break;
		}
		// send class name for absent on present
		debug::property('result', $result);
	}


	/*
	* get list of users to show
	*/
	public function post_list()
	{
		return
		[
			'list' => \lib\db\users::get_all(),
			'summary' => \lib\db\hours::summary()
		];
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
		$msg             = '';
		$date_now        = \lib\utility::date("l j F Y", false, 'default');
		$time_now        = \lib\utility::date("H:i", false, 'default');
		$name            = \lib\db\users::get_one($this->user_id);
		$name            = T_($name['displayname']);
		$this->user_name = $name;
		$name            = "*$name*";

		switch ($_type)
		{
			case 'enter':
				// if this person is first one in this day send current date
				if(\lib\db\users::live() <= 1)
				{
					$tg = self::send_telegram($date_now);
				}
				$msg = "✅ $name $time_now";
				break;


			case 'exit':
				$msg        = "💤 $name\n";
				$total      = floor(abs(strtotime('now') - \lib\db\users::get_start($this->user_id)) / 60);
				if($total < 1)
				{
					// exit from switch and show message
					$msg .= "🚷 /:";
					break;
				}
				$time_start = \lib\utility::date('H:i', \lib\db\users::get_start($this->user_id) , 'default');
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
				$total = utility\human::time($total, 'persian');
				$msg .= " 🕗 ". $total."\n‌";
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
		foreach ($list as $key => $value)
		{
		 	if($value['hour_start'] == null)
		 	{
		 		$msg .= "💤 " . T_($value['displayname']) . "\n";
		 	}
		 	else
		 	{
		 		$msg .= "❤ " . T_($value['displayname']) . "\n";
		 	}
		}
		$msg .= "‌";
		self::send_telegram($msg);
	}
}
?>