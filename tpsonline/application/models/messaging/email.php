<?php
class Email extends CI_Model{
	private $wsdl = "./application/config/wsdl/tpkhub_mailer.wsdl";
	
	public function __construct(){
		parent::__construct();	
	}
	
	public function send($outbox){
		foreach((array) $outbox as $email){
			$out = $this->parse($email['template'], $email['payload']);
			$this->push($email['tujuan'], $out);
		}
	}
	
	// Parser
	private function parse($template, $payload){
		$out = new StdClass();
		
		switch($template){
			case 'OGD_REQUEST_TRUCKING':
				$out->content_type = 'text/plain';
				$out->subject = 'Permintaan Ambil Container / Request Delivery Trucking';
				$out->content = $this->load->view('template/email/ocean_going/delivery/request_trucking', $payload, true);
				
				break;
		
			case 'OGD_TRUCK_ASSIGNED':
				$out->content_type = 'text/plain';
				$out->subject = 'Pemberitahuan Penugasan Truk / Truck Assignment Notification';
				$out->content = $this->load->view('template/email/ocean_going/delivery/truck_assigned', $payload, true);
				
				break;
				
			case 'OGD_CHANGE_TRUCK':
				
				break;
				
			case 'CP_MANDIRI_SUCCESS':
				$header  = "Kepada Yth. " . $row[0]["cm_name"] . ",\r\n\r\n";
                $message = "Anda telah melunasi tagihan " . $modul . " melalui Internet Payment Smart Cargo, Berikut detail informasi pembayaran : \r\n";
                $message .= "  Kode Bayar :" . $KD_UPER . " \r\n";
                $message .= "  Jumlah  : IDR " . number_format(($AMOUNT + 2000), 0, ',', '.') . " \r\n";
                
                $message .= "  Kode Receipt : " . $receipt_code . " \r\n";
                $message .= "  Bank Administration : Rp. 2,000 \r\n";
                $message .= "\r\n\r\n";
                $message .= "Terima kasih telah menggunakan Smart Cargo\r\n\r\n";
                $message .= "Hormat kami,\r\n\r\n";
                $message .= "IPC Cabang Tanjung Priok\r\n\r\n";
                $message .= "Bantuan \r\n";
                $message .= "Jika anda menemui kendala dan masalah silakan menghubungi kami melalui kontak berikut:\r\n\r\n";
                $message .= "Teknis:\r\n";
                $message .= "Call Center: +6221 43933377,+6221 500950 \r\n";
                $message .= "Website: http://ticket.inaportnet.com/ \r\n";
                $message .= "Cara Pembayaran: http://inaportnet.com/smartcargo/howtopayment/ \r\n\r\n";
                $message .= "Sales:\r\n";
                $message .= "Email: info@inaportnet.com\r\n";
                $message .= "Telepon: +62.\r\n";
                $message = $header . "" . $message;
				
				$out->content_type = 'text/plain';
				$out->subject = 'Pemberitahuan Penugasan Truk / Truck Assignment Notification';
				$out->content = $this->load->view('template/email/ocean_going/delivery/truck_assigned', $payload, true);
				
				break;
		}
		
		return $out;
	}
	
	// Physical Send
	public function push($tujuan, $out){
		$client = new SoapClient($this->wsdl, array(
			'exceptions' => true
		));
		
		$param = array(
			'header' => array(
				'appName' => 'SmartCargo',
				'messageID' => uniqid(),
				'timestamp' => date('Y-m-d H:i:s')
			),
			'payload' => array(
				'from' => '"SmartCargo Mailer " <noreply@ilcs.co.id>',
				'to' => $tujuan,
				'subject' => $out->subject,
				'contentType' => $out->content_type ? $out->content_type : 'text/plain',
				'content' => $out->content
			),
		);
		
		$response = $client->MailReceiverOp($param);
	}
}