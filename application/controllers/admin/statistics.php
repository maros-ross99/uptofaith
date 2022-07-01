<?php

class Statistics extends CI_Controller
{	
	var $url = "admin/statistics";
	var $global = NULL;
	var $access = NULL;
	
	function __construct()
	{
		parent::__construct();	

		$this->load->model('Global_model', 'global_model');
		$this->global = $this->global_model->get_global();
		
		$this->access = $this->global_model->admin['rights'][RIGHTS_NAME_STATISTICS];

		$this->lang->load('admin/info_messages');
		$this->lang->load('admin/statistics');		
	}

	function index($message = NULL)
	{
		switch ($this->access)
		{
			case RIGHTS_ACCESS_NONE:
				$this->global_model->access_denied(lang('statistics'));
				return;
								
			case RIGHTS_ACCESS_READ:
			default:
				break;
		}

		$this->participants_model->where(array('event_id' => $this->global_model->event_active['id']));
		$count_all_participants = $this->participants_model->count_all();
		
		$this->groups_model->where(array('event_id' => $this->global_model->event_active['id']));
		$groups = $this->groups_model->get_all();
		
		foreach ($groups as $group)
			$count_all_participants += $group['count']; 	

		
		$time = time();
		$last_day_time = 60*60*24;
		$last_week_time = $last_day_time*7;
		$last_month_time = $last_day_time*30;
	
		$this->participants_model->where(array('event_id' => $this->global_model->event_active['id'], 'registration_date >=' => ($time - $last_day_time)));
		$last_day_added_participants = $this->participants_model->count_all();
			
		$this->participants_model->where(array('event_id' => $this->global_model->event_active['id'], 'registration_date >=' => ($time - $last_week_time)));
		$last_week_added_participants = $this->participants_model->count_all();	

		$this->participants_model->where(array('event_id' => $this->global_model->event_active['id'], 'registration_date >=' => ($time - $last_month_time)));
		$last_month_added_participants = $this->participants_model->count_all();
		
		$this->groups_model->where(array('event_id' => $this->global_model->event_active['id'], 'registration_date >=' => ($time - $last_day_time)));
		$last_day_added_groups = $this->groups_model->count_all();
			
		$this->groups_model->where(array('event_id' => $this->global_model->event_active['id'], 'registration_date >=' => ($time - $last_week_time)));
		$last_week_added_groups = $this->groups_model->count_all();

		$this->groups_model->where(array('event_id' => $this->global_model->event_active['id'], 'registration_date >=' => ($time - $last_month_time)));
		$last_month_added_groups = $this->groups_model->count_all();
		
		
		// statistics by course of registration
		$statistics_course_registration = array();

		$this->participants_model->where(array('event_id' => $this->global_model->event_active['id']));
		$this->participants_model->order_by(array('registration_date' => "asc"));
		$participants = $this->participants_model->get_all();
		$this->groups_model->where(array('event_id' => $this->global_model->event_active['id']));
		$this->groups_model->order_by(array('registration_date' => "asc"));
		$groups = $this->groups_model->get_all();
		
		$min_date = time();
		$max_date = 0;
		
		// find min/max date of participants/groups registration
		foreach ($participants as $participant)
		{
			if ($participant['registration_date'] < $min_date)
				$min_date = $participant['registration_date'];
				
			if ($participant['registration_date'] > $max_date)
				$max_date = $participant['registration_date'];
		}
		
		foreach ($groups as $group)
		{
			if ($group['registration_date'] < $min_date)
				$min_date = $group['registration_date'];
				
			if ($group['registration_date'] > $max_date)
				$max_date = $group['registration_date'];
		}
		
		// 1 week
		$delta_date = 60*60*24*7;
		$step_date = $min_date;
		
		while ($step_date <= $max_date)
		{	
			$count = 0;
			
			foreach ($participants as $participant)
				if ($participant['registration_date'] <= $step_date)
					$count++;
			
			foreach ($groups as $group)
				if ($group['registration_date'] <= $step_date)
					$count += $group['count'];
			
			$statistics_course_registration[] = array('date' => $step_date, 'count' => $count);		
			
			if ($step_date == $max_date)
				break;			
			
			if (($step_date + $delta_date) > $max_date)
				$step_date = $max_date;			
			else
				$step_date += $delta_date;
		}
		// -----------
		
		// statistics by gender
		$statistics_gender = array();
		
		$this->participants_model->where(array('event_id' => $this->global_model->event_active['id'], 'gender_id' => 1));
		$participants_men_count = $this->participants_model->count_all();
		$this->participants_model->where(array('event_id' => $this->global_model->event_active['id'], 'gender_id' => 2));
		$participants_women_count = $this->participants_model->count_all();
		
		$this->groups_model->where(array('event_id' => $this->global_model->event_active['id']));
		$groups = $this->groups_model->get_all();
		
		if (($participants_men_count > 0) || ($participants_women_count > 0) || ($groups != NULL))
		{
			$statistics_gender['men'] = $participants_men_count;		
			$statistics_gender['women'] = $participants_women_count;
			
			foreach ($groups as $group)
			{
				$statistics_gender['men'] += $group['count_men'];
				$statistics_gender['women'] += $group['count_women'];
			}
		}
		// -----------	
		
		
		// statistics by countries
		$this->countries_model->order_by(array('id' => "asc"));
		$countries = $this->countries_model->get_all();
		
		$statistics_countries = array();		
		
		foreach ($countries as $country)
		{
				$this->participants_model->where(array('event_id' => $this->global_model->event_active['id'], 'country_id' => $country['id']));
				$country_num_participants = $this->participants_model->count_all();
				
				$this->groups_model->where(array('event_id' => $this->global_model->event_active['id'], 'country_id' => $country['id']));
				$groups = $this->groups_model->get_all();
				
				foreach ($groups as $group)
					$country_num_participants += $group['count'];				
				
				if ($country_num_participants > 0)
					$statistics_countries[] = array('id' => $country['id'], 'name' => $country['name'], 'count' => $country_num_participants);
		}
		// -----------	
		
		
		// statistics by cities
		$this->cities_model->order_by(array('country_id' => "asc"));
		$cities = $this->cities_model->get_all();
		
		$statistics_cities = array();		
		
		foreach ($cities as $city)
		{
				$this->participants_model->where(array('event_id' => $this->global_model->event_active['id'], 'city_id' => $city['id']));
				$city_num_participants = $this->participants_model->count_all();
				
				$this->groups_model->where(array('event_id' => $this->global_model->event_active['id'], 'city_id' => $city['id']));
				$groups = $this->groups_model->get_all();
				
				foreach ($groups as $group)
					$city_num_participants += $group['count'];				
				
				if ($city_num_participants > 0)
					$statistics_cities[] = array('id' => $city['id'], 'country_id' => $city['country_id'], 'name' => $city['name'], 'count' => $city_num_participants);
		}
		
		function cmp_cities($a, $b)
		{
			if ($a['count'] == $b['count']) 
				return 0;

			return ($a['count'] < $b['count']) ? -1 : 1;
		}
		
		uasort($statistics_cities, 'cmp_cities');
		// -----------	
		
		
		// statistics without church
		$this->participants_model->where(array('event_id' => $this->global_model->event_active['id'], 'church_id' => NULL));
		$statistics_no_church = $this->participants_model->count_all();
		
		$this->groups_model->where(array('event_id' => $this->global_model->event_active['id'], 'church_id' => NULL));
		$groups = $this->groups_model->get_all();
		
		foreach ($groups as $group)
			$statistics_no_church += $group['count'];
		// -----------	
		
		
		
		// statistics by churches
		$this->churches_model->order_by(array('country_id' => "asc"));
		$churches = $this->churches_model->get_all();
		
		$statistics_churches = array();		
		
		foreach ($churches as $church)
		{
				$this->participants_model->where(array('event_id' => $this->global_model->event_active['id'], 'church_id' => $church['id']));
				$church_num_participants = $this->participants_model->count_all();
				
				$this->groups_model->where(array('event_id' => $this->global_model->event_active['id'], 'church_id' => $church['id']));
				$groups = $this->groups_model->get_all();
				
				foreach ($groups as $group)
					$church_num_participants += $group['count'];				
				
				if ($church_num_participants > 0)
					$statistics_churches[] = array('id' => $church['id'], 'country_id' => $church['country_id'], 'name' => $church['name'], 'count' => $church_num_participants);
		}
		
		function cmp_churches($a, $b)
		{
			if ($a['count'] == $b['count']) 
				return 0;

			return ($a['count'] < $b['count']) ? -1 : 1;
		}
		
		uasort($statistics_churches, 'cmp_churches');
		// -----------	

		$data = array
		(
			'caption' => lang("statistics"),
			'count_all_participants' => $count_all_participants,
			'last_day_added_participants' => $last_day_added_participants,
			'last_week_added_participants' => $last_week_added_participants,
			'last_month_added_participants' => $last_month_added_participants,
			'last_day_added_groups' => $last_day_added_groups,
			'last_week_added_groups' => $last_week_added_groups,
			'last_month_added_groups' => $last_month_added_groups,
			'statistics_course_registration' => $statistics_course_registration,
			'statistics_gender' => $statistics_gender,
			'statistics_countries' => $statistics_countries,
			'statistics_cities' => $statistics_cities,
			'statistics_churches' => $statistics_churches,
			'statistics_no_church' => $statistics_no_church,
		);
		
		$this->views_model->back_office($this->url, array_merge($this->global, $data));
	}
}

/* End of file statistics.php */
/* Location: ./system/application/controllers/admin/statistics.php */
?>
