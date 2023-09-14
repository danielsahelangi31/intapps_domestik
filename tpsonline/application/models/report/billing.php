<?php
class Billing extends CI_Model{
	
	public function __construct(){
		parent::__construct();	
	}
	
	public function get_sub_cat(){
		$sub_cat = array(
			'billing1' => 'Billing Report 1',
			'billing2' => 'Billing Report 2',
			'billing3' => 'Billing Report 3',
			
		);
		
		return $sub_cat;
	}
	
	
	
	
	
	
}