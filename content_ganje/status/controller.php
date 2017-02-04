<?php
namespace content_ganje\status;

class controller extends \mvc\controller
{
	// for routing check
	function _route()
	{
		// check login and if not loggined, redirect to login page
		$this->check_login();

		// if($this->access('ganje','intro', 'admin'))
		// {
		// 	\lib\error::page("No status found");
		// }

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