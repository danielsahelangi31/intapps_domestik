<?php
include_once('./application/models/base/modelbase.php');

class Soap_Model extends ModelBase{
	
	public function __construct(){
		parent::__construct();	
		$config['allowed_types'] = 'EDI';
		$this->load->library('upload', $config);
	}
	
	public function get_data($data){
	
		// print_r($data);
		// die;
	
		$out = new StdClass();
		$edi_api = model('transfer/soap_api');
		
		$request_data = $edi_api->get_data_ctos($data);
				
		if($request_data->success){
			$out->success = $request_data->success;
			$out->msg_code = $request_data->msg_code;
			$out->msg = $request_data->msg;
			$out->datasource = $request_data->response;
			$out->actualRows = $request_data->row;
			$out->rowPerPage = 5;
		}else{
			$out->success = $request_data->success;
			$out->msg_code = $request_data->msg_code;
			$out->msg = $request_data->msg;
			$out->datasource = '';
			$out->actualRows = '';
			$out->rowPerPage = '';
		}
		
		return $out;
	}
	
}
?>