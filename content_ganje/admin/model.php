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
	public function post_admin($_args)
	{

		$this->access('ganje', 'admin', 'edit', 'block');

		if($_args->get_type() == 'add')
		{
			$args = [
						'date'    => $_args->get_date(),
						'start'   => $_args->get_time(),
						'end'     => $_args->get_time_end(),
						'user_id' => $_args->get_user_id(),
						'minus'   => $_args->get_minus(),
						'plus'    => $_args->get_plus()
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
		elseif($_args->get_type() == 'edit')
		{

			$arg = [
					'id'     => $_args->get_id(),
					'status' => $_args->get_status(),
					'time'   => $_args->get_time()
					];

			$result = \lib\db\hours::update($arg);

			if($result)
			{
					debug::true(T_("Saved"));
			}
			else
			{
					debug::error(T_("Can not save change"));
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