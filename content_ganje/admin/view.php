<?php
namespace content_ganje\admin;

class view extends \mvc\view
{
	public function view_list() {
		$this->data->list =  $this->model()->list();
	}





	function view_url($_arg)
	{
		$this->data->et     = $_arg->api_callback;

		// get list of users
		$this->data->users  = \lib\db\users::get_all();
	}
}
?>