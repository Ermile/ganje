<?php
namespace content_ganje\status;
use \lib\db;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{
	public function get_url($_args)
	{
		$result = null;
		$id     = $this->login("id");

		// if date isset then filter result
		if(isset($_args->match->date) && count($_args->match->date) > 3)
		{
			$date       = $_args->match->date;
			$date_year  = $date[1];
			$date_month = $date[2];
			$date_day   = $date[3];
			$date_week  = null;
			$lang       = substr(\lib\router::get_storage('language'), 0, 2);

			$args =
			[
				'user_id'   => $id,
				'day'    => $date_day,
				'week'   => $date_week,
				'month'  => $date_month,
				'year'   => $date_year,
				'lang'   => $lang,
				// 'start'  => utility::post("start"),
				// 'end'    => utility::post("end"),
			];

			$result =  \lib\db\hours::status($args);
		}
		// else show all record of this user
		else
		{
			$result = \lib\db\hours::last(['user' => $id]);
		}


		return $result;
	}
}
?>