<?php
namespace content_ganje\status;

class view extends \mvc\view
{
	function view_status($_args)
	{
		$this->data->et     = $_args->api_callback;

		$year_min     = '2016';
		$year_current = \lib\utility::date('Y', false, 'current', false);
		if(substr(\lib\router::get_storage('language'), 0, 2) == 'fa')
		{
			$year_min = '1395';
		}
		$year_default = $_args->get_date(1);

		if($year_min == $year_current && $year_default != '0000')
		{
			$year_default = '0000';
		}

		$month_default = $_args->get_date(2)? $_args->get_date(2): '00';
		$day_default = $_args->get_date(3)? $_args->get_date(3): '00';


		$this->data->default_day   = ['val' => $month_default];
		$this->data->default_month = ['val' => $day_default];
		$this->data->default_year  = ['min' => $year_min, 'max' => $year_current, 'val' => $year_default];
	}
}
?>