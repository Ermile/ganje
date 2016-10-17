<?php
namespace content_ganje\admin;

class view extends \content_ganje\home\view
{
	/**
	 * [view_list description]
	 * @return [type] [description]
	 */
	public function view_list()
	{
		$this->data->list =  $this->model()->list();
	}


	/**
	 * [view_url description]
	 * @param  [type] $_args [description]
	 * @return [type]        [description]
	 */
	function view_url($_args)
	{
		$this->data->et     = $_args->api_callback;

		// get list of users details
		$this->data->users        = \lib\db\staff::get_all();
		$this->data->default_user = $_args->get_user(0);
		$this->data->bodyclass    .= ' manage';

		// set default values for dates
		$this->get_default_values($_args);
	}
}
?>