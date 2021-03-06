<?php

class Groups extends CI_Controller
{
	var $url = "admin/groups/";
	var $session_order_by = "groups_order_by";
	var $session_filter = "groups_filter";
	var $session_offset = "groups_offset";
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
		$this->lang->load('admin/groups');
	}
	
	function delete($ids = NULL)
	{
		if ((!is_array($ids)) && ($this->input->post('ids') == NULL))	
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("groups-delete"));
				return;
			
			case RIGHTS_ACCESS_MANAGE:
				break;
		}
			
		if ($this->input->post('delete_confirm'))	
		{
			$ids = $this->input->post('ids');

			foreach ($ids as $id)
				if (!$this->groups_model->delete($id))
					return $this->index(lang("message-delete-error"));

			return $this->index(lang("message-delete-success"));
		}	
		
		$data = array
		(
			'caption' => lang("groups-delete"),
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
				$this->global_model->access_denied(lang('groups'));
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
		
		$filter = $this->session->userdata($this->session_filter);
		
		$this->session->set_userdata($this->session_offset, $offset);

		$i = 0;
		
		for ($i = 1; $i <= 2; $i++)
		{
			$this->groups_model->filter($filter);
			$this->groups_model->order_by($this->session->userdata($this->session_order_by));
			$this->groups_model->where(array('event_id' => $this->global_model->event_active['id']));

			if (($i % 2) == 0)
			{
				$this->groups_model->limit($this->global_model->pagination['per_page'], $offset);
				$groups = $this->groups_model->get_all();
			}
			else
			{
				$groups_count = $this->groups_model->count_all();
			}
		}
		
		
		$this->global_model->pagination['base_url'] = site_url($this->url . "offset/");
		$this->global_model->pagination['total_rows'] = $groups_count;
		
		$this->pagination->initialize($this->global_model->pagination);


		$data = array
		(
			'caption' => lang("groups"),
			'action' => site_url($this->url),
			'groups' => $groups,
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
		$this->form_validation->set_rules('data[count_men]', lang("groups-count"), 'required|integer|callback_count_check');
		$this->form_validation->set_rules('data[count_women]', lang("groups-count"), 'required|integer');
		$this->form_validation->set_rules('data[name]', lang("groups-name"), 'trim|required');
		$this->form_validation->set_rules('data[surname]', lang("groups-surname"), 'trim|required');
		$this->form_validation->set_rules('data[email]', lang("groups-email"), 'required|valid_email');
		$this->form_validation->set_rules('data[city_id]', lang("groups-city"), 'required');
		$this->form_validation->set_rules('data[note]', lang("groups-note"), 'trim');
		
		return;
	}
	
	public function count_check($count_men)
	{
		$data = $this->input->post('data');

		if ((int)($data['count_men'] + $data['count_women']) < 1)
		{
			$this->form_validation->set_message('count_check', lang("groups-bad-count"));
			return false;
		}
		
		return true;
	}

	function add()
	{
		if (count($this->global_model->events) < 1)
			return $this->index(lang("message-no-events"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("groups-add"));
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
			$data['count'] = (int)($data['count_men'] + $data['count_women']);
			
			$this->cities_model->where(array('id' => (int)$data['city_id']));
			$city = $this->cities_model->get();

			$result = false;

			if ($city != NULL)
			{
				$data['country_id'] = $city['country_id'];
				$result = $this->groups_model->insert($data);				
			}
							
			return $this->index(($result) ? lang("message-add-success") : lang("message-add-error"));
		}
		
		// Generate groups data
		$groups_data = array
		(
			'caption' => lang("groups-add"),
			'action' => site_url($this->url . "add/"),
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_cancel($this->url)),
			'data' => $this->input->post('save') ? $this->input->post('data') : $this->groups_model->data(array('sort_order' => 0, 'status' => 1)),
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $groups_data));
	}
	
	function edit($id = NULL)
	{
		$this->groups_model->where(array('id' => $id, 'event_id' => $this->global_model->event_active['id']));		
		$group = $this->groups_model->get();
		
		if ($group == NULL)	
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("groups-edit"));
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
				$data['count'] = (int)($data['count_men'] + $data['count_women']);

				$result = $this->groups_model->update($id, array_merge($group, $data));				
			}
				
			return $this->index(($result) ? lang("message-edit-success") : lang("message-edit-error"));
		}
		
		// Generate groups data		
		$groups_data = array
		(
			'caption' => lang("groups-edit"),
			'action' => site_url($this->url . "edit/" . $id),
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_reset(), $this->views_model->button_back($this->url . "offset/" . $this->session->userdata($this->session_offset))),
			'data' => ($this->input->post('save')) ? $this->input->post('data') : $group,
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $groups_data));	
	}
	
	function view($id = NULL)
	{
		$this->groups_model->where(array('id' => $id, 'event_id' => $this->global_model->event_active['id']));		
		$group = $this->groups_model->get();
		
		if ($group == NULL)
			return $this->index(lang("message-not-exists"));
			
		$buttons = array();

		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
				$this->global_model->access_denied(lang("groups-view"));
				return;

			case RIGHTS_ACCESS_READ:
				$buttons = array($this->views_model->button_back($this->url . "offset/" . $this->session->userdata($this->session_offset)));
				break;

			case RIGHTS_ACCESS_MANAGE:
				$buttons = array($this->views_model->button_edit($this->url . "edit/" . $id), $this->views_model->button_delete("delete"), $this->views_model->button_back($this->url . "offset/" . $this->session->userdata($this->session_offset)));
				break;
		}
		
		$groups_data = array
		(
			'caption' => lang("groups-view"),
			'action' => site_url($this->url),
			'buttons' => array($this->views_model->button_edit($this->url . "edit/" . $id), $this->views_model->button_delete("delete"), $this->views_model->button_back($this->url . "offset/" . $this->session->userdata($this->session_offset))),
			'data' => $group,
		);
		
		$this->views_model->back_office($this->url . 'view', array_merge($this->global, $groups_data));	
	}
}

/* End of file groups.php */
/* Location: ./system/application/controllers/admin/groups.php */
?>
