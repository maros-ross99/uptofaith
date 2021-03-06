<?php

class News extends CI_Controller
{
	var $url = "admin/news/";
	var $session_order_by = "news_order_by";
	var $global = NULL;
	var $access = NULL;
	
	function __construct()
	{
		parent::__construct();	

		$this->load->model('Global_model', 'global_model');
		$this->global = $this->global_model->get_global();
		
		$this->access = $this->global_model->admin['rights'][RIGHTS_NAME_NEWS];
		
		if (!$this->session->userdata($this->session_order_by))
			$this->session->set_userdata($this->session_order_by, array('date' => 'desc'));

		$this->lang->load('admin/info_messages');
		$this->lang->load('admin/news');		
	}
	
	function delete($ids = NULL)
	{
		if ((!is_array($ids)) && ($this->input->post('ids') == NULL))	
			return $this->index(lang("message-not-exists"));
			
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("news-delete"));
				return;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
				foreach ($ids as $id)
				{
					$this->news_model->where(array('id' => (int)$id));
					$new = $this->news_model->get();

					if (($new != NULL) && ($new['admin_id'] != $this->global_model->admin['id']))
					{
						$this->global_model->access_denied(lang("news-delete"));
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
				if (!$this->news_model->delete($id))
					return $this->index(lang("message-delete-error"));

			return $this->index(lang("message-delete-success"));
		}
		
		$data = array
		(
			'caption' => lang("news-delete"),
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
				$this->global_model->access_denied(lang('news'));
				return;
								
			case RIGHTS_ACCESS_READ:
				$anchors = array($this->views_model->anchor_view($this->url . "view/"));
				$buttons = array();
				break;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
				$this->news_model->where(array('admin_id' => (int)$this->global_model->admin['id']));
			case RIGHTS_ACCESS_MANAGE:
			default:
				$anchors = array($this->views_model->anchor_view($this->url . "view/"), $this->views_model->anchor_edit($this->url . "edit/"));
				$buttons = array($this->views_model->button_add($this->url . "add/"), $this->views_model->button_delete_all("delete"));
				break;
		}
		
		$this->news_model->order_by($this->session->userdata($this->session_order_by));
		$news = $this->news_model->get_all();

		$data = array
		(
			'caption' => lang('news'),
			'action' => site_url($this->url),
			'news' => $news,
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
		$this->form_validation->set_rules('data[title]', lang("news-title"), 'trim|required');
		$this->form_validation->set_rules('data[content]', lang("news-content"), 'trim|callback_content_check');
		
		return;
	}
	
	public function content_check($content)
	{
		if (strip_tags(unescape($content)) == NULL)
		{
			$this->form_validation->set_message("content_check", lang("required"));
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
				$this->global_model->access_denied(lang("news-add"));
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
			$data['admin_id'] = $this->global_model->admin['id'];
			
			$result = $this->news_model->insert($data);

							
			return $this->index(($result) ? lang("message-add-success") : lang("message-add-error"));
		}
		
		$this->events_model->order_by(array('date_from' => "asc"));
		$events = $this->events_model->get_all();
		
		// Generate news data
		$news_data = array
		(
			'caption' => lang("news-add"),
			'action' => site_url($this->url . "add/"),
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_cancel($this->url)),
			'data' => $this->input->post('save') ? $this->input->post('data') : $this->news_model->data(array('visible' => true)),
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $news_data));
	}
	
	function edit($id = NULL)
	{
		$this->news_model->where(array('id' => $id));
		$new = $this->news_model->get();
		
		if ($new == NULL)
			return $this->index(lang("message-not-exists"));
		
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
			case RIGHTS_ACCESS_READ:
				$this->global_model->access_denied(lang("news-edit"));
				return;
				
			case RIGHTS_ACCESS_MANAGE_OWN:
				if ($new['admin_id'] != $this->global_model->admin['id'])
				{
					$this->global_model->access_denied(lang("news-edit"));
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
				
			$result = $this->news_model->update($id, array_merge($new, $data));
				
			return $this->index(($result) ? lang("message-edit-success") : lang("message-edit-error"));
		}
		
		// Generate news data		
		$news_data = array
		(
			'caption' => lang("news-edit"),
			'action' => site_url($this->url . "edit/" . $id),
			'buttons' => array($this->views_model->button_save("save"), $this->views_model->button_reset(), $this->views_model->button_back($this->url)),
			'data' => ($this->input->post('save')) ? $this->input->post('data') : $new,
		);
		
		$this->views_model->back_office($this->url . 'form', array_merge($this->global, $news_data));	
	}
	
	function view($id = NULL)
	{
		$this->news_model->where(array('id' => $id));
		$new = $this->news_model->get();
		
		if ($new == NULL)
			return $this->index(lang("message-not-exists"));

		$buttons = array();

		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
				$this->global_model->access_denied(lang("news-view"));
				return;

			case RIGHTS_ACCESS_READ:
				$buttons = array($this->views_model->button_back($this->url));
				break;

			case RIGHTS_ACCESS_MANAGE_OWN:
				if ($new['admin_id'] != $this->global_model->admin['id'])
				{
					$this->global_model->access_denied(lang("news-view"));
					return;	
				}
			case RIGHTS_ACCESS_MANAGE:
				$buttons = array($this->views_model->button_edit($this->url . "edit/" . $id), $this->views_model->button_delete("delete"), $this->views_model->button_back($this->url));
				break;
		}
		
		$news_data = array
		(
			'caption' => lang("news-view"),
			'action' => site_url($this->url),
			'buttons' => $buttons,
			'data' => $new,
		);
		
		$this->views_model->back_office($this->url . 'view', array_merge($this->global, $news_data));	
	}
}

/* End of file news.php */
/* Location: ./system/application/controllers/admin/news.php */
?>
