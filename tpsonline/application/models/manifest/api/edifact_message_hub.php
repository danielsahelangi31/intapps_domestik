<?php
class Edifact_Message_Hub extends CI_Model{
	private $wsdl = "./application/config/wsdl/sc_message_hub_converter.wsdl";
	private $client = null;
	
	private $local_db = null;
	private $port_cache = array();
	
	public function get_soap_client(){
		if(!$this->client){
			$this->client = new SoapClient($this->wsdl, array(
				'exceptions' => true,
				'cache_wsdl' => WSDL_CACHE_NONE
			));
		}
		
		return $this->client;
	}
	
	public function get_db(){
		if(!$this->local_db) $this->local_db = $this->load->database(ILCS_MASTER_REFERENCE_DB, TRUE);
		return $this->local_db;
	}
	
	public function convert_date($date_str){
		return date('Ymd', strtotime($date_str));
	}
	
	public function convert_datetime($date_str){
		return date('YmdHis', strtotime($date_str));
	}
	
	public function get_port_name($locode){
		$db = $this->get_db();
		
		if(isset($this->port_cache[$locode])){
			return $this->port_cache[$locode];
		}else{		
			if($row = $db->where('locode', $locode)->get('location')->row()){
				$this->port_cache[$locode] = $row->location_name;
				return $row->location_name;
			}else{
				return NULL;
			}
		}
	}
	
	public function throw_cuscar($datasource){
		$out = new StdClass();
		
		$header = $datasource->informasi_kapal->data;
		$containers = array();
		
		foreach($datasource->containers->data as $cont){
			$temp = array(
				'container_number' => $cont->container_number,
				'container_pol' => $cont->pol,
				'container_pod' => $cont->pod,
				'container_size' => $cont->size,
				'container_type' => $cont->type,
				'container_status' => $cont->empty ? 'MTY' : $cont->status,
				'container_isocode' => $cont->iso_code_container,
				'container_reffer_temp' => $cont->reefer_min_temp,
				'container_gross_weight' => $cont->gross_weight,
				'container_tare_weight' => $cont->tare_weight,
				'direction' => $header->direction == 'INBOUND' ? 'I' : 'E',
				'dimension_left' => $cont->overdimension_left,
				'dimension_right' => $cont->overdimension_right,
				'dimension_front' => $cont->overdimension_front,
				'dimension_back' => $cont->overdimension_back,
				'dimension_general' => $cont->overdimension_height,
				'container_reffer_type' => $cont->temp_unit,
				'container_seal_carrier' => $cont->carrier_seal_number,
				'container_seal_custom' => $cont->custom_seal_number,
				'container_operator' => $cont->consignment_link ? $cont->consignment_link->carrier_operator_code : NULL
			);
			
			$containers[] = $temp;
		}
		
		$goods_info = array();
		$transport_stages = array();
		
		foreach($datasource->consignments->data as $consignment){
			if($consignment->transport_stage){
				$transport_stages[$consignment->bl_number] = $consignment->transport_stage;
			}
			
			$goods = array(
				'GD_BL_NUMBER' => $consignment->bl_number,
				'GD_PLACE_OF_DESPATCH' => $consignment->despatch_place_code,
				'GD_PLACE_OF_DESPATCH_NAME' => $this->get_port_name($consignment->despatch_place_code),
				'GD_PORT_OF_LOADING_ORIGINAL' => $consignment->load_port_code,
				'GD_PORT_LOADING_ORIGINAL_NAME' => $this->get_port_name($consignment->load_port_code),
				'GD_PORT_DISCHARGE' => $consignment->discharge_port_code,
				'GD_PORT_DISCHARGE_NAME' => $this->get_port_name($consignment->discharge_port_code),
				'GD_PORT_DESTINATION' => $consignment->destination_port_code,
				'GD_PORT_DESTINATION_NAME' => $this->get_port_name($consignment->destination_port_code),
				'GD_PORT_LOADING' => $header->pod,
				'GD_PORT_LOADING_NAME' => $this->get_port_name($header->pod),
				'GD_ETD' => $this->convert_datetime($consignment->etd),
				'GD_SHIPPER' => $consignment->shipper_name,
				'GD_CONSIGNEE' => $consignment->consignee_name,
				'GD_CONSIGNOR' => NULL,
				'GD_FF' => NULL,
				'GD_FIRST_PARTY' => $consignment->notify_name,
				'PACKAGES' => array()
			);
			
			foreach($consignment->packages as $package){
				$goods['PACKAGES'][] = array(
					'EQ_ID' => $package->container_number ? $package->container_number : NULL,
					'GD_PACKAGE_QTY' => $package->qty,
					'GD_PACKAGE_TYPE' => $package->un_packaging_code,
					'GD_DESC' => $package->description,
					'GD_VOLUME' => $package->volume,
					'GD_VOLUME_UNIT' => $package->volume_unit,
					'GD_GROSS_WEIGHT' => $package->gross_weight,
					'GD_GROSS_WEIGHT_UNIT' => $package->weight_unit,
					'GD_NET_WEIGHT' => $package->net_weight,
					'GD_NET_WEIGHT_UNIT' => $package->weight_unit,
					'GD_SHIPPING_MARKS' => $package->remarks,
					'GD_HS_CODE' => $package->hs_code,
					'msg_flag' => 'ORIGINAL', // Hardcoded, harusnya dideteksi
					'HazzardInfo' => array(
						array(
							'hazzard_flag' => $package->hazard ? 'Y' : 'N',
							'dgs_imo_number' => $package->imo_dg_code,
							'dgs_undg_number' => $package->un_dg_code,
							'dgs_temp' => $package->flash_point,
							'dgs_temp_unit' => $package->temp_unit,
							'dgs_packing_type' => $package->dg_packaging_type,
							'dgs_procedure' => $package->dg_handling_instruction,
							'dgs_description' => $package->dg_description,
						)
					)
				);
			}
			
			$goods_info[] = $goods;
		}
		
		$transports = array(
			array(
				'transport_stage' => 'MAIN-CARRIAGE TRANSPORT',
				'transport_nextport_location' => $header->next_port,
				'transport_callsign' => $header->call_sign,
				'transport_pol' => $header->pol,
				'transport_pod' => $header->pod,
				'transport_voyage_in' => $header->previous_voyage,
				'transport_voyage_out' => $header->voyage,
				'transport_name' => $header->nama_kapal,
				'transport_operator' => NULL,
				'transport_eta' => $this->convert_datetime($header->eta),
				'transport_etd' => NULL, // $this->convert_datetime($header->etd),
				'terminal_ukk_number' => $header->no_ukk
			)
		);
		
		foreach($transport_stages as $bl_number => $transport_stage){
			$transports[] = array(
				'transport_stage' => $transport_stage,
				'transport_nextport_location' => $header->next_port,
				'transport_callsign' => $header->call_sign,
				'transport_pol' => $header->pol,
				'transport_pod' => $header->pod,
				'transport_voyage_in' => $header->previous_voyage,
				'transport_voyage_out' => $header->voyage,
				'transport_name' => $header->nama_kapal,
				'transport_operator' => NULL,
				'transport_ata' => $this->convert_datetime($header->ata),
				'transport_atd' => $this->convert_datetime($header->atd),
				'transport_eta' => $this->convert_datetime($header->eta),
				'transport_etd' => NULL,
				'terminal_ukk_number' => $header->no_ukk
			);
		}
		
		$payload = array(
			'HEADER' => array(
				'processing_machine' => 'MANIFACE',
				'processing_file' => $datasource->original_filename,
				'processing_date' =>  str_replace(' ', 'T', gmdate('Y-m-d H:i:s')),
				'processing_send_date' => str_replace(' ', 'T', gmdate('Y-m-d H:i:s')),
				'processing_id' => $datasource->processing_id,
				'processing_status' => 'OK'
			),
			'DETAIL' => array(
				'type_edi' => 'CUSCAR',
				
				'PartnerInfo' => array(
					'partner_sender_id' => '782', // Shipping Agent ID
					'partner_recipient_id' => 'PCS_MANIFEST_IFACE', // 
					'partner_transaction_date' => gmdate('YmdHis'),
					'TransportInfo' => array(
						'TRANSPORT' => $transports,
					),
					'ContainerInfo' => array(
						'Container' => $containers
					),
					'GOODSInfo' => array(
						'GOODS' => $goods_info
					),
				)
			),
		);
		
		try{
			$client = $this->get_soap_client();
			
			$response = $client->JMessageHubThrowerOp($payload);
			
			$out->success = true;
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
				$out->msg = 'Tidak dapat menghubungi Service Manifest, Mungkin sedang dalam perawatan rutin atau jaringan sibuk. Silakan coba beberapa saat lagi.';
				$out->payload = null;
				$out->fault = $fault;
			}
		}catch(Exception $e){
			$out->success = false;
			$out->msg_code = 500;
			$out->msg = 'Internal Server Error';
		}
		
		return $out;
	}
}