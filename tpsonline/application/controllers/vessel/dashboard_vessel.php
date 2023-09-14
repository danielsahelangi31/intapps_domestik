<?php

class Dashboard_Vessel extends CI_Controller
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
        $this->load->view('vessel/backend/pages/dashboard/index_vessel', $view);
	}

	function passwordUpdateVessel()
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
					redirect('vessel/dashboard_vessel');
			}
		}

		$this->load->view('vessel/backend/pages/dashboard/update_password_vessel', $data);
	}
}
