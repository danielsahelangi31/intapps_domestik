 <?php
class Cartos_Api extends CI_Model{
	private $wsdl = "./application/config/wsdl/GetBillData.wsdl";
	private $client = null;
	
	private $local_db = null;
	private $port_cache = array();
	
	public function get_soap_client(){
		if(!$this->client){
			$this->client = new SoapClient($this->wsdl, array(
				'exceptions' => true,
				'cache_wsdl' => WSDL_CACHE_NONE,
				// 'location' => 'http://lips.ilcs.co.id:8990/SOAP/intfSOAPGetDataCTOS-service.serviceagent/intfwsSOAPGetDataCTOSEndpoint3'
				'location' => 'http://10.8.1.91:8990/SOAP/intfSOAPGetDataCTOS-service.serviceagent/intfwsSOAPGetDataCTOSEndpoint3'
			));
		}
		
		return $this->client;
	}
	
	public function get_db(){
		if(!$this->local_db) $this->local_db = $this->load->database(ILCS_MASTER_REFERENCE_DB, TRUE);
		return $this->local_db;
	}
	
	
	
	public function get_data($visit_id){
		$out = new StdClass();
		
		$payload = array(
			'VISIT_ID' => $visit_id
		);
		
		try{
			$client = $this->get_soap_client();
			$response = $client->SOAPGetDataCTOSOp($payload);
			
			$out->success = true;
			$out->response = $response;
		}catch(SoapFault $fault){
			if($fault->faultcode == 'SOAP-ENV:Server'){
				/*$detail = $fault->detail->faultMessage;
				
				$out->success = false;
				$out->msg_code = $detail->errorCode;
				$out->msg = $detail->errorMessage;
				$out->payload = isset($detail->payload) ? $detail->payload : null;
				*/
				$out->success = false;
				$out->msg_code = $fault->detail->ExceptionInfo->Fault->FaultCode;
				$out->msg =  $fault->detail->ExceptionInfo->Fault->FaultString;
				$out->payload = null;
				
			}else{
				$out->success = false;
				$out->msg_code = 503;
				$out->msg = 'Tidak dapat menghubungi Service Cartos, Mungkin sedang dalam perawatan rutin atau jaringan sibuk. Silakan coba beberapa saat lagi.';
				$out->payload = null;
				$out->fault = $fault;
			}
		}catch(Exception $e){
			$out->success = false;
			$out->msg_code = 500;
			$out->msg = 'Internal Server Error';
		}
		
		return $out;
	}
	
	public function flag_data($visit_id, $flag){
		$out = new StdClass();
		
		$payload = array(
			'VISIT_ID' => $visit_id,
			'FLAG_TYPE' => $flag // 1 = VALID, 2 NOT VALID
		);
		
		try{
			$client = $this->get_soap_client();
			$response = $client->SOAPFlagDataCTOSOp($payload);
			
			$out->success = true;
			$out->response = $response;
		}catch(SoapFault $fault){
			if($fault->faultcode == 'SOAP-ENV:Server'){
				/*$detail = $fault->detail->faultMessage;
				
				$out->success = false;
				$out->msg_code = $detail->errorCode;
				$out->msg = $detail->errorMessage;
				$out->payload = isset($detail->payload) ? $detail->payload : null;
				*/
				
				$out->success = false;
				$out->msg_code = $fault->detail->ExceptionInfo->Fault->FaultCode;
				$out->msg =  $fault->detail->ExceptionInfo->Fault->FaultString;
				$out->payload = null;
				
			}else{
				$out->success = false;
				$out->msg_code = 503;
				$out->msg = 'Tidak dapat menghubungi Service Cartos, Mungkin sedang dalam perawatan rutin atau jaringan sibuk. Silakan coba beberapa saat lagi.';
				$out->payload = null;
				$out->fault = $fault;
			}
		}catch(Exception $e){
			$out->success = false;
			$out->msg_code = 500;
			$out->msg = 'Internal Server Error';
		}
		
		return $out;
	}
}