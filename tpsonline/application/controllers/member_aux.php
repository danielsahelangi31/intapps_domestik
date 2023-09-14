<?php
/** Member
  *	Modul untuk penambahan member, aktivasi, dll
  *
  */
class Member_aux extends CI_Controller{
	public function __construct(){
		parent::__construct();

		// Dapatkan data login
		if(!$this->auth = $this->userauth->getLoginData()){
			redirect(LOGIN_PAGE);
		}
	}
	
	/** 
	 * Retrieve
	 * Daftar Member yang butuh di approve
	 */
	public function retrieve(){
		$mod = model('membership');

		// Content Data		
		$res = $mod->get(post('id'), true);
		//print_r($res); exit(' bye');

		// Layout Data
		$data = array(
			'id' => $res['id'],
			'nama_perusahaan' => $res['nama_perusahaan'],
			'npwp' => $res['npwp'],
			'alamat' => $res['alamat'],
			'telepon' => $res['telepon'],
			'fax' => $res['fax'],
			'status' => true
		);

		echo json_encode($data);
	}

}