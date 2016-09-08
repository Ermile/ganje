<?php
namespace content_ganje\status;
use \lib\db;
use \lib\debug;
use \lib\utility;

class model extends \mvc\model
{


	public function post_u($o)
	{

		if(empty(utility::post()))
		{
			return $this->get_u();
		}
		$args = [
				'user'   => $this->login("id") ,
				'day'    => utility::post("day")  ,
				'week'   => utility::post("week") ,
				'month'  => utility::post("month"),
				'year'   => utility::post("year") ,
				'lang'   => substr(\lib\router::get_storage('language'), 0, 2),
				'start'  => utility::post("start"),
				'end'    => utility::post("end")
				];

		$result =  \lib\db\hours::summary($args);
		return $result;
	}


	public function get_u()
	{
		$id = $this->login("id");
		return \lib\db\hours::last(['user' => $id]);
	}


	public function post_status()
	{
		$arg = [
				'lang'    => substr(\lib\router::get_storage('language'), 0, 2),
				'user' => $this->login("id"),
				'month'   => utility::post('month'),
				'year'    => utility::post('year')
				];

		$data = \lib\db\hours::summary($arg);

		return $data;
	}

	public function get_mo(){

	}
}
?>