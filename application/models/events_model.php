<?php

class Events_model extends CI_Model
{
	var $fields = array();
	var $query;
	var $table = "events";
	
	function __construct()
   {
		// Call the Model constructor
		parent::__construct();
		
		// Prepare field data
		$fields = $this->db->field_data($this->table);

		foreach ($fields as $field)
		   $this->fields[$field->name] = $field->type;
		
		$this->load->model('Common_model', 'common_model');
		$this->load->model('News_model', 'news_model');
		$this->load->model('Messages_model', 'messages_model');
		$this->load->model('Participants_model', 'participants_model');
		$this->load->model('Groups_model', 'groups_model');
		$this->load->model('Photogalleries_model', 'photogalleries_model');
		$this->load->model('Videos_model', 'videos_model');
	}

	function data($data = array()) 
	{
		return array
		(
			'admin_id' => (array_key_exists('admin_id', $data)) ? (int)$data['admin_id'] : NULL,
			'active' => (array_key_exists('active', $data)) ? (int)$data['active'] : NULL,
			'name' => (array_key_exists('name', $data)) ? escape($data['name']) : NULL,
			'url' => (array_key_exists('name', $data)) ? url($data['name']) : NULL,
			'place' => (array_key_exists('place', $data)) ? escape($data['place']) : NULL,
			'place_map' => (array_key_exists('place_map', $data)) ? escape(prep_url($data['place_map'])) : prep_url(NULL),
			'gps_coordinates' => (array_key_exists('gps_coordinates', $data)) ? escape($data['gps_coordinates']) : NULL,
			'from_date' => (array_key_exists('from_date', $data)) ? (int)strtotime($data['from_date']) : NULL,
			'to_date' => (array_key_exists('to_date', $data)) ? (int)strtotime($data['to_date']) : NULL,
			'registration_from_date' => (array_key_exists('registration_from_date', $data)) ? (int)strtotime($data['registration_from_date']) : NULL,
			'registration_to_date' => (array_key_exists('registration_to_date', $data)) ? (int)strtotime($data['registration_to_date']) : NULL,
			'description' => (array_key_exists('description', $data)) ? $data['description'] : NULL,
			'meta_keywords' => (array_key_exists('meta_keywords', $data)) ? escape($data['meta_keywords']) : NULL,
			'meta_description' => (array_key_exists('meta_description', $data)) ? escape($data['meta_description']) : NULL,
		);
	}

	function insert($data)
	{
      return ($this->query = $this->common_model->_insert($this->table, $this->data($data)));
	}
	
	function update($id, $data)
	{		
      return ($this->query = $this->common_model->_update($this->table, $id, $this->data($data))); 
	}

	function delete($id)
	{
		return (($this->messages_model->delete_where(array('event_id' => (int)$id))) &&
				  ($this->participants_model->delete_where(array('event_id' => (int)$id))) &&
				  ($this->groups_model->delete_where(array('event_id' => (int)$id))) &&
				  ($this->photogalleries_model->delete_where(array('event_id' => (int)$id))) &&
				  ($this->videos_model->delete_where(array('event_id' => (int)$id))) &&
				  ($this->query = $this->db->delete($this->table, array('id' => (int)$id))));
	}
	
	function delete_where($where)
	{
		return ($this->query = $this->common_model->_delete_where($this->table, $where));
	}
	
	function order_by($order_by)
	{
		$this->common_model->_order_by($this->fields, $order_by);
	}
	
	function where($where)
	{
		$this->common_model->_where($this->fields, $where);
	}
	
	function get()
	{
		$this->query = $this->common_model->_get($this->table);
		return $this->query->row_array();
	}
	
	function get_all()
	{
		$this->query = $this->common_model->_get($this->table);
		return $this->query->result_array();
	}
	
	function count_all()
	{
		return ($this->query = $this->common_model->_count_all($this->table));
	}
}

?>
