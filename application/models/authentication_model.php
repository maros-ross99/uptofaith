<?php

define("SESSION_NAME_ADMIN", "site_admin");
define("SESSION_NAME_AUTH", "site_authenticated");
define("SESSION_NAME_TIME", "site_authenticated_time");
define("SESSION_NAME_IP", "site_authenticated_ip");
define("SESSION_NAME_NUM_ATTEMPTS", "site_num_attempts");
define("SESSION_NAME_CAPTCHA", "site_authentication_captcha");

class Authentication_model extends CI_Model
{
	var $session_names = array(SESSION_NAME_ADMIN, SESSION_NAME_AUTH, SESSION_NAME_TIME, SESSION_NAME_IP, SESSION_NAME_NUM_ATTEMPTS, SESSION_NAME_CAPTCHA);
	var $captcha = NULL;
	var $site_config = NULL;
	
	function __construct()
   {
		// Call the Model constructor
		parent::__construct();

		$this->load->model('Options_model', 'options_model');
		$this->load->model('Admins_model', 'admins_model');

		$this->load->helper('captcha');

		$this->site_config = $this->config->item('site');
	}
	
	function num_attempts_exceed()
	{
		return ($this->session->userdata(SESSION_NAME_NUM_ATTEMPTS) >= $this->site_config['num_login_attempts']);
	}


	function generate_captcha()
	{
		$captcha_dir = $this->site_config['captcha_dir'];
		$captcha_url = $this->site_config['captcha_url'];
		
		$vals = array
		(
			'word' => rand(1000, 9999),
		   'img_path' => $captcha_dir,
		   'img_url' => site_url($captcha_url) . "/",
		   'img_height' => 30,
		);
		
		$this->captcha = create_captcha($vals);
		$this->session->set_userdata(SESSION_NAME_CAPTCHA, $this->captcha['word']);
		
		return $this->captcha;
	}


	function login($data)
	{
		// check captcha, if used
		if ($this->num_attempts_exceed() && ($data['captcha'] != $this->session->userdata(SESSION_NAME_CAPTCHA)))
			return -2;
			
		$this->admins_model->where(array('username' => escape($data['username']), 'password' => encrypt($data['password'])));
		$admin = $this->admins_model->get();
		
		// if bad login, count number of attempts (used for generating the captcha)
		if ($admin == NULL)
		{
			$num_attempts = (int)$this->session->userdata(SESSION_NAME_NUM_ATTEMPTS);
			$num_attempts++;

			$this->session->set_userdata(SESSION_NAME_NUM_ATTEMPTS, $num_attempts);
			
			return -1;
		}

		// unescape strings, because will be escaped again in update
		$admin['name'] = unescape($admin['name']);
		$admin['username'] = unescape($admin['username']);
		$admin['email'] = unescape($admin['email']);
		
		// update last login
		$admin['last_login'] = $admin['last_login2'];
		$admin['last_login2'] = time();

		if (!$this->admins_model->update($admin['id'], $admin))
			return -3;

		// unpack administrator rights
		$admin['rights'] = $this->admins_model->rights_unpack($admin['rights']);

		$this->session->set_userdata(SESSION_NAME_NUM_ATTEMPTS, 0);
		$this->session->set_userdata(SESSION_NAME_ADMIN, $admin);
		$this->session->set_userdata(SESSION_NAME_AUTH, true);
		$this->session->set_userdata(SESSION_NAME_TIME, time());
		$this->session->set_userdata(SESSION_NAME_IP, $this->input->ip_address());
		// KCFinder ENABLE
		$this->session->set_userdata(array('KCFINDER' => array('disabled' => false)));

		return 1;
	}
	
	function logout()
	{
		// destroy all sessions except following (important for login)
		$except = array(SESSION_NAME_NUM_ATTEMPTS, SESSION_NAME_CAPTCHA);
		
		foreach ($this->session_names as $session_name)
		{
			if (in_array($session_name, $except))
				continue;			
				
			$this->session->set_userdata($session_name, NULL);
		}

		// KCFinder DISABLE
		$this->session->set_userdata(array('KCFINDER' => array('disabled' => true)));

		return (!$this->session->userdata(SESSION_NAME_ADMIN) &&
				  !$this->session->userdata(SESSION_NAME_AUTH) &&
				  !$this->session->userdata(SESSION_NAME_TIME) &&
				  !$this->session->userdata(SESSION_NAME_IP));	
	}
	
	function is_authenticated($redirect = true)
	{
		if ((!$this->session->userdata(SESSION_NAME_ADMIN)) ||
			 (!$this->session->userdata(SESSION_NAME_AUTH)) || 
			 ((time() - $this->session->userdata(SESSION_NAME_TIME)) > $this->site_config['authentification_time']) ||
			 ($this->input->ip_address() != $this->session->userdata(SESSION_NAME_IP)))
		{
			$this->logout();
			
			if ($redirect)
				redirect(site_url("admin/authentication/login"), "location");

			return false;
		}

		// Update session time		
		$this->session->set_userdata(SESSION_NAME_TIME, time());

		return true;
	}
}
?>