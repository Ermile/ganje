<?php
namespace content\lists;

class controller extends \mvc\controller
{
	public function config()
	{

	}

	// for routing check
	function _route()
	{
		$this->post("lists")->ALL();
		// $this->get(null, 'datatable')->ALL('/^[^\/]d*$/');
		// $this->get(false, "list")->ALL();
	}
}
?>