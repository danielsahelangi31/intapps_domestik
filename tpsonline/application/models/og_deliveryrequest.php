<?php
include_once('base/modelbase.php');

class OG_DeliveryRequest extends ModelBase{
	// Datagrid Sortable Fields
	public $sortable = array(
		'nomor_request_inhouse' => 'Nomor Request Inhouse',
		'nomor_do' => 'Nomor DO',
		'rencana_ambil' => 'Tanggal Ambil',
		'consignee' => 'Consignee',
		'nama_terminal_petikemas' => 'Nomor Nota',
		'lunas' => 'Lunas',
	);
	
	// Datagrid Searchable Fields
	public $searchable = array(
		'nomor_do' => 'Nomor DO',
		'nomor_nota' => 'Nomor Nota',
		'nomor_nota' => 'Pelabuhan',
		'nama_terminal_petikemas' => 'Nama Terminal Petikemas',
	);
	
	public function __construct(){
		parent::__construct();	
	}
	
	public function generate_nomor_request_ilcs(){
		
	}
	
	public function generate_security_code(){
		$string = '';  
		  
		for ($i = 0; $i < 5; $i++) {  
			// this numbers refer to numbers of the ascii table (lower case)  
			$string .= chr(rand(97, 122));  
		}
		
		return $string;
	}
	
	public function select($freight_forwarder_id){
		$this->siapkanDB();
		$this->db	->select('SQL_CALC_FOUND_ROWS ogdr.*, count(ogdrl.id) AS total_container, n.nomor_faktur_pajak, n.flag_lunas, tp.nama_terminal_petikemas, p.nama_pelabuhan', FALSE);
		
		if(!$this->sort){
			$this->db->order_by('ogdr.waktu_input', 'DESC');
		}
		
		$out = new StdClass();			
		$out->datasource = $this->db->join('nota n', 'n.id = ogdr.nota_id', 'left')
									->join('terminal_petikemas tp', 'tp.id = ogdr.terminal_petikemas_id')
									->join('pelabuhan p', 'p.id = tp.pelabuhan_id')
									->join('ocean_going_delivery_request_line ogdrl', 'ogdrl.ocean_going_delivery_request_id = ogdr.id')
									->where('ogdr.freight_forwarder_id', $freight_forwarder_id)
									->group_by('ogdr.id')
									->get('ocean_going_delivery_request ogdr')->result();
		
		$out->actualRows = $this->db->query("SELECT FOUND_ROWS() as numRows")->row()->numRows;
		
		return $out;
	}
	
	public function get($id, $auth, $extend = false){
		$out = new StdClass();
		
		$datasource = $this->db	->select('ogdr.*, tp.id AS terminal_petikemas_id, tp.kode_terminal_petikemas, tp.nama_terminal_petikemas, p.id AS pelabuhan_id, p.kode_pelabuhan, p.nama_pelabuhan')
								->join('terminal_petikemas tp', 'tp.id = ogdr.terminal_petikemas_id')
								->join('pelabuhan p', 'p.id = tp.pelabuhan_id')
								->where('ogdr.id', $id)
								->where('ogdr.freight_forwarder_id', $auth->freight_forwarder_id)
								->get('ocean_going_delivery_request ogdr')->row();
							
		if($datasource){
			// Get Detail
			$where = array(
				'ogdrl.ocean_going_delivery_request_id' => $datasource->id
			);
			
			$this->db->select('
							ogdrl.*, 
							container_number AS nomor_container,
							seal_number,
							iso_code AS kode_container,
							container_size AS ukuran_container,
							container_type AS tipe_container,
							hazard,
							seal_number,
							commodity
						');
			
			if($extend){
				$this->db	->select('ogdta.id AS ocean_going_delivery_truck_assignment_id, ogdta.nama_supir, ogdta.nomor_handphone, tc.id AS trucking_company_id')
							->join('ocean_going_delivery_truck_assignment ogdta', 'ogdta.ocean_going_delivery_request_line_id = ogdrl.id', 'left')
							->join('trucking_company tc', 'tc.id = ogdta.trucking_company_id', 'left');
			}
				
			$detail = $this->db	->join('coreor_line cl', 'cl.id = ogdrl.coreor_line_id')
								->where($where)
								->get('ocean_going_delivery_request_line ogdrl')->result();
		
			$datasource->detail = $detail;
			
			$out->success = true;
			$out->msg_code = 200;
			$out->datasource = $datasource;
		}else{
			$out->success = false;
			$out->msg_code = 400;
			$out->msg = 'Delivery Request tidak ditemukan atau anda tidak memiliki akses ke data ini.';
			$out->datasource = null;
		}
		
		return $out;
	}
	
	public function get_invoice($nota_id){
		$out = new StdClass();
		
		$nota = $this->db->where('id', $nota_id)->get('nota')->row();
		if($nota){
			$nota->detail = $this->db->where('nota_id', $nota_id)->get('nota_line')->result();
			
			$out->success = true;
			$out->msg_code = 200;
			$out->datasource = $nota;
		}else{
			$out->success = false;
			$out->msg_code = 400;
			$out->msg = 'Nota tidak ditemukan';
			$out->datasource = NULL;
		}
		
		return $out;
	}
	
	public function add($auth, $send = false){
		$out = new StdClass();
		
		$nomor_do = post('nomor_do');
		
		$api = model('og_api');
		$request_data = $api->daftar_container_do($nomor_do);
		
		if($request_data->success){
			$data_do = $request_data->datasource;
			
			// Populate Container
			$available_containers = array();
			foreach($data_do->detail as $container){
				// Mark requested container
				$available_containers[$container->coreor_line_id] = $container->requested_flag ? false : true;	
			}
			
			$this->db->trans_begin();
			
			$header = array(
				'freight_forwarder_id' => $auth->freight_forwarder_id,
				'terminal_petikemas_id' => $data_do->terminal_petikemas_id,
				'nomor_request_ilcs' => NULL,
				'nomor_request_inhouse' => NULL,
				'nomor_do' => post('nomor_do'),
				'expired_do' => $data_do->do_expired,
				'nomor_sppb' => post('nomor_sppb'),
				'tanggal_sppb' => post('tanggal_sppb') ? date('Y-m-d', strtotime(post('tanggal_sppb'))) : NULL,
				'nomor_bl' => $data_do->nomor_bl,
				'nomor_ukk' => NULL,
				'consignee' => $data_do->consignee,
				'port_of_loading' => $data_do->pol,
				'port_of_discharge' => $data_do->pod,
				'kode_shipping_line' => $data_do->kode_shipping_line,
				'voyage' => $data_do->voyage,
				'call_sign' => $data_do->call_sign,
				'tanggal_datang' => $data_do->arrival_date,
				'rencana_ambil' => post('rencana_ambil') ? date('Y-m-d', strtotime(post('rencana_ambil'))) : NULL,
				'waktu_input' => date('Y-m-d H:i:s'),
				'status_kirim' => 0,
			);
			
			$this->db->insert('ocean_going_delivery_request', $header);
			$ocean_going_delivery_request_id = $this->db->insert_id();
			
			// Make sure no duplicate values
			$coreor_lines = array_unique((array) post('coreor_line_id'));
			
			foreach($coreor_lines as $id){
				// Skip container not on the list
				if(isset($available_containers[$id]) && $available_containers[$id]){
					$line = array(
						'ocean_going_delivery_request_id' => $ocean_going_delivery_request_id,
						'coreor_line_id' => $id
					);
					
					$this->db->insert('ocean_going_delivery_request_line', $line);
				}
			}
			
			if($send){
				$this->send_data($ocean_going_delivery_request_id);	
			}
			
			$this->db->trans_complete();
			
			$out->success = $this->db->trans_status();
				
			if($out->success){
				$out->msg_code = 200;
				if($send){
					$out->msg = 'Berhasil simpan dan mengirimkan delivery order';
				}else{	
					$out->msg = 'Berhasil simpan delivery order';
				}
			}else{
				$out->msg_code = 601;
				$out->msg = 'Galat basis data, silakan coba lagi';
			}
		}else{
			$out->success = false;
			$out->msg_code = $request_data->msg_code;
			$out->msg = $request_data->msg;	
		}
		
		return $out;
	}
	
	public function edit($id, $auth){
		$out = new StdClass();
		
		$where = array(
			'id' => $id, 
			'freight_forwarder_id' => $auth->freight_forwarder_id
		);
		
		$original = $this->db->where($where)->get('ocean_going_delivery_request')->row();
		
		if($original){
			if($original->status_kirim == 0){
				$this->db->trans_start();
				
				$header = array(
					'nomor_sppb' => post('nomor_sppb'),
					'tanggal_sppb' => post('tanggal_sppb') ? date('Y-m-d', strtotime(post('tanggal_sppb'))) : NULL,
					'rencana_ambil' => post('rencana_ambil') ? date('Y-m-d', strtotime(post('rencana_ambil'))) : NULL,
				);
				
				$this->db->where('id', $id)->update('ocean_going_delivery_request', $header);
				
				$this->db->trans_complete();
				
				$out->success = $this->db->trans_status();
					
				if($out->success){
					$out->msg_code = 201;
					$out->msg = 'Berhasil mengubah delivery order';
				}else{
					$out->msg_code = 601;
					$out->msg = 'Galat basis data, silakan coba lagi';
				}
			}else{
				$out->success = false;
				$out->msg_code = 401;
				$out->msg = 'Tidak dapat melakukan perubahan terhadap request deliery yang sudah dikirimkan';	
			}
		}else{
			$out->success = false;
			$out->msg_code = 400;
			$out->msg = 'Request delivery tidak ditemukan atau anda tidak memiliki hak terhadap request delivery ini';	
		}
		
		return $out;
	}
	
	public function send_data($id, $auth){
		$out = new StdClass();
		
		$datasource = $this->db	->select('ogdr.*, tp.id AS terminal_petikemas_id, tp.kode_terminal_petikemas, p.id AS pelabuhan_id, p.kode_pelabuhan')
								->join('terminal_petikemas tp', 'tp.id = ogdr.terminal_petikemas_id')
								->join('pelabuhan p', 'p.id = tp.pelabuhan_id')
								->where('ogdr.id', $id)
								->get('ocean_going_delivery_request ogdr')->row();
								
		if($datasource){
			if($datasource->status_kirim == 1){
				$out->success = false;
				$out->msg_code = 801;
				$out->msg = 'Delivery Request sudah dikirim.';			
			}else if(strtotime($datasource->rencana_ambil) < time()){
				$out->success = false;
				$out->msg_code = 802;
				$out->msg = 'Tanggal Rencana Pengambilan Delivery Request sudah berlalu. Silakan <a href="'.site_url('delivery_request_og/edit/'.$id).'"><strong>Ubah Tanggal Delivery</strong></a> lalu ulangi proses kirim.';	
			}else{
				// Get Detail
				$where = array(
					'ogdrl.ocean_going_delivery_request_id' => $datasource->id
				);
				
				$detail = $this->db	->select('
										ogdrl.*, 
										container_number AS nomor_container,
										iso_code,
										hazard,
										seal_number,
										commodity
									')
									->join('coreor_line cl', 'cl.id = ogdrl.coreor_line_id')
									->where($where)
									->get('ocean_going_delivery_request_line ogdrl')->result();
			
				$datasource->detail = $detail;
				
				// Send to Inhouse
				$mod = model('og_api');
				$response = $mod->send_data($datasource, $auth);
				
				if($response->success){
					// Update Local
					$upd = array(
						'nomor_request_inhouse' => $response->nomor_request_inhouse,
						'status_kirim' => 1
					);
					$this->db->where('id', $datasource->id)->update('ocean_going_delivery_request', $upd);
					
					$out->success = true;
					$out->msg_code = 200;
					$out->msg = 'Sukses kirim delivery order';
				}else{
					$out->success = false;
					$out->msg_code = $response->msg_code;
					$out->msg = $response->msg;
					$out->payload = $response->payload;
				}
			}
		}else{
			$out->success = false;
			$out->msg_code = 400;
			$out->msg = 'Delivery Request tidak ditemukan';
		}
		
		return $out;
	}
	
	public function save_paid_invoice($id, $member_id, $invoice){
		return $this->save_invoice($id, $member_id, $invoice, 1);
	}
	
	public function save_unpaid_invoice($id, $member_id, $invoice){
		return $this->save_invoice($id, $member_id, $invoice, 0);
	}
	
	public function save_invoice($id, $member_id, $invoice, $flag_lunas){
		$header = $invoice->header;
		
		$this->db->trans_start();
		
		$nota = array(
			'member_id' => $member_id,
			'nomor_faktur_pajak' => preg_replace('/(\.|-)+/', '', $header->noFakturPajak),
			'kode_uper' => $header->kdUper,
			'kd_cabang' => NULL,
			'currency' => $header->signCurrency,
			'discount' => $header->discount,
			'administrasi' => $header->administrasi,
			'ppn' => $header->ppn,
			'ppn_subsidi' => $header->ppnSubsidi,
			'debet' => $header->debet,
			'kredit' => $header->kredit,
			'materai' => $header->materai,
			'total' => $header->total,
			'waktu_pelunasan' => NULL,
			'tanggal_terbit' => date('Y-m-d H:i:s'),
			'flag_lunas' => $flag_lunas
		);
		
		$this->db->insert('nota', $nota);
		$nota_id = $this->db->insert_id();
		
		foreach($invoice->detail as $detail){
			$nota_line = array(
				'nota_id' => $nota_id,
				'qty' => $detail->qty,
				'size_cont' => $detail->sizeCont,
				'sty_name' => $detail->styName,
				'status_cont' => $detail->nmJenisPemilik,
				'tarif' => $detail->tarif,
				'total' => $detail->total,
				'uraian' => $detail->uraian,
				'total_hari' => $detail->totalHari,
				'hazard' => $detail->hazard,
				'ei' => $detail->ei,
				'oi' => $detail->oi,
				'crane' => $detail->crane,
				'plug_in' => $detail->plugIn,
				'plug_out' => $detail->plugOut,
				'jumlah_jam' => $detail->hours,
				'tanggal_awal' => $detail->tglAwal,
				'tanggal_akhir' => $detail->tglAkhir
			);
			
			$this->db->insert('nota_line', $nota_line);
		}
		
		$this->db->set('nota_id', $nota_id)->where('id', $id)->update('ocean_going_delivery_request');
		
		$this->db->trans_complete();
		
		return $this->db->trans_status();
	}
	
	public function assign_truck($id, $auth){
		$og_api = model('og_api');
		$sms_api = model('messaging/sms');
		$email_api = model('messaging/email');
		
		$metode_kirim 			= (array) post('metode_kirim');
		$trucking_company_id  	= (array) post('trucking_company_id');
		$truck_id  				= (array) post('truck_id');
		$nomor_handphone 		= (array) post('nomor_handphone');
		$rfid	 				= (array) post('rfid');
		
		$sms_outbox = array();
		$mail_outbox = array();
		$ticket_outbox = array();
		
		$this->db->trans_start();
		
		// Caching Freight Forwarder
		$freight_forwarder = $this->db->where('id', $auth->member_id)->get('member')->row();
										
		
		// Caching Request Line
		$det = $this->db->select('ogdrl.id, ogdr.rencana_ambil, cl.container_number, cl.container_size, cl.seal_number, cl.iso_code, cl.commodity, cl.hazard, cd.consignee, tp.nama_terminal_petikemas')
						->join('terminal_petikemas tp', 'tp.id = ogdr.terminal_petikemas_id')
						->join('ocean_going_delivery_request_line ogdrl', 'ogdr.id = ogdrl.ocean_going_delivery_request_id')
						->join('coreor_line cl', 'cl.id = ogdrl.coreor_line_id')
						->join('coreor_detail cd', 'cd.id = cl.coreor_detail_id')
						->where('ogdr.id', $id)
						->get('ocean_going_delivery_request ogdr')->result();
		
		$request_line = array();
		foreach($det as $row){
			$request_line[$row->id] = $row;
		}
		
		// Executing Each Ticket Sending Method
		$errors = array();
		
		$trucking_count = 0;
		$sms_count = 0;
		$rfid_count = 0;
		foreach($metode_kirim as $detail_id => $val){
			if(isset($request_line[$detail_id])){
				$container = $request_line[$detail_id];
				$security_code = $this->generate_security_code();
				
				// Base Value
				$truck_assignment = array(
					'ocean_going_delivery_request_line_id' => $detail_id,
					'security_code' => $security_code,
					'waktu_input' => date('Y-m-d H:i:s'),
				);
				
				switch($val){
					case 'TRUCKING_COMPANY':
						if(isset($trucking_company_id[$detail_id])){
							$tc_id = $trucking_company_id[$detail_id];
							
							$trucking_company = $this->db	->join('member m', 'm.id = tc.member_id')
															->where('tc.id', $tc_id)->get('trucking_company tc')->row();
							
							
							if($trucking_company){						
								$truck_assignment['trucking_company_id'] = $tc_id;
								$this->db->insert('ocean_going_delivery_truck_assignment', $truck_assignment);
														
								$email = array(
									'tujuan' => $trucking_company->email,
									'template' => 'OGD_REQUEST_TRUCKING',
									'payload' => array(
										'trucking_company' => $trucking_company,
										'container' => $container,
										'freight_forwarder' => $freight_forwarder
									)
								);
								
								$mail_outbox[] = $email;
								
								$trucking_count++;
							}
						}
						
						break;
					
					case 'SMS':
						if(isset($nomor_handphone[$detail_id]) && isset($truck_id[$detail_id])){
							$truck_id = $truck_id[$detail_id];
							$no_hp = '+62'.$nomor_handphone[$detail_id];
							
							$truck_assignmetn['truck_id'] = $truck_id;
							$truck_assignment['nomor_handphone'] = $no_hp;
							$truck_assignment['waktu_assign'] =  date('Y-m-d H:i:s');
							
							$this->db->insert('ocean_going_delivery_truck_assignment', $truck_assignment);
							
							$sms = array(
								'nomor_hp' => $no_hp,
								'template' => 'OGD_GATE_IN_TICKET',
								'payload' => array(
									'security_code' => $security_code,
									'container' => $container
								)
							);
							
							$sms_outbox[] = $sms;
							
							$ticket = array(
								'nomorContainer' => $container->container_number,
								'truckID' => $truck_id,
								'namaSupir' => '',
								'securityCode' => $security_code
							);
							
							$ticket_outbox[] = $ticket;
							
							
							$sms_count++;
						}
						
						break;
						
					case 'RFID':
						// NOT IMPLEMENTED
						
						$rfid_count++;
						break;
				}
			}
		}
		
		$this->db->trans_complete();
		
		if($this->db->trans_status()){
			$sms_api->send($sms_outbox);
			$email_api->send($mail_outbox);
			
			// Get Gate Ticket Information to Inhouse
			if($ticket_outbox){
				$del_req = $this->get($id, $auth)->datasource;
				$result = $og_api->send_gate_ticket($del_req, $ticket_outbox, $auth);
				
				$out->success = $result->success;
				$out->msg_code = $result->msg_code;
				$out->msg = $result->msg;
			}else{
				if($trucking_count > 0){
					$out->success = true;
					$out->msg_code = 201;
					$out->msg = 'Berhasil mendelegasikan pengambilan petikemas ke Perusahaan Angkutan terpilih.';
				}else{
					$out->success = true;
					$out->msg_code = 201;
					$out->msg = 'Tidak ada tiket yang dikirim';
				}
			}
		}else{
			$out->success = false;
			$out->msg_code = 601;
			$out->msg = 'Galat basis data, silakan coba lagi';
		}
		
		return $out;
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}