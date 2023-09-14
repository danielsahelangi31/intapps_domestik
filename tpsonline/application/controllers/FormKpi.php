<?php
/** Form KPI
  *	Modul untuk menyimpan dan edit data kpi berdasarkan tahun dan terminal
  *
  */

class FormKpi extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();
		$this->load->model(array('tps_online/model_kpi_barang'
								
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
		redirect('FormKpi/kpi_barang');
	}

	public function save($token){
		if($this->auth->token == $token){
			$out = new StdClass();			
			
			if(is_post_request()){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;			
				$val->set_rules('TERMINAL', 'TERMINAL', 'required');			
				
				if($val->run()){
					$db = $this->get_db();
					
	
					$terminal = post('TERMINAL');
					$periode_mulai = post ('PERIODE');		
					$periode = date('Y-m-d', strtotime($periode_mulai));			
					$ush = post ('USH');
					$bor = post ('BOR');
					$yor = post ('YOR');
					$etbt = post ('ET_BT');
                    $zero = post ('ZERO_DEFECT');
                    $safety = post ('SAFETY');
					$bm = post ('SLA_PRANOTABM');		    
	
					$terminal   =  "'$terminal'";
					$periode_mulai =  "'$periode_mulai'"; 			
                    $ush =  "'$ush'";				
                    $bor =  "'$bor'"; 
                    $yor =  "'$yor'"; 
                    $etbt =  "'$etbt'"; 
                    $zero =  "'$zero'"; 
					$safety =  "'$safety'";        
                    $bm =  "'$bm'";                   
					
				   $con = $this->load->database('ikt_postgree', TRUE);
				   $dataIdkpi = 'SELECT max(id_kpi) FROM "DASHBOARD_KPI"';
				   $data = $con->query($dataIdkpi)-> row();
				   $out->datakpi=$data;
				   $array = json_encode($data);
				   $x = json_decode($array);
				   $y = $x->max;
				   $kpi = $y+1;	
			
				
				   $datasource = 'SELECT "PERIODE","TERMINAL" 
				   FROM "DASHBOARD_KPI"
			   
				   ';
				   $datakpi = $con->query($datasource)-> result_array();
				   $out->datakpi=$datakpi;

				   
			       $cont = count($datakpi);
		
				   for ($x = 0; $x < $cont; $x++) {
  
					$pm1 = $datakpi[$x]['PERIODE'];				
					$tm1 = $datakpi[$x]['TERMINAL'];
				
					$tm1 =  "'$tm1'"; 
					$ps1 =  "'$ps1'"; 
			
					if ($pm1 == $periode && $tm1 == $terminal){	
						$dat = 'sama';
						$out->success = false;
						$out->msg = 'Data sudah tersedia';
					
				    break;
			   
					} else {
						$dat = 'beda';
					}

				 }
				
			
					if ($dat == 'beda'){
					  echo 'bedo';
		
				    $id_kpi =  "'$kpi'"; 
			
					$query_header = 'INSERT INTO "DASHBOARD_KPI"(
									"TERMINAL",
									"PERIODE",							
									"USH", 
									"BOR", 
									"YOR",
									"ET_BT",
									"ZERO_DEFECT",	
									"SAFETY",							
                                    "SLA_PRANOTABM",
                                   	"id_kpi"							
							
								)
					VALUES('.$terminal.',
							'.$periode_mulai.',					
							'.$ush.', 
							'.$bor.',
							'.$yor.',
							'.$etbt.',
							'.$zero.',
							'.$safety.',					
                            '.$bm.',
							'.$id_kpi.'
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
				} else  if ($kpi == 1) {
					$id_kpi =  "'$kpi'"; 
	
						$query_header = 'INSERT INTO "DASHBOARD_KPI"(
										"TERMINAL",
										"PERIODE",									
										"USH", 
										"BOR", 
										"YOR",
										"ET_BT",
										"ZERO_DEFECT",
										"SAFETY",							
										"SLA_PRANOTABM",
										"id_kpi"							
								
									)
						VALUES('.$terminal.',
								'.$periode_mulai.',							
								'.$ush.', 
								'.$bor.',
								'.$yor.',
								'.$etbt.',
								'.$zero.',
								'.$safety.',							
								'.$bm.',
								'.$id_kpi.'
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

	public function update_kpi($token){
		if($this->auth->token == $token){
			$out = new StdClass();			
		
			if(isset($_REQUEST)){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;
				
				$val->set_rules('TERMINAL', 'TERMINAL', 'required');			
				
				if($val->run()){
					$db = $this->get_db();
					
	
					$terminal = post('TERMINAL');
					$periode_mulai = post ('PERIODE');	
					$ush = post ('USH');
					$bor = post ('BOR');
					$yor = post ('YOR');
					$etbt = post ('ET_BT');
                    $zero = post ('ZERO_DEFECT');
					$safety = post ('SAFETY');
					$bm = post ('SLA_PRANOTABM');	
					$kpi = post ('id_kpi');	    
				
					$terminal   =  "'$terminal'";
					$periode_mulai =  "'$periode_mulai'"; 		
                    $ush =  "'$ush'";				
                    $bor =  "'$bor'"; 
                    $yor =  "'$yor'"; 
                    $etbt =  "'$etbt'"; 
                    $zero =  "'$zero'"; 
					$safety =  "'$safety'";                   
                    $bm =  "'$bm'"; 
					$id_kpi =  "'$kpi'"; 
			
					$query_header = 'UPDATE "DASHBOARD_KPI"
									SET 									
									"TERMINAL" = '.$terminal.',
									"PERIODE" = '.$periode_mulai.',															
									"USH" = '.$ush.', 
									"BOR" = '.$bor.', 
									"YOR" = '.$yor.',
									"ET_BT" = '.$etbt.',
									"ZERO_DEFECT" ='.$zero.',
									"SAFETY" = '.$safety.',
									"SLA_PRANOTABM" = '.$bm.'
                                    
									WHERE "id_kpi" ='.$id_kpi.' ';
				
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