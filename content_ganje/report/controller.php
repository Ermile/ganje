<?php
namespace content_ganje\report;

class controller extends \mvc\controller
{
	public function config()
	{

	}

	// for routing check
	function _route()
	{
		$this->get(false, "report")->ALL();
	}
}
?>