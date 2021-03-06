<?php

class Authentication extends CI_Controller
{	
	function __construct()
	{
		parent::__construct();	

		$this->load->library('user_agent');
		
		
		$this->load->model('Authentication_model', 'authentication_model');
		$this->load->model('Views_model', 'views_model');
		
		$this->lang->load('admin/info_messages');
		$this->lang->load('admin/authentication');
	}
	
	function login($message = "")
	{
		$captcha = NULL;
		
		$this->form_validation->set_rules('data[username]', lang("authentication-username"), 'trim|required');
		$this->form_validation->set_rules('data[password]', lang("authentication-password"), 'trim|required');
	
		if ($this->authentication_model->num_attempts_exceed())		
			$this->form_validation->set_rules('data[captcha]', lang("authentication-captcha"), 'required');
		
		if ($this->input->post('submit') && $this->form_validation->run('form'))
		{
			switch ($this->authentication_model->login($this->input->post('data')))
			{
				case -1: $message = lang("message-bad-login");
					break;
	
				case -2: $message = lang("message-bad-captcha");
					break;

				default: redirect(site_url('admin/'), 'location');
					break;
			}
		}
		
		if ($this->authentication_model->num_attempts_exceed())
			$captcha = $this->authentication_model->generate_captcha();

		$page_data = array
		(
			'caption' => lang("authentication-login"),
			'action' => "",
			'message' => $message,
			'buttons' => array($this->views_model->button_login("submit"), $this->views_model->button_main_page("")),
			'site_user' => set_value('site_user'),
			'captcha' => $captcha,
		);

		$this->views_model->back_office('admin/authentication/login', $page_data);	
	}
	
	function logout()
	{ 
		$page_data = array
		(
			'caption' => lang("authentication-logout"),
			'action' => "",
			'message' => ($this->authentication_model->logout()) ? lang("message-logout-success") : lang("message-logout-error"),
			'buttons' => array($this->views_model->button_login_anchor("admin/authentication/login"), $this->views_model->button_main_page("")),
		);

		$this->views_model->back_office('admin/authentication/logout', $page_data);	
	}
	
	
	function index()
	{
		redirect(site_url("admin/"), "location");
	}
	
}

/* End of file authentication.php */
/* Location: ./system/application/controllers/admin/authentication.php */
?>