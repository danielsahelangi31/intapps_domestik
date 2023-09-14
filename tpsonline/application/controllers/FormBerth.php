<?php
class FormBerth extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();
		$this->load->model(array('tps_online/model_berthing'
                              
                            	));
		// Dapatkan data login
		if(!$this->auth = $this->userauth->getLoginData()){
			redirect(LOGIN_PAGE);
		}
	}
	
    private function get_db() {
        if (!$this->local_db) {
            $this->local_db = $this->load->database('ikt_postgree', TRUE);
			// $this->local_db->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'DD/MM/YY HH24:MI'");
			// $this->local_db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'DD/MM/YY'");
          
        }

        return $this->local_db;
    }
	
	/** 
	 * Index
	 */
	public function index(){
		redirect('FormBerth/form_berth');
	}

    public function form_berth(){	
		
        $data = array(			
			'TYPE_SHIFT' => $this->Model_dashboard->select_type_shift(),
			'TYPE_ACTIVITY' => $this->Model_dashboard->select_type_activity()	
		);
		$this->load->view('backend/pages/dashboard/form_berth',$data);
	}
    
	public function save($token){
		// echo $this->auth->token.' : '.$token;
		// die;
		if($this->auth->token == $token){
			date_default_timezone_set('Asia/Jakarta');
			$out = new StdClass();		
		
        
			// if(is_post_request()){
                if(isset($_REQUEST)){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;
				$val->set_rules('KADE_NAME', 'KADE_NAME', 'required');
				$val->set_rules('KADE_AWAL', 'KADE_AWAL', 'required');
				$val->set_rules('KADE_AKHIR', 'KADE_AKHIR', 'required');	
		
				

				if($val->run()==TRUE){
					//echo 'masuk 4';
					$db = $this->get_db();	
					
					$visit_id = post ('VISIT_ID');
					$nama_kapal = post ('VESSEL_NAME');
					$kode_kapal = post ('VESSEL_CODE');
					$voyage_in = post('VOYAGE_IN');
					$voyage_out = post('VOYAGE_OUT');
					$kade_name = post('KADE_NAME');
					$kade_awal = post('KADE_AWAL');
					$kade_akhir = post('KADE_AKHIR');

					$visit_id   =  "'$visit_id'";
					$nama_kapal =  "'$nama_kapal'"; 
					$kode_kapal =  "'$kode_kapal'";
					$voyage_in  =  "'$voyage_in'";
					$voyage_out =  "'$voyage_out'";
					$kade_name  =  "'$kade_name'";
					$kade_awal  =  "'$kade_awal'"; 
					$kade_akhir =  "'$kade_akhir'";
					
					$con = $this->load->database('ikt_postgree', TRUE);
					$sql = 'SELECT max("id_berthing") FROM "DASHBOARD_BERTHING_PLAN"';
					$data = $con->query($sql)-> row();
					$out->dataheader=$data;
					$array = json_encode($data);
					$x = json_decode($array);
					$y = $x->max;
					$berthing= $y+1;
					//echo $header;

					$id_monitoring_header   =  "'$header'";	
					$id_berthing   =  "'$berthing'";

					$query_header = 'INSERT INTO "DASHBOARD_BERTHING_PLAN"("VISIT_ID",
															  "VESSEL_CODE",
															  "VESSEL_NAME",
															  "VOYAGE_IN", 
															  "VOYAGE_OUT", 
															  "KADE_NAME",
															  "KADE_AWAL",
															  "KADE_AKHIR",
															  "id_berthing"
															  )
														VALUES( '.$visit_id.',
																'.$kode_kapal.', 
																'.$nama_kapal.',															
																'.$voyage_in.', 
																'.$voyage_out.',
																'.$kade_name.',
																'.$kade_awal.',
																'.$kade_akhir.',
																'.$id_berthing.'
																)';
					
					  $db->query($query_header);			
					  

					  
					  // echo $db->get_compiled_insert();die();
					   $db->trans_complete();

					//    var_dump($db->error());
					//    die;
					
					if($db->trans_status()){
						echo "Berhasil";
						$out->success = true;
						$out->msg = 'Berhasil insert data';
					}else{
						echo "Gagal!";
						$out->success = false;
						$out->msg = 'Gagal input ke database, tidak ada data yang di update';
					}
				}else{
					echo 'validasi form error';
					$out->success = false;
					$out->msg = validation_errors();
				}		
			}else{
				$out->success = false;
				$out->msg = 'Anda harus menggunakan POST requestx';
			}
			
			echo @json_encode($out);
		}else{
			echo 'INVALID TOKEN';
		}
	}

	public function update($token){

		if($this->auth->token == $token){
			date_default_timezone_set('Asia/Jakarta');
			$out = new StdClass();			
       
        
			// if(is_post_request()){
                if(isset($_REQUEST)){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;		

				$val->set_rules('KADE_NAME', 'KADE_NAME', 'required');
				$val->set_rules('KADE_AWAL', 'KADE_AWAL', 'required');
				$val->set_rules('KADE_AKHIR', 'KADE_AKHIR', 'required');	
		

				if($val->run()){
					//echo 'masuk 4';
					$db = $this->get_db();
            
					$visit_id = post ('VISIT_ID');
					$nama_kapal = post ('VESSEL_NAME');
					$kode_kapal = post ('VESSEL_CODE');
					$voyage_in = post('VOYAGE_IN');
					$voyage_out = post('VOYAGE_OUT');
					$kade_name = post('KADE_NAME');
					$kade_awal = post('KADE_AWAL');
					$kade_akhir = post('KADE_AKHIR');
					$id_berthing = post ('ID_BERTHING');

					echo $id_berthing;

					$visit_id   =  "'$visit_id'";
					$nama_kapal =  "'$nama_kapal'"; 
					$kode_kapal =  "'$kode_kapal'";
					$voyage_in  =  "'$voyage_in'";
					$voyage_out =  "'$voyage_out'";
					$kade_name  =  "'$kade_name'";
					$kade_awal  =  "'$kade_awal'"; 
					$kade_akhir =  "'$kade_akhir'";

					$query_header = 'UPDATE "DASHBOARD_BERTHING_PLAN" SET 									
														      "KADE_NAME" = '.$kade_name.',
															  "KADE_AWAL" = '.$kade_awal.',
															  "KADE_AKHIR" = '.$kade_akhir.'
																				
									WHERE "id_berthing" = '.$id_berthing.'
				    ';
				
					  $db->query($query_header);
			

					  
					  // echo $db->get_compiled_insert();die();
					   $db->trans_complete();

					//    var_dump($db->error());
					//    die;
					
					if($db->trans_status()){
						echo "Berhasil";
						$out->success = true;
						$out->msg = 'Berhasil insert data';
					}else{
						echo "Gagal!";
						$out->success = false;
						$out->msg = 'Gagal input ke database, tidak ada data yang di update';
					}
				}else{
					//echo 'masukk3';
					$out->success = false;
					$out->msg = validation_errors();
				}		
			}else{
				$out->success = false;
				$out->msg = 'Anda harus menggunakan POST requestx';
			}
			
			echo @json_encode($out);
		}else{
			echo 'INVALID TOKEN';
		}
	}




	
}