<?php
/** Form Tarif TW
  *	Modul untuk menyimpan dan edit data tarif tw berdasarkan tahun dan terminal
  *
  */

class FormTarif extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();
		$this->load->model(array('tps_online/model_tarif'
								
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
		redirect('FormTarif/tarif_tw');
	}

	public function save($token){
		if($this->auth->token == $token){
			$out = new StdClass();			
		
			if(is_post_request()){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;			
				$val->set_rules('TAHUN', 'TAHUN', 'required');			
				
				if($val->run()){
					$db = $this->get_db();					
	
					$terminal = post('TERMINAL');
					$pelayanan = post('PELAYANAN');
					$tahun = post ('TAHUN');
					$golongan = post ('GOLONGAN');
					$komoditi = post ('KOMODITI');
					$tarif1 = post ('TARIF_1');
					$tarif2 = post ('TARIF_2');
					$type = post ('TYPE');
					$tarifI = post ('TARIF_I');
					$tarifII = post ('TARIF_II');
					$tariff = post ('TARIF');
		
					$terminal   =  "'$terminal'";
					$pelayanan   =  "'$pelayanan'";
					$tahun =  "'$tahun'"; 
					$golongan =  "'$golongan'";            		
					$komoditi =  "'$komoditi'";  
                    $tarif1 =  "'$tarif1'";				
                    $tarif2 =  "'$tarif2'"; 
					$type =  "'$type'"; 
					$tarifI =  "'$tarifI'";				
                    $tarifII =  "'$tarifII'"; 					
					$tariff =  "'$tariff'";			
      

				   $con = $this->load->database('ikt_postgree', TRUE);
				   $dataIdtarif = 'SELECT max("ID_TARIF") FROM "DASHBOARD_TARIF_TW"';
				   $data = $con->query($dataIdtarif)-> row();
				   $out->datakpi=$data;
				   $array = json_encode($data);
				   $x = json_decode($array);
				   $y = $x->max;
				   $tarif = $y+1;	
				   $id_tarif =  "'$tarif'"; 					 
				
				   $datasource = 'SELECT "PELAYANAN","KOMODITI","TAHUN" 
				   FROM "DASHBOARD_TARIF_TW"
			   
				   ';
				   $datatarif = $con->query($datasource)-> result_array();
				   $out->datatarif=$datatarif;

				   
				   
			       $cont = count($datatarif);
		
				   for ($x = 0; $x < $cont; $x++) {
  
					$pm1 = $datatarif[$x]['PELAYANAN'];
					$tm1 = $datatarif[$x]['KOMODITI'];
					$ps1 = $datatarif[$x]['TAHUN'];
				 
					$pm1  =  "'$pm1'";
					$tm1 =  "'$tm1'"; 
					$ps1 =  "'$ps1'"; 

					if ($pm1 == $pelayanan && $ps1 == $tahun && $tm1 == $komoditi){
						$dat = 'sama';
						$out->success = false;
						$out->msg = 'Data sudah tersedia';
				
				    break;
			   
					} else {
						$dat = 'beda';
					}

				 }
				
			
					if ($dat == 'beda'){
					
				    $id_tarif =  "'$tarif'"; 
				
					$query_header = 'INSERT INTO "DASHBOARD_TARIF_TW"(
									"TERMINAL",
									"KOMODITI",
									"PELAYANAN",								
									"GOLONGAN",
									"TAHUN",
									"TARIF_1", 
									"TARIF_2",								
                                   	"ID_TARIF",
									"TYPE"							
							
								)
					 VALUES('.$terminal.',
							'.$komoditi.',
							'.$pelayanan.',
							'.$golongan.', 
							'.$tahun.', 
							'.$tarif1.' || '.$tarifI.' || '.$tariff.', 
							'.$tarif2.' || '.$tarifII.',					
							'.$id_tarif.',
							'.$type.'
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
				} else  if ($tarif == 1) {
		
							$query_header = 'INSERT INTO "DASHBOARD_TARIF_TW"(
								"TERMINAL",
								"KOMODITI",
								"PELAYANAN",								
								"GOLONGAN",
								"TAHUN",
								"TARIF_1", 
								"TARIF_2",								
								"ID_TARIF",
								"TYPE"						
				
									)
					VALUES('.$terminal.',
							'.$komoditi.',
							'.$pelayanan.',
							'.$golongan.', 
							'.$tahun.', 
							'.$tarif1.' || '.$tarifI.' || '.$tariff.', 
							'.$tarif2.' || '.$tarifII.',					
							'.$id_tarif.',
							'.$type.'
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

	public function update_tarif($token){
		if($this->auth->token == $token){
			$out = new StdClass();			
			
			if(isset($_REQUEST)){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;
				
				$val->set_rules('TAHUN', 'TAHUN', 'required');			
				
				if($val->run()){
					$db = $this->get_db();
					
					$pelayanan = post('PELAYANAN');
					$tahun = post ('TAHUN');
					$golongan = post ('GOLONGAN');
					$komoditi = post ('KOMODITI');
					$tarif1 = post ('TARIF_1');
					$tarif2 = post ('TARIF_2');
					$type = post ('TYPE');
					$tarifI = post ('TARIF_I');
					$tarifII = post ('TARIF_II');
					$tariff = post ('TARIF');
					$terminal = post('TERMINAL');

					$terminal   =  "'$terminal'";
					$pelayanan   =  "'$pelayanan'";
					$tahun =  "'$tahun'"; 
					$golongan =  "'$golongan'";            		
					$komoditi =  "'$komoditi'";  
                    $tarif1 =  "'$tarif1'";				
                    $tarif2 =  "'$tarif2'"; 
					$type =  "'$type'"; 
					$tarifI =  "'$tarifI'";				
                    $tarifII =  "'$tarifII'"; 					
					$tariff =  "'$tariff'";	

                    $tarif = post ('ID_TARIF');	    
				
					$id_tarif =  "'$tarif'"; 
			
					$query_header = 'UPDATE "DASHBOARD_TARIF_TW"
									SET 
									"TERMINAL" = '.$terminal.',									
									"KOMODITI" = '.$komoditi.',
									"PELAYANAN" = '.$pelayanan.',
									"GOLONGAN" = '.$golongan.',		
									"TAHUN" = '.$tahun.',                                                           
									"TARIF_1" = '.$tarif1.' || '.$tarifI.' || '.$tariff.', 
									"TARIF_2" = '.$tarif2.' || '.$tarifII.',				
									"TYPE" = '.$type.'
									WHERE "ID_TARIF" ='.$id_tarif.' ';
				
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




}