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
		// var_dump($this->data->et);

		$this->data->et_col = $this->data->et['columns'];
		$this->data->et     = $this->data->et['data'];
		$this->data->et_footer = ['count', 'sum', 'sum-hour', 'sum', 'sum', 'sum-hour'];

		// get list of users
		$this->data->users  = \lib\db\users::get_all();
	}
}
?>