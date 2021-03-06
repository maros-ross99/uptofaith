<?php

class Welcome extends CI_Controller
{
	var $global = NULL;
	var $url = "admin/welcome";
	
	function __construct()
	{
		parent::__construct();	

		$this->load->model('Global_model', 'global_model');
		$this->global = $this->global_model->get_global();
		
		$this->lang->load('admin/welcome');
	}
	
	function index()
	{
		$data = array
		(
			"caption" => lang("welcome"),
		);
		
		$this->views_model->back_office($this->url, array_merge($this->global, $data));	
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/admin/welcome.php */

?>