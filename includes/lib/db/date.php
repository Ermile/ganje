<?php
namespace lib\db;
use \lib\utility\jdate;

class date {

	/**
	 * GET month and return count of that
	 *
	 * @param      <type>   $month  The month
	 *
	 * @return     integer  count of month
	 */
	public static function day_month($month) {
		switch ($month) {
			case 1:
			case 2:
			case 3:
			case 4:
			case 5:
			case 6:
				return 31;
				break;

			default:
				return 30;
				break;
		}
	}

	/**
	 * convert jalali date
	 *
	 * @param      <type>  $year   The year
	 * @param      <type>  $month  The month
	 * @example 	convert_month(1395,6) => 2016-08-12, 2016-09-11
	 * @return     <type>
	 */
	public static function convert_month($year, $month){
		$start_day  = 1;
		$end_day    = self::day_month($month);

		$start_date = jdate::mktime(0, 0, 0, $month, $start_day, $year, true);
		$end_date   = jdate::mktime(0, 0, 0, $month, $end_day, $year, true);

		$start_date = date("Y-m-d",$start_date);
		$end_date   = date("Y-m-d",$end_date);
		return [$start_date, $end_date];

	}


	/**
	 * get jalali year and convert
	 *
	 * @param      <type>  $year   The year
	 * @example 	convert_month(1395) => 2016-03-12, 2017-03-12
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public static function convert_year($year){
		$start_day   = 1;
		$end_day     = 30;

		$start_month = 1;
		$end_month   = 12;

		$start_date = jdate::mktime(0, 0, 0, $start_month, $start_day, $year, true);
		$end_date   = jdate::mktime(0, 0, 0, $end_month, $end_day, $year, true);

		$start_date = date("Y-m-d",$start_date);
		$end_date   = date("Y-m-d",$end_date);
		return [$start_date, $end_date];
	}
}
?>