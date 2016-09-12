<?php
namespace content_ganje\status;

class view extends \mvc\view
{
	function view_url($_arg)
	{
		if(isset($_arg->api_callback))
		{
			if(isset($_arg->match->date))
			{
				$this->data->et_col = ['date', 'start', 'end', 'total', 'diff', 'plus', 'minus', 'accepted'];
			}
			else
			{
				$this->data->et_col = ['date', 'total', 'diff', 'plus', 'minus', 'accepted'];
			}
			$this->data->et = $_arg->api_callback;
		}
	}
}
?>