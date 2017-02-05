<?php
namespace content_ganje\home;
use \lib\utility;
use \lib\debug;
use \lib\db;
use \lib\telegram\tg as bot;

class model extends \mvc\model
{
	/**
	 * the user name
	 */
	public static $user_name;


	/**
	 * [post_hours description]
	 * @return [type] [description]
	 */
	public function post_save()
	{
		if(!$this->login())
		{
			return false;
		}

		$user_id = false;

		if($this->access('ganje', 'home', 'admin'))
		{
			$user_id = intval(utility::post('userId'));
		}
		elseif(!$this->access('ganje', 'admin', 'admin') && $this->access('ganje', 'home', 'add'))
		{
			if((int) $this->login("id") !=  intval(utility::post('userId')))
			{
				return debug::warn(T_("Hi Dear :/"), false , 'permission');
			}
			else
			{
				$user_id = (int) $this->login('id');
			}
		}
		else
		{
			return debug::warn(T_("Can not access to set time"), false, 'permission');
		}

		$result        = null;
		//----------- get values from post
		$arg =
		[
			'user_id' => $user_id,
			'plus'    => intval(utility::post('plus')),
			'minus'   => intval(utility::post('minus'))
		];
		// set name of user
		$this->setName($arg['user_id']);

		$result = \lib\db\staff::set_time($arg);

		switch ($result)
		{
			case false:
				debug::error(T_("User not found"));
				break;

			case 'enter':
				$msg_notify = T_("Dear :name;", ['name'=> self::$user_name])."<br />". T_('Your enter was registered.').' '. T_("Have a good time.");
				debug::true($msg_notify);
				// send message from telegram
				self::generate_telegram_text('enter', $arg);
				break;

			case 'exit':
				$msg_notify = T_("Bye Bye :name ;)", ['name'=> self::$user_name]);
				debug::warn($msg_notify);
				self::generate_telegram_text('exit', $arg);
				break;

			default:
				debug::warn(':|');
				break;
		}
		// send class name for absent on present
		debug::msg('result', $result);
	}


	/*
	* get list of users to show
	*/
	public function get_list_of_users()
	{
		// the remote users can see her name
		if(!$this->access('ganje', 'admin', 'admin') && $this->access('ganje', 'remote', 'view'))
		{
			$return =
			[
				'list'    => \lib\db\staff::get_all(['user_id' => $this->login('id')]),
				'summary' => \lib\db\hours::summary(['user_id' => $this->login('id')])
			];
			return $return;
		}

		if($this->access('ganje', 'home', 'view'))
		{
			$return =
			[
				'list' => \lib\db\staff::get_all(),
				'summary' => \lib\db\hours::summary()
			];
			return $return;
		}
		return [];

	}


	/**
	 * [setName description]
	 * @param [type] $_id [description]
	 */
	private function setName($_id)
	{
		self::$user_name = \lib\db\staff::get_one($_id);
		self::$user_name = T_(self::$user_name['displayname']);
		return self::$user_name;
	}


	/**
	 * generate telegram messages and call send function to send messages directly
	 * @param  [type] $_type [description]
	 * @return [type]        [description]
	 */
	public static function generate_telegram_text($_type, $_args = null)
	{
		$msg             = '';
		$date_now        = \lib\utility::date("l j F Y", false, 'default');
		$time_now        = \lib\utility::date("H:i", false, 'default');
		$name            = "*". self::$user_name. "*";
		$plus = null;
		if(isset($_args['plus']) && $_args['plus'] > 0 )
		{
			$plus = $_args['plus'];
		}
		$minus = null;
		if(isset($_args['minus']) && $_args['minus'] > 0)
		{
			$minus = $_args['minus'];
		}

		switch ($_type)
		{
			case 'enter':
				// if this person is first one in this day send current date
				// add minus and plus if exist

				if(\lib\db\staff::enter() <= 1)
				{
					$tg = self::send_telegram($date_now);
				}
				$msg = "✅ $name";
				if($plus)
				{
					$msg .= "\n➕ ". \lib\utility\human::number($plus, 'fa');
				}
				break;


			case 'exit':
				$msg   = "💤 $name\n";
				$start = \lib\db\staff::get_start($_args['user_id']);
				$start = strtotime( date('Y/m/d'). ' '. $start);
				$total = floor(abs(strtotime('now') - $start) / 60);

				if($total < 5)
				{
					// exit from switch and show message
					$msg = "🚷 $msg";
				}

				$pure       = $total + $plus - $minus;
				$pure_human = utility\human::time($pure, 'persian');
				$time_start = \lib\utility::date('H:i', $start , 'default');

				$msg        .= $time_start. ' '. T_('to'). ' '. $time_now;

				if($plus || $minus)
				{
					$msg        .= "\n🚩 ". \lib\utility\human::number($total, 'fa');
				}
				if($minus)
				{
					if(\lib\storage::get_minus())
					{
						$msg .= "\n➖ ". \lib\utility\human::number(\lib\storage::get_minus(), 'fa');
					}
					else
					{
						$msg .= "\n➖ ". \lib\utility\human::number($minus, 'fa');
					}
				}
				$msg        .= "\n🕗 ". $pure_human;

				// if this person is first one in this day send current date
				if(\lib\db\staff::live() <= 0)
				{
					$msg .= "\n". ' 🎌'. \lib\db\staff::enter();
				}

				break;

			default:
				break;
		}

		// send telegram message
		$tg = self::send_telegram($msg);

	}


	/**
	 * send message from telegram to admin
	 * @param  [type] $text [description]
	 * @return [type]       [description]
	 */
	public static function send_telegram($_text)
	{
		bot::$api_key   = '215239661:AAHyVstYPXKJyfhDK94A-XfYukDMiy3PLKY';
		bot::$name      = 'ermile_bot';

		$msg =
		[
			'method'       => 'sendMessage',
			'text'         => $_text,
			'chat_id'      => '46898544',
		];

		// send message for arvan
		if(\lib\router::get_root_domain('domain') === 'germile')
		{
			$msg['chat_id'] = 101315542;
		}

		$result = bot::sendResponse($msg);

		return $result;
	}
}
?>