<?php
namespace content_ganje\home;

class controller extends \mvc\controller
{
	// for routing check
	function _route()
	{
		// check login and if not loggined, redirect to login page
		$this->check_login();

		if(
			!$this->access('ganje', 'remote', 'admin') &&
			!$this->access('ganje', 'intro',  'admin')
		  )
		{
			// Check permission and if user can do this operation
			// allow to do it, else show related message in notify center
			$this->redirector()->set_domain()->set_url('ganje/status')->redirect();
			return;
		}

		$this->post("save")->ALL();
	}
}
?>