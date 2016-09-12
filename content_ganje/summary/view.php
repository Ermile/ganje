<?php
namespace content_ganje\summary;

class view extends \mvc\view
{
	function view_summary($o){

		$this->data->users =  $o->api_callback['users'];
		$this->data->datatable =  $o->api_callback['summary'];
	}




	function view_url()
	{

	}
}
?>