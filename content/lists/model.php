<?php
namespace content\lists;
use \lib\db;
use \lib\debug;

class model extends \mvc\model
{
	public function list(){
		$query = "SELECT hours.*, users.user_displayname FROM hours
				  INNER JOIN users on users.id = hours.user_id
					";
		$list = db::get($query);



		return $list;
	}

}
?>