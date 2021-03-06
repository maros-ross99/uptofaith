<?php

class Participants extends CI_Controller
{
	var $url = "admin/participants/";
	var $session_order_by = "participants_order_by";
	var $session_filter = "participants_filter";
	var $session_offset = "participants_offset";
	var $global = NULL;
	var $access = NULL;
	
	function __construct()
	{
		parent::__construct();	

		$this->load->model('Global_model', 'global_model');
		$this->global = $this->global_model->get_global();
		
		$this->countries_model->order_by(array('name' => "asc"));
		$this->global['countries'] = $this->countries_model->get_all();
		
		$this->cities_model->order_by(array('name' => "asc"));
		$this->global['cities'] = $this->cities_model->get_all();
		
		$this->churches_model->order_by(array('name' => "asc"));
		$this->global['churches'] = $this->churches_model->get_all();

		$this->access = $this->global_model->admin['rights'][RIGHTS_NAME_PARTICIPANTS];
		
		if (!$this->session->userdata($this->session_order_by))
			$this->session->set_userdata($this->session_order_by, array('registration_date' => 'desc'));
		
		if (!$this->session->userdata($this->session_offset))
			$this->session->set_userdata($this->session_offset, 0);
			
		$this->lang->load('admin/info_messages');
		$this->lang->load('admin/participants');
	}

	function delete($ids = NULL)
	{
		if ((!is_array($ids)) && ($this->input->post('ids') == NULL))
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("participants-delete"));
				return;
			
			case RIGHTS_ACCESS_MANAGE:
				break;
		}
			
		if ($this->input->post('delete_confirm'))	
		{
			$ids = $this->input->post('ids');

			foreach ($ids as $id)
				if (!$this->participants_model->delete($id))
					return $this->index(lang("message-delete-error"));

			return $this->index(lang("message-delete-success"));
		}
		
		$data = array
		(
			'caption' => lang("participants-delete"),
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
		{
			return $this->delete($this->input->post('ids'));
		}
		elseif ($this->input->post('set_filter'))
		{
			$this->session->set_userdata($this->session_filter, $this->input->post('filter'));
		}
		elseif ($this->input->post('cancel_filter'))
		{
			$this->session->unset_userdata($this->session_filter, NULL);
		}
		
		$anchors = $buttons = array();

		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
				$this->global_model->access_denied(lang('participants'));
				return;
								
			case RIGHTS_ACCESS_READ:
				$anchors = array($this->views_model->anchor_view($this->url . "view/"));
				$buttons = array();
				break;

			case RIGHTS_ACCESS_MANAGE:
			default:
				$anchors = array($this->views_model->anchor_view($this->url . "view/"), $this->views_model->anchor_edit($this->url . "edit/"));
				$buttons = array($this->views_model->button_add($this->url . "add"), $this->views_model->button_delete_all("delete"));
				break;
		}
		
		$filter = $this->session->userdata($this->session_filter);
		
		$this->session->set_userdata($this->session_offset, $offset);

		$i = 0;
		
		for ($i = 1; $i <= 2; $i++)
		{
			$this->participants_model->filter($filter);
			$this->participants_model->order_by($this->session->userdata($this->session_order_by));
			$this->participants_model->where(array('event_id' => $this->global_model->event_active['id']));
			
			if (($i % 2) == 0)
			{
				$this->participants_model->limit($this->global_model->pagination['per_page'], $offset);
				$participants = $this->participants_model->get_all();
			}
			else
			{
				$participants_count = $this->participants_model->count_all();
			}
		}
		
		$this->global_model->pagination['base_url'] = site_url($this->url . "offset/");
		$this->global_model->pagination['total_rows'] = $participants_count;
		
		$this->pagination->initialize($this->global_model->pagination);

		$data = array
		(
			'caption' => lang("participants"),
			'action' => site_url($this->url),
			'participants' => $participants,
			'message' => $message,
			'filter' => $filter,
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
	
	function _form_validation_set_rules()
	{
		$this->form_validation->set_rules('data[gender_id]', lang("participants-gender"), 'required|integer');
		$this->form_validation->set_rules('data[name]', lang("participants-name"), 'trim|required');
		$this->form_validation->set_rules('data[surname]', lang("participants-surname"), 'trim|required');
		$this->form_validation->set_rules('data[email]', lang("participants-email"), 'required|valid_email');
		$this->form_validation->set_rules('data[city_id]', lang("participants-city"), 'required|integer');
		$this->form_validation->set_rules('data[note]', lang("participants-note"), 'trim');
		
		return;
	}

	function add()
	{
		if (count($this->global_model->events) < 1)
			return $this->index(lang("message-no-events"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("participants-add"));
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
			$data['registration_date'] = time();
			$data['event_id'] = $this->global_model->event_active['id'];
			
			$this->cities_model->where(array('id' => (int)$data['city_id']));
			$city = $this->cities_model->get();

			$result = false;

			if ($city != NULL)
			{
				$data['country_id'] = $city['country_id'];
				$result = $this->participants_model->insert($data);				
			}

			return $this->index(($result) ? lang("message-add-success") : lang("message-add-error"));
		}
		
		// Generate participants data
		$participants_data = array
		(
			'caption' => lang("participants-add"),
			'action' => site_url($this->url . "add"),
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_cancel($this->url)),
			'data' => $this->input->post('save') ? $this->input->post('data') : $this->participants_model->data(array('sort_order' => 0, 'status' => 1)),
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $participants_data));
	}
	
	function edit($id = NULL)
	{
		$this->participants_model->where(array('id' => $id, 'event_id' => $this->global_model->event_active['id']));		
		$participant = $this->participants_model->get();
		
		if ($participant == NULL)	
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("participants-edit"));
				return;
	
			case RIGHTS_ACCESS_MANAGE:
				break;
		}
			
		$this->_form_validation_set_rules();
		
		// Edit data		
		if ($this->input->post('save') && $this->form_validation->run('form'))
		{
			$data = $this->input->post('data');
			
			$this->cities_model->where(array('id' => (int)$data['city_id']));
			$city = $this->cities_model->get();

			$result = false;

			if ($city != NULL)
			{
				$data['country_id'] = $city['country_id'];

				$result = $this->participants_model->update($id, array_merge($participant, $data));				
			}
				
			return $this->index(($result) ? lang("message-edit-success") : lang("message-edit-error"));
		}
		
		// Generate participants data		
		$participants_data = array
		(
			'caption' => lang("participants-edit"),
			'action' => site_url($this->url . "edit/" . $id),
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_reset(), $this->views_model->button_back($this->url . "offset/" . $this->session->userdata($this->session_offset))),
			'data' => ($this->input->post('save')) ? $this->input->post('data') : $participant,
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $participants_data));	
	}
	
	function view($id = NULL)
	{
		$this->participants_model->where(array('id' => $id, 'event_id' => $this->global_model->event_active['id']));		
		$participant = $this->participants_model->get();
		
		if ($participant == NULL)
			return $this->index(lang("message-not-exists"));
			
		$buttons = array();

		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
				$this->global_model->access_denied(lang("participants-view"));
				return;

			case RIGHTS_ACCESS_READ:
				$buttons = array($this->views_model->button_back($this->url . "offset/" . $this->session->userdata($this->session_offset)));
				break;

			case RIGHTS_ACCESS_MANAGE:
				$buttons = array($this->views_model->button_edit($this->url . "edit/" . $id), $this->views_model->button_delete("delete"), $this->views_model->button_back($this->url . "offset/" . $this->session->userdata($this->session_offset)));
				break;
		}
		
		$participants_data = array
		(
			'caption' => lang("participants-view"),
			'action' => site_url($this->url),
			'buttons' => $buttons,
			'data' => $participant,
		);
		
		$this->views_model->back_office($this->url . 'view', array_merge($this->global, $participants_data));	
	}
}

/* End of file participants.php */
/* Location: ./system/application/controllers/admin/participants.php */
?>
