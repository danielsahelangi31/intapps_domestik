<?php
include_once('base/modelbase.php');

class Drivermodel extends ModelBase{

	// Searchable Fields
	public $searchable = array(
		'nama_supir' => 'Nama Driver',
		'nomor_handphone' => 'Nomor Handphone',
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
		$out = new StdClass();
		$out->datasource = $this->db->select('st.id
											, st.nama_supir
											, st.nomor_handphone
											, st.plat_nomor')
									->join('trucking_company AS tc', 'st.trucking_company_id = tc.id')
									->where('st.id', $id)
									->get('supir_truck AS st')->row();
		if($out->datasource)
			$out->success = true;
		else
			$out->success = false;
			
		return $out;
	}

	/** Get
	  * Ambil data dalam bentuk mini array of object, berguna untuk 
	  * data reference dalam <select> tag misalnya
	  */
	public function selectDS($trucking_company_id){
		return $this->db->where('trucking_company_id', $trucking_company_id)->get('supir_truck')->result();	
	}

	public function select($trucking_company_id){
		$this->siapkanDB();
		$this->db	->select('SQL_CALC_FOUND_ROWS st.*', FALSE);

		$out = new StdClass();
		$out->datasource = $this->db->join('trucking_company AS tc', 'st.trucking_company_id = tc.id')
									->where('st.trucking_company_id', $trucking_company_id)
									->get('supir_truck AS st')->result();
		$out->actualRows = $this->db->query("SELECT FOUND_ROWS() as numRows")->row()->numRows;

		return $out;
	}

	public function selectByAttribute($attr = array()){
		$this->siapkanDB();
		$this->db	->select('SQL_CALC_FOUND_ROWS st.*', FALSE);

		$out = new StdClass();
		$out->datasource = $this->db->get_where('supir_truck AS st' , $attr)->result();
		$out->actualRows = $this->db->query("SELECT FOUND_ROWS() AS numRows")->row()->numRows;

		return $out;
	}

	public function add(){
		$this->db->trans_start();

		$supir_truck = array(
			'trucking_company_id' => $this->auth->trucking_company_id,
			'nama_supir' => post('nama_supir'),
			'nomor_handphone' => post('nomor_handphone'),
			'plat_nomor' => post('plat_nomor'),
		);
		$this->db->insert('supir_truck', $supir_truck);

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
									->join('freight_forwarder ff', 'ff.users_id = u.id', 'left')
									->join('trucking_company tc', 'tc.users_id = u.id', 'left')
									->where('m.id', $id)
									->group_by('m.id')
									->get('member m')->row();
		//$out->actualRows = $this->db->query("SELECT FOUND_ROWS() as numRows")->row()->numRows;
		return $out;
	}

	public function update(){
		$this->db->trans_start();

		$supir_truck = array(
			'nama_supir' => post('nama_supir'),
			'nomor_handphone' => post('nomor_handphone'),
			'plat_nomor' => post('plat_nomor'),
		);
		$this->db->update('supir_truck', $supir_truck, array('id' => post('id')));

		$this->db->trans_complete();
		return $this->db->trans_status();
	}

	/*function delete($id)
	{
		$this->db->delete('member', array('id' => $id)); 
	}*/
}
