<?php
namespace content\home;
use \lib\utility;
class model extends \mvc\model
{

	public $user_id;

	public function post_hours($object)
	{
		
		$this->user_id = utility::post('userId');
		var_dump($this->user_id);
		$this->chekc_time();
		exit(11);
	}

	public function chekc_time (){
		$check = $this->sql()->tableHours()->whereUser_id($this->user_id)->select();
		var_dump($check->string());
	}

}
?>