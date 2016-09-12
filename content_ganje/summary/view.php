<?php
namespace content_ganje\summary;

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

		}
		$this->include->datatable  = true;
		$this->include->cp  = true;
	}


	function view_summary($o){

		$this->data->users =  $o->api_callback['users'];
		$this->data->datatable =  $o->api_callback['summary'];
	}

}
?>