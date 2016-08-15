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
		if($this->login()){
			$this->get(false,"list")->ALL("list");
		}
			$this->post("hours")->ALL();

	}
}
?>