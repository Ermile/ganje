<?php
namespace content_ganje\admin;

class view extends \mvc\view
{
	/**
	 * [view_list description]
	 * @return [type] [description]
	 */
	public function view_list()
	{
		$this->data->list =  $this->model()->list();
	}


	/**
	 * [view_url description]
	 * @param  [type] $_args [description]
	 * @return [type]        [description]
	 */
	function view_url($_args)
	{
		$this->data->et     = $_args->api_callback;

		// get list of users
		$this->data->users        = \lib\db\users::get_all();
		$this->data->default_user = $_args->get_user(0);

		$year_min     = '2016';
		$year_current = \lib\utility::date('Y', false, 'current', false);
		if(substr(\lib\router::get_storage('language'), 0, 2) == 'fa')
		{
			$year_min = '1395';
		}
		$year_default = $_args->get_date(1);

		if($year_min == $year_current && $year_default != '0000')
		{
			$year_default = $year_current;
		}

		$month_default = $_args->get_date(2)? $_args->get_date(2): '00';
		$day_default = $_args->get_date(3)? $_args->get_date(3): '00';


		$this->data->default_day   = ['val' => $month_default];
		$this->data->default_month = ['val' => $day_default];
		$this->data->default_year  = ['min' => $year_min, 'max' => $year_current, 'val' => $year_default];
	}
}
?>