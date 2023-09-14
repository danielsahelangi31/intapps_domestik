<?php
/** Form Safety
  *	Modul untuk menyimpan dan edit data safety berdasarkan tahun dan terminal
  *
  */

class FormSafety extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();
		$this->load->model(array('tps_online/model_zero_defect'
								
                            	));	
		// Dapatkan data login
		if(!$this->auth = $this->userauth->getLoginData()){
			redirect(LOGIN_PAGE);
		}
	}
	
    private function get_db() {
        if (!$this->local_db) {
            $this->local_db = $this->load->database('ikt_postgree', TRUE);

        }

        return $this->local_db;
    }
	
	/** 
	 * Index
	 */
	public function index(){
		redirect('FormZero/form_zero');
	}

	public function save($token){
		if($this->auth->token == $token){
			$out = new StdClass();			
		
			if(isset($_REQUEST)){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;
				$val->set_rules('PERIODE_BULAN', 'Periode (Bulan)', 'required');
				$val->set_rules('TAHUN', 'TAHUN', 'required');		
				$val->set_rules('TERMINAL', 'TERMINAL', 'required');
				$val->set_rules('MAKER', 'MAKER', 'required');					
				
				if($val->run()){
					$db = $this->get_db();
					
					$periode_bulan = post('PERIODE_BULAN');
					$tahun = post ('TAHUN');
					$accident = post ('ACCIDENT');
					$incident = post ('INCIDENT');
					$unitImpact = post ('UNIT_IMPACT');
			
					$terminal = post ('TERMINAL');
					$maker = post ('MAKER');
				
					$now = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("CREATED_DATE"))));

					$periode_bulan   =  "'$periode_bulan'";
					$tahun =  "'$tahun'"; 
					$now =  "'$now'"; 
					$terminal =  "'$terminal'"; 
					$maker =  "'$maker'";  
					
					$con = $this->load->database('ikt_postgree', TRUE);

				   $dataIdsafety = 'SELECT max("id_zero") FROM "DASHBOARD_ZERO_SAFETY"';
				   $data = $con->query($dataIdsafety)-> row();
				   $out->datazero=$data;
				   $array = json_encode($data);
				   $x = json_decode($array);
				   $y = $x->max;
				   $zero = $y+1;
			
					$query_header = 'INSERT INTO "DASHBOARD_ZERO_SAFETY"(
									"PERIODE_BULAN",
									"TAHUN",
									"ACCIDENT",
									"INCIDENT", 
									"UNIT_IMPACT", 							
									"CREATED_DATE",
									"TERMINAL",
									"MAKER",
									"id_zero"
									
							
								)
					VALUES('.$periode_bulan.',
							'.$tahun.',
							'.$accident.', 
							'.$incident.', 
							'.$unitImpact.',						
							'.$now.',
							'.$terminal.',
							'.$maker.',
							'.$zero.'						
					
							)';
		
					$db->query($query_header);
					$db->trans_complete();
					
					if($db->trans_status()){
						$out->success = true;
						$out->msg = 'Berhasil insert data';
					}else{
						$out->success = false;
						$out->msg = 'Gagal input ke database, tidak ada data yang di update';
					}
				}else{
					$out->success = false;
					$out->msg = validation_errors();
				}		
			}else{
				$out->success = false;
				$out->msg = 'Anda harus menggunakan POST request';
			}
			
			echo @json_encode($out);
		}else{
			echo 'INVALID TOKEN';
		}
	}

	public function update_zero($token){
		if($this->auth->token == $token){
			$out = new StdClass();			
		
			if(isset($_REQUEST)){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;
				$val->set_rules('PERIODE_BULAN', 'Periode (Bulan)', 'required');
				$val->set_rules('TAHUN', 'TAHUN', 'required');					
				
				if($val->run()){
					$db = $this->get_db();
					
					$periode_bulan = post('PERIODE_BULAN');
					$tahun = post ('TAHUN');
					$terminal = post ('TERMINAL');
					$maker = post ('MAKER');
					$accident = post ('ACCIDENT');
					$incident = post ('INCIDENT');
					$unitImpact = post ('UNIT_IMPACT');
					$now = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("CREATED_DATE"))));
					$id_zero = post ('id_zero');

					$periode_bulan   =  "'$periode_bulan'";
					$tahun =  "'$tahun'"; 
					$now =  "'$now'"; 

					$query_header = 'UPDATE "DASHBOARD_ZERO_SAFETY"
									SET 									
									"ACCIDENT" = '.$accident.',
									"INCIDENT" = '.$incident.', 
									"UNIT_IMPACT" ='.$unitImpact.', 					
									"CREATED_DATE" = '.$now.'	

									WHERE "id_zero" ='.$id_zero.' 		
							';

					$db->query($query_header);
					$db->trans_complete();
					
					if($db->trans_status()){
						$out->success = true;
						$out->msg = 'Berhasil insert data';
					}else{
						$out->success = false;
						$out->msg = 'Gagal input ke database, tidak ada data yang di update';
					}
				}else{
					$out->success = false;
					$out->msg = validation_errors();
				}		
			}else{
				$out->success = false;
				$out->msg = 'Anda harus menggunakan POST request';
			}
			
			echo @json_encode($out);
		}else{
			echo 'INVALID TOKEN';
		}
	}
	
	public function form_deskripsi(){
		
		$data['data'] = $this->Model_dashboard->load_vessel(); 
	
		$this->load->view('backend/pages/dashboard/form_deskripsi',$data);
	}


}