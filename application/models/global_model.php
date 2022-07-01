<?php
class Global_model extends CI_Model
{
	var $global = NULL;
	var $site_config = NULL;
	var $gender = NULL;
	var $pagination = NULL;
	var $events = NULL;
	var $event_active = NULL;
	var $session_event_active = "event_active";
	var $datetime_format = NULL;
	var $admin = NULL;
	
	function __construct()
   {
		// Call the Model constructor
		parent::__construct();

		$this->load->model('Admins_model', 'admins_model');
		$this->load->model('News_model', 'news_model');
		$this->load->model('Messages_model', 'messages_model');
		$this->load->model('Views_model', 'views_model');
		$this->load->model('Options_model', 'options_model');
		$this->load->model('Events_model', 'events_model');
		$this->load->model('Countries_model', 'countries_model');
		$this->load->model('Cities_model', 'cities_model');
		$this->load->model('Churches_model', 'churches_model');
		$this->load->model('News_model', 'news_model');
		$this->load->model('Participants_model', 'participants_model');
		$this->load->model('Groups_model', 'groups_model');
		$this->load->model('Photogalleries_model', 'photogalleries_model');
		$this->load->model('Videos_model', 'videos_model');
		$this->load->model('Authentication_model', 'authentication_model');
		
		$this->load->library('pagination');
		
		$this->lang->load('admin/general');
				
		$this->authentication_model->is_authenticated();
		
		// save admins data
		$this->admin = $this->session->userdata(SESSION_NAME_ADMIN);
		$this->admin = array_merge($this->admins_model->data(), $this->admin);

		// get events by rights
		$this->events = array();		

		switch ($this->admin['rights'][RIGHTS_NAME_EVENTS])
		{
			case RIGHTS_ACCESS_NONE:
				break;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
				$this->events_model->where(array('admin_id' => (int)$this->admin['id']));
			case RIGHTS_ACCESS_READ:
			case RIGHTS_ACCESS_MANAGE:
			default:
				$this->events_model->order_by(array('date_from' => "asc"));
				$this->events = $this->events_model->get_all();
				break;
		}


		// get active event
		$this->event_active = $this->session->userdata($this->session_event_active);

		if (!$this->check_event_active($this->event_active))
		{
			if (count($this->events) > 0)
			{
				$this->set_event_active($this->events[0]['id']);
			}
			else
			{
				$this->event_active = array_merge($this->events_model->data(), array('id' => 0));
			}
		}
		else
		{
			$this->event_active = array_merge($this->events_model->data(), $this->event_active);
		}
		
		
		// get site config
		$this->site_config = $this->config->item('site');
		$this->datetime_format = $this->site_config['datetime_format'];
		
		
		// PAGINATION		
		$this->pagination['full_tag_open'] = "<div id=\"pagination\"><ul>"; 
		$this->pagination['full_tag_close'] = "</ul></div>";
		
		$this->pagination['first_link'] = "&laquo;";
		$this->pagination['first_tag_open'] = "<li>"; 
		$this->pagination['first_tag_close'] = "</li>";
		
		$this->pagination['last_link'] = "&raquo;";
		$this->pagination['last_tag_open'] = "<li>";
		$this->pagination['last_tag_close'] = "</li>";
		
		$this->pagination['next_link'] = "&rsaquo;";
		$this->pagination['next_tag_open'] = "<li>"; 
		$this->pagination['next_tag_close'] = "</li>";
		
		$this->pagination['prev_link'] = "&lsaquo;";
		$this->pagination['prev_tag_open'] = "<li>";
		$this->pagination['prev_tag_close'] = "</li>";
		
		$this->pagination['num_tag_open'] = "<li>";
		$this->pagination['num_tag_close'] = "</li>"; 
		$this->pagination['cur_tag_open'] = "<li><a style=\"color:#000;\" href=\"\">";
		$this->pagination['cur_tag_close'] = "</a></li>";
		
		$this->pagination['per_page'] = $this->site_config['items_per_page']; 
		$this->pagination['uri_segment'] = 4;
		
		$this->gender = array(1 => $this->lang->line('gender-men'),	2 => $this->lang->line('gender-women'));		
		
		$this->global = array
		(
			'events' => $this->events,		
			'event_active' => $this->event_active,
			'datetime_format' => $this->datetime_format,
			'gender' => $this->gender,
			'admin' => $this->admin,
		);
	}
	
	function get_event_active($index)
	{
		if ($index == NULL)
			return $this->event_active;

		if (!array_key_exists($index, $this->event_active))
			return NULL;
			
		return $this->event_active[$index];
	}	

	function check_event_active($event)
	{
		if (!is_array($event))
			return false;

		$this->events_model->where(array('id' => (int)$event['id']));
		$event_check = $this->events_model->get();

		if ($event_check == NULL)
			return false;

		return true;
	}

	function set_event_active($id)
	{
		$this->events_model->where(array('id' => (int)$id));
		$event = $this->events_model->get();

		if ($event == NULL)
			return;

		$this->event_active = $event;
		$this->global['event_active'] = $event;
		$this->session->set_userdata($this->session_event_active, $event);
	}
	
	function access_denied($caption)
	{
		$this->views_model->back_office("admin/access_denied", array_merge($this->global, array('caption' => $caption)));
	}
	
	function get_global()
	{
		return $this->global;	
	}	

}
?>