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
		$type = utility::post('type');
		if($type == 'add')
		{
			$result = null;

			$args =
			[
				'user_id' => utility::post('user_id'),
				'date'    => utility::post('date'),
				'start'   => utility::post('time'),
				'end'     => utility::post('time_end'),
				'minus'   => utility::post('minus'),
				'plus'    => utility::post('plus')
			];

			if($this->access('ganje', 'admin', 'add'))
			{
				$result = \lib\db\hours::insert($args);
			}

			if($result)
			{
				debug::true(T_("Added"));
			}
			else
			{
				debug::error(T_("Error in insert"));
			}
		}
		elseif($type == 'edit')
		{
			$result = null;

			$arg =
			[
				'id'     => utility::post('id'),
				'status' => utility::post('status'),
				'time'   => utility::post('time')
			];

			if($this->access('ganje', 'admin', 'edit'))
			{
				$result = \lib\db\hours::update($arg);
			}

			if($result)
			{
				debug::true(T_("Saved"));
			}
			else
			{
				debug::error(T_("Can not save change"));
			}
		}
		elseif($type == 'change')
		{
			$result = null;
			// reza you must run query to give data and return new status
			$arg =
			[
				'id'   => utility::post('recordId'),
				'type' => utility::post('field'),
			];

			if($this->access('ganje', 'admin', 'admin'))
			{
				$result = \lib\db\hours::change_hours_status($arg);
			}

			if($result)
			{
				debug::msg('result', $result);
				// debug::true(T_("Saved"));
			}
			else
			{
				debug::error(T_("Can not save change"));
			}
		}
		else
		{
			debug::error(T_("How are you!"));
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
		$lang       =  \lib\define::get_language();

		$args =
		[
			'user_id' => $_args->get_user(0),
			'day'     => $date_day,
			'month'   => $date_month,
			'year'    => $date_year,
			'lang'    => $lang,
			'type'    => $_args->get_type(0),
			'export'  => $_args->get_export(),
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

		if($_args->get_export() && $this->access('ganje','admin', 'admin'))
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