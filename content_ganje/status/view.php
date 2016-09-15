<?php
namespace content_ganje\status;

class view extends \mvc\view
{
	function view_status($_arg)
	{
		$this->data->et_col = $_arg->api_callback['columns'];
		$this->data->et     = $_arg->api_callback['data'];
	}
}
?>