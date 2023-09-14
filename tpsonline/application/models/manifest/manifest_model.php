<?php
require_once('./application/models/base/modelbase.php');

class Manifest_Model extends ModelBase{
	// Datagrid Sortable Fields
	public $sortable = array(
		'nama_kapal' => 'Nama Kapal',
		'no_ukk' => 'No UKK',
		'voyage' => 'Voyage',
		'call_sign' => 'Call Sign',
		'waktu_upload' => 'Waktu Upload',
		'pol' => 'Pelabuhan Asal',
		'pod' => 'Pelabuhan Tujuan',
		'ata' => 'Waktu Datang di POL',
		'atd' => 'Waktu Berangkat di POL',
		'waktu_upload' => 'Waktu Upload',
		
	);
	
	// Datagrid Searchable Fields
	public $searchable = array(
		'nama_kapal' => 'Nama Kapal',
		'no_ukk' => 'No UKK',
		'call_sign' => 'Call Sign',
		'waktu_upload' => 'Waktu Upload',
	);
	
	public function __construct(){
		parent::__construct();
	}
		
	public function set_db($db){
		$this->db = $db;
	}
	
	public function select($users_id){
		$this->siapkanDB();
		$this->db	->select('SQL_CALC_FOUND_ROWS *', FALSE);
		
		if(!$this->sort){
			$this->db->order_by('mu.waktu_upload', 'DESC');
		}
		
		$out = new StdClass();			
		$out->datasource = $this->db->where('mu.users_id', $users_id)
									->get('manifest_upload mu')->result();
		
		$out->actualRows = $this->db->query("SELECT FOUND_ROWS() as numRows")->row()->numRows;
		
		return $out;
	}
	
	public function get($id, $users_id){	
		return $this->db->where('mu.users_id', $users_id)
						->where('mu.id', $id)
						->get('manifest_upload mu')->row();
	}
	
	public function insert($result){
		$this->db->trans_start();
		
		$informasi_kapal = $result->informasi_kapal->data;
		
		$header = array(
			'processing_id' => $result->processing_id,
			'users_id' => $result->user_id,
			'username' => $result->username,
			'cuscar_request_id' => $result->cuscar_request_id,
			'no_ukk' => $informasi_kapal->no_ukk,
			'call_sign' => $informasi_kapal->call_sign,
			'voyage' => $informasi_kapal->voyage,
			'imo_number' => $informasi_kapal->imo_number,
			'nama_kapal' => $informasi_kapal->nama_kapal,
			'pol' => $informasi_kapal->pol,
			'pod' => $informasi_kapal->pod,
			'ata' => $informasi_kapal->ata,
			'atd' => $informasi_kapal->atd,
			'eta' => $informasi_kapal->eta,
			'total_container' => count($result->containers->data),
			'total_cargo' => count($result->consignments->data),
			'nama_file_asli' => $result->original_filename,
			'waktu_upload' => $result->receive_timestamp,
			'filename' => $result->generated_filename,
		);
		
		$this->db->insert('manifest_upload', $header);
		$header_id = $this->db->insert_id();
		
		$this->db->trans_complete();
		
		return $this->db->trans_status();
	}
	
	public function dump_manifest($result){
		$this->db->trans_start();
		
		// Header
		$cuscar_header = array(
			'partner_id' => null
		);
		
		$this->db->trans_complete();
		
		return $this->db->trans_status();
	}
	
	
	
	
	
}