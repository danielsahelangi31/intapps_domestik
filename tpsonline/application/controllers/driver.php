<?php
/** Driver
  *	Modul untuk Driver
  *
  */
class Driver extends CI_Controller{
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
		redirect('driver/listview');
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
		$mod = model('drivermodel');
		$cfg = $mod->parseParameter($num_args, $get_args);

		// Apply Config
		$mod->terapkanConfig($cfg);

		// Content Data
		$res = $mod->select($this->auth->trucking_company_id);
		$cfg->totalPage		= (int) ceil($res->actualRows / $cfg->rowPerPage);

		// Layout Data
		$data = array(
			'cfg' => $cfg,
			'searchable' => $mod->searchable,
			'sortable' => $mod->sortable,
			'datasource' => $res->datasource
		);

		$this->load->view('backend/pages/driver/listview', $data);
	}
	
	/** 
	 * Add
	 * Menambah Driver Baru.
	 */
	public function add(){
		$mod = model('drivermodel');
		$this->load->library(array('form_validation'));
		$data = array();

		if (post())
		{
			$this->form_validation->set_rules('nama_supir', 'Nama Supir', 'required');
			$this->form_validation->set_rules('nomor_handphone', 'Nomor Handphone', 'required|alpha_dash');
			$this->form_validation->set_rules('plat_nomor', 'Plat Nomor', 'required|alpha_numeric');

			if ( $this->form_validation->run() == TRUE)
			{
				if($mod->add())
				{
					redirect('driver/listview');
				}
			}
		}

		$this->load->view('backend/pages/driver/add', $data);
	}
	
	
	/** 
	 * Edit
	 * Edit Delivery Request Baru. Hanya Delivery Request yang belum dikirim yang dapat diedit
	 */
	public function edit($id){
		$this->load->library(array('form_validation'));
		$mod = model('drivermodel');

		$data = array(
			'datasource' => $mod->get($id)->datasource
		);

		if (post())
		{
			$this->form_validation->set_rules('nama_supir', 'Nama Supir', 'required');
			$this->form_validation->set_rules('nomor_handphone', 'Nomor Handphone', 'required|alpha_dash');
			$this->form_validation->set_rules('plat_nomor', 'Plat Nomor', 'required|alpha_numeric');

			if ( $this->form_validation->run() == TRUE)
			{
				if($mod->update()) redirect('driver/listview');
			}
			else
			{
				foreach(post() as $key => $val)
				{
					$data['datasource']->$key = $val;
				}
			}
		}

		$this->load->view('backend/pages/driver/edit', $data);
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