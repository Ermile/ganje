<?php
namespace content\report;

class view extends \mvc\view
{
	function config()
	{
		// $this->include->css_ermile   = false;
		$this->include->js           = false;
		if($this->module() === 'home')
		{
			$this->data->bodyclass  = 'unselectable';
			$this->include->js_main      = false;
			$this->include->css          = false;
			var_dump("11111");

		}
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

	public function view_report() {
		$this->data->report =  $this->model()->report();
	}
}
?>