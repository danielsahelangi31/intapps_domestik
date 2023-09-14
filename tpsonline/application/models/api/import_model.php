<?php
class IMport_model extends CI_Model{

	
	public function __construct(){
		parent::__construct();
		$this->load->helper("url");
	}	
	


	public function send($param = null){ 
		//API URL
		$url = 'http://10.8.3.43:7809';

		//create a new cURL resource
		$ch = curl_init($url);

		//setup request to send json via POST
		$data = array(
			'GetImport_Sppb' => array(
				// 'Username'	=>	$param['Username'],
				// 'Password'	=>	$param['password'],
				'No_Sppb'	=>	$param['No_Sppb'],
				'Tgl_Sppb'	=>	$param['Tgl_Sppb'],
				'NPWP_Imp'	=>	$param['NPWP_Imp'],																					
				)
		);

		$payload = json_encode(array("user" => $data));

		//attach encoded JSON string to the POST fields
		curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

		//set the content type to application/json
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));

		//return response instead of outputting
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

		//execute the POST request
		$result = curl_exec($ch);

		//close cURL resource
		curl_close($ch);

		$data = json_decode($result, true);

		return $data;
	}
}