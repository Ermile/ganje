<?php
namespace content_ganje\admin;
use \lib\db;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{

	/**
	 * insert or update record
	 */
	public function post_last()
	{
		$this->access('ganje', 'admin', 'edit', 'block');

		if(utility::post('add'))
		{
			$args = [
						'date'    => utility::post("date"),
						'start'   => utility::post("start"),
						'end'     => utility::post("end"),
						'user_id' => utility::post("user_id"),
						'minus'   => utility::post("minus"),
						'plus'    => utility::post("plus")
					];
			$result = \lib\db\hours::insert($args);

			if($result)
			{
				debug::true(T_("Added"));
			}
			else
			{
				debug::error(T_("Error in insert"));
			}
		}
		else
		{

			$arg = [
					'id'   => utility::post("id"),
					'type' => utility::post("type"),
					'time' => utility::post("time")
					];

			$result = \lib\db\hours::update($arg);

			if($update)
			{
					debug::true("Saved");
			}
			else
			{
					debug::fatal("Can not save change");
			}
		}
	}


	/**
	 * get list of data to shwo
	 *
	 * @return     array  The datatable.
	 */
	public function get_url($_args)
	{
		$data = null;
		$result =
		[
			'columns' => null,
			'data'    => null,
			'total'   => null
		];

		$date_year  = $_args->get_date(1);
		$date_month = $_args->get_date(2);
		$date_day   = $_args->get_date(3);
		$lang       = substr(\lib\router::get_storage('language'), 0, 2);

		$args =
		[
			'user_id'  => $_args->get_user(0),
			'day'      => $date_day,
			'month'    => $date_month,
			'year'     => $date_year,
			'lang'     => $lang
		];

		$data =  \lib\db\hours::get($args);

		if(!empty($data) && count($data) > 0)
		{
			$result['columns'] = array_keys($data[0]);
		}

		$result['data']  = $data;
		$result['total'] = count($result['data']);
		return $result;
	}
}
?>