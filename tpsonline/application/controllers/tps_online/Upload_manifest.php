<?php
class Upload_manifest extends CI_Controller{
	private $local_db;
	
	
	public function __construct(){
		parent::__construct();
		
		// Dapatkan data login
		if(!$this->auth = $this->userauth->getLoginData()){
			redirect(LOGIN_PAGE);
			$this->load->model('tps_online/kunjungan_kapal_model');
			// $this->load->library('PHPExcel/PHPExcel/IOFactory');
		}
	}
	
	private function get_db(){
		if(!$this->local_db){
			$this->local_db = $this->load->database(ILCS_TPS_ONLINE, TRUE);
			$this->local_db->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
			$this->local_db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
		}
		
		return $this->local_db;
	}
	
	public function index($visit_id = NULL){
		$db = $this->get_db();
		
		$kunjungan_kapal = model('tps_online/kunjungan_kapal_model');
		$kunjungan_kapal->set_db($db);
		
		$data = array(
			'VISIT_ID' => $visit_id,
			'VISIT_ID_DS' => $kunjungan_kapal->select_ds(array('FLAG_SEND' => 0)),
			'TYPE_CARGO_DS' => $kunjungan_kapal->select_type_cargo(array('STATUS' => 'Y'))
		);

		$this->load->view('backend/pages/tps_online/consignment/form', $data);
	}

	public function import($token){

		if($this->auth->token = $token) {
			include APPPATH.'third_party/PHPExcel/PHPExcel.php';
			

				$csvreader = new PHPExcel_Reader_Excel2007();
				// $objPHPExcel->setActiveSheetIndex(1);

		        $loadcsv = $csvreader->load('./assets/csv/'.$this->input->post('nmfile_vin')); // Load file yang tadi diupload ke folder csv
				// $sheet 	  = $loadcsv->getActiveSheetIndex(0)->toArray(null, true, true ,true);
				$sheet_vin = $loadcsv->setActiveSheetIndex(0)->toArray(null, true, true ,true);
				$sheet_bl  = $loadcsv->setActiveSheetIndex(1)->toArray(null, true, true ,true);

	
					$numrow = 1;
					$db = $this->get_db();

					$vin_benar = array();
					$vin_salah = array();
					$bl_benar   = array();
					// $data_kosong = array();
					$bruto = '';
					

						foreach ($sheet_vin as $row) {

							if(strtoupper($row['G']) == 'CBU PASSENGER CAR' ){
								$type_cargo = 'CBU';
							}else{
								$type_cargo = 'CBU';
							}

							if($numrow > 1 && $row['A'] != ''){

								$bl_number_date = date("Y-m-d", strtotime($row['B']));
								$house_bl_number_date = date("Y-m-d", strtotime($row['D']));
								$visit_id = $this->input->post('VISIT_ID');
								$cartos_cargo = array(

					    			'BL_NUMBER' => $row['A'],
					    			'BL_NUMBER_DATE' => preg_replace("/\D/", "-", $bl_number_date), 
					    			'HOUSE_BL_NUMBER' => $row['C'],
					    			'HOUSE_BL_NUMBER_DATE' =>  preg_replace("/\D/", "-", $house_bl_number_date),
					    			'TYPE_CARGO' => $type_cargo , // $row['M']
					    			'FLAG_SEND_CODECO' => 0,
									'FLAG_SEND_COARRI' => 0,
									'DATE_SEND_CODECO' => null,
									'DATE_SEND_COARRI' => null,
								);

				    			try {
									
										if($db->where('VIN', $row['H'])->get('CARTOS_CARGO')->row()){
											
											$db->where('VIN', $row['H'], 'VISIT_ID', $visit_id)->update('CARTOS_CARGO', $cartos_cargo);

											// $bl_benar[]  = $row['G'];
											$vin_benar[] = $row['H'];
											
											// $data2[] = array($row['G'], $row['N']);
											// return true; 
										}else{
											// echo $row['P'];
											$vin_salah[] = 'BL: '.$row['A'].' Vin: '.$row['H'].'Mohon cek kembali data yang di Upload'; 
											// return false;
											
										}
				    				
				    			} catch (Exception $e) {
				    				echo "<h2>Exception Error!</h2>"; 
							        echo $e->getMessage(); 
				    			}
							
							}

							$numrow++;
						}
					 
						$numrow2 = 1;
						foreach ($sheet_bl as $row) {

							if(strtoupper($row['C']) == 'CBU PASSENGER CAR' ){
								$customs_cargo_code = 'CBU';
								$bruto = 0;
							}elseif(strtoupper($row['C']) == 'BX BOX' ) {
								$customs_cargo_code = 'BX';
								$bruto = $row['D'];
							}elseif (strtoupper($row['C']) == 'CS CASE' ) {
								$customs_cargo_code = 'CS';
								$bruto = $row['D'];
							}elseif (strtoupper($row['C']) == 'NE UNPACKED OR UNPACKAGED' ) {
								$customs_cargo_code = 'NE';
								$bruto = $row['D'];
							}elseif (strtoupper($row['C']) == 'PK PACKAGE' ) {
								$customs_cargo_code = 'PK';
								$bruto = $row['D'];
							}elseif (strtoupper($row['C']) == 'PX PALLET' ) {
								$customs_cargo_code = 'PX';
								$bruto = $row['D'];
							}elseif (strtoupper($row['C']) == 'HH HIGH AND HEAVY' ) {
								$customs_cargo_code = 'HH';
								$bruto = $row['D'];
							}


							if($numrow2 > 1 && $row['A'] != ''){
								
									$bl_cargo_type_mapping = array(

					    				'BL_NUMBER' => $row['A'],
										'HOUSE_BL_NUMBER' => $row['B'],
										'CUSTOMS_CARGO_CODE' => $customs_cargo_code,
										// 'VISIT_ID' => $row['A'],
						    			'BRUTO' => preg_replace("/\D/", "", $bruto) ,
						    			'JUMLAH' => $row['E'],
						    			'RECORD_TIME' => date('Y-m-d H:i:s'),
						    			// 'METODE_INPUT' => 'UPLOAD',

					    			);

									
									if(!$db->where('BL_NUMBER', $row['A'])->get('BL_CARGO_TYPE_MAPPING')->row()){

					    				$db->insert('BL_CARGO_TYPE_MAPPING', $bl_cargo_type_mapping);
					    				// print_r($db->insert);die();
					    			}
			    			}

			    			$numrow2++;
			    			
						}

						
					$respon_data = array(
						'benar' => count($vin_benar),
						'salah' => count($vin_salah),
						'vin_salah' => $vin_salah
					);

					echo json_encode($respon_data);
		}

	}

	public function upload_csv($visit_id = null)
	{
		$db = $this->get_db();
		
		$kunjungan_kapal = model('tps_online/kunjungan_kapal_model');
		$kunjungan_kapal->set_db($db);

		$this->load->helper('url');
		$nmfile_vin                 =  "file_".time();
        $config1['upload_path']      = './assets/csv'; //$_SERVER['DOCUMENT_ROOT'].'/Intapps/dokumen_bc/assets/csv/';
        $config1['file_name']        = $nmfile_vin;
        $config1['allowed_types']    = 'xlsx|xls';

        $this->upload->initialize($config1);
        $this->load->library('upload', $config1);

        if(!empty($_FILES['file_vin']['name'])){ /// && $_FILES['file_bl']['name']  
        
            if ($this->upload->do_upload('file_vin') ){ //&& $this->upload->do_upload('file_bl')   
	        	$this->upload->do_upload('file_vin');
	        	$file1 = $this->upload->data();

                $data_nmfile_vin = $file1['file_name'];
                // $data_nmfile_bl = $file2['file_name'];
                $data3 = array();
               
                include APPPATH.'third_party/PHPExcel/PHPExcel.php';
                $csvreader = new PHPExcel_Reader_Excel2007();

                $loadvin = $csvreader->load('./assets/csv/'.$data_nmfile_vin); // Load file yang tadi diupload ke folder csv
			   	
			   	$sheet_vin = $loadvin->setActiveSheetIndex(0)->toArray(null, true, true ,true);
				$sheet_bl  = $loadvin->setActiveSheetIndex(1)->toArray(null, true, true ,true);

              	$data3 = array(
              		'sheet'		=> $sheet_vin,
              		'sheet_bl'	=> $sheet_bl,
              		'nmfile_vin' => $data_nmfile_vin,
              		// 'nmfile_bl'	 => $data_nmfile_bl,
              		
              		'VISIT_ID' => $visit_id, 
					'VISIT_ID_DS' => $kunjungan_kapal->select_ds(array('FLAG_SEND' => 0)),
					'TYPE_CARGO_DS' => $kunjungan_kapal->select_type_cargo(array('STATUS' => 'Y'))
              	);
              	
                $this->load->view('backend/pages/tps_online/consignment/form', $data3);
                
            } else {
                echo $this->upload->display_errors();
            }
        }
	}

	public function download_format_vin()
	{
		$this->load->helper('url');
		$this->load->helper('download');
		$this->load->helper('file');

		// $file_name = 'Form_Input_Manifest.xlsx';
		$data = file_get_contents('./assets/template/'.$file_name);
		// print_r($data);die();
		force_download($file_name, $data);
	}


	
}