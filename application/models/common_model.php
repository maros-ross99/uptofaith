<?php

class Common_model extends CI_Model
{
	var $operators = array("!=", "=<", ">=", "<", ">", " ");
	
	function __construct()
   {
		// Call the Model constructor
		parent::__construct();
	}

	function _insert($table, $data)
	{
		if (!is_array($data))
			return 0;

      return $this->db->insert($table, $data);
	}
	
	function _update($table, $id, $data)
	{		
		if (!is_array($data))
			return 0;

      return $this->db->update($table, $data, array('id' => (int)$id)); 
	}

	function _delete($table, $id)
	{
		return $this->db->delete($table, array('id' => (int)$id));
	}
	
	function _delete_where($table, $where)
	{
		if (!is_array($where))
			return;
			
		return $this->db->delete($table, $where);
	}
	
	function _order_by($fields, $order_by)
	{
		if (!is_array($order_by))
			return;
			
		foreach ($order_by as $key => $value)
			if (array_key_exists($key, $fields))
				$this->db->order_by($key, escape($value));
	}
	
	function _where($fields, $where)
	{
		if (!is_array($where))
			return;
			
		foreach ($where as $key => $value)
			if (array_key_exists(str_replace($this->operators, "", $key), $fields))
				$this->db->where($key, escape($value));
	}
	
	function _or_where($fields, $where)
	{
		if (!is_array($where))
			return;
			
		foreach ($where as $key => $value)
			if (array_key_exists(str_replace($this->operators, "", $key), $fields))
				$this->db->or_where($key, escape($value));
	}
	
	function _filter($fields, $filter)
	{
		if (!is_array($filter))
			return;

		foreach ($filter as $key => $value)
		{
			$value = trim($value);

			if (($value == "") || !array_key_exists($key, $fields))
				continue;				

			if ($fields[$key] == "int")
				$this->db->where($key, escape($value));
			else
				$this->db->like($key, escape($value));
		}
	}
	
	function _limit($count, $offset = 0)
	{
		if ($offset)
			$this->db->limit($count, $offset);
		else
			$this->db->limit($count);	
	}
	
	function _get($table)
	{
		return $this->db->get($table);
	}
	
	function _count_all($table)
	{
		$this->db->from($table);
		return $this->db->count_all_results();
	}
}
?>