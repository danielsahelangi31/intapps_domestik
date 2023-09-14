<?php
class soap_api extends CI_Model{
	// Isikan file ini dengan WSDL SOAP, simpan file di folder /application/config/wsdl
	private $wsdl = "./application/config/wsdl/DATACTOS.wsdl";
	private $client = null;

	public function __construct(){
		parent::__construct();	
	}
	
	/** Dapatkan objek untuk melakukan request SOAP
	  * @return: PHP Soap Client
	  */
	public function get_soap_client(){
		if(!$this->client){
			$this->client = new SoapClient($this->wsdl, array(
				'exceptions' => true
			));
		}
		
		return $this->client;
	}
	
	public function get_data_ctos($data){
	
		// print_r($data);
		// die;
	
		$out = new StdClass();
		$client = $this->get_soap_client();
	
		$param = array(
			'VISIT_ID' => $data
		);
		
		try{
			$response = $client->SOAPGetDataCTOSOp($param);
					
			// print_r($response);
			// die;
			
			$out->success = true;
			$out->msg_code = 0;
			$out->msg = 'Succes';
			$out->row = 10;
			// Jika butuh return response dapat disalurkan disini
			$out->response = $response;
			//$out->response = is_array($response->RecordKapal) ? $response->RecordKapal : array($response->RecordKapal);
			
		}catch(SoapFault $fault){
			// Soap Exception

			if($fault->faultcode == 'SOAP-ENV:Server'){
				// Untuk pesan fault harap sesuaikan dengan Fault Schema service yang bersangkutan
				$out->success = false;
				$out->msg_code = -1;
				$out->msg = 'error';
				
				$out->payload = isset($detail->payload) ? $detail->payload : null;
			}else{
				$out->success = false;
				$out->msg_code = 503;
				$out->msg = 'Tidak dapat menghubungi Server API, Mungkin sedang dalam perawatan rutin atau jaringan sibuk. Silakan coba beberapa saat lagi.';
				$out->payload = null;
			}
		}catch(Exception $e){
			// Other Exception
			$out->success = false;
			$out->msg_code = 500;
			$out->msg = 'Internal Server Error';
		}
		
		return $out;
	}
	
}
?>