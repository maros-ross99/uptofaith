<?php

class Participants_model extends CI_Model
{
	var $fields = array();
	var $query;
	var $table = "participants";
	
	function __construct()
   {
		// Call the Model constructor
		parent::__construct();
		
		// Prepare field data
		$fields = $this->db->field_data($this->table);

		foreach ($fields as $field)
		   $this->fields[$field->name] = $field->type;

		
		$this->load->model('Common_model', 'common_model');
	}

	function data($data = array()) 
	{
		return array
		(
			'event_id' => (array_key_exists('event_id', $data)) ? (int)$data['event_id'] : NULL,
			'registration_date' => (array_key_exists('registration_date', $data)) ? (int)$data['registration_date'] : NULL,
			'gender_id' => (array_key_exists('gender_id', $data)) ? (int)$data['gender_id'] : NULL,
			'name' => (array_key_exists('name', $data)) ? escape($data['name']) : NULL,
			'surname' => (array_key_exists('surname', $data)) ? escape($data['surname']) : NULL,
			'email' => (array_key_exists('email', $data)) ? escape($data['email']) : NULL,
			'country_id' => (array_key_exists('country_id', $data)) ? (int)$data['country_id'] : NULL,
			'city_id' => (array_key_exists('city_id', $data)) ? (int)$data['city_id'] : NULL,
			'church_id' => (array_key_exists('church_id', $data)) ? (int)$data['church_id'] : NULL,
			'note' => (array_key_exists('note', $data)) ? $data['note'] : NULL,
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
		return ($this->query = $this->common_model->_delete($this->table, $id));
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
	
	function filter($filter)
	{
		$this->common_model->_filter($this->fields, $filter);
	}
	
	function limit($count, $offset = 0)
	{
		$this->common_model->_limit($count, $offset);
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