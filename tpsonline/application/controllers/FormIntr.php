<?php
/** Form Monitoring BM Internasional
  *	Modul untuk menyimpan dan edit data monitoring BM internasional tiap kapal
  *
  */

class FormIntr extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();
		$this->load->model(array('Model_dashboard'
                              
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
		redirect('FormDetail/form_detail');
	}
    
	public function simpan($token){
	
		if($this->auth->token == $token){
			date_default_timezone_set('Asia/Jakarta');
			$out = new StdClass();		
		
                if(isset($_REQUEST)){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;
				$val->set_rules('NAMA_KAPAL', 'Nama Kapal', 'required');
	
				$val->set_rules('VOYAGE', 'VOYAGE', 'required');

				$val->set_rules('RENCANA_BONGKAR', 'RENCANA BONGKAR', 'required');
				$val->set_rules('RENCANA_MUAT', 'RENCANA MUAT', 'required');	
	
				
				$id_monitoring_detail = 0;
				if($val->run()==TRUE){
		
					$db = $this->get_db();
					$activity = post ('ACTIVITY');				

				if ($activity != 'VESSEL DEPARTURE'){	
					$con = $this->load->database('ikt_postgree', TRUE);
							
					$nama_kapal = post ('NAMA_KAPAL');
					$kade_dermaga = post ('KADE_DERMAGA');
					$voyage = post('VOYAGE');
			
					$rencana_bongkar = post ('RENCANA_BONGKAR');
					$rencana_muat = post ('RENCANA_MUAT');
					$shift = post('SHIFT');
			
					$realisasi_bongkar = post ('REALISASI_BONGKAR');
					$realisasi_muat = post('REALISASI_MUAT');		
			
					if ($realisasi_bongkar == ''){
						$realisasi_bongkar = '0';
					}
					if ($realisasi_muat == ''){
						$realisasi_muat = '0';
					}
		
					$total = $realisasi_bongkar + $realisasi_muat;	
							$eta = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ETA"))));	
							$etb = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ETB"))));
							$etd = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ETD"))));
							$ata = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ATA"))));
							$atb = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ATB"))));
							$atd = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ATD"))));
							$commence = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("COMMENCE"))));
							$complete = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("COMPLETE"))));
							$timend = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("TIME_END"))));
							$timestart = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("TIME_START"))));
							$tanggal = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("TANGGAL_TIME"))));
							$start = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("TIME_START"))));
				     		$end = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("TIME_END"))));
							$atbs = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ATB"))));	
				
					        $date1 = strtotime($end);
							$date2 = strtotime($start);
					
							$subTime = $date1 - $date2;
						
							$y = ($subTime/(60*60*24*365));
							$d = ($subTime/(60*60*24))%365;
							$h = ($subTime/(60*60))%24;
							$m = ($subTime/60)%60;	
													

							$bt= date('H:i',strtotime($m));

							$minutes_to_add = $m;
							$hour_to_add = $h;

							$time = new DateTime('0000');
							$time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
							$time->add(new DateInterval('PT' . $hour_to_add . 'H'));

							$bt = $time->format('Y-m-d H:i:s');
							if ($activity == 'BREAK'){
								$total_not = $bt;
							} else if ($activity == 'OPEN RAMPDOOR'){
								$total_not = $bt;
							} else if ($activity == 'CLOSE RAMPDOOR'){
								$total_not = $bt;
							} else {
								$total_not = '1970-01-01 0:00';
							}
		
							if ($activity == 'BAD WEATHER'){
								$total_it = $bt;
							} else if ($activity == 'ACCIDENT'){
								$total_it = $bt;
							} else if ($activity == 'INCIDENT'){
								$total_it = $bt;
							} else if ($activity == 'ENGINE TROUBLE'){
								$total_it = $bt;
							} else if ($activity == 'UNIT TROUBLE'){
								$total_it = $bt;
							} else if ($activity == 'FORCE MAJURE'){
								$total_it = $bt;
							} else if ($activity == 'EMPTY FUEL'){
								$total_it = $bt;
							} else if ($activity == 'WAITING CLEARANCE DOCS'){
								$total_it = $bt;
							} else if ($activity == 'WAITING CARGO'){
								$total_it = $bt;
							} else {
								$total_it = '1970-01-01 0:00';
							}

							
							$nama_kapal = "'$nama_kapal'";
							$voyage   =  "'$voyage'";
							$dataVoyage = 'SELECT count("voyage") as count FROM "DASHBOARD_BM_DETAIL" WHERE "nama_kapal"='.$nama_kapal.' and voyage = '.$voyage.'';
					
							$dataVoyage = $con->query($dataVoyage)-> row();
							$out->dataheader=$dataVoyage;
						
							$count = $dataVoyage->count;
				
							if ($count == '0' || $count == 0){
								$btp = $bt;
							} else if ($count >= '1' || $count >= 1) {
					
									$dataBt = 'SELECT max("ID_MONITORING_DETAIL") as ID, max("BT") as BT FROM "DASHBOARD_BM_DETAIL" WHERE nama_kapal = '.$nama_kapal.' and voyage = '.$voyage.'';
									$dataBt = $con->query($dataBt)-> result_array();
									$out->data=$dataBt;

									$bt1 = $dataBt[0]['bt'];
									$bt2 = $bt;
							
								
									$time = date('H:i:s',strtotime($bt1));;
									$time2 = date('H:i:s',strtotime($bt2));;								
								
									$secs = strtotime($time2)-strtotime("00:00:00");
									$btp = date("Y-m-d H:i:s",strtotime($time)+$secs);	
							
							} 

							//CALCULATE BWT
							if ($count == '0' || $count == 0){
					
								$date1 = strtotime($bt);
								$date2 = strtotime($total_not);
							
								$subTime = $date1 - $date2;
							
								$y = ($subTime/(60*60*24*365));
								$d = ($subTime/(60*60*24))%365;
								$h = ($subTime/(60*60))%24;
								$m = ($subTime/60)%60;	
														
	
								$bwtt= date('H:i',strtotime($m));
	
								$minutes_to_add = $m;
								$hour_to_add = $h;
	
								$times = new DateTime('0000');
								$times->add(new DateInterval('PT' . $minutes_to_add . 'M'));
								$times->add(new DateInterval('PT' . $hour_to_add . 'H'));
	
								$bwt = $times->format('Y-m-d H:i:s');
								$bwtH = $times->format('H');
								$bwtI = $times->format('i');

								$ush_gross = (60/($bwtH*60+$bwtI))*$total;
						
								$ush_gross = ceil($ush_gross);
								if ($ush_gross == INF || $ush_gross == NaN || $ush_gross == NAN || $ush_gross == nan){
									$ush_gross = 0;
								}
								if ($total == NAN) {
									$ush_gross = 0;
								}

								if  ($total == nan){
									$ush_gross = 0;
								}
						
							//CALCULATE ET						
								$date1 = strtotime($bwt);
								$date2 = strtotime($total_it);
					
								$subTime = $date1 - $date2;
							
								$y = ($subTime/(60*60*24*365));
								$d = ($subTime/(60*60*24))%365;
								$h = ($subTime/(60*60))%24;
								$m = ($subTime/60)%60;	
														

								$ett= date('H:i',strtotime($m));

								$minutes_to_add = $m;
								$hour_to_add = $h;

								$timest = new DateTime('0000');
								$timest->add(new DateInterval('PT' . $minutes_to_add . 'M'));
								$timest->add(new DateInterval('PT' . $hour_to_add . 'H'));

								$et = $timest->format('Y-m-d H:i:s');
								$etH = $timest->format('H');
								$etI = $timest->format('i');
								//USH						
								$ush = (60/($etH*60+$etI))*$total;
								$ush = ceil($ush);							
								if ($ush == INF || $ush == NaN || $ush == NAN  || $ush == nan){
									$ush = 0;
								}
								if ($total == NAN) {
									$ush = 0;
								}

								if  ($total == nan){
									$ush = 0;
								}
				
							//CALCULATE ET-BT
								$ett1 = date('H',strtotime($et));
								$ett2 = date('i',strtotime($et));												
								$ett = (60*$ett1)+$ett2;
		
								$btt1 = date('H',strtotime($btp));
								$btt2 = date('i',strtotime($btp));	
								$btt = (60*$btt1)+$btt2;
								$etbt = ($ett/$btt)*100;
								$etbt = ceil($etbt);

								if ($etbt == NAN || $etbt == NaN || $etbt == INF){
									$etbt = 0;
								}
						
							} else if ($count >= '1' || $count >= 1) {
				
									$dataBwt = 'SELECT max("ID_MONITORING_DETAIL") as ID, max("WORKING_HOURBT") as BT, max("BWT") as BWT, max("ET") as ET FROM "DASHBOARD_BM_DETAIL" WHERE nama_kapal = '.$nama_kapal.' and voyage = '.$voyage.'';
									$data = $con->query($dataBwt)-> result_array();
									$out->data=$data;

									$bwt1 = $data[0]['bwt'];
									$bwt2 = $bt;
							
								
									$time = date('H:i:s',strtotime($bwt1));;
									$time2 = date('H:i:s',strtotime($bwt2));;
									$not  = date('H:i:s',strtotime($total_not));;								
								
									$secs = strtotime($time2)-strtotime("00:00:00");
						
									$bwtn = date("Y-m-d H:i:s",strtotime($time)+$secs);							

									$date1 = strtotime($bwtn);
									$date2 = strtotime($total_not);
						
									$subTime = $date1 - $date2;
							
									$y = ($subTime/(60*60*24*365));
									$d = ($subTime/(60*60*24))%365;
									$h = ($subTime/(60*60))%24;
									$m = ($subTime/60)%60;	
														
	
									$bwtk = date('H:i',strtotime($m));
	
									$minutes_to_add = $m;
									$hour_to_add = $h;
		
									$times = new DateTime('0000');
									$times->add(new DateInterval('PT' . $minutes_to_add . 'M'));
									$times->add(new DateInterval('PT' . $hour_to_add . 'H'));
		
									$bwt = $times->format('Y-m-d H:i:s');
									$bwtH = $times->format('H');
									$bwtI = $times->format('i');
									$ush_gross = (60/($bwtH*60+$bwtI))*$total;
									$ush_gross = ceil($ush_gross);
									if ($ush_gross == INF || $ush_gross == NaN || $ush_gross == NAN || $ush_gross == nan){
										$ush_gross = 0;
									}
								
							   //CALCULATE ET
					
							   $et1 = $data[0]['et'];
							   $et2 = $bt;

							   $time1 = date('H:i:s',strtotime($et1));;
							   $time2 = date('H:i:s',strtotime($et2));;
														   
							
							   $secs = strtotime($time2)-strtotime("00:00:00");
				   
							   $tbt = date("Y-m-d H:i:s",strtotime($time1)+$secs);
					
								$dateA = strtotime($tbt);
								$dateB = strtotime($total_not);
								$dateC = strtotime($total_it);
						
								$subTime = $dateA - $dateB;
							
								$y = ($subTime/(60*60*24*365));
								$d = ($subTime/(60*60*24))%365;
								$h = ($subTime/(60*60))%24;
								$m = ($subTime/60)%60;	
							
						
								$etno = date('H:i',strtotime($m));
	
								$minutes_to_add = $m;
								$hour_to_add = $h;
	
								$timesn = new DateTime('0000');
								$timesn->add(new DateInterval('PT' . $minutes_to_add . 'M'));
								$timesn->add(new DateInterval('PT' . $hour_to_add . 'H'));
	
								$etnot = $timesn->format('Y-m-d H:i:s');
						
								$date01 = strtotime($etnot);
								$date02 = strtotime($total_it);
						
								$subTime = $date01 - $date02;
							
								$y = ($subTime/(60*60*24*365));
								$d = ($subTime/(60*60*24))%365;
								$h = ($subTime/(60*60))%24;
								$m = ($subTime/60)%60;	
														

								$etnt= date('H:i',strtotime($m));

								$minutes_to_add = $m;
								$hour_to_add = $h;

								$timest = new DateTime('0000');
								$timest->add(new DateInterval('PT' . $minutes_to_add . 'M'));
								$timest->add(new DateInterval('PT' . $hour_to_add . 'H'));

								$et = $timest->format('Y-m-d H:i:s');
								$etH = $timest->format('H');
								$etI = $timest->format('i');
								//USH						
								$ush = (60/($etH*60+$etI))*$total;
								$ush = ceil($ush);
								if ($ush == INF || $ush == NaN || $ush == NAN  || $ush == nan){
									$ush = 0;
								}							
							
								$ett1 = date('H',strtotime($et));
								$ett2 = date('i',strtotime($et));												
								$ett = (60*$ett1)+$ett2;
		
								$btt1 = date('H',strtotime($btp));
								$btt2 = date('i',strtotime($btp));	
								$btt = (60*$btt1)+$btt2;
								$etbt = ($ett/$btt)*100;
								$etbt = ceil($etbt);

								if ($etbt == NAN || $etbt == NaN || $etbt == INF){
									$etbt = 0;
								}
							
							} 
					
							
						$kade_dermaga   =  "'$kade_dermaga'";
		
						$pbm   =  "'$pbm'";
						$rencana_bongkar   =  "'$rencana_bongkar'";
						$rencana_muat   =  "'$rencana_muat'";
						$eta   =  "'$eta'";
						$etb   =  "'$etb'";
						$etd   =  "'$etd'";
						$ata   =  "'$ata'";
						$atb   =  "'$atb'";
						$atd   =  "'$atd'";
						$commence   =  "'$commence'";
						$complete   =  "'$complete'";

						$date = "'DD-MM-YYYY HH24:MI:SS'";
						$second = "'HH24:MI:SS'";

				
						$dataIdheader = 'SELECT max("ID_HEADER") FROM "DASHBOARD_BM_HEADER"';
						$data = $con->query($dataIdheader)-> row();
						$out->dataheader=$data;
						$array = json_encode($data);
						$x = json_decode($array);
						$y = $x->max;
						$header= $y+1;
		
						$id_monitoring_header   =  "'$header'";	
						$id_header   =  "'$header'";	
	
					$query_header = 'INSERT INTO "DASHBOARD_BM_HEADER"("ID_HEADER",
															  "NAMA_KAPAL",	
															  "KADE_DERMAGA",													
															  "VOYAGE",													
															  "RENCANA_BONGKAR",
															  "RENCANA_MUAT",
															  "ETA",
															  "ETB",
															  "ETD",
															  "ATA",
															  "ATB",
															  "ATD",
															  "COMMENCE",
															  "COMPLETE",
															  "ID_MONITORING_HEADER"
															  )
														VALUES( '.$id_header.',
																'.$nama_kapal.',
																'.$kade_dermaga.',															
																'.$voyage.',														
																'.$rencana_bongkar.',
																'.$rencana_muat.',
																'.$eta.',
																'.$etb.',
																'.$etd.',
																'.$ata.',
																'.$atb.', 
																'.$atd.', 
																'.$commence.',
																'.$complete.',
																'.$id_header.'
																)';
								
							$shift   =  "'$shift'";
							$activity   =  "'$activity'";
							$tanggal   =  "'$tanggal'";
							$realisasi_bongkar   =  "'$realisasi_bongkar'";
							$realisasi_muat   =  "'$realisasi_muat'";
							$timestart  =  "'$timestart'";
							$timend   =  "'$timend'";
							$bt   =  "'$bt'";
							$bwt   =  "'$bwt'";
							$et   =  "'$et'";				
							$btp   =  "'$btp'";
							$etbt   =  "'$etbt'";
							$total_not   =  "'$total_not'";
							$total_it   =  "'$total_it'";
						
							$total  =  "'$total'";
							
							$dataIddetail = 'SELECT max("ID_MONITORING_DETAIL") FROM "DASHBOARD_BM_DETAIL"';
							$data = $con->query($dataIddetail)-> row();
							$out->datadetail=$data;
							$array = json_encode($data);
							$x = json_decode($array);
							$y = $x->max;
							$detail= $y+1;
			
							//TOTAL REMAINING
							$dataVoyage = 'SELECT count("voyage") as count FROM "DASHBOARD_BM_DETAIL" WHERE "nama_kapal"='.$nama_kapal.' and voyage = '.$voyage.'';
					
							$data = $con->query($dataVoyage)-> row();
							$out->dataheader=$data;
					
							$count = $data->count;
							if ($count == '0' || $count == 0){
								$rem_bong = $rencana_bongkar;
								$rem_muat = $rencana_muat;
							
							} else if ($count >= '1' || $count >= 1) {
							
									$hasis 	= 0;
									$dataRemaining 	= 'SELECT max("ID_MONITORING_DETAIL") as ID, max("REMAINING_BONGKAR") as RB, max("REMAINING_MUAT") as RM FROM "DASHBOARD_BM_DETAIL" WHERE nama_kapal = '.$nama_kapal.' and voyage = '.$voyage.'';
									$data 	= $con->query($dataRemaining)-> result_array();
									$out->data=$data;

									
									$remainaingBongkar = intval($data[0]['rb']);
									$inputRealisasi    = (post ('REALISASI_BONGKAR') ? post ('REALISASI_BONGKAR'):0);
									$rem_bong 		   = $remainaingBongkar - $inputRealisasi;
									
									$realisasiMuat    = (post ('REALISASI_MUAT') ? post ('REALISASI_MUAT'):0);
									$RM = $data[0]['rm'];
									$R3 = (int)$RM;
									$R4 = (int)$realisasiMuat;
									$rem_muat = ($R3-$R4);
						
							} 
							$terminal = 'INTERNASIONAL';
							$id_detail   =  "'$detail'";	
							$terminal   =  "'$terminal'";
						$query_detail = 'INSERT INTO "DASHBOARD_BM_DETAIL"("ID_MONITORING_DETAIL",
																		"ID_MONITORING_HEADER",
																		"SHIFT",
																		"ACTIVITY",
																		"TANGGAL_TIME",																																	
																		"REALISASI_BONGKAR",
																		"REALISASI_MUAT",		
																		"TIME_START",															
																		"TIME_END",
																		"WORKING_HOURBT",
																		"BWT",
																		"ET",
																		"TOTAL_NOT",
																		"TOTAL_IT",																	
																		"REMAINING_BONGKAR",
																		"REMAINING_MUAT",																	
																		"TOTAL",
																		"USH",
																		"USH_GROSS",
																		"BT",
																		"ET_BT",
																		"nama_kapal",
																		"voyage",
																		"TERMINAL"											
																		)
														VALUES('.$id_detail.',
															   '.$id_header.',
															  '.$shift.',
															  '.$activity.',
															  '.$tanggal.',															  													
															  '.$realisasi_bongkar.',
															  '.$realisasi_muat.',	
															  '.$timestart.',													
															  '.$timend.',
															  '.$bt.',
															  '.$bwt.',
															  '.$et.',
															  '.$total_not.',
														      '.$total_it.', 
															  '.$rem_bong.',
															  '.$rem_muat.',
															  '.$total.',
															  '.$ush.',
															  '.$ush_gross.',
															  '.$btp.',
															  '.$etbt.',
															  '.$nama_kapal.',
															  '.$voyage.',
															  '.$terminal.'
															  												
																								
														)';					  
					

					  $db->query($query_header);
					  $db->query($query_detail);
					
					   $db->trans_complete();

					if($db->trans_status()){
						echo "Berhasil";
						$out->success = true;
						$out->msg = 'Berhasil insert data';
					}else{
						echo "Gagal!";
						$out->success = false;
						$out->msg = 'Gagal input ke database, tidak ada data yang di update';
					}
				}else if ($activity == 'VESSEL DEPARTURE'){
					$con = $this->load->database('ikt_postgree', TRUE);
								
					$nama_kapal = post ('NAMA_KAPAL');
					$kade_dermaga = post ('KADE_DERMAGA');
					$voyage = post('VOYAGE');
			
					$rencana_bongkar = post ('RENCANA_BONGKAR');
					$rencana_muat = post ('RENCANA_MUAT');
					$shift = post('SHIFT');
					$activity = post ('ACTIVITY');				
					$realisasi_bongkar = post ('REALISASI_BONGKAR');
					$realisasi_muat = post('REALISASI_MUAT');		
			
					if ($realisasi_bongkar == ''){
						$realisasi_bongkar = '0';
					}
					if ($realisasi_muat == ''){
						$realisasi_muat = '0';
					}
		
					$total = $realisasi_bongkar + $realisasi_muat;	
							$eta = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ETA"))));	
							$etb = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ETB"))));
							$etd = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ETD"))));
							$ata = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ATA"))));
							$atb = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ATB"))));
							$atd = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ATD"))));
							$commence = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("COMMENCE"))));
							$complete = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("COMPLETE"))));
							$timend = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("TIME_END"))));
							$timestart = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("TIME_START"))));
							$tanggal = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("TANGGAL_TIME"))));
							$start = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("TIME_START"))));
				     		$end = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("TIME_END"))));
							$atbs = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ATB"))));	
			
					        $date1 = strtotime($end);
							$date2 = strtotime($start);
					
							$subTime = $date1 - $date2;
						
							$y = ($subTime/(60*60*24*365));
							$d = ($subTime/(60*60*24))%365;
							$h = ($subTime/(60*60))%24;
							$m = ($subTime/60)%60;	
													

							$bt= date('H:i',strtotime($m));

							$minutes_to_add = $m;
							$hour_to_add = $h;

							$time = new DateTime('0000');
							$time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
							$time->add(new DateInterval('PT' . $hour_to_add . 'H'));

							$bt = $time->format('Y-m-d H:i:s');
			
							if ($activity == 'BREAK'){
								$total_not = $bt;
							} else if ($activity == 'OPEN RAMPDOOR'){
								$total_not = $bt;
							} else if ($activity == 'CLOSE RAMPDOOR'){
								$total_not = $bt;
							} else {
								$total_not = '1970-01-01 0:00';
							}
		
							if ($activity == 'BAD WEATHER'){
								$total_it = $bt;
							} else if ($activity == 'ACCIDENT'){
								$total_it = $bt;
							} else if ($activity == 'INCIDENT'){
								$total_it = $bt;
							} else if ($activity == 'ENGINE TROUBLE'){
								$total_it = $bt;
							} else if ($activity == 'UNIT TROUBLE'){
								$total_it = $bt;
							} else if ($activity == 'FORCE MAJURE'){
								$total_it = $bt;
							} else if ($activity == 'EMPTY FUEL'){
								$total_it = $bt;
							} else if ($activity == 'WAITING CLEARANCE DOCS'){
								$total_it = $bt;
							} else if ($activity == 'WAITING CARGO'){
								$total_it = $bt;
							} else {
								$total_it = '1970-01-01 0:00';
							}

							$nama_kapal = "'$nama_kapal'";
							$voyage   =  "'$voyage'";
							$dataVoyage = 'SELECT count("voyage") as count FROM "DASHBOARD_BM_DETAIL" WHERE "nama_kapal"='.$nama_kapal.' and voyage = '.$voyage.'';
					
							$datas = $con->query($dataVoyage)-> row();
							$out->dataheader=$datas;
					
							$count = $datas->count;
			
							if ($count == '0' || $count == 0){
								$btp = $bt;
							} else if ($count >= '1' || $count >= 1) {
							
									$dataBt = 'SELECT max("ID_MONITORING_DETAIL") as ID, max("BT") as BT FROM "DASHBOARD_BM_DETAIL" WHERE nama_kapal = '.$nama_kapal.' and voyage = '.$voyage.'';
									$data = $con->query($dataBt)-> result_array();
									$out->data=$data;

									$bt1 = $data[0]['bt'];
									$bt2 = $bt;
							
								
									$time = date('H:i:s',strtotime($bt1));;
									$time2 = date('H:i:s',strtotime($bt2));;								
						
									$secs = strtotime($time2)-strtotime("00:00:00");
									$btp = date("Y-m-d H:i:s",strtotime($time)+$secs);	
						
							} 

							//CALCULATE BWT
							if ($count == '0' || $count == 0){
							
								$date1 = strtotime($bt);
								$date2 = strtotime($total_not);
						
								$subTime = $date1 - $date2;
							
								$y = ($subTime/(60*60*24*365));
								$d = ($subTime/(60*60*24))%365;
								$h = ($subTime/(60*60))%24;
								$m = ($subTime/60)%60;	
														
	
								$bwtt= date('H:i',strtotime($m));
	
								$minutes_to_add = $m;
								$hour_to_add = $h;
	
								$times = new DateTime('0000');
								$times->add(new DateInterval('PT' . $minutes_to_add . 'M'));
								$times->add(new DateInterval('PT' . $hour_to_add . 'H'));
	
								$bwt = $times->format('Y-m-d H:i:s');
								$bwtH = $times->format('H');
								$bwtI = $times->format('i');
								$ush_gross = (60/($bwtH*60+$bwtI))*$total;
								$ush_gross = ceil($ush_gross);
									if ($ush_gross == INF || $ush_gross == NaN || $ush_gross == NAN || $ush_gross == nan){
									$ush_gross = 0;
								}
						
							
							//CALCULATE ET						
								$date1 = strtotime($bwt);
								$date2 = strtotime($total_it);
					
								$subTime = $date1 - $date2;
							
								$y = ($subTime/(60*60*24*365));
								$d = ($subTime/(60*60*24))%365;
								$h = ($subTime/(60*60))%24;
								$m = ($subTime/60)%60;	
														

								$ett= date('H:i',strtotime($m));

								$minutes_to_add = $m;
								$hour_to_add = $h;

								$timest = new DateTime('0000');
								$timest->add(new DateInterval('PT' . $minutes_to_add . 'M'));
								$timest->add(new DateInterval('PT' . $hour_to_add . 'H'));

								$et = $timest->format('Y-m-d H:i:s');
								$etH = $timest->format('H');
								$etI = $timest->format('i');
								//USH						
								$ush = (60/($etH*60+$etI))*$total;
								$ush = ceil($ush);
								if ($ush == INF || $ush == NaN || $ush == NAN  || $ush == nan){
									$ush = 0;
								}
					
							//CALCULATE ET-BT
								$ett1 = date('H',strtotime($et));
								$ett2 = date('i',strtotime($et));												
								$ett = (60*$ett1)+$ett2;
		
								$btt1 = date('H',strtotime($btp));
								$btt2 = date('i',strtotime($btp));	
								$btt = (60*$btt1)+$btt2;
								$etbt = ($ett/$btt)*100;
								$etbt = ceil($etbt);

								if ($etbt == NAN || $etbt == NaN || $etbt == INF){
									$etbt = 0;
								}
					
			

							} else if ($count >= '1' || $count >= 1) {
						
									$dataBwt = 'SELECT max("ID_MONITORING_DETAIL") as ID, max("WORKING_HOURBT") as BT, max("BWT") as BWT, max("ET") as ET FROM "DASHBOARD_BM_DETAIL" WHERE nama_kapal = '.$nama_kapal.' and voyage = '.$voyage.'';
									$data = $con->query($dataBwt)-> result_array();
									$out->data=$data;

									$bwt1 = $data[0]['bwt'];
									$bwt2 = $bt;
							
								
									$time = date('H:i:s',strtotime($bwt1));;
									$time2 = date('H:i:s',strtotime($bwt2));;
									$not  = date('H:i:s',strtotime($total_not));;								
								
									$secs = strtotime($time2)-strtotime("00:00:00");
						
									$bwtn = date("Y-m-d H:i:s",strtotime($time)+$secs);	
							

									$date1 = strtotime($bwtn);
									$date2 = strtotime($total_not);
						
									$subTime = $date1 - $date2;
							
									$y = ($subTime/(60*60*24*365));
									$d = ($subTime/(60*60*24))%365;
									$h = ($subTime/(60*60))%24;
									$m = ($subTime/60)%60;	
														
	
									$bwtk = date('H:i',strtotime($m));
	
									$minutes_to_add = $m;
									$hour_to_add = $h;
		
									$times = new DateTime('0000');
									$times->add(new DateInterval('PT' . $minutes_to_add . 'M'));
									$times->add(new DateInterval('PT' . $hour_to_add . 'H'));
		
									$bwt = $times->format('Y-m-d H:i:s');
									$bwtH = $times->format('H');
									$bwtI = $times->format('i');
									$ush_gross = (60/($bwtH*60+$bwtI))*$total;
									$ush_gross = ceil($ush_gross);
										if ($ush_gross == INF || $ush_gross == NaN || $ush_gross == NAN || $ush_gross == nan){
										$ush_gross = 0;
									}
							
							   //CALCULATE ET
					
							   $et1 = $data[0]['et'];
							   $et2 = $bt;

							   $time1 = date('H:i:s',strtotime($et1));;
							   $time2 = date('H:i:s',strtotime($et2));;
							
					   
							   $secs = strtotime($time2)-strtotime("00:00:00");
				   
							   $tbt = date("Y-m-d H:i:s",strtotime($time1)+$secs);
						
								$dateA = strtotime($tbt);
								$dateB = strtotime($total_not);
								$dateC = strtotime($total_it);
							
								$subTime = $dateA - $dateB;
							
								$y = ($subTime/(60*60*24*365));
								$d = ($subTime/(60*60*24))%365;
								$h = ($subTime/(60*60))%24;
								$m = ($subTime/60)%60;	
							
						
								$etno = date('H:i',strtotime($m));
	
								$minutes_to_add = $m;
								$hour_to_add = $h;
	
								$timesn = new DateTime('0000');
								$timesn->add(new DateInterval('PT' . $minutes_to_add . 'M'));
								$timesn->add(new DateInterval('PT' . $hour_to_add . 'H'));
	
								$etnot = $timesn->format('Y-m-d H:i:s');
						
								$date01 = strtotime($etnot);
								$date02 = strtotime($total_it);
				
								$subTime = $date01 - $date02;
							
								$y = ($subTime/(60*60*24*365));
								$d = ($subTime/(60*60*24))%365;
								$h = ($subTime/(60*60))%24;
								$m = ($subTime/60)%60;	
														

								$etnt= date('H:i',strtotime($m));

								$minutes_to_add = $m;
								$hour_to_add = $h;

								$timest = new DateTime('0000');
								$timest->add(new DateInterval('PT' . $minutes_to_add . 'M'));
								$timest->add(new DateInterval('PT' . $hour_to_add . 'H'));

								$et = $timest->format('Y-m-d H:i:s');
								$etH = $timest->format('H');
								$etI = $timest->format('i');
								//USH						
								$ush = (60/($etH*60+$etI))*$total;
								$ush = ceil($ush);
								if ($ush == INF || $ush == NaN || $ush == NAN  || $ush == nan){
									$ush = 0;
								}
							
						
								$ett1 = date('H',strtotime($et));
								$ett2 = date('i',strtotime($et));												
								$ett = (60*$ett1)+$ett2;
		
								$btt1 = date('H',strtotime($btp));
								$btt2 = date('i',strtotime($btp));	
								$btt = (60*$btt1)+$btt2;
								$etbt = ($ett/$btt)*100;
								$etbt = ceil($etbt);
	
								if ($etbt == NAN || $etbt == NaN || $etbt == INF){
									$etbt = 0;
								}
					
							} 
									
						$kade_dermaga   =  "'$kade_dermaga'";			
						$pbm   =  "'$pbm'";
						$rencana_bongkar   =  "'$rencana_bongkar'";
						$rencana_muat   =  "'$rencana_muat'";
						$eta   =  "'$eta'";
						$etb   =  "'$etb'";
						$etd   =  "'$etd'";
						$ata   =  "'$ata'";
						$atb   =  "'$atb'";
						$atd   =  "'$atd'";
						$commence   =  "'$commence'";
						$complete   =  "'$complete'";

						$date = "'DD-MM-YYYY HH24:MI:SS'";
						$second = "'HH24:MI:SS'";

				
						$dataIdheader = 'SELECT max("ID_HEADER") FROM "DASHBOARD_BM_HEADER"';
						$data = $con->query($dataIdheader)-> row();
						$out->dataheader=$data;
						$array = json_encode($data);
						$x = json_decode($array);
						$y = $x->max;
						$header= $y+1;
		
						$id_monitoring_header   =  "'$header'";	
						$id_header   =  "'$header'";	
	
					$query_header = 'INSERT INTO "DASHBOARD_BM_HEADER"("ID_HEADER",
															  "NAMA_KAPAL",	
															  "KADE_DERMAGA",														
															  "VOYAGE",													
															  "RENCANA_BONGKAR",
															  "RENCANA_MUAT",
															  "ETA",
															  "ETB",
															  "ETD",
															  "ATA",
															  "ATB",
															  "ATD",
															  "COMMENCE",
															  "COMPLETE",
															  "ID_MONITORING_HEADER"
															  )
														VALUES( '.$id_header.',
																'.$nama_kapal.',
																'.$kade_dermaga.',															
																'.$voyage.',														
																'.$rencana_bongkar.',
																'.$rencana_muat.',
																'.$eta.',
																'.$etb.',
																'.$etd.',
																'.$ata.',
																'.$atb.', 
																'.$atd.', 
																'.$commence.',
																'.$complete.',
																'.$id_header.'
																)';
								
							$shift   =  "'$shift'";
							$activity   =  "'$activity'";
							$tanggal   =  "'$tanggal'";
							$realisasi_bongkar   =  "'$realisasi_bongkar'";
							$realisasi_muat   =  "'$realisasi_muat'";
							$timestart  =  "'$timestart'";
							$timend   =  "'$timend'";
							$bt   =  "'$bt'";
							$bwt   =  "'$bwt'";
							$et   =  "'$et'";				
							$btp   =  "'$btp'";
							$etbt   =  "'$etbt'";
							$total_not   =  "'$total_not'";
							$total_it   =  "'$total_it'";
						
							$total  =  "'$total'";
							
							$dataIddetail = 'SELECT max("ID_MONITORING_DETAIL") FROM "DASHBOARD_BM_DETAIL"';
							$data = $con->query($dataIddetail)-> row();
							$out->datadetail=$data;
							$array = json_encode($data);
							$x = json_decode($array);
							$y = $x->max;
							$detail= $y+1;
				
							//TOTAL REMAINING
							$dataVoyage = 'SELECT count("voyage") as count FROM "DASHBOARD_BM_DETAIL" WHERE "nama_kapal"='.$nama_kapal.' and voyage = '.$voyage.'';
					
							$data = $con->query($dataVoyage)-> row();
							$out->dataheader=$data;
					
							$count = $data->count;
							if ($count == '0' || $count == 0){
								$rem_bong = $rencana_bongkar;
								$rem_muat = $rencana_muat;
							
							} else if ($count >= '1' || $count >= 1) {						
									$hasis 	= 0;
									$dataRemaining 	= 'SELECT max("ID_MONITORING_DETAIL") as ID, max("REMAINING_BONGKAR") as RB, max("REMAINING_MUAT") as RM FROM "DASHBOARD_BM_DETAIL" WHERE nama_kapal = '.$nama_kapal.' and voyage = '.$voyage.'';
									$data 	= $con->query($dataRemaining)-> result_array();
									$out->data=$data;

									
									$remainaingBongkar = intval($data[0]['rb']);
									$inputRealisasi    = (post ('REALISASI_BONGKAR') ? post ('REALISASI_BONGKAR'):0);
									$rem_bong 		   = $remainaingBongkar - $inputRealisasi;

									$realisasiMuat    = (post ('REALISASI_MUAT') ? post ('REALISASI_MUAT'):0);
									$RM = $data[0]['rm'];
									$R3 = (int)$RM;
									$R4 = (int)$realisasiMuat;
									$rem_muat = ($R3-$R4);
						
							} 
							$terminal = 'INTERNASIONAL';
							$id_detail   =  "'$detail'";
							$terminal   =  "'$terminal'";	
						$query_detail = 'INSERT INTO "DASHBOARD_BM_DETAIL"("ID_MONITORING_DETAIL",
																		"ID_MONITORING_HEADER",
																		"SHIFT",
																		"ACTIVITY",
																		"TANGGAL_TIME",																																	
																		"REALISASI_BONGKAR",
																		"REALISASI_MUAT",		
																		"TIME_START",															
																		"TIME_END",
																		"WORKING_HOURBT",
																		"BWT",
																		"ET",
																		"TOTAL_NOT",
																		"TOTAL_IT",																	
																		"REMAINING_BONGKAR",
																		"REMAINING_MUAT",																	
																		"TOTAL",
																		"USH",
																		"USH_GROSS",
																		"BT",
																		"ET_BT",
																		"nama_kapal",
																		"voyage",
																		"TERMINAL"											
																		)
														VALUES('.$id_detail.',
															   '.$id_header.',
															  '.$shift.',
															  '.$activity.',
															  '.$tanggal.',															  													
															  '.$realisasi_bongkar.',
															  '.$realisasi_muat.',	
															  '.$timestart.',													
															  '.$timend.',
															  '.$bt.',
															  '.$bwt.',
															  '.$et.',
															  '.$total_not.',
														      '.$total_it.', 
															  '.$rem_bong.',
															  '.$rem_muat.',
															  '.$total.',
															  '.$ush.',
															  '.$ush_gross.',
															  '.$btp.',
															  '.$etbt.',
															  '.$nama_kapal.',
															  '.$voyage.',
															  '.$terminal.'
															  												
																								
														)';					  
							echo $etbt;
							$lndn = 'INTERNASIONAL';
							$kpir = 'REAL';
							$source = 'CARTOS';
							$lndn   =  "'$lndn'";
							$kpir   =  "'$kpir'";	
							$source  =  "'$source'";

							$query_etbt = 'INSERT INTO "MART_ETBT"("PERIODE",
							"ET_BT",
							"LN_DN",
							"KPI_REAL",
							"INSERT_DATE",
							"SOURCE"
						)
						VALUES(
						'.$atd.',
						'.$etbt.',
						'.$lndn.',
						'.$kpir.',
						'.$tanggal.',
						'.$source.'
						)';	

						$source = 'CARTOS';
						$source  =  "'$source'";

						$query_bor = 'INSERT INTO "MART_BOR"("PERIODE",
						"NM_KAPAL",
						"VOYAGE",
						"SANDAR",
						"TOLAK",
						"KADE",
						"BT",
						"INSERT_DATE",
						"SOURCE"
					)
					VALUES(
					'.$atd.',
					'.$nama_kapal.',
					'.$voyage.',
					'.$atb.',
					'.$atd.',
					'.$kade_dermaga.',
					'.$btp.',
					'.$tanggal.',
					'.$source.'
					)';	

					
					$terminal = 'INTERNASIONAL';
					$kpi = 'REAL';
					$source = 'CARTOS';
					$terminal   =  "'$terminal'";			
					$source  =  "'$source'";
					$kpi  =  "'$kpi'";

					$query_ush = 'INSERT INTO "MART_USH"("PERIODE",				
					"TERMINAL",
					"SOURCE",
					"INSERT_DATE",
					"USH",
					"KPI_REAL"
				 )
				 VALUES(
				 '.$atd.',			
				 '.$terminal.',
				 '.$source.',
				 '.$tanggal.',
				 '.$ush.',
				 '.$kpi.'
				 )';

					  $db->query($query_header);
					  $db->query($query_detail);
					  $db->query($query_etbt);		
					  $db->query($query_bor);  
					  $db->query($query_ush);  

					   $db->trans_complete();
					
					if($db->trans_status()){
						echo "Berhasil";
						$out->success = true;
						$out->msg = 'Berhasil insert data';
					}else{
						echo "Gagal!";
						$out->success = false;
						$out->msg = 'Gagal input ke database, tidak ada data yang di update';
					}


				}
				}else{
					echo 'validasi form error';
					$out->success = false;
					$out->msg = 'val err';
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
       
                if(isset($_REQUEST)){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;		

				$val->set_rules('RENCANA_BONGKAR', 'RENCANA BONGKAR', 'required');
				$val->set_rules('RENCANA_MUAT', 'RENCANA MUAT', 'required');
		
				if($val->run()){
			
					$db = $this->get_db();
					$con = $this->load->database('ikt_postgree', TRUE);
					
					$nama_kapal = post ('NAMA_KAPAL');
					$kade_dermaga = post ('KADE_DERMAGA');
					$voyage = post('VOYAGE');
					$pbm = post ('PBM');
					$rencana_bongkar = post ('RENCANA_BONGKAR');
					$rencana_muat = post ('RENCANA_MUAT');
					$shift = post('SHIFT');
					$activity = post ('ACTIVITY');				
					$realisasi_bongkar = post ('REALISASI_BONGKAR');
					$realisasi_muat = post ('REALISASI_MUAT');			
					$tot_realisasi = $realisasi_bongkar+$realisasi_muat;
					$remaining_bongkar = $rencana_bongkar;
					$remaining_muat = $rencana_muat;
					$id_mondet = post ('ID_MONITORING_DETAIL');
					
					$eta = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ETA"))));	
					$etb = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ETB"))));
					$etd = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ETD"))));
					$ata = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ATA"))));
					$atb = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ATB"))));
					$atd = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ATD"))));
					$commence = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("COMMENCE"))));
					$complete = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("COMPLETE"))));
					$timend = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("TIME_END"))));
					$timestart = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("TIME_START"))));
					$tanggal = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("TANGGAL_TIME"))));
					$start = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("TIME_START"))));
					 $end = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("TIME_END"))));
					$atbs = date('Y-m-d H:i:s',strtotime(str_replace('/','-',post("ATB"))));
				
					$bttt = date('H:i:s',strtotime(str_replace('/','-',post("WORKING_HOURBT"))));
			
					        $date1 = strtotime($end);
							$date2 = strtotime($start);
					
							$subTime = $date1 - $date2;
						
							$y = ($subTime/(60*60*24*365));
							$d = ($subTime/(60*60*24))%365;
							$h = ($subTime/(60*60))%24;
							$m = ($subTime/60)%60;	
													

							$bt= date('H:i',strtotime($m));

							$minutes_to_add = $m;
							$hour_to_add = $h;

							$time = new DateTime('0000');
							$time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
							$time->add(new DateInterval('PT' . $hour_to_add . 'H'));

							$bt = $time->format('Y-m-d H:i:s');
				
											if ($activity == 'BREAK'){
												$total_not = $bt;
											} else if ($activity == 'OPEN RAMPDOOR'){
												$total_not = $bt;
											} else if ($activity == 'CLOSE RAMPDOOR'){
												$total_not = $bt;
											} else {
												$total_not = '1970-01-01 0:00';
											}
						
											if ($activity == 'BAD WEATHER'){
												$total_it = $bt;
											} else if ($activity == 'ACCIDENT'){
												$total_it = $bt;
											} else if ($activity == 'INCIDENT'){
												$total_it = $bt;
											} else if ($activity == 'ENGINE TROUBLE'){
												$total_it = $bt;
											} else if ($activity == 'UNIT TROUBLE'){
												$total_it = $bt;
											} else if ($activity == 'FORCE MAJURE'){
												$total_it = $bt;
											} else if ($activity == 'EMPTY FUEL'){
												$total_it = $bt;
											} else if ($activity == 'WAITING CLEARANCE DOCS'){
												$total_it = $bt;
											} else if ($activity == 'WAITING CARGO'){
												$total_it = $bt;
											} else {
												$total_it = '1970-01-01 0:00';
											}
			
												$nama_kapal = "'$nama_kapal'";
												$voyage   =  "'$voyage'";
												$dataVoyage = 'SELECT count("voyage") as count FROM "DASHBOARD_BM_DETAIL" WHERE "nama_kapal"='.$nama_kapal.' and voyage = '.$voyage.'';
																						
												$datas = $con->query($dataVoyage)-> row();
												$out->dataheader=$datas;
										
												$count = $datas->count;
									
												if ($count == '0' || $count == 0){
													$btp = $bt;
												} else if ($count >= '1' || $count >= 1) {
												
														$id_mondet = post ('ID_MONITORING_DETAIL');
														$id_mondet = $id_mondet-1;
														$id_mondet   =  "'$id_mondet'";
														$dataBt = 'SELECT "BT" as bt FROM  "DASHBOARD_BM_DETAIL" 
														    WHERE "nama_kapal"='.$nama_kapal.' and voyage = '.$voyage.'and "ID_MONITORING_DETAIL" = '.$id_mondet.'
															order by "ID_MONITORING_DETAIL" asc
														';
														$data = $con->query($dataBt)-> result_array();
														$out->data=$data;
					
														if ($data[0]['bt']){
															$bt1 = $data[0]['bt'];
														} 
														
														if (empty($data[0]['bt'])) {
															$bt1 = '1970-01-01 00:00:00';
														}
														$bt2 = $bt;
												
													
														$time = date('H:i:s',strtotime($bt1));;
														$time2 = date('H:i:s',strtotime($bt2));;								
													
														$secs = strtotime($time2)-strtotime("00:00:00");
														$btp = date("Y-m-d H:i:s",strtotime($time)+$secs);	
										
												} 
					
												//CALCULATE BWT
												if ($count == '0' || $count == 0){
												
													$date1 = strtotime($bt);
													$date2 = strtotime($total_not);
											
													$subTime = $date1 - $date2;
												
													$y = ($subTime/(60*60*24*365));
													$d = ($subTime/(60*60*24))%365;
													$h = ($subTime/(60*60))%24;
													$m = ($subTime/60)%60;	
																			
						
													$bwtt= date('H:i',strtotime($m));
						
													$minutes_to_add = $m;
													$hour_to_add = $h;
						
													$times = new DateTime('0000');
													$times->add(new DateInterval('PT' . $minutes_to_add . 'M'));
													$times->add(new DateInterval('PT' . $hour_to_add . 'H'));
						
													$bwt = $times->format('Y-m-d H:i:s');
																			
													$bwtH = $times->format('H');
													$bwtI = $times->format('i');
													$ush_gross = (60/($bwtH*60+$bwtI))*$tot_realisasi;
													if ($ush_gross == INF || $ush_gross == NaN || $ush_gross == NAN ){
														$ush_gross = 0;
													}
													$ush_gross = ceil($ush_gross);
												
												//CALCULATE ET
										
													$date1 = strtotime($bwt);
													$date2 = strtotime($total_it);
												
													$subTime = $date1 - $date2;
												
													$y = ($subTime/(60*60*24*365));
													$d = ($subTime/(60*60*24))%365;
													$h = ($subTime/(60*60))%24;
													$m = ($subTime/60)%60;	
																			
					
													$ett= date('H:i',strtotime($m));
					
													$minutes_to_add = $m;
													$hour_to_add = $h;
					
													$timest = new DateTime('0000');
													$timest->add(new DateInterval('PT' . $minutes_to_add . 'M'));
													$timest->add(new DateInterval('PT' . $hour_to_add . 'H'));
					
													$et = $timest->format('Y-m-d H:i:s');
												
													$etH = $timest->format('H');
													$etI = $timest->format('i');
													//USH						
													$ush = (60/($etH*60+$etI))*$tot_realisasi;
													if ($ush == INF || $ush == NaN || $ush == NAN){
														$ush = 0;
													}
													$ush = ceil($ush);
												
												//CALCULATE ET-BT
													$ett1 = date('H',strtotime($et));
													$ett2 = date('i',strtotime($et));												
													$ett = (60*$ett1)+$ett2;

													$btt1 = date('H',strtotime($btp));
													$btt2 = date('i',strtotime($btp));	
													$btt = (60*$btt1)+$btt2;

													$etbt = ($ett/$btt)*100;
													$etbt = ceil($etbt);
					
													if ($etbt == NAN || $etbt == NaN || $etbt == INF){
														$etbt = 0;
													}
										
												} else if ($count >= '1' || $count >= 1) {
											
														$id_mondet = post ('ID_MONITORING_DETAIL');
														$id_mondet = $id_mondet-1;
														$id_mondet   =  "'$id_mondet'";
														$dataBwt = 'SELECT "ID_MONITORING_DETAIL" as ID, "WORKING_HOURBT" as BT, "BWT" as BWT, "ET" as ET FROM "DASHBOARD_BM_DETAIL" 
														WHERE nama_kapal = '.$nama_kapal.' and voyage = '.$voyage.'
														and "ID_MONITORING_DETAIL" = '.$id_mondet.'
														order by "ID_MONITORING_DETAIL" asc
														';
														$data = $con->query($dataBwt)-> result_array();
														$out->data=$data;
						
														if ($data[0]['bwt']){
															$bwt1 = $data[0]['bwt'];
														} 
														
														if (empty($data[0]['bwt'])) {
															$bwt1 = "1970-01-01 00:00:00";
														}
												
														$bwt2 = $bt;
												
													
														$time = date('H:i:s',strtotime($bwt1));;
														$time2 = date('H:i:s',strtotime($bwt2));;
														$not  = date('H:i:s',strtotime($total_not));;								
											
														$secs = strtotime($time2)-strtotime("00:00:00");
											
														$bwtn = date("Y-m-d H:i:s",strtotime($time)+$secs);	
										
					
														$date1 = strtotime($bwtn);
														$date2 = strtotime($total_not);
											
														$subTime = $date1 - $date2;
												
														$y = ($subTime/(60*60*24*365));
														$d = ($subTime/(60*60*24))%365;
														$h = ($subTime/(60*60))%24;
														$m = ($subTime/60)%60;	
																			
						
														$bwtk = date('H:i',strtotime($m));
						
														$minutes_to_add = $m;
														$hour_to_add = $h;
							
														$times = new DateTime('0000');
														$times->add(new DateInterval('PT' . $minutes_to_add . 'M'));
														$times->add(new DateInterval('PT' . $hour_to_add . 'H'));
							
														$bwt = $times->format('Y-m-d H:i:s');
											
														$bwtH = $times->format('H');
														$bwtI = $times->format('i');
														$ush_gross = (60/($bwtH*60+$bwtI))*$tot_realisasi;
														if ($ush_gross == INF || $ush_gross == NaN || $ush_gross == NAN ){
															$ush_gross = 0;
														}
														$ush_gross = ceil($ush_gross);
												   //CALCULATE ET
												    if ($data[0]['et']){
														$et1 = $data[0]['et'];
													} 
												
													if (empty($data[0]['et'])) {
														$et1 = "1970-01-01 00:00:00";
													}
											
												   $et2 = $bt;
					
												   $time1 = date('H:i:s',strtotime($et1));;
												   $time2 = date('H:i:s',strtotime($et2));;
										
										   
												   $secs = strtotime($time2)-strtotime("00:00:00");
									   
												   $tbt = date("Y-m-d H:i:s",strtotime($time1)+$secs);
											
													$dateA = strtotime($tbt);
													$dateB = strtotime($total_not);
													$dateC = strtotime($total_it);
										
													$subTime = $dateA - $dateB;
												
													$y = ($subTime/(60*60*24*365));
													$d = ($subTime/(60*60*24))%365;
													$h = ($subTime/(60*60))%24;
													$m = ($subTime/60)%60;	
												
											
													$etno = date('H:i',strtotime($m));
						
													$minutes_to_add = $m;
													$hour_to_add = $h;
						
													$timesn = new DateTime('0000');
													$timesn->add(new DateInterval('PT' . $minutes_to_add . 'M'));
													$timesn->add(new DateInterval('PT' . $hour_to_add . 'H'));
						
													$etnot = $timesn->format('Y-m-d H:i:s');
											
													$date01 = strtotime($etnot);
													$date02 = strtotime($total_it);
												
													$subTime = $date01 - $date02;
												
													$y = ($subTime/(60*60*24*365));
													$d = ($subTime/(60*60*24))%365;
													$h = ($subTime/(60*60))%24;
													$m = ($subTime/60)%60;	
																			
					
													$etnt= date('H:i',strtotime($m));
					
													$minutes_to_add = $m;
													$hour_to_add = $h;
					
													$timest = new DateTime('0000');
													$timest->add(new DateInterval('PT' . $minutes_to_add . 'M'));
													$timest->add(new DateInterval('PT' . $hour_to_add . 'H'));

													$et = $timest->format('Y-m-d H:i:s');																						
													$etH = $timest->format('H');
													$etI = $timest->format('i');
													//USH						
													$ush = (60/($etH*60+$etI))*$tot_realisasi;
													if ($ush == INF || $ush == NaN || $ush == NAN){
														$ush = 0;
													}
													$ush = ceil($ush);
											
													$ett1 = date('H',strtotime($et));
													$ett2 = date('i',strtotime($et));												
													$ett = (60*$ett1)+$ett2;

													$btt1 = date('H',strtotime($btp));
													$btt2 = date('i',strtotime($btp));	
													$btt = (60*$btt1)+$btt2;
													$etbt = ($ett/$btt)*100;
													$etbt = ceil($etbt);

													if ($etbt == NAN || $etbt == NaN || $etbt == INF){
														$etbt = 0;
													}
				
									//TOTAL REMAINING
									$dataVoyage = 'SELECT count("voyage") as count FROM "DASHBOARD_BM_DETAIL" WHERE "nama_kapal"='.$nama_kapal.' and voyage = '.$voyage.'';
					
									$data = $con->query($dataVoyage)-> row();
									$out->dataheader=$data;
							
									$count = $data->count;
									if ($count == '0' || $count == 0){
										$rem_bong = $rencana_bongkar;
										$rem_muat = $rencana_muat;
									
									} else if ($count >= '1' || $count >= 1) {
										
											$hasis 	= 0;
											$id_mondet = post ('ID_MONITORING_DETAIL');
											$id_mondet = $id_mondet-1;
											$id_mondet   =  "'$id_mondet'";							
											$dataRemaining 	= 'SELECT max("ID_MONITORING_DETAIL") as ID, max("REMAINING_BONGKAR") as RB, max("REMAINING_MUAT") as RM FROM "DASHBOARD_BM_DETAIL" WHERE nama_kapal = '.$nama_kapal.' and voyage = '.$voyage.'';
									
								
											$data 	= $con->query($dataRemaining)-> result_array();
											$out->data=$data;							
	
									
											$remainaingBongkar = intval($data[0]['rb']);
											$RM = $data[0]['rm'];							
								
										
											$inputRealisasi    = (post ('REALISASI_BONGKAR') ? post ('REALISASI_BONGKAR'):0);
											$rem_bong 		   = $remainaingBongkar - $inputRealisasi;		
											$realisasiMuat    = (post ('REALISASI_MUAT') ? post ('REALISASI_MUAT'):0);										
											$R3 = (int)$RM;
											$R4 = (int)$realisasiMuat;
											$rem_muat = ($R3-$R4);
								
									} 
								
						$kade_dermaga   =  "'$kade_dermaga'";					
						$pbm   =  "'$pbm'";
						$rencana_bongkar   =  "'$rencana_bongkar'";
						$rencana_muat   =  "'$rencana_muat'";
						$eta   =  "'$eta'";
						$etb   =  "'$etb'";
						$etd   =  "'$etd'";
						$ata   =  "'$ata'";
						$atb   =  "'$atb'";
						$atd   =  "'$atd'";
						$commence   =  "'$commence'";
						$complete   =  "'$complete'";
						$shift   =  "'$shift'";
						$activity   =  "'$activity'";
						$tanggal   =  "'$tanggal'";
						$realisasi_bongkar   =  "'$realisasi_bongkar'";
						$realisasi_muat   =  "'$realisasi_muat'";			
						$timestart  =  "'$timestart'";
						$timend   =  "'$timend'";
						$bt   =  "'$bt'";
						$bwt   =  "'$bwt'";
						$et   =  "'$et'";
						$etbt   =  "'$etbt'";
						$btp   =  "'$btp'";
						$total_not   =  "'$total_not'";
						$total_it   =  "'$total_it'";
						$tot_realisasi = "'$tot_realisasi'";
						$ush  =  "'$ush'";
						$ush_gross  =  "'$ush_gross'";
				
						$id_header = post ('ID_HEADER');
						$id_monitoring_detail = post ('ID_MONITORING_DETAIL');

						$query_header = 'UPDATE "DASHBOARD_BM_HEADER" SET 									
														      "RENCANA_BONGKAR" ='.$rencana_bongkar.',
															  "RENCANA_MUAT" = '.$rencana_muat.',
															  "ETA" = '.$eta.',
															  "ETB" = '.$etb.', 
															  "ETD" = '.$etd.', 
															  "ATA" = '.$ata.', 
															  "ATB" = '.$atb.', 
															  "ATD" = '.$atd.', 
															  "COMMENCE" = '.$commence.',
															  "COMPLETE" = '.$complete.'						
										WHERE "ID_HEADER" = '.$id_header.' ';
				
							
						$query_detail = 'UPDATE "DASHBOARD_BM_DETAIL" SET 									
																		"SHIFT" = '.$shift.',
																		"ACTIVITY" = '.$activity.',																		
																		"REALISASI_BONGKAR" = '.$realisasi_bongkar.',
																		"REALISASI_MUAT" = '.$realisasi_muat.',	
																		"REMAINING_BONGKAR" = '.$rem_bong.',
																		"REMAINING_MUAT" = '.$rem_muat.',	
																		"TIME_START" =  '.$timestart.',
																		"TIME_END" = '.$timend.',			
																		"WORKING_HOURBT" = '.$bt.',
																		"ET" = '.$et.',
																		"BWT" = '.$bwt.',
																		"BT" = '.$btp.',
																		"ET_BT" = '.$etbt.',
																		"TOTAL_NOT" = '.$total_not.',
																		"TOTAL_IT" = 	'.$total_it.',						
																		"TOTAL" ='.$tot_realisasi.',
																		"USH" = '.$ush.',
																		"USH_GROSS" = '.$ush_gross.'					
							      			
							             WHERE "ID_MONITORING_DETAIL"='.$id_monitoring_detail.' ';
					
					  $db->query($query_header);
					  $db->query($query_detail);

					
					   $db->trans_complete();

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
	public function simpann($token){
		if($this->auth->token == $token){
			$out = new StdClass();       
        
                if(isset($_REQUEST)){
				$this->load->library('form_validation');
				
				$val = $this->form_validation;
				$val->set_rules('NAMA_KAPAL', 'Nama Kapal', 'required');
				$val->set_rules('PBM', 'PBM', 'required');
        
				if($val->run()){
					$db = $this->get_db();
        
					$id_monitoring_detail = post ('ID_MONITORING_DETAIL');
					$id_monitoring_header = post ('ID_MONITORING_HEADER');
					$tanggal_time = post ('TANGGAL_TIME');
					$shift = post('SHIFT');
					$rencana_bongkar = post ('RENCANA_BONGKAR');
					$rencana_muat = post ('RENCANA_MUAT');
					$activity = post ('ACTIVITY');
					$timend = post('TIMEND');
					$bongkar = post ('REALISASI_BONGKAR');
					$muat = post('REALISASI_MUAT');        
					$bongkar_report = post('REALISASI_BONGKAR_REPORT');  
					$muat_report = post('REALISASI_MUAT_REPORT');  
					$remaining_bongkar = post('REMAINING_BONGKAR');  
					$remaining_muat = post('REMAINING_MUAT');                 
                         
					$insrt = array(				
								'ID_MONITORING_DETAIL' => 	$id_monitoring_detail,
								'ID_MONITORING_HEADER' => $id_monitoring_header,
								'TANGGAL_TIME' => $tanggal_time,
							    'SHIFT' => $shift,
								'RENCANA_BONGKAR' => $rencana_bongkar,
								'RENCANA_MUAT' => $rencana_muat,					
								'ACTIVITY' => $activity,
								'TIMEND' => $timend,
								'REALISASI_BONGKAR' => $bongkar,
								'REALISASI_muat' => $muat,
								'REALISASI_BONGKAR' => $bongkar,
								'REALISASI_BONGKAR_REPORT' => $bongkar_report,
								'REALISASI_MUAT_REPORT' => 	$muat_report,
								'REMAINING_BONGKAR' => $remaining_bongkar,
								'REMAINING_MUAT' => $remaining_muat												
							 );
					$db->insert('DASHBOARD_BM_DETAIL', $insrt);
					
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
					$out->msg = validation_error();
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