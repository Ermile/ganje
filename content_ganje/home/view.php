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