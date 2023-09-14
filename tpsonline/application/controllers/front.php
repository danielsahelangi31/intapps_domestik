<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
class Front extends CI_Controller
{
	public function __construct()
	{
		parent::__construct();

		$this->load->library(array('form_validation'));
        $this->load->library('logger');
	}

	public function index()
	{
		// echo "AAAA";
        // Dapatkan data login
				//$this->userauth->checkDB();
				// $this->userauth->checkDB_OCI();
				// exit();
        if ($this->userauth->getLoginData()) {
            $roles = explode('|', $this->userauth->getLoginData()->roles);
            if(in_array('ETICKET', $roles)){
                redirect(DASHBOARD_ETICKET);
			}elseif(in_array('DASHBOARD-IKT', $roles)){
				redirect(dashboard_ikt);
			}else{
                redirect(DASHBOARD);
            }
        }

		$view = array();

		// Proses ketika ada POST
		if (post()) {
			$this->form_validation->set_rules('username', 'Username', 'required|alpha_numeric');
			$this->form_validation->set_rules('password', 'Password', 'required');
			// $this->form_validation->set_rules('captcha', 'Captcha', 'required|callback_captcha_check');

			if ($this->form_validation->run()) {
				$username = post('username');
				$password = post('password');

				if ($this->userauth->checkLogin($username, $password)) {
					if($this->userauth->getLoginData()->role_user == ""){
						// Sukses Login
                  $roles = explode('|', $this->userauth->getLoginData()->roles);
									if(in_array('ETICKET', $roles)){
				                        $this->logger
				                            ->user($this->userauth->getLoginData()->username)
				                            ->function_name($this->router->fetch_method())
				                            ->comment('login')
				                            ->log();
				                        redirect(DASHBOARD_ETICKET);
									} else if(in_array('DASHBOARD-IKT', $roles)){
											$this->logger
												->user($this->userauth->getLoginData()->username)
												->function_name($this->router->fetch_method())
												->comment('login')
												->log();
											redirect(dashboard_ikt);
				                    }else{
				                        redirect(DASHBOARD);
				                    }

						} else if($this->userauth->getLoginData()->role_user == "VESSEL"){
							redirect('vessel/dashboard_vessel');
						}

					// redirect('domestik/dashboard_domestik');
					//redirect('vessel/dashboard_vessel');
				}
				else if($this->userauth->checkLoginOCIDomestik($username, $password)){
					redirect('domestik/dashboard_domestik');
				} else {
					// Gagal Login, tampilkan pesan kesalahan
					$view['error_msg'] = 'Username atau password salah';
				}
			} else {
				$view['error_msg'] = validation_errors();
			}
		}

		// Reset Captcha
		$this->session->set_userdata('captcha_key', uniqid());
		$this->load->view('frontend/pages/front/index', $view);
	}

	public function logout()
	{

        $this->logger
            ->user($this->userauth->getLoginData()->username)
            ->function_name($this->router->fetch_method())
            ->comment('logout')
            ->log();
		$this->userauth->clearLoginData();
		redirect(LOGIN_PAGE);
	}

	public function show_captcha()
	{
		$string = '';

		for ($i = 0; $i < 5; $i++) {
			// this numbers refer to numbers of the ascii table (lower case)
			$string .= chr(rand(97, 122));
		}

		$this->session->set_userdata('captcha_key', $string);

		$dir = "./assets/fonts/";

		$image = imagecreatetruecolor(170, 60);
		$black = imagecolorallocate($image, 0, 0, 0);
		$color = imagecolorallocate($image, 200, 100, 90); // red
		$white = imagecolorallocate($image, 255, 255, 255);

		imagefilledrectangle($image, 0, 0, 399, 99, $white);
		imagettftext($image, 30, 0, 10, 40, $color, $dir . "font1.ttf", $string);

		header("Content-type: image/png");
		imagepng($image);
	}

	// Implement Captcha Check
	public function captcha_check($str)
	{
		if ($str) {
			if ($this->session->userdata('captcha_key') && strtolower($str) == strtolower($this->session->userdata('captcha_key'))) {
			if ($this->session->userdata('captcha_key') && $str == $this->session->userdata('captcha_key')) {
				return TRUE;
			} else {
				$this->form_validation->set_message('captcha_check', 'Teks yang anda ketik salah, silakan coba kembali');
				return FALSE;
			}
		} else {
			$this->form_validation->set_message('captcha_check', 'Harap ketik teks yang tampil di layar');
			return FALSE;
		}
	}
}
}
/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */
