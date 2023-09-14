<?php
/** 	
  *	Modul untuk Freight Forwarder untuk melakukan kegiatan delivery ocean going
  *
  */
class Delivery_Request_OG_AUX extends CI_Controller{
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
	
	public function get_delivery_order(){
		$nomor_do = post('nomor_do');
		
		$mod = model('og_api');
		$out = $mod->daftar_container_do($nomor_do);
		
		echo json_encode($out);
	}
	
	public function add(){
		$send_option = post('simpan_kirim') ? true : false;
		
		$mod = model('og_deliveryrequest');
		$out = $mod->add($this->auth, $send_option);
		
		echo json_encode($out);
	}
	
	public function edit(){
		if($id = post('id')){	
			$mod = model('og_deliveryrequest');
			$out = $mod->edit($id, $this->auth);
			
			echo json_encode($out);
		}else{
			
		}
	}
	
	public function get_trucking_company(){
		$out = new StdClass();
	
		$query = post('query');
	
		$datasource = $this->db	->select('m.nama_perusahaan, m.alamat, tc.id AS trucking_company_id')
								->join('member m', 'm.id = tc.member_id')
								->like('nama_perusahaan', $query)
								->limit(10)
								->get('trucking_company tc')->result();
		
		$out->success = true;
		$out->datasource = $datasource;
		
		echo json_encode($out);
	
	}
	
	
}