<?php
namespace mvc;

class controller extends \lib\mvc\controller
{
	function check_login()
	{
		// check logined and if not loggined, redirect to login page
		if(!$this->login())
		{
			$this->redirector()->set_domain()->set_url('login')->redirect();
		}

	}
}
?>