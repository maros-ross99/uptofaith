<?php

class Admins extends CI_Controller
{	
	var $url = "admin/admins/";
	var $session_order_by = "admins_order_by";
	var $global = NULL;
	var $access = NULL;
	
	function __construct()
	{
		parent::__construct();	

		$this->load->model('Global_model', 'global_model');
		$this->global = $this->global_model->get_global();
		$this->global['rights'] = $this->admins_model->rights;
		$this->global['rights_type'] = $this->admins_model->rights_type;
		
		$this->access = $this->global_model->admin['rights'][RIGHTS_NAME_ADMINS];
		
		if (!$this->session->userdata($this->session_order_by))
			$this->session->set_userdata($this->session_order_by, array('date' => 'desc'));

		$this->lang->load('admin/info_messages');
		$this->lang->load('admin/admins');
	}
	
	function _get_system_admin_id()
	{
		$this->admins_model->order_by(array('id' => "asc"));
		$admin = $this->admins_model->get();
		
		return ($admin != NULL) ? $admin['id'] : 0;
	}
	
	function delete($ids = NULL)
	{
		if ((!is_array($ids)) && ($this->input->post('ids') == NULL))	
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("admins-delete"));
				return;
			
			case RIGHTS_ACCESS_MANAGE:
				break;
		}
			
		if ($this->input->post('delete_confirm'))	
		{
			$ids = $this->input->post('ids');

			foreach ($ids as $id)
				if (($id == $this->_get_system_admin_id()) || (!$this->admins_model->delete($id)))
					return $this->index(lang("message-delete-error"));

			return $this->index(lang("message-delete-success"));
		}
		
		$data = array
		(
			'caption' => lang("admins-delete"),
			'action' => site_url($this->url . "delete/"),
			'buttons' => array($this->views_model->button_delete("delete_confirm", NULL), $this->views_model->button_cancel($this->url)),
			'ids' => $ids,
			'message' => sprintf(lang("message-delete-confirm"), count($ids)),
		);

		$this->views_model->back_office('admin/delete', array_merge($this->global, $data));
	}
	
	function index($message = NULL)
	{
		if ($this->input->post('delete'))
			return $this->delete($this->input->post('ids'));

		$this->_get_system_admin_id();

		$anchors = $buttons = array();

		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
				$this->global_model->access_denied(lang('admins'));
				return;
								
			case RIGHTS_ACCESS_READ:
				$anchors = array($this->views_model->anchor_view($this->url . "view/"));
				$buttons = array();
				break;

			case RIGHTS_ACCESS_MANAGE:
			default:
				$anchors = array($this->views_model->anchor_view($this->url . "view/"), $this->views_model->anchor_edit($this->url . "edit/"));
				$buttons = array($this->views_model->button_add($this->url . "add/"), $this->views_model->button_delete_all("delete"));
				break;
		}

		$this->admins_model->order_by($this->session->userdata($this->session_order_by));
		$admins = $this->admins_model->get_all();

		$data = array
		(
			'caption' => lang('admins'),
			'action' => site_url($this->url),
			'admins' => $admins,
			'message' => $message,
			'anchors' => $anchors,
			'buttons' => $buttons,
		);

		$this->views_model->back_office($this->url . 'list', array_merge($this->global, $data));
	}
	
	
	function order_by($name)
	{
		$order_by = $this->session->userdata($this->session_order_by);

		if (is_array($order_by) && array_key_exists($name, $order_by))
			$data = array($name => ($order_by[$name] == "asc") ? "desc" : "asc");
		else
			$data = array($name => "asc");

		$this->session->set_userdata($this->session_order_by, $data);

		$this->index(); 			
	}
	
	function _form_validation_set_rules($id = NULL)
	{
		$this->form_validation->set_rules('data[username]', lang("admins-username"), 'trim|required|callback_username_check[' . $id . ']');
		$this->form_validation->set_rules('data[name]', lang("admins-name"), 'trim|required');
		$this->form_validation->set_rules('data[email]', lang("admins-email"), 'trim|required|valid_email');
		$this->form_validation->set_rules('data[password]', lang("admins-password"), 'trim|callback_password_check');
		
		return;
	}
	
	public function username_check($username, $id)
	{
		if ($id == NULL)
			$this->admins_model->where(array('username' => escape($username)));
		else
			$this->admins_model->where(array('username' => escape($username), 'id !=' => $id));
		
		$count = $this->admins_model->count_all();
		
		if ($count > 0)
		{
			$this->form_validation->set_message("username_check", lang("message-username-exists"));
			return false;
		}

		return true;			
	}
	
	public function password_check($password)
	{			
		// no password change
		if (empty($password))
			return true;
		
		if (strlen($password) < $this->global_model->site_config['password_min_length'])
		{
			$this->form_validation->set_message("password_check", sprintf(lang("message-bad-password-length"), $this->global_model->site_config['password_min_length']));
			return false;
		}
		
		return true;			
	}

	function add()
	{
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("admins-add"));
				return;

			case RIGHTS_ACCESS_MANAGE:
			default:
				break;
		}
		
		$this->_form_validation_set_rules();

		// Save data
		if ($this->input->post('save') && $this->form_validation->run('form'))
		{
			$data = $this->input->post('data');
			$data['password'] = encrypt($data['new_password']);
			$data['rights'] = $this->admins_model->rights_pack($data['rights']);
			$result = $this->admins_model->insert($data);

			return $this->index(($result) ? lang("message-add-success") : lang("message-add-error"));
		}
		
		// Generate admins data
		$admins_data = array
		(
			'caption' => lang("admins-add"),
			'action' => site_url($this->url . "add/"),
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_cancel($this->url)),
			'data' => $this->input->post('save') ? $this->input->post('data') : $this->admins_model->data(),
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $admins_data));
	}
	
	function edit($id = NULL)
	{		
		$this->admins_model->where(array('id' => $id));
		$admin = $this->admins_model->get();
		
		if ($admin == NULL)
			return $this->index(lang("message-not-exists"));
			
		if (($id == $this->_get_system_admin_id()) && ($id != $this->global_model->admin['id']))
		{
			$this->global_model->access_denied(lang("admins-edit"));
			return;
		}
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("admins-edit"));
				return;
	
			case RIGHTS_ACCESS_MANAGE:
				break;
		}
			
		$this->_form_validation_set_rules($id);
		
		// Edit data		
		if ($this->input->post('save') && $this->form_validation->run('form'))
		{
			$data = $this->input->post('data');
			$data['rights'] = $this->admins_model->rights_pack($data['rights']);
			
			if (!empty($data['new_password']))
				$data['password'] = encrypt($data['new_password']);

			$result = $this->admins_model->update($id, array_merge($admin, $data));
				
			return $this->index(($result) ? lang("message-edit-success") : lang("message-edit-error"));
		}
		
		$admin['rights'] = $this->admins_model->rights_unpack($admin['rights']);
		
		// Generate admins data		
		$admins_data = array
		(
			'caption' => lang("admins-edit"),
			'action' => site_url($this->url . "edit/" . $id),
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_reset(), $this->views_model->button_back($this->url)),
			'data' => ($this->input->post('save')) ? $this->input->post('data') : $admin,
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $admins_data));	
	}
	
	function view($id = NULL)
	{
		$this->admins_model->where(array('id' => $id));
		$admin = $this->admins_model->get();
		
		if ($admin == NULL)
			return $this->index(lang("message-not-exists"));
			
		$buttons = array();

		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
				$this->global_model->access_denied(lang("admins-view"));
				return;

			case RIGHTS_ACCESS_READ:
				$buttons = array($this->views_model->button_back($this->url));
				break;

			case RIGHTS_ACCESS_MANAGE:
				$buttons = array($this->views_model->button_edit($this->url . "edit/" . $id), $this->views_model->button_delete("delete"), $this->views_model->button_back($this->url));
				break;
		}
			
		$admin['rights'] = $this->admins_model->rights_unpack($admin['rights']);
		
		$admins_data = array
		(
			'caption' => lang("admins-view"),
			'action' => site_url($this->url),
			'buttons' => $buttons,
			'data' => $admin,
		);
		
		$this->views_model->back_office($this->url . 'view', array_merge($this->global, $admins_data));	
	}
}

/* End of file admins.php */
/* Location: ./system/application/controllers/admin/admins.php */
?>
