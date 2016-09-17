<?php
namespace content_ganje\home;

class view extends \mvc\view
{
	function config()
	{
		if($this->module() === 'home')
		{
			$this->data->bodyclass  = 'unselectable register';
		}
		else
		{
			$this->data->bodyclass  = 'unselectable';
		}
		//----- list of users
		$this->data->datatable      = $this->model()->get_list_of_users();


		$this->data->site['title']  = T_("Ganje");
		$this->data->site['desc']   = T_("Free & open source attendance service!");
		$this->data->site['slogan'] = T_("Enjoy work time");
		$this->data->module         = $this->module();

		// add deadline of projects
		$deadline                      = strtotime("2016/10/22");
		$this->data->deadline          = ['title' => T_('Demo'), 'value' => '', 'date' => $deadline];
		$this->data->deadline['value'] = floor(abs(time() - $deadline) / (60 * 60 * 24));

	}
}
?>