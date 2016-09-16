<?php
namespace content_ganje\admin;

class view extends \mvc\view
{
	public function view_list() {
		$this->data->list =  $this->model()->list();
	}





	function view_url($_args)
	{
		$this->data->et     = $_args->api_callback;

		// get list of users
		$this->data->users  = \lib\db\users::get_all();

		$this->data->default_user = $_args->get_user(0);
	}
}
?>