<?php
namespace content_ganje\status;

class controller extends \mvc\controller
{
	public function config()
	{

	}

	// for routing check
	function _route()
	{
		$this->get()->ALL();
		$this->post()->ALL();
	}
}
?>