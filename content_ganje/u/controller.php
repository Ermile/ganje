<?php
namespace content_ganje\u;

class controller extends \mvc\controller
{
	public function config()
	{

	}

	// for routing check
	function _route()
	{
		$this->get("u", "u")->ALL();
	}
}
?>