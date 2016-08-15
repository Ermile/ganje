<?php
namespace database\ganje;
class hours
{
	public $id         = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'int@10'];
	public $user_id    = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'user'            ,'type'=>'int@10'                          ,'foreign'=>'users@id!user_displayname'];
	public $hour_date  = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'date'            ,'type'=>'date@'];
	public $hour_start = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'start'           ,'type'=>'time@'];
	public $hour_end   = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'end'             ,'type'=>'time@'];
	public $hour_idle  = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'hour le'         ,'type'=>'int@3'];
	public $hour_sum   = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'sum'             ,'type'=>'time@'];

	//--------------------------------------------------------------------------------id
	public function id(){}
	//--------------------------------------------------------------------------------foreign
	public function user_id()
	{
		$this->form()->type('select')->name('user_')->required();
		$this->setChild();
	}

	public function hour_date()
	{
		$this->form()->type('text')->name('date')->required();
	}

	public function hour_start()
	{
		$this->form()->type('text')->name('start')->required();
	}

	public function hour_end()
	{
		$this->form()->type('text')->name('end')->required();
	}
	//--------------------------------------------------------------------------------foreign
	public function hour_idle()
	{
		$this->form()->type('select')->name('hour_le')->required();
		$this->setChild();
	}

	public function hour_sum()
	{
		$this->form()->type('text')->name('sum')->required();
	}
}
?>