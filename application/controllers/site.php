<?php

class Site extends CI_Controller
{	
	var $page_data = array();
	var $events = NULL;
	var $countries = NULL;
	var $cities = NULL;
	var $churches = NULL;

	function __construct()
	{
		parent::__construct();

		$this->load->model('News_model', 'news_model');
		$this->load->model('Messages_model', 'messages_model');
		$this->load->model('Views_model', 'views_model');
		$this->load->model('Options_model', 'options_model');
		$this->load->model('Events_model', 'events_model');
		$this->load->model('Countries_model', 'countries_model');
		$this->load->model('Cities_model', 'cities_model');
		$this->load->model('Churches_model', 'churches_model');
		$this->load->model('News_model', 'news_model');
		$this->load->model('Participants_model', 'participants_model');
		$this->load->model('Groups_model', 'groups_model');
		$this->load->model('Photogalleries_model', 'photogalleries_model');
		$this->load->model('Videos_model', 'videos_model');
		
		$this->events_model->order_by(array("name" => "asc"));
		$this->events = $this->events_model->get_all();
		
		$this->countries_model->order_by(array("name" => "asc"));
		$this->countries = $this->countries_model->get_all();
		
		$this->cities_model->order_by(array("name" => "asc"));
		$this->cities = $this->cities_model->get_all();
		
		$this->churches_model->order_by(array("name" => "asc"));
		$this->churches = $this->churches_model->get_all();
		
		$this->page_data = array
		(
			'title' => $this->options_model->get('site_title'),
			'content' => "",
			'aside_text' => unescape($this->options_model->get('site_aside_text')),			
			'action' => "",	
			'meta_keywords' => $this->options_model->get('eshop_meta_keywords'),
			'meta_description' => $this->options_model->get('eshop_meta_description'),
		);
	}
	
	function index()
	{
		$this->page_data['content'] = unescape($this->options_model->get('site_welcome_text'));
		$this->views_model->front_office("content", $this->page_data);
	}
	
	function about()
	{
		$this->page_data['content'] = unescape($this->options_model->get('site_about_text'));
		$this->views_model->front_office("content", $this->page_data);
	}

	function future()
	{
		$this->views_model->front_office("content", $this->page_data);
	}
	
	function media()
	{
		$this->views_model->front_office("content", $this->page_data);
	}
	
	function contact()
	{
		$this->page_data['content'] = unescape($this->options_model->get('site_contact_text'));
		$this->views_model->front_office("content", $this->page_data);
	}
	
	function registration()
	{
		$this->page_data['events'] = $this->events;
		$this->page_data['countries'] = $this->countries;
		$this->page_data['cities'] = $this->cities;
		$this->page_data['churches'] = $this->churches;
		
		$this->views_model->front_office("registration", $this->page_data);
	}
	
	function registration_group()
	{
		$this->page_data['events'] = $this->events;
		$this->page_data['countries'] = $this->countries;
		$this->page_data['cities'] = $this->cities;
		$this->page_data['churches'] = $this->churches;

		$this->views_model->front_office("registration_groups", $this->page_data);
	}
}

/* End of file site.php */
/* Location: ./application/controllers/site.php */

?>
