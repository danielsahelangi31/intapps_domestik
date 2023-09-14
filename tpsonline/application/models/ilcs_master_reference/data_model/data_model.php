<?php
class Data_Model{
	public $data;
	public $key;
	public $suggestions = array();
	public static $suggestion_fields = array();
	public static $entities = array(
		'vessel',
		'vessel_type',
		'vessel_agent',
		'location',
		'country',
		'company',
		'port_company',
		'shipping_agent',
		'shipping_line',
		'freight_forwarder',
		'trucking_company',
		'container_terminal',
		'passanger_terminal',
		'car_terminal',
		'cargo_terminal'
	);
	
	public static $row_per_page = 10;
	
	public $parents = array();
	
	public function get_class(){
		return get_class($this);
	}
	
	public function get($id, $db){
		if($id === NULL) return NULL;
		
		$current_class = $this->get_class();
		
		$row = $db->where($current_class.'_id', $id)->get($current_class)->row_array();
		foreach($row as $key => $val){
			$this->{$key} = $val;
		}
		
		return $this;
	}
	
	public function get_tree($id, $db){
		if($id === NULL) return NULL;
		
		$current_class = $this->get_class();
		
		$this->data = $db->where($current_class.'_id', $id)->get($current_class)->row();
		
		$this->get_suggestion($db);
		$this->find_parents($db);
		
		return $this;
	}
	
	public function get_suggestion(){
	
	}
	
	public function get_lookup_fields(){
		return self::$lookup_fields;
	}
	
	public function prepare_lookup($db, $entity, $param, $fields){
		$allowable_sort = array('ASC', 'DESC');
		
		// Searching
		if(is_array($param->lookup)){
			foreach($param->lookup as $lookup){
				if(isset($lookup->field) && isset($lookup->value)){
					if(in_array($lookup->field, $fields)){
						$db->like($lookup->field, $lookup->value);
					}
				}
			}
		}
		
		// Paging
		if(isset($param->page)){
			$db->limit(self::$row_per_page, $param->page);
		}
		
		// Sorting
		if(isset($param->sort_field) && isset($param->sort_method)){
			if(in_array($param->sort_field, $fields) && in_array($param->sort_method, $allowable_sort)){
				$db->order_by($param->sort_field, $param->sort_method);
			}
		}
	}
	
	public function find_similar(){
		$current_class = $this->get_class();
		
		
	}
}

class vessel extends Data_Model{
	public static $lookup_fields = array(
		'vessel_id' => 'ID',
		'imo_number' => 'IMO Number',
		'vessel_name' => 'MMSI',
		'call_sign' => 'Call Sign',
		'vessel_name' => 'Nama Kapal',
	);
	
	public function find_parents($db){
		$this->parents['vessel_type'] = (new vessel_type())->get_tree($this->data->vessel_type_id, $db);
	}
	
	public function get_suggestion($db){
		$keyed_fields = array_keys(self::$lookup_fields);
		
		foreach(explode(' ', $this->data->vessel_name) as $word){
			if(strlen($word) > 3){
				$db->or_like('vessel_name', $word);
			}
		}
		
		$this->suggestions = $db	->select(implode(', ', $keyed_fields))
									->get('vessel')
									->result();
	}
}

class vessel_type extends Data_Model{
	public static $lookup_fields = array(
		'vessel_type_id' => 'ID',
		'vessel_type' => 'Jenis Kapal',
	);
	
	public function find_parents($db){	
		
	}
}

class country extends Data_Model{
	public function find_parents($db){
	
	}
}

class location_status extends Data_Model{
	public function find_parents($db){
	
	}
}

class location extends Data_Model{
	public static $lookup_fields = array(
		'location_id' => 'ID',
		'locode' => 'Locode',
		'location_name' => 'Nama Lokasi',
		'longitude' => 'Longitude',
		'latitude' => 'Latitude',
	);
	
	public function find_parents($db){
		$this->parents['country'] = (new country())->get_tree($this->data->country_id, $db);
		$this->parents['location_status'] = (new location_status())->get_tree($this->data->location_status_id, $db);
	}
}

class port extends Data_Model{
	public static $lookup_fields = array(
		'port_name' => 'Port Name',
	);
	
	public function find_parents($db){
		$this->parents['location'] = (new location())->get_tree($this->data->location_id, $db);
		$this->parents['port_company'] = (new port_company())->get_tree($this->data->port_company_id, $db);
	}
}

class company extends Data_Model{
	public static $lookup_fields = array(
		'company_name' => 'Nama Perusahaan',
		'npwp' => 'NPWP',
	);
	
	public function find_parents($db){
		
	}
}

class port_company extends Data_Model{
	public static $lookup_fields = array(
		'company_name' => 'Nama Perusahaan',
		'npwp' => 'NPWP',
	);

	public function find_parents($db){
		$this->parents['company'] = (new company())->get_tree($this->data->company_id, $db);
	}
}
























