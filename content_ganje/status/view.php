<?php
namespace content_ganje\status;

class view extends \content_ganje\home\view
{
	function view_status($_args)
	{
		$this->data->et     = $_args->api_callback;

		// set default values for dates
		$this->get_default_values($_args);
	}
}
?>