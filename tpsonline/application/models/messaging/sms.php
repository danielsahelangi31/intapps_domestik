<?php
class SMS extends CI_Model{
	private $sms_wsdl = "./application/config/wsdl/wssms.wsdl";

	public function __construct(){
		parent::__construct();
	}
	
	public function send($outbox){
		foreach((array) $outbox as $sms){
			$text = $this->parse($sms['template'], $sms['payload']);
			$this->push($sms['nomor_hp'], $text);
		}
	}
	
	// Parser
	private function parse($template, $payload){
		switch($template){
			case 'OGD_GATE_IN_TICKET':
				$container = $payload['container'];
				
				$template = "
				SMARTCARGO GATE IN TICKET: \n\n
				TERMINAL: %s\n
				NO_CONT: %s\n
				CONSIGNEE: %s\n
				SECURITY_CODE: %s\n
				";
				
				$template = str_replace("\t", '', $template);
				
				return sprintf($template, $container->nama_terminal_petikemas, $container->container_number, $container->consignee, $payload['security_code']);
				
				break;
		}
	}
	
	// Physical Send
	public function push($no_hp, $msg){
		try{
			$client = new SoapClient($this->sms_wsdl, array(
			   'exceptions' => true,
			   'trace' => 1
			));
			
			$param = array(
				'nohp' => $no_hp,
				'msg' => $msg
			);
			
			// Body of the Soap Header
			$headerbody = new StdClass();
			$headerbody->UserName = 'smsuser';
			$headerbody->Password = 'passsms';
			
			// Create Soap Header.        
			$header = new SOAPHeader('urn:WSDL_SMSServicePoint.wsdl', 'AuthSoapHeader', $headerbody);        
			
			// Set the Headers of Soap Client. 
			$client->__setSoapHeaders($header); 
			
			$response = $client->smspush($param);
		}catch(SoapFault $fault){
			trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
		}
	}
}