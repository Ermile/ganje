<?php
namespace content_ganje\summary;
use \lib\db;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{


	public function get_u()
	{

		$report = \lib\db\hours::summary();

		return ['users' => \lib\db\users::get_all(), 'summary' => $report];
	}


	/**
	 * export data to csv file
	 *
	 * @return     <type>  ( description_of_the_return_value )
	 */
	public function post_export()
	{

		$month = utility::post('month');
		$year  = utility::post('year');
		$lang  = 'fa';

		$arg = [
				'lang'  => $lang,
				'month' => $month,
				'year'  => $year
				];

		$data = \lib\db\hours::sum($arg);

		if(utility::post("submit"))
		{
			if($month == 0)
			{
				$month = "all";
			}

			$name = 'ganje-export-'. $year . '-' . $month;
			\lib\utility\export::csv(['name' => $name ,'data' => $data]);
		}
		else
		{
			return $data;
		}
	}
}
?>