<?php
/** Form Zero Defect
  *	Modul untuk menyimpan dan edit data zero defect berdasarkan tahun dan terminal
  *
  */

class FormZero extends CI_Controller{
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
				$val->set_rules('LQ_GATE_1_BACK_KCY', 'LQ_GATE_1_BACK_KCY', 'required');
				$val->set_rules('LQ_GATE_1_QUARANTINE', 'LQ_GATE_1_QUARANTINE', 'required');
				$val->set_rules('LQ_GATE_2', 'LQ_GATE_2', 'required');
				$val->set_rules('LQ_GATE_3', 'LQ_GATE_3', 'required');
				$val->set_rules('CARGO_DEFECT', 'CARGO_DEFECT', 'required');	
				$val->set_rules('TERMINAL', 'TERMINAL', 'required');
				$val->set_rules('MAKER', 'MAKER', 'required');					
				
				if($val->run()){
					$db = $this->get_db();
					
					$periode_bulan = post('PERIODE_BULAN');
					$tahun = post ('TAHUN');
					$back_ky = post ('LQ_GATE_1_BACK_KCY');
					$quarantine = post ('LQ_GATE_1_QUARANTINE');
					$gate2 = post ('LQ_GATE_2');
					$gate3 = post ('LQ_GATE_3');
					$cargo_defect = post ('CARGO_DEFECT');
					$terminal = post ('TERMINAL');
					$maker = post ('MAKER');
				
					$now = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("CREATED_DATE"))));

					$periode_bulan   =  "'$periode_bulan'";
					$tahun =  "'$tahun'"; 
					$now =  "'$now'"; 
					$terminal =  "'$terminal'"; 
					$maker =  "'$maker'";  
					
					$con = $this->load->database('ikt_postgree', TRUE);

				   $dataIdzero = 'SELECT max("id_zero") FROM "DASHBOARD_ZERO_DEFECT"';
				   $data = $con->query($dataIdzero)-> row();
				   $out->datazero=$data;
				   $array = json_encode($data);
				   $x = json_decode($array);
				   $y = $x->max;
				   $zero = $y+1;
			
					$query_header = 'INSERT INTO "DASHBOARD_ZERO_DEFECT"(
									"PERIODE_BULAN",
									"TAHUN",
									"LQ_GATE_1_BACK_KCY",
									"LQ_GATE_1_QUARANTINE", 
									"LQ_GATE_2", 
									"LQ_GATE_3",
									"CARGO_DEFECT",
									"CREATED_DATE",
									"TERMINAL",
									"MAKER",
									"id_zero"
									
							
								)
					VALUES('.$periode_bulan.',
							'.$tahun.',
							'.$back_ky.', 
							'.$quarantine.', 
							'.$gate2.',
							'.$gate3.',
							'.$cargo_defect.',
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
				$val->set_rules('LQ_GATE_1_BACK_KCY', 'LQ_GATE_1_BACK_KCY', 'required');
				$val->set_rules('LQ_GATE_1_QUARANTINE', 'LQ_GATE_1_QUARANTINE', 'required');
				$val->set_rules('LQ_GATE_2', 'LQ_GATE_2', 'required');
				$val->set_rules('LQ_GATE_3', 'LQ_GATE_3', 'required');
				$val->set_rules('CARGO_DEFECT', 'CARGO_DEFECT', 'required');				
				
				if($val->run()){
					$db = $this->get_db();
					
					$periode_bulan = post('PERIODE_BULAN');
					$tahun = post ('TAHUN');
					$terminal = post ('TERMINAL');
					$maker = post ('MAKER');
					$back_ky = post ('LQ_GATE_1_BACK_KCY');
					$quarantine = post ('LQ_GATE_1_QUARANTINE');
					$gate2 = post ('LQ_GATE_2');
					$gate3 = post ('LQ_GATE_3');
					$cargo_defect = post ('CARGO_DEFECT');
					$now = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("CREATED_DATE"))));
					$id_zero = post ('id_zero');

					$periode_bulan   =  "'$periode_bulan'";
					$tahun =  "'$tahun'"; 
					$now =  "'$now'"; 

					$query_header = 'UPDATE "DASHBOARD_ZERO_DEFECT"
									SET 									
									"LQ_GATE_1_BACK_KCY" = '.$back_ky.',
									"LQ_GATE_1_QUARANTINE" = '.$quarantine.', 
									"LQ_GATE_2" ='.$gate2.', 
									"LQ_GATE_3" ='.$gate3.',
									"CARGO_DEFECT" = '.$cargo_defect.',
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