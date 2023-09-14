<?php
require_once('./application/models/manifest/parser/excel_parser_base.php');
class Excel_Parser_Manifest extends Excel_Parser_Base{
	

	public function parse_informasi_kapal(&$raw){
		$out = new StdClass();
		$errors = array();
		
		$header = new StdClass();
		$header->no_ukk = NULL;
		$header->nama_kapal = NULL;
		$header->direction = NULL;
		$header->voyage = NULL;
		$header->voyage_ref = NULL;
		$header->call_sign = NULL;
		$header->imo_number = NULL;
		$header->last_port = NULL;
		$header->pol = NULL;
		$header->pod = NULL;
		$header->eta = NULL;
		$header->etd = NULL;		
		
		if(!empty($raw[3][2])){
			$header->no_ukk = $raw[3][2];
		}
		
		if(!empty($raw[4][2])){
			$header->nama_kapal = $raw[4][2];
		}else{
			$errors[] = 'Nama kapal harus diisi';
		}
		
		if(!empty($raw[5][2])){
			$header->direction = $raw[5][2];
		}else{
			$errors[] = 'Direction harus diisi';
		}
		
		if(!empty($raw[6][2])){
			$header->voyage = $raw[6][2];
		}else{
			$errors[] = 'Voyage harus diisi';
		}
		
		if(!empty($raw[7][2])){
			$header->voyage_ref = $raw[7][2];
		}else{
			$errors[] = 'Voyage Ref (No Voyage sebelumnya) harus diisi';
		}
		
		if(!empty($raw[8][2])){
			$header->call_sign = $raw[8][2];
		}else{
			$errors[] = 'Call Sign harus diisi';
		}
		
		if(!empty($raw[9][2])){
			$header->imo_number = $raw[9][2];
		}else{
			$errors[] = 'IMO Number harus diisi';
		}
		
		if(!empty($raw[10][2])){
			$header->last_port = $raw[10][2];
		}else{
			$errors[] = 'Pelabuhan terakhir sebelum berangkat harus diisi';
		}
		
		if(!empty($raw[11][2])){
			$header->pol = $raw[11][2];
		}else{
			$errors[] = 'POD (Port of Loading / Pelabuhan Muat) harus diisi';
		}
		
		if(!empty($raw[12][2])){
			$header->pod = $raw[12][2];
		}else{
			$errors[] = 'POD (Port of Discharge / Pelabuhan Bongkar) harus diisi';
		}
		
		if(!empty($raw[13][2])){
			$value = $this->convertDateTime($raw[13][2]);
			$header->eta = $value;
		}else{
			$errors[] = 'ETA (Estimated Time Arrival / Perkiraan Waktu Tiba) harus diisi';
		}
		
		if(!empty($raw[14][2])){
			$value = $this->convertDateTime($raw[14][2]);
			$header->etd = $value;
		}else{
			$errors[] = 'ETD (Estimated Time Departure / Perkiraan Waktu Berangkat) harus diisi';
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
		$offset_row = 4;
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
			'seal_number' => 'Seal Number|string|required',
			'temp_unit' => 'Temp Unit|string|required',
			'weight_unit' => 'Weight Unit|string|required',
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
				
				// Extended Validation
				if($temp->container_number && !$this->check_container_number($temp->container_number)){
					$curr_errors[] = 'Container no: '.$temp->container_number.' tidak valid cek kembali nomornya';
				}
				
				// Pool Result
				if(isset($data[$temp->container_number])){
					$duplicate = $data[$temp->container_number];
					$curr_errors[] = 'Container no: '.$temp->container_number.' memiliki duplikat di baris ke '.$duplicate->row_address;
				}
				
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
	
	public function parse_goods(&$raw, &$containers){
		$out = new StdClass();
		
		$total_rows = count($raw);
		
		$data = array();
		$errors = array();
		
		// Field Definition
		$offset_row = 4;
		$f_def = array(
			'bl_number' => 'No BL|string|required',
			'bl_date' => 'Tanggal BL|date|required',
			'master_bl' => 'Master BL|string',
			'packaging' => 'Kemasan|string|required',
			'container_number' => 'No Container|string',
			'polluting' => 'Mengganggu|string',
			'refrigeration_required' => 'Refrigerating Required|string',
			'refrigeration_min_temp' => 'Refrigerating Min Temp|string',
			'refrigeration_max_temp' => 'Refrigerating Max Temp|string',
			'circulation_required' => 'Circullation Required|string',
			'fumigation_required' => 'Fumigation Required|string',
			'fumigation_by' => 'Fumigation By|string',
			'hazard' => 'Hazard|string|required',
			'despatch_place_code' => 'Kode Alamat Pengiriman|string|required',
			'despatch_place_name' => 'Nama Alamat Pengiriman|string',
			'previous_port_code' => 'Kode Pelabuhan Sebelumnya|string|required',
			'previous_port_name' => 'Nama Pelabuhan Sebelumnya|string',
			'discharge_port_code' => 'Kode Pelabuhan Bongkar|string|required',
			'discharge_port_name' => 'Nama Pelabuhan Bongkar|string',
			'next_port_code' => 'Kode Pelabuhan Selanjutnya|string|required',
			'next_port_name' => 'Nama Pelabuhan Selanjutnya|string',
			'destination_port_code' => 'Kode Pelabuhan Tujuan|string|required',
			'destination_port_name' => 'Nama Pelabuhan Tujuan|string',
			'etd' => 'ETD|string|date',
			'shipper_name' => 'Nama Pengirim|string|required',
			'shipper_address' => 'Nama Pengirim|string|required',
			'consignee_name' => 'Nama Consignee|string|required',
			'consignee_address' => 'Alamat Consignee|string|required',
			'notify_name' => 'Nama Notify|string|required',
			'notify_address' => 'Alamat Notify|string|required',
			'qty' => 'Qty|string|required',
			'volume' => 'Volume|string|required',
			'gross_weight' => 'Gross Weight|string|required',
			'net_weight' => 'Net Weight|string|required',
			'hs_code' => 'HS Code|string',
			'temp_unit' => 'Temp Unit|string|required',
			'weight_unit' => 'Weight Unit|string|required',
			'volume_unit' => 'Volume Unit|string|required',
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
				$temp->polluting = $temp->polluting == 'Y' ? true : false;
				$temp->refrigeration_required = $temp->refrigeration_required == 'Y' ? true : false;
				$temp->circulation_required = $temp->circulation_required == 'Y' ? true : false;
				$temp->fumigation_required = $temp->fumigation_required == 'Y' ? true : false;
				$temp->hazard = $temp->hazard == 'Y' ? true : false;
				
				// Extended Validation
				if($temp->container_number && !isset($containers[$temp->container_number])){
					$curr_errors[] = 'BL no: '.$temp->bl_number.' memiliki container yang tidak ada dalam daftar container harap cek kembali. Cont No: '.$temp->container_number;
				}
				
				if($temp->refrigeration_required){
					if(!$temp->refrigeration_min_temp){
						$curr_errors[] = 'BL no: '.$temp->bl_number.' jika memerlukan refrigeration maka min temperaturnya harus diisi';
					}
					
					if(!$temp->refrigeration_max_temp){
						$curr_errors[] = 'BL no: '.$temp->bl_number.' jika memerlukan refrigeration maka min temperaturnya harus diisi';
					}
				}
				
				if($temp->fumigation_required){
					if(!$temp->fumigation_by){
						$curr_errors[] = 'BL no: '.$temp->bl_number.' jika memerlukan fumigasi maka pelaksana fumigasi (Fumigation By) harus diisi';
					}
				}
				
				// Append Result & Errors
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
	
	public function parse_dangerous_goods(&$raw, &$goods){
		$out = new StdClass();
		
		$total_rows = count($raw);
		
		$data = array();
		$errors = array();
		
		// Field Definition
		$offset_row = 4;
		$f_def = array(
			'bl_number' => 'No BL|string|required',
			'imo_dg_code' => 'Kode DG IMO|string|required',
			'un_dg_code' => 'Kode DG UN|string|required',
			'temperature' => 'Temperature|number|required',
			'flash_point' => 'Flash Point|number|required',
			'temp_unit' => 'Temp. Unit|string|required',
			'packaging' => 'Jenis Kemasan|string|required',
			'handling_procedure' => 'Prosedur|string|required',
			'remarks' => 'Keterangan|string|required',
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
			$curr_errors = array();
			
			$row = $raw[$i];
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
			if($temp->bl_number && !isset($goods[$temp->bl_number])){
				$curr_errors[] = 'BL no: '.$temp->bl_number.' tidak terdaftar dalam daftar barang (Goods). Harap cek kembali.';
			}
			
			// Append Result & Errors
			$data[$temp->bl_number] = $temp;
			
			if($curr_errors){
				$errors[$i] = $curr_errors;
			}
		}
		
		$out->data = $data;
		$out->errors = $errors;
		
		return $out;
	}
	
	
	
	
	
	
	public function parse($file){
		$out = new StdClass();
		$out->status = true;
		
		if(isset($_FILES[$file]['tmp_name'])){			
			try{
				set_time_limit(900);
				ini_set('memory_limit', '320M');
				
				$parser = library('SimpleXLSX/simplexlsx');
				$parser->load($file);
				
				$errors = array();
				
				$informasi_kapal = NULL;
				$container = NULL;
				$goods = NULL;
				$dangerous_goods = NULL;
				
				$total_sheets = $parser->sheetsCount();
				for($i = 1; $i <= $total_sheets; $i++){
				
					switch($parser->sheetName($i)){
						case 'InformasiKapal':
							$informasi_kapal = $parser->rows($i);
							break;
						
						case 'Container':
							$container = $parser->rows($i);
							break;
						
						case 'Goods':
							$goods = $parser->rows($i);
							break;
						
						case 'DangerousGoods':
							$dangerous_goods = $parser->rows($i);
							break;
						
						default:
					}
				}
				
				if($informasi_kapal){					
					$out->informasi_kapal = $this->parse_informasi_kapal($informasi_kapal);
					
					if($container){
						$out->container = $this->parse_container($container);
						
						if($goods){
							$out->goods = $this->parse_goods($goods, $out->container->data);
							
							if($dangerous_goods){
								$out->dangerous_goods = $this->parse_dangerous_goods($dangerous_goods, $out->goods->data);
								
								$out->status = !$out->informasi_kapal->errors && !$out->container->errors && !$out->goods->errors && !$out->dangerous_goods->errors;
							}else{
								$out->parser_error = 'Sheet \'DangerousGoods\' tidak ada';
							}
						}else{
							$out->status = false;
							$out->parser_error = 'Sheet \'Goods\' tidak ada';
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
							$out->parser_error = 'Format file tidak dikenal. Harap upload file excel 2007 yang sesuai contoh.'; break;
					case 103:
							$out->parser_error = 'File corrupt dan tidak dapat digunakan. Upload kembali file anda dan pastikan filenya dapat dibuka di Ms Excel 2007.'; break;
					case 201:
							$out->parser_error = 'Tidak dapat unzip file. Mungkin file corrupt atau ini bukan file excel 2007.'; break;
					case 1001:
							$out->parser_error = 'Harap upload file';
							break;
					
					default:
							$out->parser_error = $e->getMessage();
				}
				
				return $out;
			}
		}else{
			$out->status = false;
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