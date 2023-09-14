<?php
/** Delivery Request Ocean Going 
  *	Modul untuk Freight Forwarder untuk melakukan kegiatan delivery ocean going
  *
  */
class Ocean_Going extends CI_Controller{
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
		redirect('laporan/ocean_going/listview');
	}

	/** 
	 * Listview
	 * Halaman utama modul delivery request, menampilkan daftar delivery request yang sudah pernah
	 * dilakukan dan sebagai launcher untuk membuat delivery request baru ataupun tindakan-tindakan
	 * lain terhadap delivery request yang sudah dilakukan.
	 */
	public function riwayat_delivery(){
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

		$this->load->view('backend/pages/laporan/ocean_going/listview', $data);
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
			$out = $mod->send_data($id);
			
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
			$data = array(
				'del_req' => $del_req
			);
			
			$this->load->view('backend/pages/delivery_request_og/preview_invoice', $data);
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
		
		$data = array(
			'del_req' => $mod->get($id, $this->auth)->datasource
		);
		
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