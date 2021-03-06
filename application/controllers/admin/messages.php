<?php

class Messages extends CI_Controller
{
	var $url = "admin/messages/";
	var $session_order_by = "messages_order_by";
	var $global = NULL;
	var $access = NULL;
	
	function __construct()
	{
		parent::__construct();	

		$this->load->model('Global_model', 'global_model');
		$this->global = $this->global_model->get_global();
		$this->access = $this->global_model->admin['rights'][RIGHTS_NAME_MESSAGES];
		
		if (!$this->session->userdata($this->session_order_by))
			$this->session->set_userdata($this->session_order_by, array('date' => 'desc'));
			
		$this->load->library('email');

		$this->lang->load('admin/info_messages');
		$this->lang->load('admin/messages');	
	}
	
	function delete($ids = NULL)
	{
		if ((!is_array($ids)) && ($this->input->post('ids') == NULL))
			return $this->index(lang("message-not-exists"));

		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("messages-delete"));
				return;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
				foreach ($ids as $id)
				{
					$this->messages_model->where(array('id' => (int)$id));
					$message = $this->messages_model->get();

					if (($message != NULL) && ($message['admin_id'] != $this->global_model->admin['id']))
					{
						$this->global_model->access_denied(lang("messages-delete"));
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
				if (!$this->messages_model->delete($id))
					return $this->index(lang("message-delete-error"));

			return $this->index(lang("message-delete-success"));
		}
		
		$data = array
		(
			'caption' => lang("messages-delete"),
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
			
		$anchors = $buttons = array();

		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
				$this->global_model->access_denied(lang('messages'));
				return;
								
			case RIGHTS_ACCESS_READ:
				$anchors = array($this->views_model->anchor_view($this->url . "view/"));
				$buttons = array();
				break;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
				$this->messages_model->where(array('admin_id' => (int)$this->global_model->admin['id']));
			case RIGHTS_ACCESS_MANAGE:
			default:
				$anchors = array($this->views_model->anchor_view($this->url . "view/"));
				$buttons = array($this->views_model->button_send_anchor($this->url . "send"), $this->views_model->button_delete_all("delete"));
				break;
		}

		$this->messages_model->order_by($this->session->userdata($this->session_order_by));
		$this->messages_model->where(array('event_id' => $this->global_model->event_active['id']));
		$messages = $this->messages_model->get_all();

		$data = array
		(
			'caption' => lang("messages"),
			'action' => site_url($this->url),
			'messages' => $messages,
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
	
	function _form_validation_set_rules()
	{
		$this->form_validation->set_rules('data', lang("messages-recipients"), 'callback_recipients_check');
		$this->form_validation->set_rules('data[subject]', lang("messages-subject"), 'trim|required');
		$this->form_validation->set_rules('data[message]', lang("messages-message"), 'trim|callback_message_check');
		
		return;
	}
	
	public function message_check($content)
	{
		if (strip_tags(unescape($content)) == NULL)
		{
			$this->form_validation->set_message("message_check", lang("required"));
			return false;
		}
		
		return true;
	}
	
	public function recipients_check($data)
	{
		if ((!array_key_exists('cities', $data)) && (!array_key_exists('churches', $data)))
		{
			$this->form_validation->set_message('recipients_check', lang("recipients-required"));
			return false;
		}
		
		return true;
	}

	function send()
	{
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("messages-send"));
				return;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
			case RIGHTS_ACCESS_MANAGE:
			default:
				break;
		}

		if (count($this->global_model->events) < 1)
			return $this->index(lang("message-no-events"));

		if ((($site_name = $this->options_model->get("site_title")) == NULL) ||
			 (($site_user_name = $this->options_model->get("site_messages_name")) == NULL) ||
			 (($site_user_email = $this->options_model->get("site_messages_email")) == NULL))
			return $this->index(lang("message-fill-options"));
			
		$this->_form_validation_set_rules();
		

		// Save data and send email
		if ($this->input->post('send') && $this->form_validation->run('form'))
		{
			$data = $this->input->post('data');
			$data['date'] = time();
			$data['event_id'] = $this->global_model->event_active['id'];
			$data['admin_id'] = $this->global_model->admin['id'];
			
			$recipients = "";
			$emails = array();
			
			$churches = array();
			$cities = array();
			
			if (array_key_exists('all', $data))
			{
				$recipients = lang("messages-recipients-from-all");
				
				$this->participants_model->where(array('event_id' => $this->global_model->event_active['id']));
				$participants = $this->participants_model->get_all();
				$this->groups_model->where(array('event_id' => $this->global_model->event_active['id']));
				$groups = $this->groups_model->get_all();
				
				foreach ($participants as $participant)
					$emails[] = $participant['email'];
				
				foreach ($groups as $group)
					$emails[] = $group['email'];
			}
			else if (array_key_exists('countries', $data))
			{
				$recipients = lang("messages-recipients-from-countries");
				
				foreach ($data['countries'] as $id)
				{
					$this->countries_model->where(array('id' => (int)$id));
					$country = $this->countries_model->get();
					$recipients .= "&nbsp - " . $country['name'] . "<br />";
					
					$this->participants_model->where(array('event_id' => $this->global_model->event_active['id'], 'country_id' => (int)$id));
					$participants = $this->participants_model->get_all();
					$this->groups_model->where(array('event_id' => $this->global_model->event_active['id'], 'country_id' => (int)$id));
					$groups = $this->groups_model->get_all();
					
					foreach ($participants as $participant)
						$emails[] = $participant['email'];
					
					foreach ($groups as $group)
						$emails[] = $group['email'];
				}
			}
			else
			{
				if (array_key_exists('cities', $data))
				{
					foreach ($data['cities'] as $id)
					{
						$this->cities_model->where(array('id' => (int)$id));
						$cities[] = $this->cities_model->get();
						
						$this->participants_model->where(array('event_id' => $this->global_model->event_active['id'], 'city_id' => (int)$id));
						$participants = $this->participants_model->get_all();
						$this->groups_model->where(array('event_id' => $this->global_model->event_active['id'], 'city_id' => (int)$id));
						$groups = $this->groups_model->get_all();
						
						foreach ($participants as $participant)
							$emails[] = $participant['email'];
						
						foreach ($groups as $group)
							$emails[] = $group['email'];
					}
				}
				
				if (array_key_exists('churches', $data))
				{
					foreach ($data['churches'] as $id)
					{
						$this->churches_model->where(array('id' => (int)$id));
						$churches[] = $this->churches_model->get();
						
						$this->participants_model->where(array('event_id' => $this->global_model->event_active['id'], 'church_id' => (int)$id));
						$participants = $this->participants_model->get_all();
						$this->groups_model->where(array('event_id' => $this->global_model->event_active['id'], 'church_id' => (int)$id));
						$groups = $this->groups_model->get_all();
						
						foreach ($participants as $participant)
							$emails[] = $participant['email'];
						
						foreach ($groups as $group)
							$emails[] = $group['email'];
					}
				}

				
				$countries = $this->countries_model->get_all();
				
				foreach ($countries as $country)
				{
					$country_printed = false;
					$from_cities_printed = false;
					$from_churches_printed = false;
					 
					foreach ($cities as $city)
					{
						if ($country['id'] == $city['country_id'])
						{
							if (!$country_printed)
							{
								$recipients .= "<strong>" . $country['name'] . ":</strong><br />";
								$country_printed = true;	
							}
							
							if (!$from_cities_printed)
							{
								$recipients .= lang("messages-recipients-from-cities");
								$from_cities_printed = true;	
							}
							
							$recipients .= "&nbsp - " . $city['name'] . "<br />";				
						}	
					}
					
					foreach ($churches as $church)
					{
						if ($country['id'] == $church['country_id'])
						{
							if (!$country_printed)
							{
								$recipients .= "<strong>" . $country['name'] . ":</strong><br />";
								
								$country_printed = true;	
							}
							
							if (!$from_churches_printed)
							{
								$recipients .= lang("messages-recipients-from-churches");
								$from_churches_printed = true;	
							}
							
							$recipients .= "&nbsp - " . $church['name'] . "<br />";				
						}	
					}
					
					$recipients .= "<br />";	
				}
			}
			
			$data['recipients'] = $recipients;
			
			if (!($result = $this->messages_model->insert($data)))
				return $this->index(lang("message-save-error"));
			
			// e-mail body
			$emails = array_unique($emails, SORT_STRING);
			//print_r($emails);
			
			$this->email->clear(true);
			$this->email->from($site_user_email, $site_user_name);
			$this->email->reply_to($site_user_email, $site_user_name);
			$this->email->to($emails);

			$data['message_head'] = "<html>\n<head>\n<meta http-equiv=\"content-type\" content=\"text/html;charset=utf-8\" />\n<title>" . $site_name . "</title>\n</head>\n<body>\n";						
			$data['message_foot'] =	"\n</body>\n</html>";			
			$data['message'] = $data['message_head'] . $data['message'] . $data['message_foot'];	

			$this->email->subject($data['subject']);
			$this->email->message($data['message']);
			
			//echo $this->email->print_debugger();

			return $this->index(($this->email->send()) ? lang("message-send-success") : lang("message-send-error"));
			//return $this->index(lang("message-send-success"));
		}
		
		$countries = $this->countries_model->get_all();
		
		$this->cities_model->order_by(array('name' => "asc"));
		$cities = $this->cities_model->get_all();
		
		$this->churches_model->order_by(array('name' => "asc"));
		$churches = $this->churches_model->get_all();
		
		
		// delete countries where is not registered any participant
		foreach ($countries as $key => $country)
		{
			$this->participants_model->where(array('event_id' => $this->global_model->event_active['id'], 'country_id' => $country['id']));
			$participants_count = $this->participants_model->count_all();
			
			$this->groups_model->where(array('event_id' => $this->global_model->event_active['id'], 'country_id' => $country['id']));
			$groups_count = $this->groups_model->count_all();
			
			if (($participants_count < 1) && ($groups_count < 1))
				unset($countries[$key]);
		}
		
		// delete cities where is not registered any participant
		foreach ($cities as $key => $city)
		{
			$this->participants_model->where(array('event_id' => $this->global_model->event_active['id'], 'city_id' => $city['id']));
			$participants_count = $this->participants_model->count_all();
			
			$this->groups_model->where(array('event_id' => $this->global_model->event_active['id'], 'city_id' => $city['id']));
			$groups_count = $this->groups_model->count_all();
			
			if (($participants_count < 1) && ($groups_count < 1))
				unset($cities[$key]);
		}
		
		// delete churches where is not registered any participant
		foreach ($churches as $key => $church)
		{
			$this->participants_model->where(array('event_id' => $this->global_model->event_active['id'], 'church_id' => $church['id']));
			$participants_count = $this->participants_model->count_all();
			
			$this->groups_model->where(array('event_id' => $this->global_model->event_active['id'], 'church_id' => $church['id']));
			$groups_count = $this->groups_model->count_all();
			
			if (($participants_count < 1) && ($groups_count < 1))
				unset($churches[$key]);
		}
		
		
		if (count($countries) < 1)
			return $this->index(lang("message-no-participants"));
		
		// Generate messages data
		$messages_data = array
		(
			'caption' => lang("messages-add"),
			'action' => site_url($this->url . "send"),
			'buttons' => array($this->views_model->button_send("send"), $this->views_model->button_cancel($this->url)),
			'data' => $this->input->post('send') ? $this->input->post('data') : $this->messages_model->data(array('sort_order' => 0, 'status' => 1)),
			'countries' => $countries,
			'cities' => $cities,
			'churches' => $churches,
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $messages_data));
	}

	function view($id = NULL)
	{
		$this->messages_model->where(array('id' => $id, 'event_id' => $this->global_model->event_active['id']));		
		$message = $this->messages_model->get();
		
		if ($message == NULL)
			return $this->index(lang("message-not-exists"));
		
		$buttons = array();

		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
				$this->global_model->access_denied(lang("messages-view"));
				return;

			case RIGHTS_ACCESS_READ:
				$buttons = array($this->views_model->button_back($this->url));
				break;

			case RIGHTS_ACCESS_MANAGE_OWN:
				if ($message['admin_id'] != $this->global_model->admin['id'])
				{
					$this->global_model->access_denied(lang("messages-view"));
					return;	
				}
			case RIGHTS_ACCESS_MANAGE:
				$buttons = array($this->views_model->button_delete("delete"), $this->views_model->button_back($this->url));
				break;
		}
		
		$messages_data = array
		(
			'caption' => lang("messages-view"),
			'action' => site_url($this->url),
			'buttons' => $buttons,
			'data' => $message,
		);
		
		$this->views_model->back_office($this->url . 'view', array_merge($this->global, $messages_data));	
	}
}

/* End of file messages.php */
/* Location: ./system/application/controllers/admin/messages.php */
?>
