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
			$args =
			[
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

			$arg =
			[
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
			'user_id' => $_args->get_user(0),
			'day'     => $date_day,
			'month'   => $date_month,
			'year'    => $date_year,
			'lang'    => $lang,
			'export'  => $_args->get_export()
		];

		$data = \lib\db\hours::get($args);

		if(!empty($data) && count($data) > 0)
		{
			$result['columns']  = array_keys($data[0]);
			$result['columns']  = array_fill_keys($result['columns'], null);
			$result['totalrow'] = $result['columns'];
		}

		// fill array keys for totalrow of table, on all fields
		if(isset($result['totalrow']))
		{
			foreach ($result['totalrow'] as $col => $attr)
			{
				switch ($col)
				{
					case 'diff':
					case 'plus':
					case 'minus':
					case 'accepted':
					case 'count':
						$result['totalrow'][$col] = 'sum';
						break;

					case 'date':
					case 'day':
						$result['totalrow'][$col] = 'count';
						break;

					default:
						$result['totalrow'][$col] = '';
						break;
				}
			}
		}

		unset($result['columns']['id']);
		unset($result['columns']['status']);

		// remove user name from table if user is selected

		$result['data']  = $data;
		$result['total'] = count($result['data']);

		if($_args->get_export())
		{

			$name = 'ganje-u'. $_args->get_user(0).'['. $date_year. $date_month. $date_day.']';
			\lib\utility\export::csv(['name' => $name ,'data' => $data]);
		}
		else
		{
			return $result;
		}
	}
}
?>