<?php

/** Dashboard 
 *	Halaman landing ketika user berhasil login
 *
 */
class Dashboard_Ikt extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();
		$this->load->model(array(
			'cargo',
			'rekap_data',
			'log_autogate'
		));

		// 		Dapatkan data login
		if (!$this->auth = $this->userauth->getLoginData()) {
			redirect(LOGIN_PAGE);
		}
        // $roles = explode('|', $this->userauth->getLoginData()->roles);
        // if(in_array('ETICKET', $roles)){
        //     show_404();
        // }
	}

	/** 
	 * Index
	 * Di Halaman ini system akan menampilkan ucapan selamat datang dan jadwal kapal
	 */
	public function index()
	{
		$view = array();

		$this->load->view('backend/pages/dashboard/indexx', $view);
	}


	function passwordUpdate()
	{
		$this->load->library(array('form_validation'));

		$mod = model('usermodel');
		$res = $mod->details($this->auth->id);
		$data = array('datasource' => $res->datasource);

		if (post()) {
			$this->form_validation->set_rules('oldpassword', 'Password Lama', 'required|callback_oldpassword_check');
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

			if ($this->form_validation->run() == TRUE) {
				if ($this->usermodel->updatePassword())
					redirect('dashboard');
			}
		}

		$this->load->view('backend/pages/dashboard/update_password', $data);
	}

	public function oldpassword_check($str)
	{
		$mod = model('usermodel');
		$res = $mod->getPassword($this->auth->id);

		if (md5($str) != $res->password) {
			$this->form_validation->set_message('oldpassword_check', 'Password sekarang masih salah input');
			return FALSE;
		} else
			return TRUE;
	}

	private $local_db;

	private function get_db()
	{
		if (!$this->local_db) $this->local_db = $this->load->database(ILCS_DASHBOARD, TRUE);
		$this->local_db->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
		$this->local_db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");

		return $this->local_db;
	}

	private function get_db_log_autogate()
	{
		if (!$this->local_db) $this->local_db = $this->load->database(ILCS_LOG_AUTOGATE, TRUE);
		$this->local_db->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
		$this->local_db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");

		return $this->local_db;
	}

	
}
