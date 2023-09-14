<?php
/** 	
  *	Modul untuk Freight Forwarder untuk melakukan kegiatan delivery ocean going
  *
  */
class Users_AUX extends CI_Controller{
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
	public function get_company(){
		$key_word = post('q');

		$mod = model('usermodel');
		foreach ($mod->listCompaniesByAttribute(array('nama_perusahaan' => $key_word)) as $key => $val)
		{
			//print_r($val->nama_perusahaan);exit(' bye');
			//print_r($key);exit(' bye');
			//$out['options'][$key]['id'] = $val->id;
			$out['options'][] = $val->nama_perusahaan . ":" . $val->id;
		}
		//print_r($out['options']);exit(' bye');

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

	public function check_username(){
		$key_word = post('q');

		$mod = model('usermodel');
		print_r($mod->getUserName(array('username' => $key_word))); exit(' bye');
		foreach ($mod->getUserName(array('username' => $key_word)) as $key => $val)
		{
			//print_r($val->nama_perusahaan);exit(' bye');
			//print_r($key);exit(' bye');
			//$out['options'][$key]['id'] = $val->id;
			$out['options'][] = $val->nama_perusahaan . ":" . $val->id;
		}
		//print_r($out['options']);exit(' bye');

		echo json_encode($out);
	
	}
}