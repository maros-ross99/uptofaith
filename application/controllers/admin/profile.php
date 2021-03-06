<?php

class Profile extends CI_Controller
{	
	var $url = "admin/profile";
	var $global = NULL;
	
	function __construct()
	{
		parent::__construct();	

		$this->load->model('Global_model', 'global_model');
		$this->global = $this->global_model->get_global();

		$this->lang->load('admin/info_messages');
		$this->lang->load('admin/admins');
	}

	function index($message = NULL)
	{
		if ($this->input->post('delete'))
			return $this->delete($this->input->post('ids'));

		$this->admins_model->where(array('id' => (int)$this->global_model->admin['id'])); 
		$admin = $this->admins_model->get();
		
		$this->_form_validation_set_rules();
		
		if ($this->input->post('save') && $this->form_validation->run('form'))
		{	
			$data = $this->input->post('data');
			
			if (!empty($data['new_password']))
				$data['password'] = encrypt($data['new_password']);

			$message = ($this->admins_model->update($admin['id'], array_merge($admin, $data))) ? lang("message-edit-success") : lang("message-edit-error");
		}

		$data = array
		(
			'caption' => lang('admins-profile'),
			'action' => site_url($this->url),
			'data' => ($this->input->post('save')) ? $this->input->post('data') : array_merge($admin, array('old_password' => NULL, 'new_password' => NULL, 'new_password2' => NULL)),
			'message' => $message,
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_reset()),
		);

		$this->views_model->back_office($this->url, array_merge($this->global, $data));
	}
	
	function _form_validation_set_rules()
	{
		$this->form_validation->set_rules('data[name]', lang("admins-name"), 'trim|required');
		$this->form_validation->set_rules('data[email]', lang("admins-email"), 'trim|required|valid_email');
		$this->form_validation->set_rules('data[old_password]', lang("admins-password"), 'trim|required|callback_password_check');
		$this->form_validation->set_rules('data[new_password]', lang("admins-new-password"), 'trim|callback_new_password_match');
		$this->form_validation->set_rules('data[new_password2]', lang("admins-new-password2"), 'trim');
		
		return;
	}
	
	public function password_check($password)
	{
		if (encrypt($password) != $this->global_model->admin['password'])
		{
			$this->form_validation->set_message("password_check", lang("message-bad-password"));
			return false;
		}
		
		return true;
	}
	
	public function new_password_match($new_password)
	{
		$data = $this->input->post('data');
		$new_password2 = $data['new_password2'];
		
		// no password change
		if (empty($new_password))
			return true;
			
		if ($new_password != $new_password2)
		{
			$this->form_validation->set_message("new_password_match", lang("message-no-password-match"));
			return false;
		}
		
		if (strlen($new_password) < $this->global_model->site_config['password_min_length'])
		{
			$this->form_validation->set_message("new_password_match", sprintf(lang("message-bad-password-length"), $this->global_model->site_config['password_min_length']));
			return false;
		}
		
		return true;			
	}
}

/* End of file profile.php */
/* Location: ./system/application/controllers/admin/profile.php */
?>
