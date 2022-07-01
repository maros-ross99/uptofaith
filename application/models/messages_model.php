<?php

class Messages_model extends CI_Model
{
	var $fields = array();
	var $query;
	var $table = "messages";
	
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
			'admin_id' => (array_key_exists('admin_id', $data)) ? (int)$data['admin_id'] : NULL,
			'event_id' => (array_key_exists('event_id', $data)) ? (int)$data['event_id'] : NULL,
			'date' => (array_key_exists('date', $data)) ? (int)$data['date'] : NULL,
			'recipients' => (array_key_exists('recipients', $data)) ?  $data['recipients'] : NULL,
			'subject' => (array_key_exists('subject', $data)) ? escape($data['subject']) : NULL,
			'message' => (array_key_exists('message', $data)) ? $data['message'] : NULL,
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
}
?>