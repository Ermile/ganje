<?php
namespace content_ganje\status;

class view extends \mvc\view
{
	function config()
	{
		// $this->include->css_ermile   = false;
		// $this->include->js           = false;
		$this->include->datatable    = true;
		if($this->module() === 'home')
		{
			$this->data->bodyclass  = 'unselectable';
			$this->include->js_main      = false;
			$this->include->css          = false;
		}
	}


	function view_url($_arg)
	{
		if(isset($_arg->api_callback))
		{
			if(isset($_arg->match->date))
			{
				$this->data->datatable_col = ['date', 'start', 'end', 'total', 'diff', 'plus', 'minus', 'accepted'];
			}
			else
			{
				$this->data->datatable_col = ['date', 'total', 'diff', 'plus', 'minus', 'accepted'];
			}
			$this->data->datatable     = $_arg->api_callback;
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
}
?>