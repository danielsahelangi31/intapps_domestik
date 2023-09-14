<?php 

class SoapTest extends CI_Controller{
	private $wsdl = "./application/config/wsdl/ter3simulator.wsdl";
	private $sms_wsdl = "./application/config/wsdl/wssms.wsdl";
	
	public function __construct(){
		parent::__construct();	
	}
	
	public function test(){
		$xml = '<?xml version = "1.0" encoding = "UTF-8"?>
		<sendData>
			<messageID>348609346</messageID>
			<kodeTerminalPetikemas>TER3</kodeTerminalPetikemas>
			<callSign>sdggasd</callSign>
			<voyageIn>asdg</voyageIn>
			<kdPBM/>
			<agen/>
			<deliveryViaStatus/>
			<tujuan/>
			<keterangan/>
			<rencanaDelivery/>
			<tanggalRequest/>
			<username/>
			<detail>
				<nomorContainer>90386903426</nomorContainer>
				<noSP2/>
				<noBL>347234734</noBL>
				<noBPID/>
				<commodity>43743273427</commodity>
				<hazard>0</hazard>
				<kdSize>22</kdSize>
				<kdType>HC</kdType>
				<kdStatusCont>MTY</kdStatusCont>
				<noUKK/>
			</detail>
			<detail>
				<nomorContainer/>
				<noSP2/>
				<noBL/>
				<noBPID/>
				<commodity/>
				<hazard/>
				<kdSize/>
				<kdType/>
				<kdStatusCont/>
				<noUKK/>
			</detail>
		</sendData>';

		$param = json_decode(json_encode((array)simplexml_load_string($xml)), 1);
		
		try{
			$client = new SoapClient($this->wsdl, array(
			   'exceptions' => true,
			));
			
			$param = array(
				'messageID' => uniqid(),
				'kodeTerminalPetikemas' => 'TER3',
				'kodeCabang' => '01',
				'noRequestILCS' => 'REQ092094260942',
				'nomorDO' => '',
				'nomorBL' => '',
				'callSign' => 'sdglk',
				'voyageIn' => 'gdskjl',
			);
			
			$response = $client->SendDataOp($param);
			
			var_dump($response);
		}catch(SoapFault $fault){
			var_dump($fault->detail); exit();
		
			trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
		}
	}
	
	public function assign_tinfo(){
		$result = $this->db->get('tdetail')->result();
		foreach($result as $row){
			$tinfo = $this->db->where('blNumber', $row->blNumber)->get('tinfo')->row();
			if($tinfo){
				$this->db->set('tinfo_id', $tinfo->id)->where('id', $row->id)->update('tdetail');	
			}
		}
	}
	
	public function truncate_del_req(){
		$this->db->query('SET FOREIGN_KEY_CHECKS = 0');
		$this->db->query('TRUNCATE ocean_going_delivery_truck_assignment');
		$this->db->query('TRUNCATE ocean_going_delivery_request_line');
		$this->db->query('TRUNCATE ocean_going_delivery_request');
			
	}
	
	public function test_wsdl(){
		$mod = model('og_api');
		$mod->send_data();	
	}
	
	public function sms(){
		try{
			$client = new SoapClient($this->sms_wsdl, array(
			   'exceptions' => true,
			   'trace' => 1
			));
			
			$param = array(
				'nohp' => '+6285711611076',
				'msg' => 'Hello World From SmartCargo'
			);
			
			//Body of the Soap Header
			$headerbody = new StdClass();
			$headerbody->UserName = 'smsuser';
			$headerbody->Password = 'passsms';
			
			//Create Soap Header.        
			$header = new SOAPHeader('urn:WSDL_SMSServicePoint.wsdl', 'AuthSoapHeader', $headerbody);        
			
			//set the Headers of Soap Client. 
			$client->__setSoapHeaders($header); 
			
			$response = $client->smspush($param);
			
			var_dump($response);
			
		}catch(SoapFault $fault){
			// echo  $client->__getLastRequest();
			trigger_error("SOAP Fault: (faultcode: {$fault->faultcode}, faultstring: {$fault->faultstring})", E_USER_ERROR);
		}
	}
}