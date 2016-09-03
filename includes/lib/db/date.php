<?php
namespace lib\db;
use \lib\utility\jdate;

class date {

	/**
	 * [convert_date description]
	 * @param  [type] $year  [description]
	 * @param  [type] $month [description]
	 * @param  [type] $day   [description]
	 * @return array 		 converted date
	 */
	public static function convert($year = null, $month = null, $day = null) {

		//----- current jalali year
		if (!$year) {
			$year = jdate::date("Y", false, false);
		}

		if (!$month) {
			$month = jdate::date("m", false, false);
		}

		if (!$day) {
			$day = jdate::date("d", false, false);
		}

		$current_date = jdate::mktime(0, 0, 0, $month, $day, $year, true);

		return [date("Y",$current_date), date("m", $current_date), date("d", $current_date)];
	}
}
?>