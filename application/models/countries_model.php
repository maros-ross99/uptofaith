<?php

class Countries_model extends CI_Model
{
	var $fields = array();
	var $query;
	var $table = "countries";
	
	function __construct()
   {
		// Call the Model constructor
		parent::__construct();
		
		// Prepare field data
		$fields = $this->db->field_data($this->table);

		foreach ($fields as $field)
		   $this->fields[$field->name] = $field->type;
		
		
		$this->load->model('Cities_model', 'cities_model');
		$this->load->model('Churches_model', 'churches_model');
		$this->load->model('Common_model', 'common_model');
	}

	function data($data = array()) 
	{
		return array
		(
			'name' => (array_key_exists('name', $data)) ? escape($data['name']) : NULL,
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
		return (($this->cities_model->delete_where(array('country_id' => (int)$id))) && 
				  ($this->churches_model->delete_where(array('country_id' => (int)$id))) &&
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