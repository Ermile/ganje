<?php
namespace content_ganje\status;
use \lib\db;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{


	public function post_u($o){

		if(empty(utility::post())){
			return $this->get_u();
		}

			$args = [
					'user'   => $this->login("id") ,
					'day'    => utility::post("day")  ,
					'week'   => utility::post("week") ,
					'month'  => utility::post("month"),
					'year'   => utility::post("year") ,
					'lang'   => 'fa',
					'start'  => utility::post("start"),
					'end'    => utility::post("end")
					];

		$result =  \lib\db\summary::get($args);
		return $result;

	}


	public function get_u(){

		$id = $this->login("id");

		return \lib\db\last::get(['user' => $id]);
	}
}
?>