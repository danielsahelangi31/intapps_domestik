<?php
class Import_sppb extends CI_Controller{
	private $local_db;
	
	public function __construct(){
		parent::__construct();
		
		// Dapatkan data login
		if(!$this->auth = $this->userauth->getLoginData()){
			redirect(LOGIN_PAGE);
		}
	}
	
	public function index(){

		$db = $this->get_db();
		$kunjungan_kapal = model('tps_online/kunjungan_kapal_model');
		$kunjungan_kapal->set_db($db);
		$visitID  = $kunjungan_kapal->select_ds(array('FLAG_SEND' => 0));
		$doc_type = $kunjungan_kapal->get_doc_type();
		
		foreach ($visitID as $row) {
			$dataAuto[] = $row->VISIT_ID.' '.$row->VISIT_NAME;
		}
		
		
		$data = array(
			"visitAuto" => $dataAuto,
			"doc_type"  => $doc_type

		);
		$this->load->view('backend/pages/tps_online/SPPB/manual_import_sppb',$data);
	}

	private function get_db(){
		if(!$this->local_db){
			$this->local_db = $this->load->database(ILCS_TPS_ONLINE, TRUE);
			$this->local_db->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
			$this->local_db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
		}
		
		return $this->local_db;
	}
	

	public function recall(){
				
		$auth = $this->auth;
		$ui_messages = array();
		$out = new StdClass();
		$mod_transaksi = model('api/import_model');
		
			
			$param = Array ( 
	            'UserName' => 'CART', 
	            'Password' => 'CARTERMINAL',
	            'No_Sppb'  => $this->input->post('No_Sppb'),  // '622869/KPU.01/2019', 
	            'Tgl_Sppb' => str_replace('-','',$this->input->post('Tgl_Sppb')) , //'31220019', 
	            'NPWP_Imp' => str_replace('.','',str_replace('-','',$this->input->post('NPWP_Imp'))) // '024144388056000'
	 		);

		 		try { 

		 			// http://10.8.1.91:8320/svcTPSOline/Services/SPPBWeb-service3.serviceagent/PortTypeEndpoint3
				    
				    $client = new SoapClient("http://10.8.1.91:8320/svcTPSOline/Services/SPPBWeb-service3.serviceagent/PortTypeEndpoint3", array(
				       					'location' => "http://10.8.1.91:8320/svcTPSOline/Services/SPPBWeb-service3.serviceagent/PortTypeEndpoint3",
						                'uri'      => "/svcTPSOline/Services/SPPBWeb-service3.serviceagent/PortTypeEndpoint3",
						                'style'    => SOAP_DOCUMENT,
						                'use'      => SOAP_LITERAL));


				    $results = $client->op_sppbondemandbc20($param);

						$hasil = array(
							'result'=> $results
					   	);
					   	
		  				 echo json_encode($hasil);

				} catch (Exception $e) { 
				        echo "<h2>Exception Error!</h2>"; 
				        echo $e->getMessage(); 
				}		
	}

	public function view_doc23()
	{
		$this->load->view('backend/pages/tps_online/SPPB/manual_import_sppb_doc23');
	}

	public function dokumen_bc23()
	{

		$auth = $this->auth;
		$ui_messages = array();
		$out = new StdClass();
		$this->load->library('form_validation');

		$mod_transaksi = model('api/import_model');

	
			$param = Array ( 
	            'UserName' => 'CART', 
	            'Password' => 'CARTERMINAL',
	            'No_Sppb'  => $this->input->post('No_Sppb'),  // '622869/KPU.01/2019', 
	            'Tgl_Sppb' => str_replace('-','',$this->input->post('Tgl_Sppb')) , //'31220019', 
	            'NPWP_Imp' => str_replace('.','',str_replace('-','',$this->input->post('NPWP_Imp'))) // '024144388056000'
	 		);


				try { 
	    
				    $client = new SoapClient("http://10.8.1.91:8320/svcTPSOline/Services/SPPBWeb-service3.serviceagent/PortTypeEndpoint3", array(
				       					'location' => "http://10.8.1.91:8320/svcTPSOline/Services/SPPBWeb-service3.serviceagent/PortTypeEndpoint3",
						                'uri'      => "/svcTPSOline/Services/SPPBWeb-service3.serviceagent/PortTypeEndpoint3",
						                'style'    => SOAP_DOCUMENT,
						                'use'      => SOAP_LITERAL));


				    $results = $client->op_sppbondemandbc23($param);
					
					$hasil = array(
						'result'=> $results
				   	);
	  				 echo json_encode($hasil);

			 

				} catch (Exception $e) { 
				        echo "<h2>Exception Error!</h2>"; 
				        echo $e->getMessage(); 
				}
	

							
	}	

	public function view_spjm()
	{
		$this->load->view('backend/pages/tps_online/SPPB/manual_import_sppb_spjm');
	}

	public function dokumen_bc_spjm()
	{
		$auth = $this->auth;
		$ui_messages = array();
		$out = new StdClass();
		$mod_transaksi = model('api/import_model');


			$param = Array ( 
	            'UserName' => 'CART', 
	            'Password' => 'CARTERMINAL',
	            'NoPib'  => $this->input->post('NoPib'),  // '622869/KPU.01/2019', 
	            'tglPib' => str_replace('-','',$this->input->post('tglPib'))  //'31220019', 
	            // 'DocType' => str_replace('.','',str_replace('-','',$this->input->post('DocType'))) // '024144388056000'
	 		);


			try { 

			    
			    $client = new SoapClient("http://10.8.1.91:8320/svcTPSOline/Services/SPPBWeb-service3.serviceagent/PortTypeEndpoint3", array(
			       					'location' => "http://10.8.1.91:8320/svcTPSOline/Services/SPPBWeb-service3.serviceagent/PortTypeEndpoint3",
					                'uri'      => "/svcTPSOline/Services/SPPBWeb-service3.serviceagent/PortTypeEndpoint3",
					                'style'    => SOAP_DOCUMENT,
					                'use'      => SOAP_LITERAL));


			    $results = $client->op_sppbondemandspjm($param);

				
					
					$hasil = array(
						'result'=> $results
				   	);
	  				 echo json_encode($hasil);

			 

			} catch (Exception $e) { 
			        echo "<h2>Exception Error!</h2>"; 
			        echo $e->getMessage(); 
			}
		
	}

	public function pabean()
	{
		
		$auth = $this->auth;
		$ui_messages = array();
		$out = new StdClass();
		$mod_transaksi = model('api/import_model');


			$param = Array ( 
	            'UserName' => 'CART', 
	            'Password' => 'CARTERMINAL',
	            'KdDok'  => $this->input->post('KdDok'),  // '622869/KPU.01/2019', 
	            'NoDok'  => $this->input->post('NoDok'),
	            'TglDok' => str_replace('-','',$this->input->post('TglDok'))  //'31220019', 
	            // 'DocType' => str_replace('.','',str_replace('-','',$this->input->post('DocType'))) // '024144388056000'
	 		);


			try { 

			    
			    $client = new SoapClient("http://10.8.1.91:8320/svcTPSOline/Services/SPPBWeb-service3.serviceagent/PortTypeEndpoint3", array(
			       					'location' => "http://10.8.1.91:8320/svcTPSOline/Services/SPPBWeb-service3.serviceagent/PortTypeEndpoint3",
					                'uri'      => "/svcTPSOline/Services/SPPBWeb-service3.serviceagent/PortTypeEndpoint3",
					                'style'    => SOAP_DOCUMENT,
					                'use'      => SOAP_LITERAL));

			    // echo "<pre>";
			    // print_r($param);
			    // print_r($client);die();


			    $results = $client->op_sppbondemandpabean($param);

				// print_r($results);die();
					
					$hasil = array(
						'result'=> $results
				   	);
	  				 echo json_encode($hasil);

			 

			} catch (Exception $e) { 
			        echo "<h2>Exception Error!</h2>"; 
			        echo $e->getMessage(); 
			}
	}

	public function dokumen_manual()
	{
		$auth = $this->auth;
		$ui_messages = array();
		$out = new StdClass();
		$mod_transaksi = model('api/import_model');


			$param = Array ( 
	            'UserName' => 'CART', 
	            'Password' => 'CARTERMINAL',
   	            'KodeDok'  => $this->input->post('pilih_dokumen_manual'),  // '622869/KPU.01/2019', 
	            'NoDok'  => $this->input->post('NoDok_manual'),
	            'TglDok' => str_replace('-','',$this->input->post('TglDok_manual'))  //'31220019', 
	            // 'DocType' => str_replace('.','',str_replace('-','',$this->input->post('DocType'))) // '024144388056000'
	 		);


			try { 


			    $client = new SoapClient("http://10.8.1.91:8320/svcTPSOline/Services/SPPBWeb-service3.serviceagent/PortTypeEndpoint3", array(
			       					'location' => "http://10.8.1.91:8320/svcTPSOline/Services/SPPBWeb-service3.serviceagent/PortTypeEndpoint3",
					                'uri'      => "/svcTPSOline/Services/SPPBWeb-service3.serviceagent/PortTypeEndpoint3",
					                'style'    => SOAP_DOCUMENT,
					                'use'      => SOAP_LITERAL
					            )
				);


			    $results = $client->op_sppbmanual($param);

				
					
					$hasil = array(
						'result'=> $results
				   	);
	  				 echo json_encode($hasil);

			 

			} catch (Exception $e) { 
			        echo "<h2>Exception Error!</h2>"; 
			        echo $e->getMessage(); 
			}
	}		
	
}

/* End of file import_sppb.php */
/* Location: ./application/controllers/tps_online/import_sppb.php */