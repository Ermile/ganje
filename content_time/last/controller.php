<?php
namespace content_time\last;

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