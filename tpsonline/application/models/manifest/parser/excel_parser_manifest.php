<?php
require_once('./application/models/manifest/parser/excel_parser_base.php');

/** Excel Format Version 2
  *
  */
class Excel_Parser_Manifest extends Excel_Parser_Base{
	

	public function parse_informasi_kapal(&$raw){	
		$out = new StdClass();
		$errors = array();
		
		$header = new StdClass();
		$header->no_ukk = NULL;
		$header->nama_kapal = NULL;
		$header->direction = NULL;
		$header->inaportnet_atp_number = NULL;
		$header->voyage = NULL;
		$header->previous_voyage = NULL;
		$header->call_sign = NULL;
		$header->imo_number = NULL;
		$header->last_port = NULL;
		$header->pol = NULL;
		$header->pod = NULL;
		$header->next_port = NULL;
		$header->ata = NULL;
		$header->atd = NULL;
		$header->eta = NULL;
		
		// Start Row
		$i = 2;
		
		if(!empty($raw[++$i][2])){
			$header->no_ukk = $raw[$i][2];
		}
		
		if(!empty($raw[++$i][2])){
			$header->nama_kapal = $raw[$i][2];
		}else{
			$errors[] = 'Nama kapal harus diisi';
		}
		
		if(!empty($raw[++$i][2])){
			$header->direction = $raw[$i][2];
		}else{
			$errors[] = 'Direction harus diisi';
		}
		
		if(!empty($raw[++$i][2])){
			$header->inaportnet_atp_number = $raw[$i][2];
		}else{
			$errors[] = 'Inaportnet ATP Number harus diisi';
		}
		
		if(!empty($raw[++$i][2])){
			$header->voyage = $raw[$i][2];
		}else{
			$errors[] = 'Voyage harus diisi';
		}
		
		if(!empty($raw[++$i][2])){
			$header->previous_voyage = $raw[$i][2];
		}else{
			$errors[] = 'Previous Voyage (No Voyage sebelumnya) harus diisi';
		}
		
		if(!empty($raw[++$i][2])){
			$header->call_sign = $raw[$i][2];
		}else{
			$errors[] = 'Call Sign harus diisi';
		}
		
		if(!empty($raw[++$i][2])){
			$header->imo_number = $raw[$i][2];
		}else{
			$errors[] = 'IMO Number harus diisi';
		}
		
		if(!empty($raw[++$i][2])){
			$header->last_port = $raw[$i][2];
		}else{
			$errors[] = 'Pelabuhan terakhir sebelum berangkat harus diisi';
		}
		
		if(!empty($raw[++$i][2])){
			$header->pol = $raw[$i][2];
		}else{
			$errors[] = 'POD (Port of Loading / Pelabuhan Muat) harus diisi';
		}
		
		if(!empty($raw[++$i][2])){
			$header->pod = $raw[$i][2];
		}else{
			$errors[] = 'POD (Port of Discharge / Pelabuhan Bongkar) harus diisi';
		}
		
		if(!empty($raw[++$i][2])){
			$header->next_port = $raw[$i][2];
		}else{
			$errors[] = 'Next Port (Pelabuhan selanjutnya) harus diisi';
		}
		
		if(!empty($raw[++$i][2])){
			$value = $this->convertDateTime($raw[$i][2]);
			$header->ata = $value;
		}else{
			$errors[] = 'ATA (Actual Time Arrival / Waktu Tiba Sesungguhnya) harus diisi';
		}
		
		if(!empty($raw[++$i][2])){
			$value = $this->convertDateTime($raw[$i][2]);
			$header->atd = $value;
		}else{
			$errors[] = 'ATD (Actual Time Departure / Waktu Berangkat Sesungguhnya) harus diisi';
		}
		
		if(!empty($raw[++$i][2])){
			$value = $this->convertDateTime($raw[$i][2]);
			$header->eta = $value;
		}else{
			$errors[] = 'ETA (Estimated Time Arrival / Perkiraan Waktu Tiba di Tujuan) harus diisi';
		}
		
		$out->data = $header;
		$out->errors = $errors;
		
		return $out;
	}
	
	public function parse_container(&$raw){
		$out = new StdClass();
		
		$total_rows = count($raw);
		
		$data = array();
		$errors = array();
		
		// Field Definition
		$offset_row = 5;
		$f_def = array(
			'container_number' => 'Container Number|string|required',
			'iso_code_container' => 'Iso Code Container|string|required',
			'type' => 'Container Type|string|required',
			'size' => 'Container Size|string|required',
			'status' => 'Status|string|required',
			'plugging_required' => 'Plugging Required|string',
			'fumigation_required' => 'Fumigation Required|string',
			'fumigation_by' => 'Fumigation By|string',
			'empty' => 'Kosong|string|required',
			'hazard' => 'HZ|string|required',
			'reefer_min_temp' => 'Reefer Min Temp|string',
			'reefer_max_temp' => 'Reefer Max Temp|string',
			'gross_weight' => 'Gross Weight|string|required',
			'tare_weight' => 'Tare Weight|string|required',
			'overdimension_height' => 'Overdimension Height|string',
			'overdimension_left' => 'Overdimension Left|string',
			'overdimension_right' => 'Overdimension Right|string',
			'overdimension_front' => 'Overdimension Front|string',
			'overdimension_back' => 'Overdimension Back|string',
			'pol' => 'POL|string|required',
			'pod' => 'POD|string|required',
			'temp_unit' => 'Temp Unit|string|required',
			'length_unit' => 'Length Unit|string|required',
			'weight_unit' => 'Weight Unit|string|required',
			'carrier_seal_number' => 'Carrier Seal Number|string',
			'custom_seal_number' => 'Custom Seal Number|string',
		);
		
		// Process Field Definition
		foreach($f_def as &$val){
			$temp = new StdClass();
			
			$parts = explode('|', $val);
			$total_parts = count($parts);
			
			$temp->label = $parts[0];
			$temp->type = $parts[1];
			
			$total_parts > 2 ? $temp->required = true : $temp->required = false;
			
			$val = $temp;
		}
		
		// Process Each Rows
		for($i = $offset_row; $i < $total_rows; $i++){
			$row = $raw[$i];
			
			if(!$this->empty_row($row)){
				$curr_errors = array();
				
				$temp = new StdClass();
				$temp->row_address = $i;
				
				// Get Value per Column at Current Row
				$j = 0;
				foreach($f_def as $key => $def){
					$temp->{$key} = $this->get($row, $j, $def->type);
					
					if($def->required && !$temp->{$key}){
						$curr_errors[] = 'Kolom '.$def->label.' wajib diisi';
					}
					
					$j++;
				}
				
				// Translate Values
				$temp->plugging_required = $temp->plugging_required == 'Y' ? true : false;
				$temp->fumigation_required = $temp->fumigation_required == 'Y' ? true : false;
				$temp->empty = $temp->empty == 'Y' ? true : false;
				$temp->hazard = $temp->hazard == 'Y' ? true : false;
				
				/* Extended Validation */
				
				// Check Digit Container Number
				if($temp->container_number && !$this->check_container_number($temp->container_number)){
					$curr_errors[] = 'Container no: '.$temp->container_number.' tidak valid cek kembali nomornya';
				}
				
				// Cek Reefer
				if(
					$temp->type == 'RFR' && 
					$temp->plugging_required &&
					(empty($temp->reefer_min_temp) || empty($temp->reefer_max_temp))
				){
					$curr_errors[] = 'Container reefer yang memerlukan plugging harus diisi minimum temperaturnya';
				}
				
				// Pool Result
				if(isset($data[$temp->container_number])){
					$duplicate = $data[$temp->container_number];
					$curr_errors[] = 'Container no: '.$temp->container_number.' memiliki duplikat di baris ke '.$duplicate->row_address;
				}
				
				// Persiapan Link to Bill of Lading (Consigment)
				$temp->consignment_link = NULL;
				
				// Append Result & Errors
				$data[$temp->container_number] = $temp;
				
				if($curr_errors){
					$errors[$i] = $curr_errors;
				}
			}
		}
		
		$out->data = $data;
		$out->errors = $errors;
		
		return $out;
	}
	
	public function parse_consignment(&$raw){
		$out = new StdClass();
		
		$total_rows = count($raw);
		
		$data = array();
		$errors = array();
		
		// Field Definition
		$offset_row = 5;
		$f_def = array(
			'bl_number' => 'No BL|string|required',
			'bl_date' => 'Tanggal BL|date|required',
			'master_bl' => 'Master BL|string',
			'carrier_operator_code' => 'Carrier Code|string|required',
			'transport_stage' => 'Transport Stage|string',
			'despatch_place_code' => 'Kode Alamat Pengiriman|string|required',
			'previous_port_code' => 'Kode Pelabuhan Sebelumnya|string|required',
			'load_port_code' => 'Kode Pelabuhan Bongkar|string|required',
			'discharge_port_code' => 'Kode Pelabuhan Bongkar|string|required',
			'next_port_code' => 'Kode Pelabuhan Selanjutnya|string|required',
			'destination_port_code' => 'Kode Pelabuhan Tujuan|string|required',
			'etd' => 'ETD|date|required',
			'shipper_name' => 'Nama Pengirim|string|required',
			'shipper_address' => 'Nama Pengirim|string|required',
			'consignee_name' => 'Nama Consignee|string|required',
			'consignee_address' => 'Alamat Consignee|string|required',
			'notify_name' => 'Nama Notify|string|required',
			'notify_address' => 'Alamat Notify|string|required',
			'description' => 'Description|string',
			'remarks' => 'Remarks|string',
		);
		
		// Process Field Definition
		foreach($f_def as &$val){
			$temp = new StdClass();
			
			$parts = explode('|', $val);
			$total_parts = count($parts);
			
			$temp->label = $parts[0];
			$temp->type = $parts[1];
			
			$total_parts > 2 ? $temp->required = true : $temp->required = false;
			
			$val = $temp;
		}
		
		// Process Each Rows
		for($i = $offset_row; $i < $total_rows; $i++){
			$row = $raw[$i];
			
			if(!$this->empty_row($row)){
				$curr_errors = array();
				
				$temp = new StdClass();
				$temp->row_address = $i;
				
				// Get Value per Column at Current Row
				$j = 0;
				foreach($f_def as $key => $def){
					$temp->{$key} = $this->get($row, $j, $def->type);
					
					if($def->required && !$temp->{$key}){
						$curr_errors[] = 'Kolom '.$def->label.' wajib diisi';
					}
					
					$j++;
				}
				
				// Translate Values
				
				// Extended Validation
				
				// Append Result & Errors
				$temp->packages = array();
				$data[$temp->bl_number] = $temp;
				
				if($curr_errors){
					$errors[$i] = $curr_errors;
				}
			}
		}
		
		$out->data = $data;
		$out->errors = $errors;
		
		return $out;
	}
	
	public function parse_package(&$raw, &$containers, &$consignments){
		$out = new StdClass();
		
		$total_rows = count($raw);
		
		$data = array();
		$errors = array();
		
		// Field Definition
		$offset_row = 5;
		$f_def = array(
			'bl_number' => 'No BL|string|required',
			'qty' => 'Qty|string|required',
			'qty_unit' => 'Satuan Qty|string|required',
			'un_packaging_code' => 'Kemasan|string|required',
			'container_number' => 'No Container|string',
			'gross_weight' => 'Gross Weight|string|required',
			'net_weight' => 'Net Weight|string|required',
			'volume' => 'Volume|string|required',
			'polluting' => 'Mengganggu|string',
			'hazard' => 'Hazard|string|required',
			'un_dg_code' => 'Kode DG UN|string',
			'imo_dg_code' => 'Kode DG IMO|string',
			'flash_point' => 'Flash Point|number',
			'dg_packaging_type' => 'DG Packaging Type|number',
			'refrigeration_required' => 'Refrigerating Required|string',
			'refrigeration_min_temp' => 'Refrigerating Min Temp|string',
			'refrigeration_max_temp' => 'Refrigerating Max Temp|string',
			'fumigation_required' => 'Fumigation Required|string',
			'fumigation_by' => 'Fumigation By|string',
			'circulation_required' => 'Circullation Required|string',
			'hs_code' => 'HS Code|string|required',
			'temp_unit' => 'Temp Unit|string|required',
			'weight_unit' => 'Weight Unit|string|required',
			'volume_unit' => 'Volume Unit|string|required',
			'dg_handling_instruction' => 'DG Handling Instruction|string',
			'dg_description' => 'DG Description|string',
			'description' => 'Description|string|required',
			'remarks' => 'Remarks|string',
		);
		
		// Process Field Definition
		foreach($f_def as &$val){
			$temp = new StdClass();
			
			$parts = explode('|', $val);
			$total_parts = count($parts);
			
			$temp->label = $parts[0];
			$temp->type = $parts[1];
			
			$total_parts > 2 ? $temp->required = true : $temp->required = false;
			
			$val = $temp;
		}
		
		// Process Each Rows
		for($i = $offset_row; $i < $total_rows; $i++){
			$row = $raw[$i];
			
			if(!$this->empty_row($row)){
				$curr_errors = array();
				
				$temp = new StdClass();
				$temp->row_address = $i;
				
				// Get Value per Column at Current Row
				$j = 0;
				foreach($f_def as $key => $def){
					$temp->{$key} = $this->get($row, $j, $def->type);
					
					if($def->required && !$temp->{$key}){
						$curr_errors[] = 'Kolom '.$def->label.' wajib diisi';
					}
					
					$j++;
				}
				
				// Translate Values
				$temp->polluting = $temp->polluting == 'Y' ? true : false;
				$temp->refrigeration_required = $temp->refrigeration_required == 'Y' ? true : false;
				$temp->circulation_required = $temp->circulation_required == 'Y' ? true : false;
				$temp->fumigation_required = $temp->fumigation_required == 'Y' ? true : false;
				$temp->hazard = $temp->hazard == 'Y' ? true : false;
				
				// Extended Validation
				if(!isset($consignments[$temp->bl_number])){
					$curr_errors[] = 'BL no: '.$temp->bl_number.' tidak terdaftar dalam worksheet consignment.';
				}
				
				if($temp->container_number){
					if(isset($containers[$temp->container_number])){
						// Assign Consignment Link
						if(isset($consignments[$temp->bl_number])){
							$containers[$temp->container_number]->consignment_link = $consignments[$temp->bl_number];
						}
					}else{
						$curr_errors[] = 'BL no: '.$temp->bl_number.' memiliki container yang tidak ada dalam daftar container harap cek kembali. Cont No: '.$temp->container_number;
					}
				}
				
				if($temp->hazard){
					if(!$temp->un_dg_code || !$temp->imo_dg_code){
						$curr_errors[] = 'Karena didefinisikan sebagai Hazard, code DG UN dan IMO salah satunya harus disi.';
					}
					
					if(!$temp->flash_point){
						$curr_errors[] = 'Karena didefinisikan sebagai Hazard, flash point harus disi.';
					}
					
					if(!$temp->dg_handling_instruction){
						$curr_errors[] = 'Karena didefinisikan sebagai Hazard, DG Handling Instruction harus disi.';
					}
					
					if(!$temp->dg_description){
						$curr_errors[] = 'Karena didefinisikan sebagai Hazard, DG Description harus disi.';
					}
				}
				
				if($temp->refrigeration_required){
					if(empty($temp->refrigeration_min_temp)){
						$curr_errors[] = 'BL no: '.$temp->bl_number.' jika memerlukan refrigeration maka min temperaturnya harus diisi';
					}
					
					if(empty($temp->refrigeration_max_temp)){
						$curr_errors[] = 'BL no: '.$temp->bl_number.' jika memerlukan refrigeration maka max temperaturnya harus diisi';
					}
				}
				
				if($temp->fumigation_required){
					if(!$temp->fumigation_by){
						$curr_errors[] = 'BL no: '.$temp->bl_number.' jika memerlukan fumigasi maka pelaksana fumigasi (Fumigation By) harus diisi';
					}
				}
				
				// Append Result & Errors
				if($curr_errors){
					$errors[$i] = $curr_errors;
				}else{
					$consignments[$temp->bl_number]->packages[] = $temp;
				}
			}
		}
		
		$out->errors = $errors;
		
		return $out;
	}
	
	
	
	
	
	
	public function parse($file_path, $file_name){
		$out = new StdClass();
		$out->status = true;
		
		if($file_path){			
			try{
				set_time_limit(900);
				ini_set('memory_limit', '320M');
				
				$parser = library('SimpleXLSX/simplexlsx');
				$parser->__construct($file_path);
				
				// Basic Information
				$out->processing_id = uniqid();
				$out->original_filename = $file_name;
				$out->receive_timestamp = date('Y-m-d H:i:s');
				$out->process_timestamp = date('Y-m-d H:i:s');
				
				$errors = array();
				
				$informasi_kapal = NULL;
				$containers = NULL;
				$consignments = NULL;
				$packages = NULL;
				
				$total_sheets = $parser->sheetsCount();
				for($i = 1; $i <= $total_sheets; $i++){
					switch($parser->sheetName($i)){
						case 'InformasiKapal':
							$informasi_kapal = $parser->rows($i);
							break;
						
						case 'Container':
							$containers = $parser->rows($i);
							break;
						
						case 'Consignment':
							$consignments = $parser->rows($i);
							break;
						
						case 'Packages':
							$packages = $parser->rows($i);
							break;
						
						default:
					}
				}
				
				if($informasi_kapal){					
					$out->informasi_kapal = $this->parse_informasi_kapal($informasi_kapal);
					
					if($containers){
						$out->containers = $this->parse_container($containers);
						
						if($consignments){
							$out->consignments = $this->parse_consignment($consignments);
							
							if($packages){
								$out->packages = $this->parse_package($packages, $out->containers->data, $out->consignments->data);
								
								$out->status = 	!$out->informasi_kapal->errors && 
												!$out->containers->errors && 
												!$out->consignments->errors && 
												!$out->packages->errors;
							}else{
								$out->parser_error = 'Sheet \'Package\' tidak ada';
							}
						}else{
							$out->status = false;
							$out->parser_error = 'Sheet \'Consigment\' tidak ada';
						}
					}else{
						$out->status = false;
						$out->parser_error = 'Sheet \'Container\' tidak ada';
					}
				}else{
					$out->status = false;
					$out->parser_error = 'Sheet \'InformasiKapal\' tidak ada';
				}
								
				return $out;
				
			}catch(Exception $e){
				$out->status = false;
				
				switch($e->getCode()){
					case 101:
					case 102:
							$out->parser_error = 'Format file tidak dikenal. Harap unggah file excel 2007 yang sesuai contoh.'; break;
					case 103:
							$out->parser_error = 'File corrupt dan tidak dapat digunakan. Unggah kembali file anda dan pastikan filenya dapat dibuka di Ms Excel 2007.'; break;
					case 201:
							$out->parser_error = 'Tidak dapat unzip file. Mungkin file corrupt atau ini bukan file excel 2007.'; break;
					case 1001:
							$out->error_header = 'Grrrr...';
							$out->parser_error = 'Anda tidak mengunggah apapun!';
							break;
					default:
							$out->parser_error = $e->getMessage();
				}
				
				return $out;
			}
		}else{
			$out->status = false;
			$out->parser_error = 'Alamat File kosong atau tidak ada.';
		}
		
		return $out;
	}
	
	public function parse_uploaded_file($field){
		$out = new StdClass();
		
		if(isset($_FILES[$field]['tmp_name'])){
			$out = $this->parse($_FILES[$field]['tmp_name'], $_FILES[$field]['name']);
		}else{
			$out->status = false;
			$out->error_header = 'Ini Memalukan!';
			$out->parser_error = 'File field tidak ada. [DEVELOPER] Apakah form sudah benar?';
		}
		
		return $out;
	}
	
	
	
	
	
	
	
	
	
	
	public function check_container_number($container_number){
		$length = strlen($container_number);
		
		if($length == 11){
			$container_number = strtoupper($container_number);
			
			if(preg_match('/[A-Z]{3}(U|J|Z|R)[0-9]{7}/', $container_number)){
				$check_digit = (int) $container_number[10];
				
				$total = 0;
				$multiplier = 1;
				
				for($i = 0; $i < 10; $i++){
					$ascii = ord($container_number[$i]);
					
					if($ascii >= 48 && $ascii <= 57){
						$curr_val = ($ascii - 48) * $multiplier;
					}else{
						$curr_val = $ascii - 65;
						
						if($curr_val >= 21){
							$curr_val += 3;
						}else if($curr_val >= 11){
							$curr_val += 2;
						}else if($curr_val >= 1){
							$curr_val += 1;
						}
						
						$curr_val = ($curr_val + 10) * $multiplier;
					}
					
					$total += $curr_val;
					$multiplier *= 2;
				}
				
				return $check_digit == $total % 11;
			}else{	
				return false;
			}
		}else{
			return false;
		}
	}
}