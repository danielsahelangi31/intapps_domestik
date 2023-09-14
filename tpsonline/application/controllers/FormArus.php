<?php
/** Form RKAP Arus Barang
  *	Modul untuk menyimpan dan edit data trafik atau arus barang berdasarkan tahun dan terminal
  *
  */

class FormArus extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();
		$this->load->model(array('tps_online/model_rkap_arus_barang'
								
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
			
			if(isset($_REQUEST)){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;
				$val->set_rules('TAHUN', 'TAHUN', 'required');				
				
				if($val->run()){
					$db = $this->get_db();
						
					$con = $this->load->database('ikt_postgree', TRUE);
					$terminal = post('TERMINAL');
					$tahun = post ('TAHUN');
					$jenis = post ('JENIS');
					$komoditi = post ('KOMODITI');
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
					$jenis =  "'$jenis'"; 
                    $satuan =  "'$satuan'"; 			
					$komoditi =  "'$komoditi'"; 
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
					

				   $idBarang = 'SELECT max("id_barang") FROM "DASHBOARD_RKAP_ARUS_BARANG"';
				   $data = $con->query($idBarang)-> row();
				   $out->datazero=$data;
				   $array = json_encode($data);
				   $x = json_decode($array);
				   $y = $x->max;
				   $barang = $y+1;
				
				   
				   $databarang = 'SELECT "TERMINAL", "JENIS", "TAHUN", "KOMODITI", "SATUAN"
				   FROM "DASHBOARD_RKAP_ARUS_BARANG"
			   
				   ';
				   $databarang = $con->query($databarang)-> result_array();
				   $out->databarang=$databarang;

				   
			    	$cont = count($databarang);
		
				   for ($x = 0; $x < $cont; $x++) {
  
					$tm1 = $databarang[$x]['TERMINAL'];
					$j1 = $databarang[$x]['JENIS'];
					$th1 = $databarang[$x]['TAHUN'];
					$km1 = $databarang[$x]['KOMODITI'];
					$st1 = $databarang[$x]['SATUAN'];
				 
					$j1  =  "'$j1'";
					$tm1 =  "'$tm1'"; 
					$th1 =  "'$th1'"; 
					$km1 =  "'$km1'";
					$st1 =  "'$st1'"; 

					if ($tm1 == $terminal && $j1 == $jenis && $km1 == $komoditi && $th1 == $tahun && $st1 == $satuan){
						$dat = 'sama';
			
						$out->success = false;
						$out->amsg = 'Data sudah tersedia';
					
				    break;
			   
					} else {
						$dat = 'beda';
					}
		
				}
				
			
					if ($dat == 'beda'){
				
				    $id_barang =  "'$barang'"; 

					$query_header = 'INSERT INTO "DASHBOARD_RKAP_ARUS_BARANG"(
									"TERMINAL",
									"TAHUN",
									"JENIS",
									"KOMODITI",
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
									"id_barang"								
							
								)
					VALUES('.$terminal.',
							'.$tahun.',
							'.$jenis.', 
							'.$komoditi.',
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
							'.$id_barang.'
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
				} else  if ($barang == 1) {
					$id_barang =  "'$barang'"; 
			
					   $query_header = 'INSERT INTO "DASHBOARD_RKAP_ARUS_BARANG"(
									   "TERMINAL",
									   "TAHUN",
									   "JENIS",
									   "KOMODITI",
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
									   "id_barang"								
							   
								   )
					   VALUES('.$terminal.',
							   '.$tahun.',
							   '.$jenis.', 
							   '.$komoditi.',
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
							   '.$id_barang.'
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

	public function update_barang($token){
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
					$jenis = post ('JENIS');
					$komoditi = post ('KOMODITI');
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
			
					$id_barang = post ('id_barang');

					$terminal   =  "'$terminal'";				
					$jenis =  "'$jenis'"; 
                    $satuan =  "'$satuan'"; 			
					$komoditi =  "'$komoditi'"; 
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
					$id_barang =  "'$id_barang'"; 

					$query_header = 'UPDATE "DASHBOARD_RKAP_ARUS_BARANG"
									SET 									
									"TERMINAL" = '.$terminal.',
									"TAHUN" = '.$tahun.',
									"JENIS" = '.$jenis.',
									"KOMODITI"='.$komoditi.',
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

									WHERE "id_barang" ='.$id_barang.' 		
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