<?php
/** Form RKAP Trafik Kapal
  *	Modul untuk menyimpan dan edit data rkap trafik kapal berdasarkan tahun dan terminal
  *
  */

class FormTrafik extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();
		$this->load->model(array('tps_online/model_rkap_trafik_kapal'
								
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
		redirect('FormTraafik/rkap_trafik_kapal');
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
					$tahun = post ('TAHUN');
					$pelayaran = post ('PELAYARAN');
					$satuan = post ('SATUAN');
					$jan = post ('JANUARI');
					$feb = post ('FEBRUARI');
					$mar = post ('MARET');
                    $apr = post ('APRIL');
                    $mei = post ('MEI');
                    $juni = post ('JUNI');
                    $juli = post ('JULI');
                    $agus = post ('AGUSTUS');
                    $sep = post ('SEPTEMBER');
                    $okt = post ('OKTOBER');
                    $nov = post ('NOVEMBER');
                    $des = post ('DESEMBER');
				
					$terminal   =  "'$terminal'";
					$tahun =  "'$tahun'"; 
					$pelayaran =  "'$pelayaran'"; 
                    $satuan =  "'$satuan'";		
                    $jan =  "'$jan'"; 
                    $feb =  "'$feb'"; 
                    $mar =  "'$mar'"; 
                    $apr =  "'$apr'"; 
                    $mei =  "'$mei'"; 
                    $juni =  "'$juni'"; 
                    $juli =  "'$juli'"; 
                    $agus =  "'$agus'"; 
                    $sep =  "'$sep'"; 
                    $okt =  "'$okt'"; 
                    $nov =  "'$nov'"; 
                    $des =  "'$des'"; 
			

					$con = $this->load->database('ikt_postgree', TRUE);
				   $dataIdtrafik = 'SELECT max(id_trafik) FROM "DASHBOARD_RKAP_TRAFFIK"';
				   $data = $con->query($dataIdtrafik)-> row();
				   $out->datatrafik=$data;
				   $array = json_encode($data);
				   $x = json_decode($array);
				   $y = $x->max;
				   $trafik = $y+1;	
			
				   $datasource = 'SELECT "PELAYARAN","TERMINAL","TAHUN", "SATUAN"
				   FROM "DASHBOARD_RKAP_TRAFFIK"
			   
				   ';
				   $datatrafik = $con->query($datasource)-> result_array();
				   $out->datatrafik=$datatrafik;

				   
				   
			   	$cont = count($datatrafik);
		
				   for ($x = 0; $x < $cont; $x++) {
  
					$p1 = $datatrafik[$x]['PELAYARAN'];
					$tm1 = $datatrafik[$x]['TERMINAL'];
					$th1 = $datatrafik[$x]['TAHUN'];
					$st1 = $datatrafik[$x]['SATUAN'];
				 
					$p1  =  "'$p1'";
					$tm1 =  "'$tm1'"; 
					$th1 =  "'$th1'"; 
					$st1 =  "'$st1'"; 

					if ($p1 == $pelayaran && $tm1 == $terminal && $th1 == $tahun && $st1 == $satuan){
						$dat = 'sama';
						$out->success = error;
						$out->msg = 'Data sudah tersedia';
					
				    break;
			   
					} else {
						$dat = 'beda';
					}

				}
				
			
					if ($dat == 'beda'){
				
				  $id_trafik =  "'$trafik'"; 
	
					$query_header = 'INSERT INTO "DASHBOARD_RKAP_TRAFFIK"(
									"TERMINAL",
									"TAHUN",
									"PELAYARAN",
									"SATUAN", 
									"JANUARI", 
									"FEBRUARI",
									"MARET",
									"APRIL",
									"MEI",
                                    "JUNI",
                                    "JULI",
                                    "AGUSTUS",
                                    "SEPTEMBER",
                                    "OKTOBER",
                                    "NOVEMBER",
                                    "DESEMBER",
									"id_trafik"							
							
								)
					VALUES('.$terminal.',
							'.$tahun.',
							'.$pelayaran.', 
							'.$satuan.', 
							'.$jan.',
							'.$feb.',
							'.$mar.',
							'.$apr.',
							'.$mei.',
                            '.$juni.',
							'.$juli.',
							'.$agus.',
							'.$sep.',
							'.$okt.',
                            '.$nov.',
							'.$des.',
							'.$id_trafik.'
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
				
					} else  if ($trafik == 1) {
						$id_trafik =  "'$trafik'"; 
				
							$query_header = 'INSERT INTO "DASHBOARD_RKAP_TRAFFIK"(
											"TERMINAL",
											"TAHUN",
											"PELAYARAN",
											"SATUAN", 
											"JANUARI", 
											"FEBRUARI",
											"MARET",
											"APRIL",
											"MEI",
											"JUNI",
											"JULI",
											"AGUSTUS",
											"SEPTEMBER",
											"OKTOBER",
											"NOVEMBER",
											"DESEMBER",
											"id_trafik"							
									
										)
							VALUES('.$terminal.',
									'.$tahun.',
									'.$pelayaran.', 
									'.$satuan.', 
									'.$jan.',
									'.$feb.',
									'.$mar.',
									'.$apr.',
									'.$mei.',
									'.$juni.',
									'.$juli.',
									'.$agus.',
									'.$sep.',
									'.$okt.',
									'.$nov.',
									'.$des.',
									'.$id_trafik.'
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

	public function update_trafik($token){
		if($this->auth->token == $token){
			$out = new StdClass();			
		
			if(isset($_REQUEST)){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;
				
				$val->set_rules('TAHUN', 'TAHUN', 'required');				
				
				if($val->run()){
					$db = $this->get_db();
							
					
					$terminal = post('TERMINAL');
					$tahun = post ('TAHUN');
					$pelayaran = post ('PELAYARAN');
					$satuan = post ('SATUAN');
					$jan = post ('JANUARI');
					$feb = post ('FEBRUARI');
					$mar = post ('MARET');
                    $apr = post ('APRIL');
                    $mei = post ('MEI');
                    $juni = post ('JUNI');
                    $juli = post ('JULI');
                    $agus = post ('AGUSTUS');
                    $sep = post ('SEPTEMBER');
                    $okt = post ('OKTOBER');
                    $nov = post ('NOVEMBER');
                    $des = post ('DESEMBER');
			
					$id_trafik = post ('id_trafik');

					$terminal   =  "'$terminal'";			
					$pelayaran=  "'$pelayaran'"; 
                    $satuan =  "'$satuan'"; 					
                    $jan =  "'$jan'"; 
                    $feb =  "'$feb'"; 
                    $mar =  "'$mar'"; 
                    $apr =  "'$apr'"; 
                    $mei =  "'$mei'"; 
                    $juni =  "'$juni'"; 
                    $juli =  "'$juli'"; 
                    $agus =  "'$agus'"; 
                    $sep =  "'$sep'"; 
                    $okt =  "'$okt'"; 
                    $nov =  "'$nov'"; 
                    $des =  "'$des'"; 
					$id_trafik =  "'$id_trafik'"; 

					$query_header = 'UPDATE "DASHBOARD_RKAP_TRAFFIK"
									SET 									
									"TERMINAL" = '.$terminal.',
									"TAHUN" = '.$tahun.',
									"PELAYARAN" = '.$pelayaran.',								
									"SATUAN" = '.$satuan.', 
									"JANUARI" = '.$jan.', 
									"FEBRUARI" = '.$feb.',
									"MARET" = '.$mar.',
									"APRIL" ='.$apr.',
									"MEI" = '.$mei.',
                                    "JUNI"='.$juni.',
                                    "JULI"='.$juli.',
                                    "AGUSTUS" = '.$agus.',
                                    "SEPTEMBER" = '.$sep.',
                                    "OKTOBER" = '.$okt.',
                                    "NOVEMBER" = '.$nov.',
                                    "DESEMBER" = '.$des.'										

									WHERE "id_trafik" ='.$id_trafik.' ';
				
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