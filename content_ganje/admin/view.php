<?php
namespace content_ganje\admin;

class view extends \mvc\view
{
	function config()
	{
		// $this->include->css_ermile   = false;
		$this->include->js           = false;
		if($this->module() === 'home')
		{
			$this->data->bodyclass  = 'unselectable';
			$this->include->js_main = false;

		}
		// $this->include->ermile_css = true;
		$this->include->datatable  = true;
		$this->include->cp  = true;

		$this->data->datatable     =  $this->model()->get_datatable();
		// var_dump($this->data->datatable);
	}


	public function view_list() {
		$this->data->list =  $this->model()->list();
	}





	function view_url($_arg)
	{
		$this->data->et     = $_arg->api_callback;
		$this->data->et     = $this->model()->get_datatable();

		$this->data->et_col = $this->data->et['columns'];
		$this->data->et     = $this->data->et['data'];

		// get list of users
		$this->data->users  = \lib\db\users::get_all();
	}
}
?>