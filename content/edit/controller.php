<?php
namespace content\edit;

class controller extends \mvc\controller
{
	public function config()
	{

	}

	// for routing check
	function _route()
	{
		// $this->post("edit")->ALL();
		$this->get(null,"edit")->ALL("/edit\=\d+^/");
	}
}
?>