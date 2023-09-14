<?php
class MandiriClickpay{
	private $url = 'http://127.0.0.1:22335/velispayment/';
	private $biaya_transaksi = 2000;
	
	function __construct(){
		
	}
	
	public function do_payment($trx_id, $invoice){
		$out = new StdClass();
		
		$xml = $this->generate_xml($trx_id, $invoice);
		try{
			$response_text = $this->post_request($this->url, $xml);
			
			$response = new SimpleXMLElement($response_text);
		
			if($response){
				if($response->response_code == '0000'){
					$out->success = true;
					$out->transaction_id = $response->transaction_id;
					$out->receipt_code = $response->receipt_code;
					$out->response_desc = $response->response_desc;
				}else{
					$out->success = false;
					$out->msg = '';
				}
			}else{
				die("INVALID RESPONSE:\n\n".$response_text);
			}
		}catch(Exception $e){
			$out->success = false;
			$out->msg = $e->getMessage();
		}
		
		return $out;
	}
	
	private function generate_xml($trx_id, $invoice){
		$card_no 		= post('nomor_kartu'); 				// "4616999900000028";
		$amount  		= $invoice->header->kredit + $this->biaya_transaksi; 	// "12000"m + biaya transaksi;
		$last10_card_no = substr(post('nomor_kartu'), -10);	// "9900000028";
		$noreq	 		= $invoice->header->kdUper;			//
		$token_reponse  = post('token_response'); 			// "000000";
		$datetime       = date('Ymdhis');					// "20110321142745" => "YYYYMMDDhhmmss";
		
		$xml = 
			'<payment_request>
				<user_id>user</user_id>
				<password>pwd</password>
				<card_no>'.$card_no.'</card_no>
				<amount>'.$amount.'</amount>
				<transaction_id>'.$trx_id.'</transaction_id>
				<data_field1>'.$last10_card_no.'</data_field1>
				<data_field2>'.$amount.'</data_field2>
				<data_field3>'.$noreq.'</data_field3>
				<token_response>'.$token_reponse.'</token_response>
				<date_time>'.$datetime.'</date_time>
				<bank_id>1</bank_id>';			
		
		$xml .=	'<items count="'.count($invoice->detail).'">';
		$i = 1;
		foreach($invoice->detail as $det){
			$xml .=	'<item no="'.($i++).'" name="'.$det->uraian.'" price="'.$det->tarif.'" qty="'.$det->qty.'" />';
		}
		$xml .=	"</items>";
		$xml .="</payment_request>";
		
		return $xml;
	}
	
	private function post_request($url, $data, $optional_headers = null) {
		$params = array(
			'http' => array(
				'method' => 'POST',
				'content' => $data
			)
		);
		
		if($optional_headers !== null) {
			$params['http']['header'] = $optional_headers;
		}
		
		$ctx = stream_context_create($params);
		$fp = @fopen($url, 'rb', false, $ctx);
		
		if (!$fp) {
			throw new Exception("Tidak dapat menyambung ke Bank Mandiri, pembayaran belum dilakukan silakan hubungi Customer Service kami.");
		}
		
		$response = @stream_get_contents($fp);
		if ($response === false) {
			throw new Exception("Tidak dapat membaca data, Silakan hubungi Customer Service kami.");
		}
		
		return $response;
	}
	
	
	
}