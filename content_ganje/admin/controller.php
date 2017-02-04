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
		if(!$this->access('ganje', 'admin', 'view'))
		{
			$this->redirector()->set_domain()->set_url('ganje/status')->redirect();
			return;
		}

		// $this->post("last")->ALL();
		$this->get('url', 'url')->ALL(
		[
			'property' =>
			[
				"user"   => ["/^\d+$/", true, 'user'],
				"page"   => ["/^\d+$/", true, 'page'],
				"export" => ["/^(.*)$/", true, 'export'],
				"q"      => ["/^(.*)$/", true, 'search'],
				"type"   => ["/^summary|detail$/", true, 'type'],
				'date'   => ["/^(\d{4})\-(0?[0-9]|1[0-2])\-(0?[0-9]|[12][0-9]|3[01])$/", true, 'date']
			]
		]);

		$this->post('admin')->ALL(
		[
			'property' =>
			[
				"type"     => ["/^(add|edit)$/", true, 'type'],
				"id"       => ["/^\d+$/", true, 'id'],
				"status"   => ["/^(active|awaiting|deactive|removed|filter)$/", true, 'status'],
				"user_id"  => ["/^\d+$/", true, 'user_id'],
				'time'     => ["/^(\d{2})\:(\d{2})\:(\d{2})$/", true, 'time'],
				'time_end' => ["/^(\d{2})\:(\d{2})\:(\d{2})$/", true, 'time_end'],
				"minus"    => ["/^\d+$/", true, 'minus'],
				"plus"     => ["/^\d+$/", true, 'plus']
			]
		]);
	}
}
?>