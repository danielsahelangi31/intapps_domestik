<?php
class EDI_API extends CI_Model{
	// Isikan file ini dengan WSDL SOAP, simpan file di folder /application/config/wsdl
	private $wsdl = "./application/config/wsdl/nama_file_wsdl.wsdl";
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
	
	/** Sample Request
	  * Gunakan kerangka method ini untuk request SOAP
	  *
	  */
	public function sample_request(){
		// Out untuk  menampung return, karena biasanya hasil returnnya kompleks ditampung
		// dalam sebuah objek generik. Properti bisa disesuaikan dengan kebutuhan namun yg 
		// harus ada adalah properti sbb:
		// 1. $out->success (true | false). Hasil operasi berhasil atau gagal
		// 2. $out->msg_code (integer). Kode pesan yang menggambarkan hasil operasi
		// 3. $out->msg (string). Isi pesna dalam bahasa indonesia / inggris
		// 
		// Jika dibutuhkan untuk mereturn hasil response bisa menambahkan properti sesuai kebutuhan
		// Contoh yang biasa digunakan dalam smartcargo:
		// $out->payload, $out->response, $out->datasource
		//
		$out = new StdClass();
		$client = $this->get_soap_client();
	
		$param = array(
			'param1' => uniqid(),
			'param2' => $param2,
			'param3' => 'Test',
		);
		
		try{
			$response = $client->NamaMethod($param);
			
			$out->success = true;
			$out->msg_code = 200;
			$out->msg = 'Isikan dengan pesan ketika sukses';
			
			// Jika butuh return response dapat disalurkan disini
			$out->response = $response;
		}catch(SoapFault $fault){
			// Soap Exception
			if($fault->faultcode == 'SOAP-ENV:Server'){
				// Untuk pesan fault harap sesuaikan dengan Fault Schema service yang bersangkutan
				$detail = $fault->detail->faultMessage;
				
				$out->success = false;
				$out->msg_code = $detail->errorCode;
				$out->msg = $detail->errorMessage;
				
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