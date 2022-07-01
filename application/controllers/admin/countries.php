<?php

class Countries extends CI_Controller
{
	var $url = "admin/countries/";
	var $session_order_by = "countries_order_by";
	var $global = NULL;
	var $access = NULL;
	
	function __construct()
	{
		parent::__construct();	
		
		$this->load->model('Global_model', 'global_model');
		$this->global = $this->global_model->get_global();
		
		$this->access = $this->global_model->admin['rights'][RIGHTS_NAME_CATALOG];
		
		if (!$this->session->userdata($this->session_order_by))
			$this->session->set_userdata($this->session_order_by, array('name' => 'asc'));
			
		$this->lang->load('admin/info_messages');
		$this->lang->load('admin/countries');
	}
	
	function delete($ids = NULL)
	{
		if ((!is_array($ids)) && ($this->input->post('ids') == NULL))
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("countries-delete"));
				return;
			
			case RIGHTS_ACCESS_MANAGE:
				break;
		}
			
		if ($this->input->post('delete_confirm'))
		{
			$ids = $this->input->post('ids');
			
			foreach ($ids as $id)
				if (!$this->countries_model->delete($id))
					return $this->index(lang("message-delete-error"));

			return $this->index(lang("message-delete-success"));
		}
		
		$data = array
		(
			'caption' => lang("countries-delete"),
			'action' => site_url($this->url . "delete/"),
			'buttons' => array($this->views_model->button_delete("delete_confirm", NULL), $this->views_model->button_cancel($this->url)),
			'ids' => $ids,
			'message' => sprintf(lang("countries-delete-message"), count($ids)),
		);

		$this->views_model->back_office('admin/delete', array_merge($this->global, $data));
	}
	
	function index($message = NULL)
	{
		if ($this->input->post('delete'))
			return $this->delete($this->input->post('ids'));
			
		$anchors = $buttons = array();

		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
				$this->global_model->access_denied(lang('countries'));
				return;
								
			case RIGHTS_ACCESS_READ:
				$anchors = array();
				$buttons = array();
				break;

			case RIGHTS_ACCESS_MANAGE:
			default:
				$anchors = array($this->views_model->anchor_edit($this->url . "edit/"));
				$buttons = array($this->views_model->button_add($this->url . "add"), $this->views_model->button_delete_all("delete", NULL));
				break;
		}

		$this->countries_model->order_by($this->session->userdata($this->session_order_by));
		$countries = $this->countries_model->get_all();

		$data = array
		(
			'caption' => lang("countries"),
			'action' => base_url() . $this->url,
			'countries' => $countries,
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
		$this->form_validation->set_rules('data[name]', lang("countries-name"), 'trim|required|callback_name_check[' . $id . ']');
		
		return;
	}
	
	public function name_check($name, $id)
	{
		if ($id == NULL)
			$this->countries_model->where(array('name' => escape($name)));
		else
			$this->countries_model->where(array('name' => escape($name), 'id !=' => $id));
		
		$count = $this->countries_model->count_all();
		
		if ($count > 0)
		{
			$this->form_validation->set_message("name_check", lang("message-name-exists"));
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
				$this->global_model->access_denied(lang("countries-add"));
				return;

			case RIGHTS_ACCESS_MANAGE:
			default:
				break;
		}
		
		$this->_form_validation_set_rules();

		// Save data
		
		if ($this->input->post('save') && $this->form_validation->run('form'))
		{
			$result = $this->countries_model->insert($this->input->post('data'));			
			return $this->index(($result) ? lang("message-add-success") : lang("message-add-error"));
		}
		
		// Generate page data
		$page_data = array
		(
			'caption' => lang("countries-add"),
			'action' => base_url() . $this->url . "add",
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_cancel($this->url)),
			'data' => $this->input->post('save') ? $this->input->post('data') : $this->countries_model->data(),
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $page_data));
	}
	
	function edit($id = NULL)
	{
		$this->countries_model->where(array('id' => $id));		
		$country = $this->countries_model->get();
		
		if ($country == NULL)	
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("countries-edit"));
				return;
	
			case RIGHTS_ACCESS_MANAGE:
				break;
		}
			
		$this->_form_validation_set_rules($id);
		
		// Edit data		
		if ($this->input->post('save') && $this->form_validation->run('form'))
		{
			$result = $this->countries_model->update($id, $this->input->post('data'));
			return $this->index(($result) ? lang("message-edit-success") : lang("message-edit-error"));
		}
		
		// Generate page data		
		$page_data = array
		(
			'caption' => lang("countries-edit"),
			'action' => base_url() . $this->url . "edit/" . $id,
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_reset(), $this->views_model->button_back($this->url)),
			'data' => ($this->input->post('save')) ? $this->input->post('data') : $country,
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $page_data));	
	}
}

/* End of file countries.php */
/* Location: ./system/application/controllers/admin/countries.php */
?>
