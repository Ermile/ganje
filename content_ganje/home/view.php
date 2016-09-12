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
		//----- list of users
		$this->data->datatable = $this->model()->post_list();


		$this->data->site['title']    = T_("Ganje");
		$this->data->site['desc']     = T_("Free & open source attendance service!");
		$this->data->site['slogan']   = T_("Enjoy work time");
		$this->data->module           = $this->module();
	}
}
?>