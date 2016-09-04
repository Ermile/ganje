<?php
namespace content_ganje\summary;

class controller extends \mvc\controller
{
	public function config()
	{

	}

	// for routing check
	function _route()
	{
		// check login and if not loggined, redirect to login page
		$this->check_login();
		// Check permission and if user can do this operation
		// allow to do it, else show related message in notify center
		$this->access('ganje', 'summary', 'view', 'block');

		$this->get("u", "u")->ALL();
		$this->post("export")->ALL();
	}
}
?>