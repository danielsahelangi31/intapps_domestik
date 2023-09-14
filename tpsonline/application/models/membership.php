<?php
include_once('base/modelbase.php');

class Membership extends ModelBase{
	
	// Searchable Fields
	public $searchable = array(
		'nama_perusahaan' => 'Nama Perusahaan',
		'npwp' => 'NPWP',
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
		$rs = $this->db->where('id', $id)->get('member');
		
		if($array) return $rs->row_array();
		else return $rs->row();
	}
	
	/** Get
	  * Ambil data dalam bentuk mini array of object, berguna untuk 
	  * data reference dalam <select> tag misalnya
	  */
	public function selectDS(){
		return $this->db->get('member')->result();	
	}
	
	public function select(){
		$this->siapkanDB();
		$this->db	->select('SQL_CALC_FOUND_ROWS m.*
								, count(DISTINCT u.id) AS jumlah_user
								, ff.id AS freight_forwarder_id
								, tc.id AS trucking_company_id', FALSE);
		
		$out = new StdClass();			
		$out->datasource = $this->db->join('users u', 'u.member_id = m.id')
									->join('freight_forwarder ff', 'ff.member_id = m.id', 'left')
									->join('trucking_company tc', 'tc.member_id = m.id', 'left')
									->group_by('m.id')
									->get('member m')->result();
		$out->actualRows = $this->db->query("SELECT FOUND_ROWS() as numRows")->row()->numRows;
		
		return $out;
	}

	public function selectByAttribute($attr = array()){
		$this->siapkanDB();
		$this->db	->select('SQL_CALC_FOUND_ROWS m.*
								, count(DISTINCT u.id) AS jumlah_user
								, ff.id AS freight_forwarder_id
								, tc.id AS trucking_company_id'
								, FALSE);

		$out = new StdClass();
		$out->datasource = $this->db->join('users u', 'u.member_id = m.id')
									->join('freight_forwarder ff', 'ff.member_id = m.id', 'left')
									->join('trucking_company tc', 'tc.member_id = m.id', 'left')
									->group_by('m.id')
									->get_where('member m' , $attr)->result();
		$out->actualRows = $this->db->query("SELECT FOUND_ROWS() AS numRows")->row()->numRows;

		return $out;
	}

	public function add(){
		$this->db->trans_start();
		
		$member = array(
			'npwp' => post('npwp'),
			'nama_perusahaan' => post('nama_perusahaan'),
			'alamat' => post('com_alamat'),
			'telepon' => post('com_telepon'),
			'email' => post('cp_email'),
			'fax' => post('com_fax'),
			'waktu_bergabung' => date('Y-m-d H:i:s'),
			'active' => 1 // Sementara dianggap aktif dulu
		);
		
		$this->db->insert('member', $member);
		$member_id = $this->db->insert_id();
		
		$user = array(
			'username' => post('username'),
			'member_id' => $member_id,
			'nama_lengkap' => post('nama_lengkap'),
			'password' => $this->userauth->encryptPassword(post('password')),
			'email' => post('cp_email'),
			'telepon' => post('cp_telepon'),
			'handphone' => post('cp_handphone'),
			'active' => 1 // Sementara dianggap aktif dulu
		);

		$this->db->insert('users', $user);
		$users_id = $this->db->insert_id();
		
		$table_name = NULL;
		
		switch(post('membership_type')){
			case 'FREIGHT_FORWARDER':
				$table_name = 'freight_forwarder';
				break;
			
			case 'TRUCKING_COMPANY':
				$table_name = 'trucking_company';
				break;
				
			case 'SHIPPING_LINE':
				$table_name = 'shipping_line';
				break;
			
			case 'SHIPPING_AGENT':
				$table_name = 'shipping_agent';
				break;
		}
		
		if($table_name){
			$membership_type = array(
				'member_id' => $member_id
			);
			
			$this->db->insert($table_name, $membership_type);	
		}
		
		$this->db->trans_complete();
		
		return $this->db->trans_status();
	}	

	public function details($id){
		$out = new StdClass();
		$out->datasource = $this->db->select('m.id
											, m.npwp
											, m.nama_perusahaan
											, m.alamat
											, m.telepon AS com_telepon
											, m.email AS com_email
											, m.fax
											, m.waktu_bergabung
											, m.active
											, u.username
											, u.member_id
											, u.nama_lengkap
											, u.email
											, u.telepon
											, u.handphone
											, u.active
											, ff.id AS freight_id
											, ff.id AS freight_id
											, tc.id AS trucking_id')
									->join('users u', 'u.member_id = m.id')
									->join('freight_forwarder ff', 'ff.member_id = m.id', 'left')
									->join('trucking_company tc', 'tc.member_id = m.id', 'left')
									->where('m.id', $id)
									->group_by('m.id')
									->get('member m')->row();
		//$out->actualRows = $this->db->query("SELECT FOUND_ROWS() as numRows")->row()->numRows;
		return $out;
	}

	public function update(){
		$this->db->trans_start();

		$member = array(
			'npwp' => post('npwp'),
			'nama_perusahaan' => post('nama_perusahaan'),
			'alamat' => post('alamat'),
			'telepon' => post('telepon'),
			//'email' => post('com_email'),
			'fax' => post('fax')
		);
		$this->db->update('member', $member, array('id' => post('id')));

		$user = array(
			'username' => post('username'),
			'nama_lengkap' => post('nama_lengkap'),
			'email' => post('email'),
			//'telepon' => post('cp_telepon'),
			'handphone' => post('handphone')
		);
		$this->db->update('users', $user, array('member_id' => post('id')));

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	public function approve($id){
		$this->db->trans_start();

		$member = array(
			'diperiksa' => true,
			'waktu_approved ' => date('Y-m-d')
		);
		$this->db->update('member', $member, array('id' => $id));

		$this->db->trans_complete();
		return $this->db->trans_status();
	}
	
	public function reject($id){
		$this->db->trans_start();

		$member = array(
			'diperiksa' => true,
			'active ' => false
		);
		$this->db->update('member', $member, array('id' => $id));

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	/*function delete($id)
	{
		$this->db->delete('member', array('id' => $id)); 
	}*/
}
