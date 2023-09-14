<?php
/** Member
  *	Modul untuk penambahan member, aktivasi, dll
  *
  */
class Member extends CI_Controller{
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

		$configPaging['base_url'] = site_url($cfg->base);
		$configPaging['total_rows'] = count($res->datasource);
		$configPaging['full_tag_open'] = '<ul class="pagination pull-right">';
		$configPaging['full_tag_close'] = '</ul>';

		$configPaging['next_link'] = '&laquo;';
		$configPaging['next_tag_open'] = '<li class="disabled">';
		$configPaging['next_tag_close'] = '</li>';
		$configPaging['prev_link'] = '&raquo;';
		$configPaging['prev_tag_open'] = '<li class="disabled">';
		$configPaging['prev_tag_close'] = '</li>';

		$configPaging['cur_tag_open'] = '<li><a href="#">';
		$configPaging['cur_tag_close'] = '</a></li>';
		$configPaging['num_tag_open'] = '<li>';
		$configPaging['num_tag_close'] = '</li>';

		$this->pagination->initialize($configPaging);

		// Layout Data
		$data = array(
			'cfg' => $cfg,
			'searchable' => $mod->searchable,
			'sortable' => $mod->sortable,
			'datasource' => $res->datasource
		);

		$this->load->view('backend/pages/member/listview', $data);
	}
	
	/** 
	 * Activationlist
	 * Daftar Member yang butuh di approve
	 */
	public function activationlist(){
		$num_args = func_num_args();
		$get_args = func_get_args();

		// Load Model & Parsing Parameter untuk sorting, searching dan paging
		$mod = model('membership');
		$cfg = $mod->parseParameter($num_args, $get_args);

		// Apply Config
		$mod->terapkanConfig($cfg);

		// Content Data		
		$res = $mod->selectByAttribute(array( 'diperiksa' => null));
		$cfg->totalPage		= (int) ceil($res->actualRows / $cfg->rowPerPage);

		// Layout Data
		$data = array(
			'cfg' => $cfg,
			'searchable' => $mod->searchable,
			'sortable' => $mod->sortable,
			'datasource' => $res->datasource
		);

		$this->load->view('backend/pages/member/activationlist', $data);
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

	/** 
	 * Delete
	 * Menghapus User.
	 
	public function delete($id){
		if($this->membership->delete()) redirect('member/listview');
	}
	*/

	/** 
	 * Delete
	 * Menghapus User.
	 */
	public function send(){
		
	}
}