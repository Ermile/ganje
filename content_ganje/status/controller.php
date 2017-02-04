<?php
namespace content_ganje\status;

class controller extends \mvc\controller
{
	// for routing check
	function _route()
	{
		// check login and if not loggined, redirect to login page
		$this->check_login();

		if(!$this->access('ganje','admin', 'admin') && $this->access('ganje','secret', 'view'))
		{
			$this->redirector('/ganje')->redirect();
			return;
		}

		$this->get('status', 'status')->ALL(
			[
				'property' => [
				"page" => ["/^\d+$/", true, 'page'],
				"q"    => ["/^(.*)$/", true, 'search'],
				'date' => ["/^(\d{4})\-(0?[0-9]|1[0-2])\-(0?[0-9]|[12][0-9]|3[01])$/", true, 'date']
				]
			]);
	}
}
?>