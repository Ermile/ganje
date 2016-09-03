<?php
namespace content_ganje\status;

class view extends \mvc\view
{
	function config()
	{
		// $this->include->css_ermile   = false;
		$this->include->js           = false;
		$this->include->datatable    = true;
		if($this->module() === 'home')
		{
			$this->data->bodyclass  = 'unselectable';
			$this->include->js_main      = false;
			$this->include->css          = false;

		}
		$this->data->datatable = $this->model()->get_u();
		$this->data->datatable_col = ['id', 'date', 'start', 'end', 'total', 'diff', 'plus', 'minus', 'accepted'];
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