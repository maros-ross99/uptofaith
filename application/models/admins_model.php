<?php

define("RIGHTS_NAME_STATISTICS", "stats");
define("RIGHTS_NAME_NEWS", "news");
define("RIGHTS_NAME_MESSAGES", "msgs");
define("RIGHTS_NAME_PARTICIPANTS", "partics");
define("RIGHTS_NAME_MEDIA", "media");
define("RIGHTS_NAME_EVENTS", "events");
define("RIGHTS_NAME_CATALOG", "catalog");
define("RIGHTS_NAME_ADMINS", "admins");
define("RIGHTS_NAME_OPTIONS", "options");

define("RIGHTS_TYPE_BASIC", 0);
define("RIGHTS_TYPE_EXTENDED", 1);
define("RIGHTS_TYPE_FULL", 2);

define("RIGHTS_ACCESS_NONE", 0);
define("RIGHTS_ACCESS_READ", 1);
define("RIGHTS_ACCESS_MANAGE_OWN", 3);
define("RIGHTS_ACCESS_MANAGE", 5);


class Admins_model extends CI_Model
{
	var $fields = array();
	var $query;
	var $table = "admins";
	var $rights_name = NULL;
	var $rights_type = NULL;
	
	function __construct()
   {
		// Call the Model constructor
		parent::__construct();
		
		// Prepare field data
		$fields = $this->db->field_data($this->table);

		foreach ($fields as $field)
		   $this->fields[$field->name] = $field->type;

		$this->load->model('Common_model', 'common_model');
		
		$this->lang->load('admin/admins');
		$this->lang->load('admin/menu');
		
		$this->rights_type = array
		(
			RIGHTS_TYPE_BASIC => array
			(
				RIGHTS_ACCESS_NONE => lang("admins-rights-access-none"),
				RIGHTS_ACCESS_READ => lang("admins-rights-access-read"),
			),

			RIGHTS_TYPE_EXTENDED => array
			(
				RIGHTS_ACCESS_NONE => lang("admins-rights-access-none"),
				RIGHTS_ACCESS_READ => lang("admins-rights-access-read"),
				RIGHTS_ACCESS_MANAGE => lang("admins-rights-access-manage"),
			),

			RIGHTS_TYPE_FULL => array
			(
				RIGHTS_ACCESS_NONE => lang("admins-rights-access-none"),
				RIGHTS_ACCESS_READ => lang("admins-rights-access-read"),
				RIGHTS_ACCESS_MANAGE_OWN => lang("admins-rights-access-manage-own"),
				RIGHTS_ACCESS_MANAGE => lang("admins-rights-access-manage"),
			),
		);		
		
		$this->rights = array
		(
			RIGHTS_NAME_STATISTICS => array('name' => lang("menu-statistics"), 'type' => RIGHTS_TYPE_BASIC),
			RIGHTS_NAME_NEWS => array('name' => lang("menu-news"), 'type' => RIGHTS_TYPE_FULL),
			RIGHTS_NAME_MESSAGES =>	array('name' => lang("menu-messages"), 'type' => RIGHTS_TYPE_FULL),
			RIGHTS_NAME_PARTICIPANTS => array('name' => lang("menu-participants"), 'type' => RIGHTS_TYPE_EXTENDED),
			RIGHTS_NAME_MEDIA => array('name' => lang("menu-media"), 'type' => RIGHTS_TYPE_FULL),
			RIGHTS_NAME_EVENTS => array('name' => lang("menu-events"), 'type' => RIGHTS_TYPE_FULL),
			RIGHTS_NAME_CATALOG => array('name' => lang("menu-catalog"), 'type' => RIGHTS_TYPE_EXTENDED),
			RIGHTS_NAME_ADMINS => array('name' => lang("menu-admins"), 'type' => RIGHTS_TYPE_EXTENDED),
			RIGHTS_NAME_OPTIONS => array('name' => lang("menu-options"), 'type' => RIGHTS_TYPE_EXTENDED),
		);
	}
	
	function rights_unpack($rights)
	{
		if (!is_string($rights))
			return false;
			
		parse_str($rights, $result);
		return $result;
	}
	
	function rights_pack($rights)
	{
		if (!is_array($rights))
			return false;

		return http_build_query($rights);
	}

	function data($data = array()) 
	{
		return array
		(
			'name' => (array_key_exists('name', $data)) ? escape($data['name']) : NULL,
			'username' => (array_key_exists('username', $data)) ? escape($data['username']) : NULL,
			'password' => (array_key_exists('password', $data)) ? $data['password'] : NULL,
			'email' => (array_key_exists('email', $data)) ? escape($data['email']) : NULL,
			'rights' => (array_key_exists('rights', $data)) ? $data['rights'] : array
				(
					RIGHTS_NAME_STATISTICS => RIGHTS_ACCESS_NONE,
					RIGHTS_NAME_NEWS => RIGHTS_ACCESS_NONE, 
					RIGHTS_NAME_MESSAGES => RIGHTS_ACCESS_NONE, 
					RIGHTS_NAME_PARTICIPANTS => RIGHTS_ACCESS_NONE, 
					RIGHTS_NAME_MEDIA => RIGHTS_ACCESS_NONE, 
					RIGHTS_NAME_EVENTS => RIGHTS_ACCESS_NONE, 
					RIGHTS_NAME_CATALOG => RIGHTS_ACCESS_NONE, 
					RIGHTS_NAME_ADMINS => RIGHTS_ACCESS_NONE, 
					RIGHTS_NAME_OPTIONS => RIGHTS_ACCESS_NONE, 
				),
			'last_login' => (array_key_exists('last_login', $data)) ? (int)$data['last_login'] : NULL,
			'last_login2' => (array_key_exists('last_login2', $data)) ? (int)$data['last_login2'] : NULL,
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
	
	function count_all()
	{
		return ($this->query = $this->common_model->_count_all($this->table));
	}
}
?>
