<?php
namespace content\report;
use \lib\db;
use \lib\debug;

class model extends \mvc\model
{
	public function report(){
		$query = "SELECT sum(hours.hour_total), hours.*, users.user_displayname FROM hours
				  INNER JOIN users on users.id = hours.user_id
					";
		$report = db::get($query);



		return $report;
	}

}
?>