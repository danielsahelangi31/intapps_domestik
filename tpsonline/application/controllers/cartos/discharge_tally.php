<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @author: djati@ilcs.co.id
 * @date: 2014 06 27
 */
class Discharge_Tally extends CI_Controller {
	public function verify()
	{
		$view = array();
	
		if(is_post_request()) {
			$visit_id = post('visit_id');
		
			if($visit_id){
				$api = model('cartos/api/cartos_api');
				
				if(post('approve')){
					$result = $api->flag_data($visit_id, 1);
					
					if($result->success){
						$view['success_msg'] = 'Data berhasil di approve';
					}else{
						$view['error_msg'] = $result->msg;
					}
				}else if(post('reject')){
					$result = $api->flag_data($visit_id, 2);
					
					if($result->success){
						$view['success_msg'] = 'Data berhasil di reject';
					}else{
						$view['error_msg'] = $result->msg;
					}
				}else{
					$result = $api->get_data($visit_id);
					
					if($result->success){
						if($result->response->IS_VALID == 'Y'){
							$view['success_msg'] = 'Data berhasil di validasi';
						}else{
							$view['error_msg'] = 'Data tidak berhasil divalidasi. VISIT ID ini tidak dapat ditampilkan detailnya.';
						}
										
						$view['datasource'] = $result->response;
					}else{
						$view['error_msg'] = $result->msg;
					}
				}
			}else{
				$view['error_msg'] = 'Harap isi Visit ID';
			}
		}
	
		$this->load->view('backend/pages/cartos/discharge_tally/verify', $view);
	}
}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */