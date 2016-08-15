<?php
namespace content\home;

class controller extends \mvc\controller
{
	public function config()
	{

	}

	// for routing check
	function _route()
	{

		if(!$this->login()){
			$redirector = new \lib\redirector();
			$redirector->set_domain()->set_url('login')->redirect();
		}else{
			$this->post("hours")->ALL();
			
			// $this->get(false, "list")->ALL();
		}

	}
}
?>