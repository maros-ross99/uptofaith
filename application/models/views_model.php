<?php
class Views_model extends CI_Model
{
	var $error_start_delimiter = "<div class=\"msg-error\">";
	var $error_end_delimiter = "</div>";
	
	function __construct()
   {
		// Call the Model constructor
		parent::__construct();
		
		$this->load->model('Authentication_model', 'authentication_model');
		$this->load->model('Options_model', 'options_model');
		
		$this->form_validation->set_error_delimiters($this->error_start_delimiter, $this->error_end_delimiter);
		
		$this->lang->load('admin/general');
		$this->lang->load('admin/menu');
	}

	function back_office($page, $data = NULL)
	{
		$is_authenticated = $this->authentication_model->is_authenticated(false);
		
		$this->load->view('admin/header', $data);		
		
		if ($is_authenticated)
			$this->load->view('admin/menu', $data);
			
		$this->load->view('admin/box');
			
		if (array_key_exists("buttons", $data))
			$this->load->view('admin/buttons', $data);

		$this->load->view('admin/content');
		$this->load->view($page, $data);
			
		$this->load->view('admin/footer');	
		
		return;	
	}
	
	function front_office($page, $data)
	{		
		$this->load->view('header', $data);
		$this->load->view($page, $data);
		
		//if (array_key_exists("products", $data) && !empty($data['products']))
			//$this->load->view('products', $data);
			
		$this->load->view('footer', $data);	
		
		return;
	}

	function button_login_anchor($action)
	{
		return "<input type='button' name='" . $action . "' class='button button-login' value='" . lang("login") . "'>";
	}	
	
	function button_login($name)
	{
		return "<input type='submit' class='button button-login' name='" . $name . "' value='" . lang("login") . "'>";
	}
	
	function button_main_page($action)
	{
		return "<input type='button' name='" . $action . "' class='button button-main-page' value='" . lang("main-page") . "'>";	
	}
	
	function button_send_anchor($action)
	{
		return "<input type='button' name='" . $action . "' class='button button-add' value='" . lang("send") . "'>";
	}
	
	function button_send($name)
	{
		return "<input type='submit' class='button button-login' name='" . $name . "' value='" . lang("send") . "'>";
	}
	
	function button_add($action)
	{
		return "<input type='button' name='" . $action . "' class='button button-add' value='" . lang("add") . "'>";
	}
	
	function button_add_photos($action)
	{
		return "<input type='button' name='" . $action . "' class='button button-add' value='" . lang("add-photos") . "'>";
	}
	
	function button_delete($name)
	{
		return "<input type='submit' class='button button-delete' name='" . $name . "' value='" . lang("delete") . "'>";	
	}
	
	function button_delete_all($name)
	{
		return "<input type='submit' class='button button-delete' name='" . $name . "' value='" . lang("delete") . "'>";	
	}
	
	function button_save($name)
	{
		return "<input type='submit' class='button button-save' name='" . $name . "' value='" . lang("save") . "'>";
	}
	
	function button_edit($action)
	{
		return "<input type='button' name='" . $action . "' class='button button-edit' value='" . lang("edit") . "'>";
	}
	
	function button_cancel($action)
	{
		return "<input type='button' name='" . $action . "' class='button button-cancel' value='" . lang("cancel") . "'>";
	}
	
	function button_reset()
	{
		return "<input type='reset' class='button button-reset' value='" . lang("reset") . "' />";
	}
	
	function button_back($action)
	{
		return "<input type='button' name='" . $action . "' class='button button-back' value='" . lang("back") . "'>";
	}
	
	function button_cancel_upload($id, $action)
	{
		return "<a id=\"" . $id . "\" href=\"" . $action . "\"><input type='button' class='button button-cancel' value='" . lang("cancel-upload") . "'></a>";	
	}
	
	function anchor_add_photos($action)
	{
		return anchor_img($action . "%s", "images/admin/add.png", lang("add-photos"));
	}
	
	function anchor_view($action)
	{
		return anchor_img($action . "%s", "images/admin/magnifier.png", lang("view"));
	}
	
	function anchor_edit($action)
	{
		return anchor_img($action . "%s", "images/admin/pencil.png", lang("edit"));
	}
}
?>