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

		if(!$this->access('ganje','admin', 'admin') && $this->access('ganje', 'remote', 'view'))
		{
			$this->data->remote = true;
		}

		$this->data->site['title']  = T_("Ganje");
		$this->data->site['desc']   = T_("Free & open source attendance service!");
		$this->data->site['slogan'] = T_("Enjoy work time");
		$this->data->module         = $this->module();

		// add deadline of projects
		$deadline                        = strtotime("2017/01/20");
		$this->data->deadline            = ['title' => T_('Sarshomar v1'), 'value' => '', 'date' => $deadline, 'class' => '', 'start' => 20];
		$this->data->deadline['value']   = floor(($deadline - time()) / (60 * 60 * 24));
		// calc percent of time form start of this perion until end of it
		if($this->data->deadline['value'] <= 0)
		{
			$this->data->deadline['value'] = 0;
		}

		$this->data->deadline['percent'] = round(($this->data->deadline['value'] * 100) / $this->data->deadline['start'], 0);

		// add warn class to show best color
		if($this->data->deadline['percent'] <= 1)
		{
			$this->data->deadline['class'] = 'black';
			$this->data->deadline['value'] = '?';
		}
		elseif($this->data->deadline['percent'] <= 10)
		{
			$this->data->deadline['class'] = 'red';
		}
		elseif($this->data->deadline['percent'] <= 30)
		{
			$this->data->deadline['class'] = 'orange';
		}
		elseif($this->data->deadline['percent'] <= 50)
		{
			$this->data->deadline['class'] = 'yellow';
		}
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
		if(\lib\define::get_language() == 'fa')
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