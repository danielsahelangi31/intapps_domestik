<?php
/** Auth
  * Modul pengatur authentikasi user terhadap sistem
  * secara keseluruhan. Untuk otorisasi user terhadap
  * modul harap gunakan secara lokal di modul masing2
  *
  * @author		: Djati Satria (djati.satria@gmail.com)
  * @version 	: 2.0
  * @revision	: n/a
  */

class UserAuth extends CI_Model{
	// Key Untuk Simpan data login di session
	private $key 			= "demo.sc8d7gads";

	function __construct(){
		parent::__construct();
	}

	public function encryptPassword($str){
		// Sementara pakai MD5
		return md5($str);
	}

	/* Get Login Data untuk otorisasi user */
	public function getLoginData(){
		// Development Use
		/*
		$dummy = new StdClass();
		$dummy->logged_in = true;
		$dummy->users_id = 1;
		$dummy->nama_lengkap = 'Development Dummy';
		$dummy->member_id = 1;
		$dummy->freight_forwarder_id = 1;
		$dummy->trucking_company_id = 1;

		return $dummy;
		*/

		// Production Use
		if($sess_data = $this->session->userdata($this->key)){
			if($sess_data->logged_in){
				return $sess_data;
			}else{
				return false;
			}
		}
		else return false;
	}

  public function checkDB(){
    echo "<pre>\n";
    print_r($this->db);
    echo "</pre>\n";
  }


  public function checkLoginOCIDomestik($username, $password){
    $integrasi_cardom_dev = $this->load->database('integrasi_cardom_dev', TRUE);
    $query = $integrasi_cardom_dev->query("select * from M_USERS where ROLE_USER = 'DOMESTIK' and USERNAME = '".$username."' and PASSWORD = '".$this->encryptPassword($password)."'");
    // echo "select * from M_USERS where ROLE_USER = 'DOMESTIK' and USERNAME = '".$username."' and PASSWORD = '".$this->encryptPassword($password)."'";
    // exit();
    $hasil = $query->result_array();
    if(sizeof($hasil) > 0){
      $ld = new StdClass();

			// Token untuk ajax request key
			$ld->token = uniqid();
			$ld->logged_in = true;
      foreach($hasil[0] as $key => $val) $ld->{strtolower($key)} = $val;

			$this->session->set_userdata($this->key, $ld);
      return true;
    } else {
      return false;
    }
  }
  public function checkDB_OCI(){
    $integrasi_cardom_dev = $this->load->database('integrasi_cardom_dev', TRUE);
    $query = $integrasi_cardom_dev->query("select * from M_USERS");
    $hasil = $query->result_array();

    echo "<pre>\n";
    print_r($hasil);
    print_r($integrasi_cardom_dev);
    echo "</pre>\n";
  }

	/* Set Login Data Ketika Login Sukses */
	public function checkLogin($username, $password){
		// Daftar field yang akan diambil
		$fields = array(
			'u.id',
			'u.username',
			'u.member_id',
			'u.sender',
			'u.nama_lengkap',
			'u.email AS email_user',
			'm.npwp',
			'm.nama_perusahaan',
			'm.alamat',
			'm.telepon',
			'm.email AS email_perusahaan',
			'ff.id AS freight_forwarder_id',
			'tc.id AS trucking_company_id',
			'sl.id AS shipping_line_id',
			'sa.id AS shipping_agent_id',
			'a.id AS administrator_id',
			'a.roles',
      'u.role_user',
		);

		$where = array(
			'username' => $username,
			'password' => $this->encryptPassword($password),
			'u.active' => 1,
			'm.active' => 1
		);

		$userdata = $this->db	->select(implode(',', $fields))
								->join('member m', 'm.id = u.member_id')
								->join('freight_forwarder ff', 'ff.member_id = m.id', 'left')
								->join('trucking_company tc', 'tc.member_id = m.id', 'left')
								->join('shipping_line sl', 'sl.member_id = m.id', 'left')
								->join('shipping_agent sa', 'sa.member_id = m.id', 'left')
								->join('administrator a', 'a.member_id = m.id', 'left')
								->where($where)
								->limit(1)
								->get('users u')->row();

		if($userdata){
			$ld = new StdClass();

			// Token untuk ajax request key
			$ld->token = uniqid();
			$ld->logged_in = true;
			foreach($userdata as $key => $val) $ld->{$key} = $val;

			$this->session->set_userdata($this->key, $ld);

			return true;
		}else{
			return false;
		}
	}

	/* Update Login Data, akan extends atau override data session yang sudah ada */
	public function updateLoginData($upd){
		$ld = $this->session->userdata($this->key);

		if($ld){
			foreach($upd as $key => $val) $ld->{$key} = $val;
			$this->session->set_userdata($this->key, $ld);

			return true;
		}else{
			return false;
		}
	}

	public function clearLoginData(){
		$this->session->unset_userdata($this->key);
	}

	public function checkToken($token){
		return true;
	}
}
