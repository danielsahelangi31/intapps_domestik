<?php
/** Delivery Request Ocean Going 
  *	Modul untuk Freight Forwarder untuk melakukan kegiatan delivery ocean going
  *
  */
class Delivery_Request_OG extends CI_Controller{
	public function __construct(){
		parent::__construct();
		
		// Dapatkan data login
		if(!$this->auth = $this->userauth->getLoginData()){
			redirect(LOGIN_PAGE);
		}
	}
	
	
	/** 
	 * Index
	 */
	public function index(){
		redirect('delivery_request_og/listview');
	}
	
	
	/** 
	 * Listview
	 * Halaman utama modul delivery request, menampilkan daftar delivery request yang sudah pernah
	 * dilakukan dan sebagai launcher untuk membuat delivery request baru ataupun tindakan-tindakan
	 * lain terhadap delivery request yang sudah dilakukan.
	 */
	public function listview(){
		$num_args = func_num_args();
		$get_args = func_get_args();

		// Load Model & Parsing Parameter untuk sorting, searching dan paging
		$mod = model('og_deliveryrequest');
		$cfg = $mod->parseParameter($num_args, $get_args);

		// Apply Config
		$mod->terapkanConfig($cfg);

		// Content Data
		$res = $mod->select($this->auth->freight_forwarder_id);
		$cfg->totalPage		= (int) ceil($res->actualRows / $cfg->rowPerPage);

		// Layout Data
		$data = array(
			'cfg' => $cfg,
			'searchable' => $mod->searchable,
			'sortable' => $mod->sortable,
			'datasource' => $res->datasource
		);

		$this->load->view('backend/pages/delivery_request_og/listview', $data);
	}
	
	
	/** 
	 * Add
	 * Menambah Delivery Request Baru.
	 */
	public function add(){
		$data = array();
		$this->load->view('backend/pages/delivery_request_og/add', $data);
	}
	
	
	/** 
	 * Edit
	 * Edit Delivery Request Baru. Hanya Delivery Request yang belum dikirim yang dapat diedit
	 */
	public function edit($id){
		$mod = model('og_deliveryrequest');
		
		$data = array(
			'del_req' => $mod->get($id, $this->auth)->datasource
		);
		
		$this->load->view('backend/pages/delivery_request_og/edit', $data);
	}
	
	/** 
	 * View
	 * Lihat detail delivery request
	 */
	public function view($id){
		$mod = model('og_deliveryrequest');
		
		$data = array(
			'del_req' => $mod->get($id, $this->auth)->datasource
		);
		
		$this->load->view('backend/pages/delivery_request_og/view', $data);
	}
	
	/** 
	 * Send
	 * Mengirim Delivery Request Sesuai Target Terminal.
	 */
	public function send($id){
		$mod = model('og_deliveryrequest');
		
		$data = array(
			'del_req' => $mod->get($id, $this->auth)->datasource
		);
		
		if(post('kirim')){
			$mod = model('og_deliveryrequest');
			$out = $mod->send_data($id, $this->auth);
			
			if($out->success){
				$data['info_msg'] = 'Berhasil kirim data. Silakan lanjutkan dengan pembayaran.';
				$data['del_req']->status_kirim = 1;
			}else{
				$data['error_msg'] = $out->msg;
				$data['error_code'] = $out->msg_code;
			}
		}
		
		$this->load->view('backend/pages/delivery_request_og/send', $data);
	}
	
	
	/** 
	 * Preview Invoice
	 * Melihat rincian biaya yang harus dibayar.
	 */
	public function preview_invoice($id){
		$auth = $this->auth;
		$mod = model('og_deliveryrequest');
		
		$del_req = $mod->get($id, $this->auth)->datasource;
		
		if(!$del_req->nota_id){
			if(post('payment_channel')){
				$api = model('og_api');
				
				// Goto Specified Form for each payment method
				switch(post('payment_channel')){
					case 'OTHER_NOT_CP':
						// Other payment method should generate invoice first
						$invoice = $api->generate_invoice($del_req, $auth);
						$mod->save_unpaid_invoice($id, $auth->member_id, $invoice);
						
						redirect('delivery_request_og/invoice/'.$id);
						break;
						
					case 'CP_MANDIRI':
						redirect('delivery_request_og/payment/cp_mandiri/'.$id);
						break;
						
					default:
				}
			}else{
				$api = model('og_api');
				$invoice = $api->preview_invoice($del_req);
				
				$member = $this->db->where('id', $this->auth->member_id)->get('member')->row();
				
				$data = array(
					'member' => $member,
					'del_req' => $del_req,
					'invoice' => $invoice
				);
				
				$this->load->view('backend/pages/delivery_request_og/preview_invoice', $data);
			}
		}else{
			redirect('delivery_request_og/invoice/'.$id);
		}
	}
	
	/** 
	 * Interface Payment
	 * Melihat rincian biaya yang harus dibayar.
	 */
	public function payment($provider = null, $req_id = null){
		$auth = $this->auth;
		$mod = model('og_deliveryrequest');
		$api = model('og_api');
		
		$del_req = $mod->get($req_id, $auth)->datasource;
		$invoice = $api->preview_invoice($del_req);
		
		$view = array(
			'del_req' => $del_req,
			'invoice' => $invoice
		);
		
		if($del_req){
			$this->load->library('form_validation');
			$trx_id = $del_req->id;
		
			switch($provider){
				case 'cp_mandiri':
					if($_SERVER['REQUEST_METHOD'] == 'POST'){
						$this->form_validation->set_rules('nomor_kartu', 'Nomor Kartu Debit', 'required|numeric|exact_length[16]');
						$this->form_validation->set_rules('token_response', 'Token Response', 'required|numeric|exact_length[6]');
						
						if($this->form_validation->run()){
							$payment_api = model('payment/mandiriclickpay');
							$out = $payment_api->do_payment($trx_id, $invoice);
						
							if($out->success){
								// Generate Paid Invoice
								$response = $api->generate_paid_invoice($del_req, $auth);
								if($response->success){
									$mod->save_paid_invoice($trx_id, $auth->member_id, $response->invoice);
								
									$view['info_msg'] = 'Terima kasih pembayaran berhasil.';
								}else{
									$view['error_msg'] = $response->msg;
								}
							}else{
								$view['error_msg'] = $out->msg;
							}
						}else{
							$view['error_msg'] = validation_errors();
						}
					}
					
					$this->load->view('backend/pages/delivery_request_og/pembayaran/cp_mandiri', $view);
					break;
				
				default:
					redirect('delivery_request_og/listview');
			}
		}else{
			redirect('delivery_request_og/listview');
		}
	}
	
	
	public function force_flag($id){
		echo 'DISABLED';
		return false;
	
		$mod = model('og_deliveryrequest');
		$api = model('og_api');
		
		$del_req = $mod->get($id, $this->auth)->datasource;
		
		$response = $api->generate_paid_invoice($del_req, $this->auth);
		if($response->success){
			$mod->save_paid_invoice($id, $this->auth->member_id, $response->invoice);
		}else{
			var_dump($response);
		}
	}
	
	/** 
	 * Invoice
	 * Melihat / Cetak faktur pajak.
	 */
	public function invoice($id, $presentation_type = 'WEB'){
		$mod = model('og_deliveryrequest');
		$del_req = $mod->get($id, $this->auth)->datasource;
		
		// Data delivery request
		if($del_req){
			$invoice = $mod->get_invoice($del_req->nota_id)->datasource;
			
			// Inovoice generated
			if($invoice){
				$member = $this->db	->select('m.*')
									->join('member m', 'm.id = ff.member_id')
									->where('ff.id', $del_req->freight_forwarder_id)
									->get('freight_forwarder ff')->row();
			
				$data = array(
					'member' => $member,
					'del_req' => $del_req,
					'invoice' => $invoice
				);
			}else{
				redirect('delivery_request_og/preview_invoice/'.$id);
			}
		}
		
		switch(strtoupper($presentation_type)){
			case 'WEB':
				$this->load->view('backend/pages/delivery_request_og/invoice', $data);
				break;
				
			case 'PRINT':
				$this->load->view('template/print/ocean_going/delivery/invoice', $data);
				break;
				
			default:
				$this->load->view('backend/pages/delivery_request_og/invoice', $data);
		}
		
		
	}

	/** 
	 * Assign Truck
	 * Memilih trucking, hanya bisa dilhat setelah lunas
	 */
	public function assign_truck($id){
		$mod = model('og_deliveryrequest');
		
		$del_req = $mod->get($id, $this->auth, true)->datasource;
		
		if($del_req){
			$data = array(
				'del_req' => $del_req
			);
		
			if(post()){
				$mod = model('og_deliveryrequest');
				$out = $mod->assign_truck($id, $this->auth);
				
				if($out->success){
					$data['info_msg'] = $out->msg;
				}else{
					$data['error_msg'] = $out->msg;
				}
			}
			
			$this->load->view('backend/pages/delivery_request_og/assign_truck', $data);
		}else{
			redirect('delivery_request_og/listview');
		}
	}
}