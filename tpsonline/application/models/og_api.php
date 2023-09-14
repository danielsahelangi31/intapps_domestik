<?php
class OG_API extends CI_Model{
	private $wsdl = "./application/config/wsdl/tpkhub.wsdl";
	private $client = null;

	public function __construct(){
		parent::__construct();	
	}
	
	public function get_soap_client(){
		if(!$this->client){
			$this->client = new SoapClient($this->wsdl, array(
				'exceptions' => true
			));
		}
		
		return $this->client;
	}
	
	public function get_local_customer_code($terminal_petikemas_id, $freight_forwarder_id, $npwp){
		$client = $this->get_soap_client();
	
		// Get Local Company Code
		$where = array(
			'freight_forwarder_id' => $freight_forwarder_id,
			'terminal_petikemas_id' => $terminal_petikemas_id
		);
		$local_code = $this->db->where($where)->get('kode_lokal_freight_forwarder')->row();
	
		// Not found then search on the terminal
		if(!$local_code){
			$terminal = $this->db->where('id', $terminal_petikemas_id)->get('terminal_petikemas')->row();
			
			$data = array(
				'messageID' => uniqid(),
				'kodeTerminalPetikemas' => $terminal->kode_terminal_petikemas,
				'npwp' => $npwp
			);
		
			$response = $client->GetLocalFreightForwarderCodeOp($data);
			
			// Insert local
			$local_code = new StdClass();
			$local_code->freight_forwarder_id = $freight_forwarder_id;
			$local_code->terminal_petikemas_id = $terminal_petikemas_id;
			$local_code->kode_pelanggan = $response->kodePelanggan;
			$local_code->nomor_pelanggan = $response->nomorPelanggan;
			
			$this->db->insert('kode_lokal_freight_forwarder', $local_code);
		}
		
		return $local_code;
	}
	
	public function daftar_container_do($nomor_do){
		$out = new StdClass();
		
		$datasource = $this->db->select('
									tp.id AS terminal_petikemas_id,
									tp.nama_terminal_petikemas, 
									tp.kode_terminal_petikemas,
									p.nama_pelabuhan, 
									ch.sender_identification AS kode_shipping_line,
									cd.consignee,
									cd.call_sign,
									cd.voyage,
									cd.vessel_name,
									cd.port_of_loading AS pol,
									cd.port_of_destination AS pod,
									cd.arrival_date as arrival_date,
									
									cd.id AS coreor_detail_id,
									cd.do_expired,
									cd.bl_number AS nomor_bl
								')
								->join('coreor_header ch', 'ch.id = cd.coreor_header_id')
								->join('terminal_petikemas tp', 'tp.kode_terminal_petikemas = ch.recipient')
								->join('pelabuhan p', 'p.id = tp.pelabuhan_id')
								->where('cd.do_number', $nomor_do)
								->where('cd.active', 1)
								->get('coreor_detail cd')
								->row();
								
		if($datasource){
			if(strtotime($datasource->do_expired) > time()){
				$detail = $this->db	->select('
										cl.id AS coreor_line_id,
										container_number AS nomor_container,
										iso_code,
										hazard,
										seal_number AS seal_number,
										commodity,
										ogdrl.id AS requested_flag
									')
									->join('ocean_going_delivery_request_line ogdrl', 'ogdrl.coreor_line_id = cl.id', 'left')
									->where('coreor_detail_id', $datasource->coreor_detail_id)
									->where('active', 1)
									->get('coreor_line cl')->result();
									
				// Enrich Detail
				$need_tobe_checked = array();
				foreach($detail as $row){
					// Has been requested
					$row->siap_delivery = false;
					
					if($row->requested_flag){
						$row->status_code = 401;
						$row->status_msg = 'Petikemas sudah di request sebelumnya di Smart Cargo';	
					}else{
						// Tambahkan ke check redseal
						$need_tobe_checked[$row->nomor_container] = $row;
						
						$row->siap_delivery = true;
						$row->status_msg = 'Siap Delivery';
					}
				}
				
				try{
					if($need_tobe_checked){
						$param = array(
							'messageID' => uniqid(),
							'kodeTerminalPetikemas' => $datasource->kode_terminal_petikemas,
							'noUKK' => '',
							'callSign' => $datasource->call_sign,
							'voyage' => $datasource->voyage,
							'container' => array()
						);
					
						foreach($need_tobe_checked as $nomor_container => $row_ref){
							$param['container'][] = array(
								'containerNumber' => $nomor_container
							);
						}
						
						$client = $this->get_soap_client();
						$response = $client->ContainerCheckOp($param);
						
						if(!is_array($response->containerResult)){
							$response->containerResult = array($response->containerResult);
						}
						
						foreach($response->containerResult as $res){
							$target = $need_tobe_checked[$res->containerNo];
							
							if($res->requestedFlag){
								$target->siap_delivery = false;
								$target->status_code = 401;
								$target->status_msg = 'Petikemas sudah di request di Inhouse Terminal';
							}
							
							if($res->redSealFlag){
								$target->siap_delivery = false;
								$target->status_code = 401;
								$target->status_msg = 'Tidak bisa diambil. Petikemas masuk jalur merah.';
							}
						}
					}
					
					$out->success = true;
					$out->msg_code = 200;
					$out->datasource = $datasource;
					$out->datasource->detail = $detail;
				}catch(SoapFault $fault){				
					if($fault->faultcode == 'SOAP-ENV:Server'){
						$detail = $fault->detail->faultMessage;
						
						$out->success = false;
						$out->msg_code = $detail->errorCode;
						$out->msg = $detail->errorMessage;
						$out->payload = isset($detail->payload) ? $detail->payload : null;
					}else{
						$out->success = false;
						$out->msg_code = 503;
						$out->msg = 'Tidak dapat menghubungi Terminal Petikemas, Mungkin sedang dalam perawatan rutin atau jaringan sibuk. Silakan coba beberapa saat lagi.';
						$out->payload = null;
					}
				}catch(Exception $e){
					$out->success = false;
					$out->msg_code = 500;
					$out->msg = 'Internal Server Error';
				}
			}else{
				$out->success = false;
				$out->msg_code = 401;
				$out->msg = 'Delivery Order telah kadaluarsa.';	
			}
		}else{
			$out->success = false;
			$out->msg_code = 400;
			$out->msg = 'Delivery Order tidak ditemukan.';	
		}
		
		return $out;
	}
	
	public function send_data($row, $auth){
		$out = new StdClass();
		$client = $this->get_soap_client();
	
		// Get Local Company Code
		$local_code = $this->get_local_customer_code($row->terminal_petikemas_id, $auth->freight_forwarder_id, $auth->npwp);
	
		$param = array(
			'messageID' => uniqid(),
			'kodeTerminalPetikemas' => $row->kode_terminal_petikemas,
			'kodeCabang' => $row->pelabuhan_id,
			'noRequestILCS' => $row->nomor_request_ilcs,
			'nomorDO' => $row->nomor_do,
			'nomorBL' => $row->nomor_bl,
			'nomorSPPB' => $row->nomor_sppb,
			'tanggalSPPB' => $row->tanggal_sppb,
			'callSign' => $row->call_sign,
			'voyageIn' => $row->voyage,
			'kdPBM' => $local_code->kode_pelanggan,
			'agen' => $row->kode_shipping_line,
			'deliveryViaStatus' => 'TRUCKING',
			'tujuan' => $row->consignee,
			'keterangan' => NULL,
			'rencanaDelivery' => $row->rencana_ambil,
			'tanggalRequest' => $row->waktu_input,
			'username' => NULL,
			'detail' => array()
		);
		
		foreach($row->detail as $cont){
			$param['detail'][] = array(
				'nomorContainer' => $cont->nomor_container,
				'noSP2' => NULL,
				'noBPID' => NULL,
				'commodity' => $cont->commodity,
				'hazard' => $cont->hazard,
				'containerISOCode' => $cont->iso_code
			);
		}
		
		try{
			$response = $client->SendDataOp($param);
			
			$out->success = true;
			$out->nomor_request_inhouse = $response->noReqInhouse;
		}catch(SoapFault $fault){
			if($fault->faultcode == 'SOAP-ENV:Server'){
				$detail = $fault->detail->Data->faultMessage;
				
				$out->success = false;
				$out->msg_code = $detail->errorCode;
				$out->msg = $detail->errorMessage;
				$out->payload = isset($detail->payload) ? $detail->payload : null;
			}else{
				$out->success = false;
				$out->msg_code = 503;
				$out->msg = 'Tidak dapat menghubungi Terminal Petikemas, Mungkin sedang dalam perawatan rutin atau jaringan sibuk. Silakan coba beberapa saat lagi.';
				$out->payload = null;
			}
		}catch(Exception $e){
			$out->success = false;
			$out->msg_code = 500;
			$out->msg = 'Internal Server Error';
		}
		
		return $out;
	}
	
	public function preview_invoice($data){
		$param = array(
			'messageID' => uniqid(),
			'kodeTerminalPetikemas' => $data->kode_terminal_petikemas,
			'noReqInhouse' => $data->nomor_request_inhouse
		);
		
		$client = $this->get_soap_client();
		$response = $client->PreviewInvoiceOp($param);
		
		return $this->extract_invoice($response);
	}
	
	public function generate_invoice($data, $auth){
		$client = $this->get_soap_client();
	
		// Get Local Company Code
		$local_code = $this->get_local_customer_code($data->terminal_petikemas_id, $auth->freight_forwarder_id, $auth->npwp);
	
		$param = array(
			'messageID' => uniqid(),
			'kodeTerminalPetikemas' => $data->kode_terminal_petikemas,
			'noReqInhouse' => $data->nomor_request_inhouse,
			'kdCustomer' => $local_code->kode_pelanggan
		);
		
		$response = $client->GenerateInvoiceOp($param);
		
		return $this->extract_invoice($response);
	}
	
	public function generate_paid_invoice($data, $auth){
		$out = new StdClass();
		$client = $this->get_soap_client();
	
		// Get Local Company Code
		$local_code = $this->get_local_customer_code($data->terminal_petikemas_id, $auth->freight_forwarder_id, $auth->npwp);
	
		$param = array(
			'messageID' => uniqid(),
			'kodeTerminalPetikemas' => $data->kode_terminal_petikemas,
			'noReqInhouse' => $data->nomor_request_inhouse,
			'kdCustomer' => $local_code->kode_pelanggan
		);
		
		try{
			$response = $client->GeneratePaidInvoiceOp($param);
			
			$out->success = true;
			$out->msg_code = 200;
			$out->msg = 'Invoice berhasil dilunasi';
			$out->invoice = $this->extract_invoice($response);
		}catch(SoapFault $fault){		
			if($fault->faultcode == 'SOAP-ENV:Server'){
				$detail = $fault->detail->faultMessage;
				
				$out->success = false;
				$out->msg_code = $detail->errorCode;
				$out->msg = $detail->errorMessage;
				$out->payload = isset($detail->payload) ? $detail->payload : null;
			}else{
				$out->success = false;
				$out->msg_code = 503;
				$out->msg = 'Tidak dapat menghubungi Terminal Petikemas, Mungkin sedang dalam perawatan rutin atau jaringan sibuk. Silakan coba beberapa saat lagi.';
				$out->payload = null;
			}
		}catch(Exception $e){
			$out->success = false;
			$out->msg_code = 500;
			$out->msg = 'Internal Server Error';
		}
		
		return $out;
	}
	
	public function send_gate_ticket($request, $ticket, $auth){
		$out = new StdClass();
		$client = $this->get_soap_client();
	
		// Get Local Company Code
		$local_code = $this->get_local_customer_code($request->terminal_petikemas_id, $auth->freight_forwarder_id, $auth->npwp);
	
		$param = array(
			'messageID' => uniqid(),
			'kodeTerminalPetikemas' => $request->kode_terminal_petikemas,
			'noReqInhouse' => $request->nomor_request_inhouse,
			'ticket' => $ticket
		);
		
		try{
			$response = $client->SendGateInTicketOp($param);
			
			$out->success = true;
			$out->msg_code = 200;
			$out->msg = 'Berhasil mengirimkan tiket gate in';
			$out->response = $response;
		}catch(SoapFault $fault){		
			if($fault->faultcode == 'SOAP-ENV:Server'){
				$detail = $fault->detail->faultMessage;
				
				$out->success = false;
				$out->msg_code = $detail->errorCode;
				$out->msg = $detail->errorMessage;
				$out->payload = isset($detail->payload) ? $detail->payload : null;
			}else{
				$out->success = false;
				$out->msg_code = 503;
				$out->msg = 'Tidak dapat menghubungi Terminal Petikemas, Mungkin sedang dalam perawatan rutin atau jaringan sibuk. Silakan coba beberapa saat lagi.';
				$out->payload = null;
			}
		}catch(Exception $e){
			$out->success = false;
			$out->msg_code = 500;
			$out->msg = 'Internal Server Error';
		}
		
		return $out;
	}
	
	
	
	
	
	
	
	
	private function extract_invoice($response){
		if(!isset($response->invoice->detail)){
			$response->invoice->detail = array();
		}else if(!is_array($response->invoice->detail)){
			$response->invoice->detail = array($response->invoice->detail);
		}
		
		return $response->invoice;
	}	
	
	private function extract_ticket($response){
		// Not implemented
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}