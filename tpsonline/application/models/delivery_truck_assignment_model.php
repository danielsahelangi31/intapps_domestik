<?php
include_once('base/modelbase.php');

class Delivery_Truck_Assignment_Model extends ModelBase{

	// Datagrid Searchable Fields
	public $searchable = array(
		'container_number' => 'Nomor Container',
		'nama_supir' => 'Nama Driver',
		'nomor_handphone' => 'Nomor Handphone',
	);
	
	// Datagrid Sortable Fields
	public $sortable = array(
		'nomor_do' => 'Nomor DO',
		'nomor_nota' => 'Nomor Nota',
		'nomor_nota' => 'Pelabuhan',
		'nama_terminal_petikemas' => 'Nama Terminal Petikemas',
	);

	public function __construct(){
		parent::__construct();	

		$this->sortable = $this->searchable;
	}
	
	public function generate_security_code(){
		$string = '';  
		  
		for ($i = 0; $i < 5; $i++) {  
			// this numbers refer to numbers of the ascii table (lower case)  
			$string .= chr(rand(97, 122));  
		}
		
		return $string;
	}
	
	/** Get
	  * Ambil data dalam bentuk single row
	  */
	public function get($id, $auth, $array = false){
		$where = array(
			'ogdta.id' => $id,
			'ogdta.trucking_company_id' => $auth->trucking_company_id
		);
		
		$datasource = $this->db	->select('cl.*, m.*, ff.id AS freight_forwarder_id, ogdta.*, ogdr.rencana_ambil, ogdr.consignee, tp.nama_terminal_petikemas, p.nama_pelabuhan')
								->join('ocean_going_delivery_request_line ogdrl', 'ogdrl.id = ogdta.ocean_going_delivery_request_line_id')
								->join('coreor_line cl', 'cl.id = ogdrl.coreor_line_id')
								->join('ocean_going_delivery_request ogdr', 'ogdr.id = ogdrl.ocean_going_delivery_request_id')
								->join('terminal_petikemas tp', 'tp.id = ogdr.terminal_petikemas_id')
								->join('pelabuhan p', 'p.id = tp.pelabuhan_id')
								->join('freight_forwarder ff', 'ff.id = ogdr.freight_forwarder_id')
								->join('member m', 'm.id = ff.member_id')
								->where($where)
								->get('ocean_going_delivery_truck_assignment AS ogdta')->row();
			
		return $datasource;
	}

	/** selectDS
	  * Ambil data dalam bentuk mini array of object, berguna untuk 
	  * data reference dalam <select> tag misalnya
	  */
	public function selectDS(){
            return $this->db->get('supir_truck')->result();	
	}
	
    public function select($trucking_company_id){
		$this->siapkanDB();
		$this->db->select('SQL_CALC_FOUND_ROWS 
							ogdta.id,
							ogdta.waktu_input,
							cl.container_number,
							ogdr.rencana_ambil,
							tp.nama_terminal_petikemas,
							ogdr.consignee,
							ogdta.tanggal_expired,
							iso_code,
							container_size,
							container_type,
							truck_id,
							ogdr.status_kirim
						', FALSE);

		if(!$this->sort){
			$this->db->order_by('ogdta.waktu_input', 'DESC');
		}

		$out = new StdClass();
		$out->datasource = $this->db->join('ocean_going_delivery_request_line AS ogdrl', 'ogdta.ocean_going_delivery_request_line_id = ogdrl.id')
									->join('coreor_line AS cl', 'ogdrl.coreor_line_id = cl.id')
									->join('ocean_going_delivery_request AS ogdr', 'ogdrl.ocean_going_delivery_request_id = ogdr.id')
									->join('terminal_petikemas AS tp', 'ogdr.terminal_petikemas_id = tp.id')
									->where('ogdta.trucking_company_id', $trucking_company_id)
									->get('ocean_going_delivery_truck_assignment AS ogdta')
									->result();
		$out->actualRows = $this->db->query("SELECT FOUND_ROWS() as numRows")->row()->numRows;

		return $out;
	}

	public function assign_truck($id, $trucking_request, $auth){
		$out = new StdClass();
		$security_code = $this->generate_security_code();
		
		$trucking_company = $this->db	->select('m.*')
										->join('member m', 'm.id = tc.member_id')
										->where('tc.id', $auth->trucking_company_id)
										->get('trucking_company tc')->row();
										
		$freight_forwarder = $this->db	->select('m.*')
										->join('member m', 'm.id = ff.member_id')
										->where('ff.id', $trucking_request->freight_forwarder_id)
										->get('freight_forwarder ff')->row();
		
		$assign_truck = new StdClass();
		$assign_truck->nama_supir = post('nama_supir');
		$assign_truck->nomor_handphone = '+62'.post('nomor_handphone');
		$assign_truck->truck_id = post('truck_id');
		$assign_truck->security_code = $security_code;
		$assign_truck->waktu_assign = date('Y-m-d H:i:s');
		
		$out->success = $this->db->where('id', $id)->update('ocean_going_delivery_truck_assignment', $assign_truck);
		
		if($out->success){
			$sms_api = model('messaging/sms');
			$email_api = model('messaging/email');
			
			// Messaging
			$sms = array(
				'nomor_hp' => '+62'.post('nomor_handphone'),
				'template' => 'OGD_GATE_IN_TICKET',
				'payload' => array(
					'assignment' => $assign_truck,
					'security_code' => $security_code,
					'container' => $trucking_request
				)
			);
			
			$email = array(
				'tujuan' => $trucking_company->email,
				'template' => 'OGD_TRUCK_ASSIGNED',
				'payload' => array(
					'assignment' => $assign_truck,
					'trucking_company' => $trucking_company,
					'freight_forwarder' => $freight_forwarder,
					'container' => $trucking_request
				)
			);
			
			$sms_api->send(array($sms));
			$email_api->send(array($email));
		}
		
		return $out;
	}

	public function update(){
		$out = new StdClass();
		
		$out->success = true;
	
		return $out;
	}
}
