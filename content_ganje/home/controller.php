<?php
namespace content_ganje\home;

class controller extends \mvc\controller
{
	// for routing check
	function _route()
	{
		// check login and if not loggined, redirect to login page
		$this->check_login();

		// Check permission and if user can do this operation
		// allow to do it, else show related message in notify center
		if(!$this->access('ganje', 'home', 'view'))
		{
			$this->redirector()->set_domain()->set_url('ganje/status')->redirect();
		}

		$this->post("hours")->ALL();
	}
}
?>