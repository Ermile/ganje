<?php
namespace content_ganje\status;
use \lib\db;
use \lib\debug;
use \lib\utility;

class model extends \content_ganje\admin\model
{
	public function get_status($_args)
	{
		$_args->match->user = [0 => $this->login('id')];
		return $this->get_url($_args);
	}
}
?>