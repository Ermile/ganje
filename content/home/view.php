<?php
namespace content\home;

class view extends \mvc\view
{
	function config()
	{
		$this->include->css_ermile   = false;
		$this->include->js           = true;
		if($this->module() === 'home')
		{
			$this->data->bodyclass  = 'unselectable';
			// $this->include->js_main      = false;
			$this->include->css          = true;
		}
		//----- list of users
		$this->data->list = $this->model()->post_list();
	}


	/**
	 * [pushState description]
	 * @return [type] [description]
	 */
	function pushState()
	{
		if($this->module() !== 'home')
		{
			$this->data->display['mvc']     = "content/home/layout-xhr.html";
		}
	}
}
?>