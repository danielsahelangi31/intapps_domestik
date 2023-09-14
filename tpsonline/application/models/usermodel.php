<?php
include_once('base/modelbase.php');

class Usermodel extends ModelBase{

	// Searchable Fields
	public $searchable = array(
		'nama_lengkap' => 'Nama Lengkap',
		'username' => 'Username',
		'telepon' => 'Telepon',
		'handphone' => 'Hand Phone',
	);

	public $sortable = array();

	public function __construct(){
		parent::__construct();
		$this->sortable = $this->searchable;
	}

	/** Get
	 * Ambil data dalam bentuk single row
	 */
	public function get($id, $array = false){
		$rs = $this->db->where('id', $id)->get('users');

		if($array) return $rs->row_array();
		else return $rs->row();
	}

	/** Get
	 * Ambil data dalam bentuk mini array of object, berguna untuk
	 * data reference dalam <select> tag misalnya
	 */
	public function selectDS(){
		return $this->db->get('users')->result();
	}

	public function listAll($table){
		return $this->db->get($table)->result();
	}

	public function select(){
		$this->siapkanDB();
		$this->db->select('SQL_CALC_FOUND_ROWS u.*
							, u.id
							, u.username
							, u.member_id
							, u.nama_lengkap
							, u.email
							, u.telepon
							, u.handphone
							, u.active
							, m.id AS com_id
							, m.nama_perusahaan', FALSE);

		$out = new StdClass();
		$out->datasource = $this->db->join('member AS m', 'u.member_id = m.id')
		->order_by('u.id', 'desc')
		->get('users AS u')->result();
		$out->actualRows = $this->db->query("SELECT FOUND_ROWS() as numRows")->row()->numRows;

		return $out;
	}

	public function add(){
		$this->db->trans_start();

		$user = array(
			'username' => post('username'),
			'member_id' => post('member_id'),
			'nama_lengkap' => post('nama_lengkap'),
			'password' => $this->userauth->encryptPassword(post('password')),
			'email' => post('email'),
			'telepon' => post('telepon'),
			'handphone' => post('handphone'),
			'active' => 1 // Sementara dianggap aktif dulu
		);

		$this->db->insert('users', $user);

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function details($id){
		$out = new StdClass();
		$out->datasource = $this->db->select('u.id
											, u.username
											, u.member_id
											, u.nama_lengkap
											, u.email
											, u.telepon
											, u.handphone
											, u.active
											, m.id AS com_id
											, m.npwp
											, m.nama_perusahaan
											, m.alamat
											, m.telepon AS com_telepon
											, m.email AS com_email
											, m.fax
											, m.waktu_bergabung
											, m.active AS com_active')
		->join('member AS m', 'u.member_id = m.id')
		->where('u.id', $id)
		->get('users AS u')->row();
		return $out;
	}

	public function details_domestik($id){

		$integrasi_cardom_dev = $this->load->database('integrasi_cardom_dev', TRUE);
		$out = new StdClass();
		//echo "select * from M_USERS where ROLE_USER = 'DOMESTIK' and ID_USER = '".$id."'";
		//print_r($id);

		$query = $integrasi_cardom_dev->query("select * from M_USERS where ROLE_USER = 'DOMESTIK' and ID_USER = '".$id."'");
		$hasil = $query->result_array();
		$out->datasource = $hasil[0];
		return $out;
		// print_r($out->datasource);
		// exit();
		// $query = $integrasi_cardom_dev->query('select * from M_USERS where ROLE_USER = 'DOMESTIK' and ID_USER = '".$id."'');
		// $hasil = $query->result_array();
		// $out->datasource = $hasil[0];
		//
		// // print_r($out->datasource);
		// // exit();
		//
		//return $out;


	}

	public function update(){
		$this->db->trans_start();

		$user = array(
			'username' => post('username'),
			'member_id' => post('member_id'),
			'nama_lengkap' => post('nama_lengkap'),
			'password' => $this->userauth->encryptPassword(post('password')),
			'email' => post('email'),
			'telepon' => post('telepon'),
			'handphone' => post('handphone'),
			'active' => 1 // Sementara dianggap aktif dulu
		);
		$this->db->update('users', $user, array('id' => post('id')));

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	function delete($id)
	{
		$this->db->delete('users', array('id' => $id));
	}

	public function listCompaniesByAttribute($attr = array()){
		$out = $this->db->select('m.id, m.nama_perusahaan')
		->like($attr)
		->get('member AS m')->result();

		return $out;
	}

	public function getCompanyName($com_id){
		$out = $this->db->select('m.id, m.nama_perusahaan')->get_where('member AS m', array('id' => $com_id))->row();

		return $out;
	}

	public function updatePassword(){
		$this->db->trans_start();

		$password = md5(trim(post('password')));
		$password_update = array(
									'password' 	=> $password
		);
		$this->db->update('users', $password_update, array('id' => post('id')));

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function updatePasswordDomestik(){
		// $this->db->trans_start();
		//
		// $password = md5(trim(post('password')));
		// $password_update = array(
		// 							'PASSWORD' 	=> $password
		// );
		// $this->db->update('M_USERS', $password_update, array('ID_USER' => post('id')));
		//
		// $this->db->trans_complete();
		// return $this->db->trans_status();
		$password = md5(trim(post('password')));
		$id = post('id');
		$integrasi_cardom_dev = $this->load->database('integrasi_cardom_dev', TRUE);
		$query = $integrasi_cardom_dev->query("UPDATE M_USERS SET PASSWORD = '".$password."' WHERE ID_USER = '".$id."'");

		return TRUE;
	}

	public function getPassword($id){
		return $this->db->select('u.password')->get_where('users AS u', array('id' => $id))->row();
	}

	public function getUserName($attr = array()){
		$out = $this->db->select('u.id, u.username')
		->get_where('users AS u', $attr)->row();

		return $out;
	}

}
