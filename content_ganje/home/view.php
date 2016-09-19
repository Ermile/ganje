<?php
namespace content_ganje\home;

class view extends \mvc\view
{
	function config()
	{
		if($this->module() === 'home')
		{
			$this->data->bodyclass  = 'unselectable register';
			//----- list of users
			$this->data->datatable      = $this->model()->get_list_of_users();
		}
		else
		{
			$this->data->bodyclass  = 'unselectable';
		}

		$this->data->site['title']  = T_("Ganje");
		$this->data->site['desc']   = T_("Free & open source attendance service!");
		$this->data->site['slogan'] = T_("Enjoy work time");
		$this->data->module         = $this->module();

		// add deadline of projects
		$deadline                      = strtotime("2016/10/22");
		$this->data->deadline          = ['title' => T_('Sarshomar Demo'), 'value' => '', 'date' => $deadline];
		$this->data->deadline['value'] = floor(abs(time() - $deadline) / (60 * 60 * 24));
	}


	/**
	 * [get_default_values description]
	 * @param  [type] $_args [description]
	 * @return [type]        [description]
	 */
	public function get_default_values($_args)
	{
		$year_min     = '2016';
		$year_current = \lib\utility::date('Y', false, 'current', false);
		if(substr(\lib\router::get_storage('language'), 0, 2) == 'fa')
		{
			$year_min = '1395';
		}
		$year_default = $_args->get_date(1);

		if($year_min == $year_current && $year_default != $year_current)
		{
			$year_default = '0000';
		}

		$month_default = $_args->get_date(2)? $_args->get_date(2): '00';
		$day_default   = $_args->get_date(3)? $_args->get_date(3): '00';

		$this->data->default_day   = ['val' => $day_default];
		$this->data->default_month = ['val' => $month_default];
		$this->data->default_year  = ['min' => $year_min, 'max' => $year_current, 'val' => $year_default];
	}
}
?>