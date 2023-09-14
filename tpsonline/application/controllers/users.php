<?php
/** Users
  *	Modul untuk penambahan user, aktivasi, dll
  *
  */
class Users extends CI_Controller{
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
		$mod = model('usermodel');
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

		$this->load->view('backend/pages/users/listview', $data);
	}
	
	/** 
	 * Add
	 * Menambah Member Baru.
	 */
	public function add(){
		$this->load->model(array('usermodel'));
		$this->load->library(array('form_validation'));
		$data = array();

		if (post())
		{
			$this->form_validation->set_rules('username', 'Username', 'required|alphanumeric|is_unique[users.username]');
			$this->form_validation->set_rules('password', 'Password', 'required|is_unique[member.npwp]');
			$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');
			$this->form_validation->set_rules('nama_lengkap', 'Full Name', 'required');
			$this->form_validation->set_rules('handphone', 'Mobile phone', 'required|alpha_dash');
			$this->form_validation->set_rules('telepon', 'Phone', 'required|alpha_dash');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('member_select', 'Company Name', 'required');

			if ( $this->form_validation->run() == TRUE)
			{
				if($this->usermodel->add()) redirect('users/listview');
			}
			else
			{
				foreach(post() as $key => $val)
				{
					$data['datasource']->$key = $val;
				}
			}
		}

		$this->load->view('backend/pages/users/add', $data);
	}
	
	
	/** 
	 * Edit
	 * Edit Data Member
	 */
	public function edit($id){
		$this->load->library(array('form_validation'));

		$mod = model('usermodel');
		$res = $mod->details($id);
		$data = array('datasource' => $res->datasource);

		if (post())
		{
			$this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric');
			/*$this->form_validation->set_rules('password', 'Password', 'required|is_unique[member.npwp]');
			$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');*/
			$this->form_validation->set_rules('nama_lengkap', 'Full Name', 'required');
			$this->form_validation->set_rules('handphone', 'Mobile phone', 'required|alpha_dash');
			$this->form_validation->set_rules('telepon', 'Phone', 'required|alpha_dash');
			$this->form_validation->set_rules('email', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('member_select', 'Company Name', 'required');

			if ( $this->form_validation->run() == TRUE)
			{
				if($this->usermodel->update()) redirect('users/listview');
			}
			else
			{
				foreach(post() as $key => $val)
				{
					$data['datasource']->$key = $val;
				}
			}
		}

		$this->load->view('backend/pages/users/edit', $data);
	}

	/** 
	 * Delete
	 * Menghapus User.
	 */ 
	public function delete($id){
		if($this->usermodel->delete($id)) redirect('users/listview');
	}

	/** 
	 * Delete
	 * Menghapus User.
	 */
	public function send(){
		
	}
}