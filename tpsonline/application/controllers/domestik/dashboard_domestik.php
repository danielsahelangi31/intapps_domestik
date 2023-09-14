<?php

class Dashboard_Domestik extends CI_Controller
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
    }

	public function index()
	{
		$view = array();
        $this->load->view('domestik/backend/pages/dashboard/index_domestik', $view);
	}

	function passwordUpdateDomestik()
	{

		// $view = array();
    // $this->load->view('domestik/backend/pages/dashboard/index_domestik', $view);
		$this->load->library(array('form_validation'));


		$mod = model('usermodel');
		$res = $mod->details_domestik($this->auth->id_user);
		$data = array('datasource' => $res->datasource);

		if (post()) {
			$this->form_validation->set_rules('oldpassword', 'Password Lama', 'required|callback_oldpassword_check');
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');

			if ($this->form_validation->run() == TRUE) {
				if ($this->usermodel->updatePasswordDomestik())
					redirect('domestik/dashboard_domestik');
			}
		}

		$this->load->view('domestik/backend/pages/dashboard/update_password_domestik', $data);
	}
}
