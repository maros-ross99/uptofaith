<?php

class Churches extends CI_Controller
{	
	var $url = "admin/churches/";
	var $session_order_by = "churches_order_by";
	var $session_offset = "churches_offset";
	var $countries = NULL;
	var $global = NULL;
	var $access = NULL;
	
	function __construct()
	{
		parent::__construct();	
		
		$this->load->model('Global_model', 'global_model');
		$this->global = $this->global_model->get_global();
		
		$this->access = $this->global_model->admin['rights'][RIGHTS_NAME_CATALOG];
		
		if (!$this->session->userdata($this->session_order_by))
			$this->session->set_userdata($this->session_order_by, array('country_id' => "asc"));

		if (!$this->session->userdata($this->session_offset))
			$this->session->set_userdata($this->session_offset, 0);

		$this->countries = $this->countries_model->get_all();
		
		$this->lang->load('admin/info_messages');
		$this->lang->load('admin/churches');
	}
	
	function delete($ids = NULL)
	{
		if ((!is_array($ids)) && ($this->input->post('ids') == NULL))
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("churches-delete"));
				return;
			
			case RIGHTS_ACCESS_MANAGE:
				break;
		}
			
		if ($this->input->post('delete_confirm'))
		{
			$ids = $this->input->post('ids');
			
			foreach ($ids as $id)
				if (!$this->churches_model->delete($id))
					return $this->index(lang("message-delete-error"));

			return $this->index(lang("message-delete-success"));
		}
		
		$data = array
		(
			'caption' => lang("churches-delete"),
			'action' => site_url($this->url . "delete/"),
			'buttons' => array($this->views_model->button_delete("delete_confirm", NULL), $this->views_model->button_cancel($this->url)),
			'ids' => $ids,
			'message' => sprintf(lang("message-delete-confirm"), count($ids)),
		);

		$this->views_model->back_office('admin/delete', array_merge($this->global, $data));
	}
	
	function offset($offset = 0)
	{
		return $this->index(NULL, $offset);	
	}
	
	function index($message = NULL, $offset = 0)
	{
		if ($this->input->post('delete'))
			return $this->delete($this->input->post('ids'));
			
		$anchors = $buttons = array();

		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
				$this->global_model->access_denied(lang('churches'));
				return;
								
			case RIGHTS_ACCESS_READ:
				$anchors = array();
				$buttons = array();
				break;

			case RIGHTS_ACCESS_MANAGE:
			default:
				$anchors = array($this->views_model->anchor_edit($this->url . "edit/"));
				$buttons = array($this->views_model->button_add($this->url . "add"), $this->views_model->button_delete_all("delete"));
				break;
		}
			
		$this->session->set_userdata($this->session_offset, $offset);

		$i = 0;
		
		for ($i = 1; $i <= 2; $i++)
		{
			$this->churches_model->order_by($this->session->userdata($this->session_order_by));
			
			if (($i % 2) == 0)
			{
				$this->churches_model->limit($this->global_model->pagination['per_page'], $offset);
				$churches = $this->churches_model->get_all();
			}
			else
			{
				$churches_count = $this->churches_model->count_all();
			}
		}

		$this->global_model->pagination['base_url'] = site_url($this->url . "offset/");
		$this->global_model->pagination['total_rows'] = $churches_count;
		
		$this->pagination->initialize($this->global_model->pagination);

		$data = array
		(
			'caption' => lang("churches"),
			'action' => base_url() . $this->url,
			'churches' => $churches,
			'message' => $message,
			'countries' => $this->countries,
			'pagination_links' => $this->pagination->create_links(),
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
		$this->form_validation->set_rules('data[name]', lang("churches-name"), 'trim|required|callback_name_check[' . $id . ']');
		$this->form_validation->set_rules('data[country_id]', lang("churches-country"), 'required');
		
		return;
	}
	
	public function name_check($name, $id)
	{
		$data = $this->input->post('data');

		if ($id == NULL)
			$this->churches_model->where(array('name' => escape($name), 'country_id' => (int)$data['country_id']));
		else
			$this->churches_model->where(array('name' => escape($name), 'country_id' => (int)$data['country_id'], 'id !=' => $id));
		
		$count = $this->churches_model->count_all();
		
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
				$this->global_model->access_denied(lang("churches-add"));
				return;

			case RIGHTS_ACCESS_MANAGE:
			default:
				break;
		}
		
		$this->_form_validation_set_rules();
		
		// Save data
		
		if ($this->input->post('save') && $this->form_validation->run('form'))
		{
			$result = $this->churches_model->insert($this->input->post('data'));			
			return $this->index(($result) ? lang("message-add-success") : lang("message-add-error"));
		}
		
		// Generate page data
		$page_data = array
		(
			'caption' => lang("churches-add"),
			'action' => base_url() . $this->url . "add",
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_cancel($this->url)),
			'data' => $this->input->post('save') ? $this->input->post('data') : $this->churches_model->data(),
			'countries' => $this->countries,
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $page_data));
	}
	
	function edit($id = NULL)
	{
		$this->churches_model->where(array('id' => $id));		
		$church = $this->churches_model->get();
		
		if ($church == NULL)	
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("churches-edit"));
				return;
	
			case RIGHTS_ACCESS_MANAGE:
				break;
		}
			
		$this->_form_validation_set_rules($id);
		
		// Edit data		
		if ($this->input->post('save') && $this->form_validation->run('form'))
		{
			$result = $this->churches_model->update($id, $this->input->post('data'));
			return $this->index(($result) ? lang("message-edit-success") : lang("message-edit-error"));
		}
		
		// Generate page data		
		$page_data = array
		(
			'caption' => lang("churches-edit"),
			'action' => base_url() . $this->url . "edit/" . $id,
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_reset(), $this->views_model->button_back($this->url . "offset/" . $this->session->userdata($this->session_offset))),
			'data' => ($this->input->post('save')) ? $this->input->post('data') : $church,
			'countries' => $this->countries,
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $page_data));	
	}

}

/* End of file churches.php */
/* Location: ./system/application/controllers/admin/churches.php */
?>
