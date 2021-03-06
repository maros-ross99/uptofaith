<?php

class Options_model extends CI_Model
{
	var $table = "options";

	function __construct()
   {
		// Call the Model constructor
		parent::__construct();
		
		$this->load->model('Common_model', 'common_model');
	}

	function data($data = array()) 
	{
		return array
		(
			'site_name' => (array_key_exists('site_name', $data)) ? escape($data['site_name']) : NULL,
			'site_title' => (array_key_exists('site_title', $data)) ? escape($data['site_title']) : NULL,
			'site_welcome_text' => (array_key_exists('site_welcome_text', $data)) ? $data['site_welcome_text'] : NULL,
			'site_about_text' => (array_key_exists('site_about_text', $data)) ? $data['site_about_text'] : NULL,
			'site_contact_text' => (array_key_exists('site_contact_text', $data)) ? $data['site_contact_text'] : NULL,
			'site_aside_text' => (array_key_exists('site_aside_text', $data)) ? $data['site_aside_text'] : NULL,
			'site_meta_keywords' => (array_key_exists('site_meta_keywords', $data)) ? escape($data['site_meta_keywords']) : NULL,
			'site_meta_description' => (array_key_exists('site_meta_description', $data)) ? escape($data['site_meta_description']) : NULL,
			'site_messages_name' => (array_key_exists('site_messages_name', $data)) ? escape($data['site_messages_name']) : NULL,
			'site_messages_email' => (array_key_exists('site_messages_email', $data)) ? escape($data['site_messages_email']) : NULL,
		);
	}
	
	function update($data)
	{		
		if (!is_array($data))
			return 0;

		foreach ($this->data($data) as $key => $value)
		{
			$id = $this->_get_id($key);
			 
			if ($id != NULL)
				$this->query = $this->common_model->_update($this->table, $id, array('value' => $value));
			else				
      		$this->query = $this->common_model->_insert($this->table, array('key' => $key, 'value' => $value));
      }
      return $this->query; 
	}
	
	function get_all()
	{
		$this->query = $this->db->get($this->table);
		$result_array = array();

		foreach ($this->query->result_array() as $key => $value)
			$result_array[$value['key']] = $value['value']; 

		return $result_array;
	}
	
	function _get_id($key)
	{
		$this->db->select("id");
		$this->query = $this->db->get_where($this->table, array('key' => escape($key)));
			
		$row_array = $this->query->row_array();

		if (!$row_array)
			return NULL;
			 
		return $row_array['id'];
	}
	
	function get($key)
	{
		$this->db->select("value");
		$this->query = $this->db->get_where($this->table, array('key' => escape($key)));
			
		$row_array = $this->query->row_array();

		if (!$row_array)
			return NULL;
			 
		return $row_array['value'];
	}
	
	function last_query()
	{
		return $this->query;	
	}
}
?>