<?php

define("ORIGINAL_WIDTH", 1280);
define("ORIGINAL_HEIGHT", 1024);
define("THUMBNAILS_DIRECTORY", "small/");
define("THUMBNAIL_WIDTH", 180);
define("THUMBNAIL_HEIGHT", 135);

class Photogalleries_model extends CI_Model
{
	var $fields = array();
	var $query;
	var $table = "photogalleries";
	var $upload_error = NULL;

	
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
			'visible' => (array_key_exists('visible', $data)) ? (int)$data['visible'] : NULL,
			'date' => (array_key_exists('date', $data)) ? (int)$data['date'] : NULL,
			'name' => (array_key_exists('name', $data)) ? escape($data['name']) : NULL,
			'url' => (array_key_exists('name', $data)) ? url($data['name']) : NULL,
			'description' => (array_key_exists('description', $data)) ? $data['description'] : NULL,
		);
	}	
	
	function insert($data)
	{
		if (!is_array($data))
			return 0;

      if (!($this->query = $this->db->insert($this->table, $this->data($data))))
      	return false;
      	
      $insert_id = $this->db->insert_id();

      if (!mkdir($this->get_original_directory($insert_id), 0777))
      	return false;

      if (!mkdir($this->get_thumbnail_directory($insert_id), 0777))
      	return false;
      	
      return true;
	}
	
	function update($id, $data)
	{		
      return ($this->query = $this->common_model->_update($this->table, $id, $this->data($data))); 
	}

	function delete($id)
	{
		if (!($this->query = $this->db->delete($this->table, array('id' => (int)$id))))
			return false;
			
		$original_directory = $this->get_original_directory($id);

		if (!delete_files($original_directory, true))
			return false;
			
		if (is_dir($original_directory) && (!rmdir($original_directory)))
			return false;
			
		return true;
	}

	function save_photos($id, $photos)
	{
		$original_directory = $this->get_original_directory($id);
		$thumbnail_directory = $this->get_thumbnail_directory($id);
		
		if (!is_dir($original_directory))
		{
			//echo "invalid dir=" . $original_directory . "<br/>";
			return false;	
		}
		
		if (!is_array($photos))
		{
			return false;	
		}

		$config_upload['upload_path'] = $original_directory;
		$config_upload['allowed_types'] = 'gif|jpg|jpeg|png';
		$config_upload['max_size']	= '4096';
		
		$this->upload->initialize($config_upload);
		
		$error_count = 0;

		for ($i = 0; $i < count($photos['tmp_name']); $i++)
		{
			$_FILES['userfile']['name'] = $photos['name'][$i];
		   $_FILES['userfile']['type'] = $photos['type'][$i];
		   $_FILES['userfile']['tmp_name'] = $photos['tmp_name'][$i];
		   $_FILES['userfile']['error'] = $photos['error'][$i];
		   $_FILES['userfile']['size'] = $photos['size'][$i];
	
			if (!$this->upload->do_upload())
			{
				$error_count++;
				//return false;
				unset($photos['tmp_name'][$i]);
				unset($photos['name'][$i]);
			}
			
			//print_r($this->upload->data());
		}

		if ($error_count >= count($photos['tmp_name']))
		{
			$this->upload_error = $this->upload->display_errors($this->views_model->error_start_delimiter, $this->views_model->error_end_delimiter);
			return false;
		}


		$this->load->library('image_lib');

		$config_original['quality'] = "100%";
		$config_original['width'] = ORIGINAL_WIDTH;
		$config_original['height'] = ORIGINAL_HEIGHT;
		$config_original['maintain_ratio'] = true;
		
		$config_thumbnail['quality'] = "100%";
		$config_thumbnail['width'] = THUMBNAIL_WIDTH;
		$config_thumbnail['height'] = THUMBNAIL_HEIGHT;
		$config_thumbnail['maintain_ratio'] = true;
		
		$config_cropping['quality'] = "100%";
		$config_cropping['width'] = THUMBNAIL_WIDTH;
		$config_cropping['height'] = THUMBNAIL_HEIGHT;
		$config_cropping['maintain_ratio'] = false;

		foreach ($photos['name'] as $photo)
		{
			$original_path = $original_directory . $photo;
			$thumbnail_path = $thumbnail_directory . $photo;
			
			// resize original if is needed
			list($width, $height, $type, $attr) = @getimagesize($original_path);

			if (($width > ORIGINAL_WIDTH) && ($height > ORIGINAL_HEIGHT))
			{
				$this->image_lib->clear();
				$config_original['source_image'] = $original_path;
				
				$this->image_lib->initialize($config_original);
				
				if (!$this->image_lib->resize())
					return false;
			}

			// create thumbnail
			$this->image_lib->clear();
			$config_thumbnail['source_image'] = $original_path;
			$config_thumbnail['new_image'] = $thumbnail_path;
			$config_thumbnail['master_dim'] = ((($height*THUMBNAIL_WIDTH)/$width) >= THUMBNAIL_HEIGHT) ? "width" : "height";

			$this->image_lib->initialize($config_thumbnail);

			if (!$this->image_lib->resize())
				return false;

			// crop thumbnail to get same thumbnails
			list($width, $height, $type, $attr) = @getimagesize($thumbnail_path);
			
			$this->image_lib->clear();
			$config_cropping['source_image'] = $thumbnail_path;
			$config_cropping['x_axis'] = ($width > THUMBNAIL_WIDTH) ? (int)($width - THUMBNAIL_WIDTH)/2 : 0;
			$config_cropping['y_axis'] = ($height > THUMBNAIL_HEIGHT) ? (int)($height - THUMBNAIL_HEIGHT)/2 : 0;

			$this->image_lib->initialize($config_cropping);

			if (!$this->image_lib->crop())
				return false;
		
		}

		return true;
	}

	function delete_where($where)
	{
		$this->where($where);
		$photogalleries = $this->get_all();
		
		foreach ($photogalleries as $photogallery)
			if (!($this->query = $this->delete($photogallery['id'])))
				return false;
				
		return true;
	}
	
	function delete_photos($id, $photos)
	{
		if (!is_array($photos))
			return false;

		$original_directory = $this->get_original_directory($id);
		$thumbnails_directory = $this->get_thumbnail_directory($id);

		foreach ($photos as $photo)
		{
			$original_photo = $original_directory . $photo;
			$thumbnail_photo = $thumbnails_directory . $photo;

			if (file_exists($original_photo) && (!@unlink($original_photo)))
				return false;

			if (file_exists($thumbnail_photo) && (!@unlink($thumbnail_photo)))
				return false;
		}

		return true;	
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
	
	function get_original_directory($id)
	{
		return $this->global_model->site_config['photogalleries_dir'] . $id . "/";
	}
	
	function get_thumbnail_directory($id)
	{
		return $this->get_original_directory($id) . THUMBNAILS_DIRECTORY;
	}
	
	function get_photos($id)
	{
		$original_directory = $this->get_original_directory($id);
		$thumbnails_directory = $this->get_thumbnail_directory($id);
		$result = array();

		if (is_dir($original_directory) && is_dir($thumbnails_directory))
		{
			$photos = get_filenames($thumbnails_directory);

			foreach ($photos as $photo)
				$result[$photo] = array('original' => $original_directory . $photo, 'thumbnail' => $thumbnails_directory . $photo);
		}

		return $result;
	}
}
?>