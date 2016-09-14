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
		$result =
		[
			'columns' => null,
			'data'    => null,
			'total'   => null
		];
		// if date is not set show last records of user enter exit
		if(!isset($_args->match->date) && !isset($_args->match->user))
		{
			$result['columns'] = ['id','name','date','start','end','total','plus','minus','diff','status','accepted'];

			$data = \lib\db\hours::last();
		}
		else
		{
			$result['columns'] = ['id','name','date','start','end','total','plus','minus','diff','status','accepted'];

			// if date isset then filter result
			if(isset($_args->match->date) && count($_args->match->date) > 3)
			{
				if(isset($_args->match->user[0]))
				{
					$user_id = $_args->match->user[0];
					// remove user from columns if user is selected
					if(($key = array_search('name', $result['columns'])) !== false)
					{
						unset($result['columns'][$key]);
					}
				}
				else
				{
					$user_id = null;
				}

				if(isset($_args->match->page[0]))
				{
					$page = $_args->match->page[0];
				}
				else
				{
					$page = null;
				}

				$date       = $_args->match->date;
				$date_year  = $date[1];
				$date_month = $date[2];
				$date_day   = $date[3];
				$date_week  = null;
				$lang       = substr(\lib\router::get_storage('language'), 0, 2);


				if($date_year)
				{

				}

				$args =
				[
					'user_id'  => $user_id,
					'day'      => $date_day,
					'week'     => $date_week,
					'month'    => $date_month,
					'year'     => $date_year,
					'lang'     => $lang,
					'page'     => $page,
					'lenght'   => 7,
					// 'start' => utility::post("start"),
					// 'end'   => utility::post("end"),
				];

				$data =  \lib\db\hours::status($args);
			}
		}

		$result['data']  = $data;
		$result['total'] = count($result['data']);
		return $result;
	}
}
?>