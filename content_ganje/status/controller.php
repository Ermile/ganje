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
		// check login and if not loggined, redirect to login page
		$this->check_login();
		// Check permission and if user can do this operation
		// allow to do it, else show related message in notify center
		$this->access('ganje', null, null, 'block');


		$this->get('mo', false)->ALL([
			'property' => [
			"page" => ["/^\d+$/", true, 'page'],
			"q" => ["/^(.*)$/", true, 'search'],
			'date' => ["/^(\d{4})\-(0?[0-9]|1[0-2])\-(0?[0-9]|[12][0-9]|3[01])$/", true, 'date']
			]
			]);
	}
}
?>