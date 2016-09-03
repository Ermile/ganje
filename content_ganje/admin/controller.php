<?php
namespace content_ganje\admin;

class controller extends \mvc\controller
{
	public function config()
	{

	}

	// for routing check
	function _route()
	{
		$this->post("last")->ALL();
	}
}
?>