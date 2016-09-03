<?php
namespace content_time\edit;

class controller extends \mvc\controller
{
	public function config()
	{

	}

	// for routing check
	function _route()
	{

		// edit question
		$this->get("edit", "edit")->ALL("/id\=(\d+)/");
		$this->post("edit")->ALL("/^edit\=(\d+)$/");

	}
}
?>