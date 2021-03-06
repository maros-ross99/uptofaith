<?php

class Videos extends CI_Controller
{
	var $url = "admin/videos/";
	var $session_order_by = "videos_order_by";
	var $global = NULL;
	var $access = NULL;
	
	function __construct()
	{
		parent::__construct();	

		$this->load->model('Global_model', 'global_model');
		$this->global = $this->global_model->get_global();
		$this->access = $this->global_model->admin['rights'][RIGHTS_NAME_MEDIA];
		
		if (!$this->session->userdata($this->session_order_by))
			$this->session->set_userdata($this->session_order_by, array('date' => 'desc'));
			
		$this->lang->load('admin/info_messages');
		$this->lang->load('admin/videos');
	}
	
	function delete($ids = NULL)
	{
		if ((!is_array($ids)) && ($this->input->post('ids') == NULL))
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("videos-delete"));
				return;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
				foreach ($ids as $id)
				{
					$this->videos_model->where(array('id' => (int)$id));
					$video = $this->videos_model->get();

					if (($video != NULL) && ($video['admin_id'] != $this->global_model->admin['id']))
					{
						$this->global_model->access_denied(lang("videos-delete"));
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
				if (!$this->videos_model->delete($id))
					return $this->index(lang("message-delete-error"));

			return $this->index(lang("message-delete-success"));
		}
		
		$data = array
		(
			'caption' => lang("videos-delete"),
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
				$this->global_model->access_denied(lang('photogalleries'));
				return;
								
			case RIGHTS_ACCESS_READ:
				$anchors = array($this->views_model->anchor_view($this->url . "view/"));
				$buttons = array();
				break;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
				$this->videos_model->where(array('admin_id' => (int)$this->global_model->admin['id']));
			case RIGHTS_ACCESS_MANAGE:
			default:
				$anchors = array($this->views_model->anchor_view($this->url . "view/"), $this->views_model->anchor_edit($this->url . "edit/"));
				$buttons = array($this->views_model->button_add($this->url . "add"), $this->views_model->button_delete_all("delete"));
				break;
		}

		$this->videos_model->order_by($this->session->userdata($this->session_order_by));
		$this->videos_model->where(array('event_id' => $this->global_model->event_active['id']));
		$videos = $this->videos_model->get_all();

		$data = array
		(
			'caption' => lang("videos"),
			'action' => site_url($this->url),
			'videos' => $videos,
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
		$this->form_validation->set_rules('data[name]', lang("videos-name"), 'trim|required');
		$this->form_validation->set_rules('data[code]', lang("videos-code"), 'trim');
		$this->form_validation->set_rules('data[description]', lang("videos-description"), 'trim');		
		
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
				$this->global_model->access_denied(lang("videos-add"));
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
			$data['date'] = time();
			$data['event_id'] = $this->global_model->event_active['id'];
			$data['admin_id'] = $this->global_model->admin['id'];
			
			$result = $this->videos_model->insert($data);
			
			if (($result != false) && (!empty($data['news_add'])))
			{
				$new_data = array
				(
					'date' => $data['date'],
					'title' => lang("videos-news-title"),
					'content' => sprintf(lang("videos-news-content"), $data['name']),
				);
				
				$this->news_model->insert($new_data);
			}

			return $this->index(($result) ? lang("message-add-success") : lang("message-add-error"));
		}
		
		// Generate videos data
		$videos_data = array
		(
			'caption' => lang("videos-add"),
			'action' => site_url($this->url . "add"),
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_cancel($this->url)),
			'data' => $this->input->post('save') ? $this->input->post('data') : array_merge($this->videos_model->data(array('visible' => 1)), array('news_add' => "1")),
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $videos_data));
	}
	
	function edit($id = NULL)
	{
		$this->videos_model->where(array('id' => $id, 'event_id' => $this->global_model->event_active['id']));		
		$video = $this->videos_model->get();
		
		if ($video == NULL)	
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("videos-edit"));
				return;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
				if ($video['admin_id'] != $this->global_model->admin['id'])
				{
					$this->global_model->access_denied(lang("videos-edit"));
					return;	
				}

				break;
			
			case RIGHTS_ACCESS_MANAGE:
				break;
		}
			
		$this->_form_validation_set_rules();
		
		// Edit data		
		if ($this->input->post('save') && $this->form_validation->run('form'))
		{
			$data = $this->input->post('data');
			
			if (!array_key_exists("visible", $data))
				$data['visible'] = 0;
				
			$result = $this->videos_model->update($id, array_merge($video, $data));
				
			return $this->index(($result) ? lang("message-edit-success") : lang("message-edit-error"));
		}
		
		// Generate videos data		
		$videos_data = array
		(
			'caption' => lang("videos-edit"),
			'action' => site_url($this->url . "edit/" . $id),
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_reset(), $this->views_model->button_back($this->url)),
			'data' => ($this->input->post('save')) ? $this->input->post('data') : $video,
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $videos_data));	
	}
	
	function view($id = NULL)
	{
		$this->videos_model->where(array('id' => $id, 'event_id' => $this->global_model->event_active['id']));		
		$video = $this->videos_model->get();
		
		if ($video == NULL)
			return $this->index(lang("message-not-exists"));
			
		$buttons = array();

		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
				$this->global_model->access_denied(lang("videos-view"));
				return;

			case RIGHTS_ACCESS_READ:
				$buttons = array($this->views_model->button_back($this->url));
				break;

			case RIGHTS_ACCESS_MANAGE_OWN:
				if ($video['admin_id'] != $this->global_model->admin['id'])
				{
					$this->global_model->access_denied(lang("videos-view"));
					return;	
				}
			case RIGHTS_ACCESS_MANAGE:
				$buttons = array($this->views_model->button_edit($this->url . "edit/" . $id), $this->views_model->button_delete("delete"), $this->views_model->button_back($this->url));
				break;
		}
	
		$videos_data = array
		(
			'caption' => lang("videos-view"),
			'action' => site_url($this->url),
			'buttons' => $buttons,
			'data' => $video,
		);
		
		$this->views_model->back_office($this->url . 'view', array_merge($this->global, $videos_data));	
	}
}

/* End of file videos.php */
/* Location: ./system/application/controllers/admin/videos.php */
?>
