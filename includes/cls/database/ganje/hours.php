<?php
namespace database\ganje;
class hours
{
	public $id            = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'id'              ,'type'=>'int@10'];
	public $user_id       = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'user'            ,'type'=>'int@10'                          ,'foreign'=>'users@id!user_displayname'];
	public $hour_date     = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'date'            ,'type'=>'date@'];
	public $hour_start    = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'start'           ,'type'=>'time@'];
	public $hour_end      = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'end'             ,'type'=>'time@'];
	public $hour_diff     = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'diff'            ,'type'=>'smallint@5'];
	public $hour_minus    = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'minus'           ,'type'=>'smallint@5'];
	public $hour_plus     = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'plus'            ,'type'=>'smallint@5'];
	public $hour_total    = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'total'           ,'type'=>'smallint@5'];
	public $hour_status   = ['null'=>'NO'  ,'show'=>'YES'     ,'label'=>'status'          ,'type'=>'enum@raw,minus,plus,all,disable,enable,expire!raw'];
	public $hour_accepted = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'accepted'        ,'type'=>'smallint@6'];
	public $date_modified = ['null'=>'YES' ,'show'=>'YES'     ,'label'=>'modified'        ,'type'=>'timestamp@'];

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
		$this->form()->type('text')->name('end');
	}

	public function hour_diff()
	{
		$this->form()->type('number')->name('diff')->min()->max('99999');
	}

	public function hour_minus()
	{
		$this->form()->type('number')->name('minus')->min()->max('99999');
	}

	public function hour_plus()
	{
		$this->form()->type('number')->name('plus')->min()->max('99999');
	}

	public function hour_total()
	{
		$this->form()->type('number')->name('total')->min()->max('99999');
	}

	public function hour_status()
	{
		$this->form()->type('radio')->name('status')->required();
		$this->setChild();
	}

	public function hour_accepted()
	{
		$this->form()->type('number')->name('accepted')->max('999999');
	}

	public function date_modified(){}
}
?>