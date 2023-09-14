<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Register extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->library(array('form_validation'));
		$this->load->helper(array('form'));
	}

	public function index(){
		$this->load->helper('captcha');
		$this->load->model(array('membership'));

		if (post())
		{
			$this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric|is_unique[users.username]');
			$this->form_validation->set_rules('password', 'Password', 'required');
			$this->form_validation->set_rules('passconf', 'Password Confirmation', 'required|matches[password]');
			$this->form_validation->set_rules('nama_lengkap', 'Full Name', 'required');
			$this->form_validation->set_rules('cp_handphone', 'Mobile phone', 'required|alpha_dash');
			$this->form_validation->set_rules('cp_telepon', 'Phone', 'alpha_dash');
			$this->form_validation->set_rules('cp_email', 'Email', 'required|valid_email');
			$this->form_validation->set_rules('nama_perusahaan', 'Company Name', 'required');
			$this->form_validation->set_rules('npwp', 'Tax Reference (NPWP)', 'required|is_unique[member.npwp]');
			$this->form_validation->set_rules('com_telepon', 'Company phone', 'required|alpha_dash');
			$this->form_validation->set_rules('com_fax', 'Company fax', 'alpha_dash');
			$this->form_validation->set_rules('com_alamat', 'Company address');
			$this->form_validation->set_rules('cc', 'Check Terms', 'required');
			$this->form_validation->set_rules('captcha', 'Captcha', 'required|callback_captcha_check');

			if ( $this->form_validation->run() == TRUE)
			{
				if($this->membership->add())
				{
					redirect('register/success');
				}
			}
		}
		
		// Reset Captcha
		$this->session->set_userdata('captcha_key', uniqid()); 
		$this->load->view('frontend/pages/register/index');
	}

	public function success(){
		$this->load->view('frontend/pages/register/success');
	}

	// Implement Captcha Check
	public function captcha_check($str)
	{
		if($str){
			if($this->session->userdata('captcha_key') && strtolower($str) == strtolower($this->session->userdata('captcha_key'))){
				return TRUE;
			}else{
				$this->form_validation->set_message('captcha_check', 'Teks yang anda ketik salah, silakan coba kembali');
				return FALSE;
			}
		}else{
			$this->form_validation->set_message('captcha_check', 'Harap ketik teks yang tampil di layar');
			return FALSE;
		}
	}

}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */