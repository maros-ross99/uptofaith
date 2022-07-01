<?php

class Events extends CI_Controller
{
	var $url = "admin/events/";
	var $session_order_by = "events_order_by";
	var $global = NULL;
	var $access = NULL;
	
	function __construct()
	{
		parent::__construct();

		$this->load->model('Global_model', 'global_model');
		$this->global = $this->global_model->get_global();
		$this->access = $this->global_model->admin['rights'][RIGHTS_NAME_EVENTS];
		
		if (!$this->session->userdata($this->session_order_by))
			$this->session->set_userdata($this->session_order_by, array('active' => 'desc'));
			
		$this->lang->load('admin/info_messages');
		$this->lang->load('admin/events');
	}
	
	function delete($ids = NULL)
	{
		if ((!is_array($ids)) && ($this->input->post('ids') == NULL))
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("events-delete"));
				return;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
				foreach ($ids as $id)
				{
					$this->events_model->where(array('id' => (int)$id));
					$event = $this->events_model->get();

					if (($event != NULL) && ($event['admin_id'] != $this->global_model->admin['id']))
					{
						$this->global_model->access_denied(lang("events-delete"));
						return;	
					}
				}

				break;
			
			case RIGHTS_ACCESS_MANAGE:
				break;
		}
			
		if ($this->input->post('delete_confirm'))
		{
			$ids = $this->input->post('ids');
			
			foreach ($ids as $id)
				if (!$this->events_model->delete($id))
					return $this->index(lang("message-delete-error"));

			return $this->index(lang("message-delete-success"));
		}
		
		$data = array
		(
			'caption' => lang("events-delete"),
			'action' => site_url($this->url . "delete/"),
			'buttons' => array($this->views_model->button_delete("delete_confirm", NULL), $this->views_model->button_cancel($this->url)),
			'ids' => $ids,
			'message' => sprintf(lang("events-delete-message"), count($ids)),
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
				$this->global_model->access_denied(lang('photogalleries'));
				return;
								
			case RIGHTS_ACCESS_READ:
				$anchors = array($this->views_model->anchor_view($this->url . "view/"));
				$buttons = array();
				break;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
				$this->events_model->where(array('admin_id' => (int)$this->global_model->admin['id']));
			case RIGHTS_ACCESS_MANAGE:
			default:
				$anchors = array($this->views_model->anchor_view($this->url . "view/"), $this->views_model->anchor_edit($this->url . "edit/"));
				$buttons = array($this->views_model->button_add($this->url . "add"), $this->views_model->button_delete_all("delete"));
				break;
		}

		$this->events_model->order_by($this->session->userdata($this->session_order_by));
		$events = $this->events_model->get_all();

		$data = array
		(
			'caption' => lang("events"),
			'action' => site_url($this->url),
			'events' => $events,
			'message' => $message,
			'anchors' => $anchors,
			'buttons' => $buttons,
		);

		$this->views_model->back_office($this->url . 'list', array_merge($this->global, $data));
	}
	
	
	function order_by($name = NULL)
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
		$this->form_validation->set_rules('data[name]', lang("events-name"), 'trim|required|callback_name_check[' . $id . ']');
		$this->form_validation->set_rules('data[place]', lang("events-place"), 'trim|required');
		
		return;
	}
	
	public function name_check($name, $id)
	{
		if ($id == NULL)
			$this->events_model->where(array('name' => escape($name)));
		else
			$this->events_model->where(array('name' => escape($name), 'id !=' => $id));
		
		$count = $this->events_model->count_all();
		
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
				$this->global_model->access_denied(lang("events-add"));
				return;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
			case RIGHTS_ACCESS_MANAGE:
			default:
				break;
		}		
		
		$this->_form_validation_set_rules();

		// Save data
		
		if ($this->input->post('save') && $this->form_validation->run('form'))
		{
			$data = $this->input->post('data');
			$data['admin_id'] = $this->global_model->admin['id'];
			
			$result = $this->events_model->insert($data);			
			return $this->index(($result) ? lang("message-add-success") : lang("message-add-error"));
		}
		
		// Generate page data
		$page_data = array
		(
			'caption' => lang("events-add"),
			'action' => base_url() . $this->url . "add",
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_cancel($this->url)),
			'data' => $this->input->post('save') ? $this->input->post('data') : $this->events_model->data(array('active' => 1)),
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $page_data));
	}
	
	function edit($id = NULL)
	{
		$this->events_model->where(array('id' => $id));		
		$event = $this->events_model->get();
		
		if ($event == NULL)	
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("events-edit"));
				return;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
				if ($event['admin_id'] != $this->global_model->admin['id'])
				{
					$this->global_model->access_denied(lang("events-edit"));
					return;	
				}

				break;
			
			case RIGHTS_ACCESS_MANAGE:
				break;
		}
			
		$this->_form_validation_set_rules($id);
		
		// Edit data		
		if ($this->input->post('save') && $this->form_validation->run('form'))
		{
			$data = $this->input->post('data');
			
			if (!array_key_exists("visible", $data))
				$data['visible'] = 0;
				
			$result = $this->events_model->update($id, $data);
			return $this->index(($result) ? lang("message-edit-success") : lang("message-edit-error"));
		}
		
		// Generate page data		
		$page_data = array
		(
			'caption' => lang("events-edit"),
			'action' => base_url() . $this->url . "edit/" . $id,			
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_reset(), $this->views_model->button_back($this->url)),
			'data' => ($this->input->post('save')) ? $this->input->post('data') : $event,
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $page_data));	
	}
	
	function view($id = NULL)
	{
		$this->events_model->where(array('id' => $id));		
		$event = $this->events_model->get();
		
		if ($event == NULL)
			return $this->index(lang("message-not-exists"));
			
		$buttons = array();

		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
				$this->global_model->access_denied(lang("events-view"));
				return;

			case RIGHTS_ACCESS_READ:
				$buttons = array($this->views_model->button_back($this->url));
				break;

			case RIGHTS_ACCESS_MANAGE_OWN:
				if ($event['admin_id'] != $this->global_model->admin['id'])
				{
					$this->global_model->access_denied(lang("events-view"));
					return;	
				}
			case RIGHTS_ACCESS_MANAGE:
				$buttons = array($this->views_model->button_edit($this->url . "edit/" . $id), $this->views_model->button_delete("delete"), $this->views_model->button_back($this->url));
				break;
		}
		
		$page_data = array
		(
			'caption' => lang("events-view"),
			'action' => site_url($this->url),
			'buttons' => $buttons,
			'id' => $id,
			'data' => $event,
		);
		
		$this->views_model->back_office($this->url . 'view', array_merge($this->global, $page_data));	
	}
	
	function set($id = NULL)
	{
		$this->events_model->where(array('id' => $id));		
		$event = $this->events_model->get();
		
		if ($event == NULL)
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
				$this->global_model->access_denied(lang("events-set"));
				return;

			case RIGHTS_ACCESS_READ:
				break;

			case RIGHTS_ACCESS_MANAGE_OWN:
				if ($event['admin_id'] != $this->global_model->admin['id'])
				{
					$this->global_model->access_denied(lang("events-set"));
					return;	
				}
			case RIGHTS_ACCESS_MANAGE:
				break;
		}

		$this->global_model->set_event_active($id);
		// refresh global array, where is event_id
		$this->global = $this->global_model->get_global();
		
		$page_data = array
		(
			'caption' => lang("events-set"),
			'action' => site_url($this->url),
			'buttons' => array(),
			'id' => $id,
			'data' => $event,
		);
		
		$this->views_model->back_office($this->url . 'set', array_merge($this->global, $page_data));	
	}
}

/* End of file events.php */
/* Location: ./system/application/controllers/admin/events.php */
?>
