<?php
/** Port
  *	Daftar Pelabuhan yang belum berhasil di map
  *
  */
class Mapper_Port extends CI_Controller{
	public function __construct(){
		parent::__construct();

		// Dapatkan data login
		if(!$this->auth = $this->userauth->getLoginData()){
			redirect(LOGIN_PAGE);
		}
	}
	
	/** 
	 * Listview
	 * Daftar Member
	 */
	public function listview(){
		$this->load->library('pagination');

		$num_args = func_num_args();
		$get_args = func_get_args();

		// Load Model & Parsing Parameter untuk sorting, searching dan paging
		$mod = model('membership');
		$cfg = $mod->parseParameter($num_args, $get_args);

		// Apply Config
		$mod->terapkanConfig($cfg);

		// Content Data		
		$res = $mod->select();
		$cfg->totalPage		= (int) ceil($res->actualRows / $cfg->rowPerPage);

		// Layout Data
		$data = array(
			'cfg' => $cfg,
			'searchable' => $mod->searchable,
			'sortable' => $mod->sortable,
			'datasource' => $res->datasource
		);

		$this->load->view('backend/pages/ilcs_mapper/mapper_port/listview', $data);
	}
	
	/**
	 * Approve
	 * Proses Approve
	 */
	public function approve($id = ''){
		$this->load->model(array('membership'));
		$data = array();

		if ($id != '')
		{
			if($this->membership->approve($id))
			{
				redirect('member/activationlist');
			}
		}
	}
	
	/**
	 * Reject
	 * Proses Reject
	 */
	public function reject($id = ''){
		$this->load->model(array('membership'));
		$data = array();

		if ($id != '')
		{
			if($this->membership->reject($id))
			{
				redirect('member/activationlist');
			}
		}
	}

	/** 
	 * Add
	 * Menambah Member Baru.
	 */
	public function add(){
		$this->load->model(array('membership'));
		$this->load->library(array('form_validation'));
		$data = array();

		if (post())
		{
			$this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric|is_unique[users.username]');
			$this->form_validation->set_rules('password', 'Password', 'required|is_unique[member.npwp]');
			$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');
			$this->form_validation->set_rules('nama_lengkap', 'Full Name', 'required');
			$this->form_validation->set_rules('cp_handphone', 'Mobile phone', 'required|alpha_dash');
			$this->form_validation->set_rules('cp_telepon', 'Phone', 'required|alpha_dash');
			$this->form_validation->set_rules('cp_email', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('nama_perusahaan', 'Company Name', 'required');
			$this->form_validation->set_rules('npwp', 'Tax Reference (NPWP)', 'required');
			$this->form_validation->set_rules('com_telepon', 'Company phone', 'required|alpha_dash');
			$this->form_validation->set_rules('com_fax', 'Company fax', 'alpha_dash');
			$this->form_validation->set_rules('com_alamat', 'Company address');

			if ( $this->form_validation->run() == TRUE)
			{
				if($this->membership->add())
				{
					redirect('member/listview');
				}
			}
		}

		$this->load->view('backend/pages/member/add', $data);
	}
	
	
	/** 
	 * Edit
	 * Edit Data Member
	 */
	public function edit($id){
		$this->load->library(array('form_validation'));

		$mod = model('membership');
		$res = $mod->details($id);
		$data = array('datasource' => $res->datasource);

		if (post())
		{
			$this->form_validation->set_rules('id', 'ID Member', 'required');
			$this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric');
			$this->form_validation->set_rules('nama_lengkap', 'Full Name', 'required');
			$this->form_validation->set_rules('handphone', 'Mobile phone', 'required|alpha_dash');
			$this->form_validation->set_rules('telepon', 'Phone', 'required|alpha_dash');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('nama_perusahaan', 'Company Name', 'required');
			$this->form_validation->set_rules('npwp', 'Tax Reference (NPWP)', 'required');
			$this->form_validation->set_rules('com_telepon', 'Company phone', 'required|alpha_dash');
			$this->form_validation->set_rules('fax', 'Company fax', 'alpha_dash');
			$this->form_validation->set_rules('alamat', 'Company address');

			if ( $this->form_validation->run() == TRUE)
			{
				if($this->membership->update()) redirect('member/listview');
			}
			else
			{
				foreach(post() as $key => $val)
				{
					$data['datasource']->$key = $val;
				}
			}
		}

		$this->load->view('backend/pages/member/edit', $data);
	}
}