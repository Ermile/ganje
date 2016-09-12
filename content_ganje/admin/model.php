<?php
namespace content_ganje\admin;
use \lib\db;
use \lib\utility;
use \lib\debug;

class model extends \mvc\model
{

	/**
	 * insert or update record
	 */
	public function post_last(){

		if(utility::post('add')){
			$args = [
						'date'    => utility::post("date"),
						'start'   => utility::post("start"),
						'end'     => utility::post("end"),
						'user_id' => utility::post("user_id"),
						'minus'   => utility::post("minus"),
						'plus'    => utility::post("plus")
					];
			$result = \lib\db\hours::insert($args);

			if($result){
				debug::true(T_("Added"));
			}else{
				debug::error(T_("Error in insert"));
			}
		}else{

			$arg = [
					'id'   => utility::post("id"),
					'type' => utility::post("type"),
					'time' => utility::post("time")
					];

			$result = \lib\db\hours::update($arg);

			if($update){
					debug::true("Saved");
			}else{
					debug::fatal("Can not save change");
			}
		}
	}


	/**
	 * get list of data to shwo
	 *
	 * @return     array  The datatable.
	 */
	public function get_datatable() {
		// creat data for datatable
		$result =
		[
			'columns' => ['id','name','date','start','end','total','plus','minus','diff','status','accepted'],
			'data'    => \lib\db\hours::last(),
			'total'   => 98,
			'filter'  => 4
		];
		return $result;
	}





	public function get_url($_args)
	{

	}
}
?>