<?php

class Photogalleries extends CI_Controller
{
	var $url = "admin/photogalleries/";
	var $session_order_by = "photogalleries_order_by";
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
		$this->lang->load('admin/photogalleries');
	}
	
	function delete($ids = NULL)
	{
		if ((!is_array($ids)) && ($this->input->post('ids') == NULL))
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("photogalleries-delete"));
				return;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
				foreach ($ids as $id)
				{
					$this->photogalleries_model->where(array('id' => (int)$id));
					$photogallery = $this->photogalleries_model->get();

					if (($photogallery != NULL) && ($photogallery['admin_id'] != $this->global_model->admin['id']))
					{
						$this->global_model->access_denied(lang("photogalleries-delete"));
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
				if (!$this->photogalleries_model->delete($id))
					return $this->index(lang("message-delete-error"));

			return $this->index(lang("message-delete-success"));
		}
		
		$data = array
		(
			'caption' => lang("photogalleries-delete"),
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
				$this->photogalleries_model->where(array('admin_id' => (int)$this->global_model->admin['id']));
			case RIGHTS_ACCESS_MANAGE:
			default:
				$anchors = array($this->views_model->anchor_view($this->url . "view/"), $this->views_model->anchor_edit($this->url . "edit/"), $this->views_model->anchor_add_photos($this->url . "upload/"));
				$buttons = array($this->views_model->button_add($this->url . "add"), $this->views_model->button_delete_all("delete"));
				break;
		}

		$this->photogalleries_model->order_by($this->session->userdata($this->session_order_by));
		$this->photogalleries_model->where(array('event_id' => $this->global_model->event_active['id']));
		$photogalleries = $this->photogalleries_model->get_all();

		$data = array
		(
			'caption' => lang("photogalleries"),
			'action' => site_url($this->url),
			'photogalleries' => $photogalleries,
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
		$this->form_validation->set_rules('data[name]', lang("photogalleries-name"), 'trim|required');
		$this->form_validation->set_rules('data[description]', lang("photogalleries-description"), 'trim');
		
		return;
	}
	
	function upload($id = NULL)
	{			
		$this->photogalleries_model->where(array('id' => $id, 'event_id' => $this->global_model->event_active['id']));		
		$photogallery = $this->photogalleries_model->get();
		
		if ($photogallery == NULL)	
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("photogalleries-edit"));
				return;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
				if ($photogallery['admin_id'] != $this->global_model->admin['id'])
				{
					$this->global_model->access_denied(lang("photogalleries-edit"));
					return;	
				}

				break;
			
			case RIGHTS_ACCESS_MANAGE:
				break;
		}

		$message = "";

		// Save data
		if (($this->input->post('save')) && (array_key_exists("photos", $_FILES)) && (count($_FILES['photos']) > 0))
		{
			if (!$this->photogalleries_model->save_photos($id, $_FILES['photos']))
			{
				if ($this->photogalleries_model->upload_error != NULL)
					$message = $this->photogalleries_model->upload_error;
				else
					$message = lang("message-upload-error");
			}
			else
			{
				$message = lang("message-upload-success");
			}
		}
		
		// Generate photogalleries data
		$photogalleries_data = array
		(
			'caption' => lang("photogalleries-add-photos"),
			'action' => site_url($this->url . "upload/" . $id),
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_back($this->url)),
			'data' => $photogallery,
			'photos' => $this->photogalleries_model->get_photos($id),
			'message' => $message,
		);
		
		$this->views_model->back_office($this->url . 'upload', array_merge($this->global, $photogalleries_data));
	}

	function add()
	{
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("photogalleries-add"));
				return;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
			case RIGHTS_ACCESS_MANAGE:
			default:
				break;
		}
		
		if (count($this->global_model->events) < 1)
			return $this->index(lang("message-no-events"));

		$this->_form_validation_set_rules();

		// Save data
		if ($this->input->post('save') && $this->form_validation->run('form'))
		{
			$data = $this->input->post('data');
			$data['date'] = time();
			$data['event_id'] = $this->global_model->event_active['id'];
			$data['admin_id'] = $this->global_model->admin['id'];
			
			$result = $this->photogalleries_model->insert($data);

			if (($result != false) && (!empty($data['news_add'])))
			{
				$new_data = array
				(
					'date' => $data['date'],
					'title' => lang("photogalleries-news-title"),
					'content' => sprintf(lang("photogalleries-news-content"), $data['name']),
				);
				
				$this->news_model->insert($new_data);
			}			
			
			return $this->index(($result) ? lang("message-add-success") : lang("message-add-error"));
		}
		
		// Generate photogalleries data
		$photogalleries_data = array
		(
			'caption' => lang("photogalleries-add"),
			'action' => site_url($this->url . "add"),
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_cancel($this->url)),
			'data' => $this->input->post('save') ? $this->input->post('data') : array_merge($this->photogalleries_model->data(), array('news_add' => "1")),
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $photogalleries_data));
	}
	
	function edit($id = NULL)
	{	
		$this->photogalleries_model->where(array('id' => $id, 'event_id' => $this->global_model->event_active['id']));		
		$photogallery = $this->photogalleries_model->get();
		
		if ($photogallery == NULL)	
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("photogalleries-edit"));
				return;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
				if ($photogallery['admin_id'] != $this->global_model->admin['id'])
				{
					$this->global_model->access_denied(lang("photogalleries-edit"));
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
			
			$result = $this->photogalleries_model->update($id, array_merge($photogallery, $data));

			// Delete photogallery photos					
			if (($result != true) || ((array_key_exists('photos', $data)) && (!$this->photogalleries_model->delete_photos($id, $data['photos']))))
				return $this->index(lang("message-edit-error"));
			else
				return $this->index(lang("message-edit-success"));
		}
		
		// Generate photogalleries data		
		$photogalleries_data = array
		(
			'caption' => lang("photogalleries-edit"),
			'action' => site_url($this->url . "edit/" . $id),
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_reset(), $this->views_model->button_back($this->url)),
			'data' => ($this->input->post('save')) ? $this->input->post('data') : $photogallery,
			'photos' => $this->photogalleries_model->get_photos($id),
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $photogalleries_data));	
	}
	
	function view($id = NULL)
	{
		$this->photogalleries_model->where(array('id' => $id, 'event_id' => $this->global_model->event_active['id']));		
		$photogallery = $this->photogalleries_model->get();
		
		if ($photogallery == NULL)
			return $this->index(lang("message-not-exists"));
			
		$buttons = array();

		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
				$this->global_model->access_denied(lang("photogalleries-view"));
				return;

			case RIGHTS_ACCESS_READ:
				$buttons = array($this->views_model->button_back($this->url));
				break;

			case RIGHTS_ACCESS_MANAGE_OWN:
				if ($photogallery['admin_id'] != $this->global_model->admin['id'])
				{
					$this->global_model->access_denied(lang("photogalleries-view"));
					return;	
				}
			case RIGHTS_ACCESS_MANAGE:
				$buttons = array($this->views_model->button_add_photos($this->url . "upload/" . $id), $this->views_model->button_edit($this->url . "edit/" . $id), $this->views_model->button_delete("delete"), $this->views_model->button_back($this->url));
				break;
		}
		
		$photogalleries_data = array
		(
			'caption' => lang("photogalleries-view"),
			'action' => site_url($this->url),
			'buttons' => $buttons,
			'data' => $photogallery,
			'photos' => $this->photogalleries_model->get_photos($id),
		);
		
		$this->views_model->back_office($this->url . 'view', array_merge($this->global, $photogalleries_data));	
	}
}

/* End of file photogalleries.php */
/* Location: ./system/application/controllers/admin/photogalleries.php */
?>
