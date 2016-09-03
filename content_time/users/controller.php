<?php
namespace content_time\users;

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