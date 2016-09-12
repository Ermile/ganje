<?php
namespace content_ganje\admin;

class controller extends \mvc\controller
{
	// for routing check
	function _route()
	{
		// check login and if not loggined, redirect to login page
		$this->check_login();
		// Check permission and if user can do this operation
		// allow to do it, else show related message in notify center
		$this->access('ganje', 'admin', 'view', 'block');

		// $this->post("last")->ALL();


		$this->get('url', 'url')->ALL([
				'property' => [
				"user" => ["/^\d+$/", true, 'page'],
				"page" => ["/^\d+$/", true, 'page'],
				"q"    => ["/^(.*)$/", true, 'search'],
				'date' => ["/^(\d{4})\-(0?[0-9]|1[0-2])\-(0?[0-9]|[12][0-9]|3[01])$/", true, 'date']
				]
			]);
	}
}
?>