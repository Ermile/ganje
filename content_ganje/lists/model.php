<?php
namespace content_ganje\lists;
use \lib\db;
use \lib\utility;
use \lib\debug;
use \lib\utility\jdate;

class model extends \mvc\model
{
	public function list(){
		$query = "SELECT

				  hours.*,
				  users.user_displayname

				  FROM hours

				  INNER JOIN users on users.id = hours.user_id

				  order by
				  hours.hour_date  DESC ,
				  hours.hour_end   ASC,
				  hours.hour_start DESC
				  LIMIT 30 ";

		$list = db::get($query);

		return $list;
	}

	public function post_lists() {



	}

}
?>