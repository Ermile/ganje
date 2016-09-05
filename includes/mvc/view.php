<?php
namespace mvc;

class view extends \lib\mvc\view
{
	function _construct()
	{
		// define default value for global

		$this->data->site['title']    = T_("Tejarak");
		$this->data->site['desc']     = T_("Tejarak was born to serve small and beautiful service for e-business");
		$this->data->site['slogan']   = T_("Enjoy work time");

		$this->data->page['desc']     = $this->data->site['slogan'];
		$this->data->display['ganje'] = "content_ganje/home/layout.html";


		// if(! ($this->url('sub') === 'cp' || $this->url('sub') === 'account') )
		// 	$this->url->MainStatic       = false;

		/*
		// add language list for use in display
		$this->global->langlist		= array(
												'fa_IR' => 'فارسی',
												'en_US' => 'English',
												'de_DE' => 'Deutsch'
												);


		// if you need to set a class for body element in html add in this value
		$this->data->bodyclass      = null;
		*/

		if(method_exists($this, 'options')){
			$this->options();
		}
	}
}
?>