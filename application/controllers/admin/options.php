<?php

class Options extends CI_Controller
{
	var $url = "admin/options";
	var $global = NULL;
	var $access = NULL;
	
	function __construct()
	{
		parent::__construct();
		
		$this->load->model('Global_model', 'global_model');
		$this->global = $this->global_model->get_global();
		
		$this->access = $this->global_model->admin['rights'][RIGHTS_NAME_OPTIONS];
		
		$this->lang->load('admin/info_messages');
		$this->lang->load('admin/options');
	}
	
	function _update($data)
	{
		$this->form_validation->set_rules('data[site_title]', lang("options-site-title"), 'trim|required');
		$this->form_validation->set_rules('data[site_welcome_text]', lang("options-welcome-text"), 'trim|callback_welcome_text_check');
		$this->form_validation->set_rules('data[site_about_text]', lang("options-about-text"), 'trim|callback_about_text_check');
		$this->form_validation->set_rules('data[site_contact_text]', lang("options-contact-text"), 'trim|callback_contact_text_check');
		$this->form_validation->set_rules('data[site_messages_name]', lang("options-messages-name"), 'trim|required');
		$this->form_validation->set_rules('data[site_messages_email]', lang("options-messages-email"), 'trim|required|valid_email');
		$this->form_validation->set_rules('data[site_meta_keywords]', lang("options-meta-keywords"), 'trim|required');
		$this->form_validation->set_rules('data[site_meta_description]', lang("options-meta-description"), 'trim|required');
		$this->form_validation->set_rules('data[password]', lang("options-password"), 'trim|required|callback_password_check');
		
		if ($this->input->post('save') && $this->form_validation->run('form'))
		{
			return ($this->options_model->update($data)) ? lang("message-edit-success") : lang("message-edit-error");
		}
	}
	
	public function welcome_text_check($content)
	{
		if (strip_tags(unescape($content)) == NULL)
		{
			$this->form_validation->set_message("welcome_text_check", lang("required"));
			return false;
		}
		
		return true;
	}
	
	public function password_check($password)
	{
		if (encrypt($password) != $this->global_model->admin['password'])
		{
			$this->form_validation->set_message("password_check", lang("options-bad-password"));
			return false;
		}
		
		return true;
	}

	function index($message = "")
	{
		$buttons = array();
		
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
				$this->global_model->access_denied(lang('options'));
				return;
								
			case RIGHTS_ACCESS_READ:
				$buttons = array();
				break;

			case RIGHTS_ACCESS_MANAGE:
			default:
				$buttons = array($this->views_model->button_save("save"), $this->views_model->button_reset());
				break;
		}

		$data = $this->options_model->data($this->options_model->get_all());
		
		if ($this->input->post('save'))
			$message = $this->_update($this->input->post('data'));

		$page_data = array
		(
			'caption' => lang("options"),
			'action' => site_url($this->url),			
			'message' => $message,
			'data' => ($this->input->post('save')) ? $this->input->post('data') : $data,
			'buttons' => $buttons,
		);

		$this->views_model->back_office($this->url, array_merge($this->global, $page_data));
	}	
	
}
		
?>