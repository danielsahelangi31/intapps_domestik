<?php
/** 	
  *	Modul untuk Freight Forwarder untuk melakukan kegiatan delivery ocean going
  *
  */
class Trucking_AUX extends CI_Controller{
	public function __construct(){
		parent::__construct();
		
		// Dapatkan data login
		if(!$this->auth = $this->userauth->getLoginData()){
			redirect(LOGIN_PAGE);
		}
		
		$token = post('token');
		if(!$this->userauth->checkToken($token)){
			echo 'INVALID TOKEN';
			exit();
		}
	}
	
	
	/** 
	 * Index
	 */
	 
	 public function get_driver(){
		$out = new StdClass();
	
		$query = post('query');
	
		$datasource = $this->db	->where('trucking_company_id', $this->auth->trucking_company_id)
								->like('nama_supir', $query)
								->limit(10)
								->get('supir_truck')->result();
		
		$out->success = true;
		$out->datasource = $datasource;
		
		echo json_encode($out);
	 }
	 
	public function get_trucking_contact_number(){
		$nomor_do = post('token');

		/*$mod = model('og_api');
		$out = $mod->daftar_container_do($nomor_do);*/
		$out = array('options' => array('ab', 'abc', 'abcd'));

		echo json_encode($out);
	}
	
	public function set_driver_detail(){
		/*$send_option = post('simpan_kirim') ? true : false;
		
		$mod = model('og_deliveryrequest');
		$out = $mod->add($this->auth, $send_option);*/

		$out = array('name' => 'sukirman', 'number_plate' => 'B6555BXM');
		echo json_encode($out);
	}

	public function retrieve_trucking_contact_data(){
		$out = array(
						'nomor_tiket' => 'B6555BXM', 
						'nomor_hp' => '082121237654918238128946918274812481836273812376',
						'nama_supir' => 'Sukirman',
						'security_code' => '53cur1ty-c0d3',
					);
		echo json_encode($out);
	}
	
	
}