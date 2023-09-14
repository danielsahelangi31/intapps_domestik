<?php
/** Delivery Request Ocean Going 
  *	Modul untuk Freight Forwarder untuk melakukan kegiatan delivery ocean going
  *
  */
class Delivery_Truck_Assignment_OG extends CI_Controller{
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
		redirect('Delivery_Truck_Assignment_OG/listview');
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
		$mod = model('Delivery_Truck_Assignment_Model');
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

		$this->load->view('backend/pages/delivery_truck_assignment_og/listview', $data);
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
			}else{
				$data['error_msg'] = $out->msg;
			}
		}
		
		$this->load->view('backend/pages/delivery_request_og/send', $data);
	}
	
	
	/** 
	 * Preview Invoice
	 * Melihat rincian biaya yang harus dibayar.
	 */
	public function preview_invoice($id){
		$mod = model('og_deliveryrequest');
		
		$del_req = $mod->get($id, $this->auth)->datasource;
		
		if(!$del_req->nota_id){
			if(post('payment_channel') == 'other'){
				$api = model('og_api');
				$invoice = $api->generate_invoice($del_req, $this->auth);
				
				$mod->save_invoice($id, $invoice);
				
				redirect('delivery_request_og/invoice/'.$id);
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
	 * Invoice
	 * Melihat rincian biaya yang harus dibayar.
	 */
	public function invoice($id){
		$mod = model('og_deliveryrequest');
		$del_req = $mod->get($id, $this->auth)->datasource;
		
		// Data delivery request
		if($del_req){
			$invoice = $mod->get_invoice($del_req->nota_id)->datasource;
			
			// Inovoice generated
			if($invoice){
				$member = $this->db	->select('m.*')
									->join('users u', 'u.id = ff.users_id')
									->join('member m', 'm.id = u.member_id')
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
		
		$this->load->view('backend/pages/delivery_request_og/invoice', $data);
	}

	/** 
	 * Preview Invoice
	 * Melihat rincian biaya yang harus dibayar.
	 */
	public function assign_truck($id){
		$mod = model('og_deliveryrequest');
		
		$data = array(
			'del_req' => $mod->get($id, $this->auth)->datasource
		);
		
		$this->load->view('backend/pages/delivery_request_og/assign_truck', $data);
	}
}