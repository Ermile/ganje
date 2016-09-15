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
		$this->data->datatable = $this->model()->get_list_of_users();


		$this->data->site['title']    = T_("Ganje");
		$this->data->site['desc']     = T_("Free & open source attendance service!");
		$this->data->site['slogan']   = T_("Enjoy work time");
		$this->data->module           = $this->module();

		// var_dump(($this->data->site['currentLang']));
		// // $this->data->default_year     = ;
	}
}
?>