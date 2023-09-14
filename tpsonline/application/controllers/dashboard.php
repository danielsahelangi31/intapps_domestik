<?php

/** Dashboard 
 *	Halaman landing ketika user berhasil login
 *
 */
class Dashboard extends CI_Controller
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
        $roles = explode('|', $this->userauth->getLoginData()->roles);
        if(in_array('ETICKET', $roles)){
            show_404();
        }
	}

	/** 
	 * Index
	 * Di Halaman ini system akan menampilkan ucapan selamat datang dan jadwal kapal
	 */
	public function index()
	{
		$view = array();

		$this->load->view('backend/pages/dashboard/index', $view);
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

	public function truck()
	{
		// $data = array('visit_id');
		$this->load->view('backend/pages/dashboard/truck2');
	}

	public function truck_data()
	{

		$search = $this->input->get('search');
		$sort = $this->input->get('sort');
		$order = $this->input->get('order');
		$offset = $this->input->get('offset');
		$limit = $this->input->get('limit');
		$start = $this->input->get('start');
		$end = $this->input->get('end');

		if (empty($offset)) $offset = 0;
		if (empty($limit)) $limit = 10;

		$this->load->model('truck2');
		$datas = $this->truck2->list_truck($search, $sort, $order, $offset, $limit, $start, $end);
		$data = array();
		foreach ($datas as $key => $value) {
			$button = '<a href="' . base_url() . 'dashboard/cargo/' . $value->VISIT_ID . '" target="_blank">' . $value->VISIT_ID . '</a>';

			$value->VISIT_ID = $button;
			$value->SEGEL = '';
			// print_r($value);
			// die();
			$data[] = $value;
		}
		// // print_r($data);
		// print_r($data);
		// die();
		$output = array(
			"total" => $this->truck2->count_all_truck($search, $start, $end),
			"rows" => $data,
		);

		echo json_encode($output);
	}

	public function cargo($VISIT_ID = null)
	{
		$data = array('visit_id' => $VISIT_ID);
		$this->load->view('backend/pages/dashboard/cargo2', $data);
	}

	public function cargo_data()
	{
		$visit_id = $this->input->get('visit_id');
		$search = $this->input->get('search');
		$sort = $this->input->get('sort');
		$order = $this->input->get('order');
		$offset = $this->input->get('offset');
		$limit = $this->input->get('limit');
		$periode_start = $this->input->get('start');
		$periode_end = $this->input->get('end');

		if (empty($offset)) $offset = 0;
		if (empty($limit)) $limit = 10;

		$start = $offset + 1;
		$end = $limit + $offset;

		$this->load->model('cargo2');
		$data = $this->cargo2->list_cargo($visit_id, $search, $sort, $order, $start, $end, $periode_start, $periode_end, 'list');
		$output = array(
			"total" => $this->cargo2->count_all_cargo($visit_id, $periode_start, $periode_end, $search),
			"rows" => $data,
		);
		echo json_encode($output);
	}

	public function export_cargo()
	{
		$start = $this->input->get('start');
		$end = $this->input->get('end');

		$periode_start = (!empty($start)) ? date('d-M-Y', strtotime($start)) : date('d-M-Y', strtotime('-1 months'));
		$periode_end = (!empty($end)) ? date('d-M-Y', strtotime($end)) : date('d-M-Y');

		$this->load->model('cargo2');
		$params['data'] = $data = $this->cargo2->list_cargo(null, null, null, null, null, null, $periode_start, $periode_end, 'export');
		// $params['periode'] = $periode_start . " s/d " . $periode_end;
		// $contents = $this->load->view('backend/pages/dashboard/export_cargo', $params, TRUE);

		// header("Content-type: application/vnd-ms-excel");
		header('Content-type: application/csv');
		header("Content-Disposition: attachment; filename=rekap_data_cargo.csv");

		$fp = fopen('php://output', 'w');

		$title1 = array('REKAPITULASI DATA CARGO');
		fputcsv($fp, $title1);
		$title2 = array('Periode : ' . $periode_start . ' s/d ' . $periode_end);
		fputcsv($fp, $title2);
		$separator = array(' ');
		fputcsv($fp, $separator);

		$header = array(
			'Vin', 'Status', 'On Terminal', 'Loaded', 'Left',
			'Actual Position', 'Direction', 'Maker', 'Model', 'Jenis',
			'Consignee', 'Asal', 'Tujuan Terakhir', 'Vessel', 'Status Custom',
			'Visit ID (Vessel)', 'Custom Number', 'Custom Date'
		);
		fputcsv($fp, $header);
		foreach ($data as $obj) {
			fputcsv($fp, $obj);
		}
		fclose($fp);
		exit;
	}

	/*
	public function cargo_data(){
		$mod = model('cargo');
		
		$mod->set_db($this->get_db());

		$list = $mod->list_cargo();
		$data = array();
		foreach ($list as $d) {
			$row = array();
			$row[] = $d->LOGISTIC_COMPANY;
			$row[] = $d->TRUCK;
			$row[] = $d->DRIVER;
			$row[] = $d->LAST_CHANGE;
			$row[] = $d->VIN;
			$row[] = $d->SPPB;
			$row[] = $d->HOLD;
			$row[] = $d->STATUS;
			$row[] = $d->VISIT_ID;
			$row[] = $d->VESSEL;
			$row[] = $d->DIRECTION;
			$row[] = $d->JENIS;
			$row[] = $d->MAKER;
			$row[] = $d->MODEL;
			$row[] = $d->CONSIGNEE;
			$row[] = $d->FINAL_LOCATION;
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsFiltered" => $mod->count_filtered_cargo(),
			"recordsTotal" => $mod->count_all_cargo(),
			"data" => $data,
		);
		echo json_encode($output);
	}
*/
	public function rekap_data()
	{
		$this->load->view('backend/pages/dashboard/rekap_data');
	}

	public function rekap_data_data()
	{
		$mod = model('rekap_data');

		$mod->set_db($this->get_db());

		$list = $mod->list_rekap();
		$data = array();
		foreach ($list as $d) {
			$row = array();
			$row[] = $d->LOGISTIC_COMPANY;
			$row[] = $d->TRUCK;
			$row[] = $d->DRIVER;
			$row[] = $d->LAST_CHANGE;
			$row[] = $d->VIN;
			$row[] = $d->SPPB;
			$row[] = $d->HOLD;
			$row[] = $d->STATUS;
			$row[] = $d->VISIT_ID;
			$row[] = $d->VESSEL;
			$row[] = $d->DIRECTION;
			$row[] = $d->JENIS;
			$row[] = $d->MAKER;
			$row[] = $d->MODEL;
			$row[] = $d->CONSIGNEE;
			$row[] = $d->FINAL_LOCATION;
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsFiltered" => $mod->count_filtered_rekap(),
			"recordsTotal" => $mod->count_all_rekap(),
			"data" => $data,
		);
		echo json_encode($output);
	}

	public function export_rekap($var)
	{
		$par   		= explode("_", $var);
		$tgl_awal 	= $par[0];
		$tgl_akhir 	= $par[1];
		$isi   = '';

		if ($tgl_awal) {
			$periode = $this->tanggal_indo($tgl_awal) . " - " . $this->tanggal_indo($tgl_akhir);
		} else {
			$periode = '6 Bulan Terakhir';
		}

		header("Content-type: application/vnd-ms-excel");
		// Mendefinisikan nama file ekspor "hasil-export.xls"
		header("Content-Disposition: attachment; filename=Rekap_data.xls");

		$this->rekap_data->set_db($this->get_db());
		$res = $this->rekap_data->get_data_export($tgl_awal, $tgl_akhir);
		foreach ($res as $i => $val) {
			$LAST_CHANGE 	= ($val->LAST_CHANGE) ? date('d/m/Y H:i:s', strtotime($val->LAST_CHANGE)) : '-';

			$isi .= "<tr>
				        <td>" . ($i + 1) . "</td>
				        <td>" . $val->VIN . "</td>
				        <td>" . $val->STATUS . "</td>
				        <td>" . $LAST_CHANGE . "</td>
				        <td>" . $val->JENIS . "</td>
				        <td>" . $val->DIRECTION . "</td>
				        <td>" . $val->MAKER . "</td>
				        <td>" . $val->MODEL . "</td>
				        <td>" . $val->FINAL_LOCATION . "</td>
				        <td>" . $val->LOGISTIC_COMPANY . "</td>
				        <td>" . $val->CONSIGNEE . "</td>
				        <td>" . $val->SPPB . "</td>
				        <td>" . $val->HOLD . "</td>
				        <td>" . $val->VESSEL . "</td>
				        <td>" . $val->TRUCK . "</td>
				        <td>" . $val->DRIVER . "</td>
				        <td>" . $val->VISIT_ID . "</td>
			        </tr>";
		}

		$ret = "
	        <div>
	          <label>&nbsp;</label><br/>
	          <label><b> REKAPITULASI DATA</b></label><br/>
	          <label><b> Periode : " . $periode . "</b></label><br/>
	          <label>&nbsp;</label>
	        </div>
	        <font size=\"10\" face=\"Helvetica\">
	        <table border=\"1px\" cellpadding=\"2\">
	          <thead>
		          <tr align=\"center\">
		              <th>No.</th>
		              <th>VIN</th>
		              <th>STATUS</th>
		              <th>LAST_CHANGE</th>
		              <th>JENIS</th>
		              <th>DIRECTION</th>
		              <th>MAKER</th>
		              <th>MODEL</th>
		              <th>FINAL_LOCATION</th>
		              <th>LOGISTIC_COMPANY</th>
		              <th>CONSIGNEE</th>
		              <th>SPPB</th>
		              <th>HOLD</th>
		              <th>VESSEL</th>
		              <th>TRUCK</th>
		              <th>DRIVER</th>
		              <th>VISIT_ID</th>
		          </tr>
	          </thead>
	          <tbody>
	         	" . $isi . "
	          </tbody>
	        </table></font>
	    ";
		echo $ret;
		die;
	}

	function tanggal_indo($tanggal)
	{
		$bulan = array(
			1 =>   'Januari',
			'Februari',
			'Maret',
			'April',
			'Mei',
			'Juni',
			'Juli',
			'Agustus',
			'September',
			'Oktober',
			'November',
			'Desember'
		);
		$split = explode('-', $tanggal);
		return $split[2] . ' ' . $bulan[(int) $split[1]] . ' ' . $split[0];
	}

	// public function truck(){
	// 	$this->load->view('backend/pages/dashboard/truck');
	// }

	// public function truck_data(){
	// 	$mod = model('truck');

	// 	$mod->set_db($this->get_db());

	// 	$list = $mod->list_truck();
	// 	$data = array();
	// 	foreach ($list as $d) {
	// 		$row = array();
	// 		$row[] = '<a href="'.base_url().'dashboard/cargo/'.$d->VISIT_ID.'" target="_blank">'.$d->VISIT_ID.'</a>';
	// 		$row[] = $d->TRUCKING;
	// 		$row[] = $d->PLAT_NO;
	// 		$row[] = $d->DRIVER;
	// 		$row[] = $d->GATE_IN;
	// 		$row[] = $d->GATE_OUT;
	// 		$row[] = $d->LASTCHANGE;
	// 		$row[] = $d->STATUS_SPPB;
	// 		$row[] = $d->STATUS;
	// 		$row[] = $d->DIRECTION;
	// 		$row[] = $d->NPE_EXPORT;
	// 		$row[] = $d->HOLD_EXPORT;
	// 		$row[] = $d->CARGO_EXPORT;
	// 		$row[] = $d->STATUS_NPE;
	// 		$row[] = $d->CARGO_IMPORT;
	// 		$row[] = $d->HOLD_IMPORT;
	// 		$row[] = $d->SPPB_IMPORT;
	// 		$data[] = $row;
	// 	}
	// 	$output = array(
	// 		"draw" => $_POST['draw'],
	// 		"recordsFiltered" => $mod->count_filtered_truck(),
	// 		"recordsTotal" => $mod->count_all_truck(),
	// 		"data" => $data,
	// 	);
	// 	echo json_encode($output);
	// }

	public function log_autogate()
	{
		$this->load->view('backend/pages/dashboard/log_autogate');
	}

	public function log_autogate_data()
	{
		$mod = model('log_autogate');

		$mod->set_db($this->get_db_log_autogate());

		$list = $mod->list_log_autogate();
		$data = array();
		foreach ($list as $d) {
			$row = array();
			$row[] = $d->LOG_TIME;
			$row[] = $d->GATE_TYPE;
			$row[] = $d->LICENSE_PLATE;
			$row[] = $d->CODE;
			$row[] = $d->MESSAGE;
			$row[] = $d->VISIT_ID;
			$data[] = $row;
		}
		$output = array(
			"draw" => $_POST['draw'],
			"recordsFiltered" => $mod->count_filtered_log_autogate(),
			"recordsTotal" => $mod->count_all_log_autogate(),
			"data" => $data,
		);
		echo json_encode($output);
	}
	public function export_truck()
	{
		$start = $this->input->get('start');
		$end = $this->input->get('end');


		$periode_start = (!empty($start)) ? date('d-M-Y', strtotime($start)) : date('d-M-Y', strtotime('-1 months'));
		$periode_end = (!empty($end)) ? date('d-M-Y', strtotime($end)) : date('d-M-Y');

		$this->load->model('truck2');
		// $data = $this->cargo2->list_cargo(null, null, null, null, null, null, $periode_start, $periode_end, 'export');
		// $data = $this->cargo2->list_cargo(null, null, null, null, null, null, $periode_start, $periode_end, 'export');

		$data = $this->truck2->list_truck(null, null, null, null, null, $start, $end, 'export');
		// print_r($data);
		// die();
		$params['data'] = $data;

		// $params['periode'] = $periode_start . " s/d " . $periode_end;
		// $contents = $this->load->view('backend/pages/dashboard/export_cargo', $params, TRUE);

		// header("Content-type: application/vnd-ms-excel");

		header('Content-type: application/csv');
		header("Content-Disposition: attachment; filename=rekap_data_truck.csv");

		$fp = fopen('php://output', 'w');

		$title1 = array('REKAPITULASI DATA TRUCK');
		fputcsv($fp, $title1);
		$title2 = array('Periode : ' . $periode_start . ' s/d ' . $periode_end);
		fputcsv($fp, $title2);
		$separator = array(' ');
		fputcsv($fp, $separator);

		$header = array(
			'Vin', 'Status', 'On Terminal', 'Loaded', 'Left',
			'Actual Position', 'Direction', 'Maker', 'Model', 'Jenis',
			'Consignee', 'Asal', 'Tujuan Terakhir', 'Vessel', 'Status Custom',
			'Visit ID (Vessel)', 'Custom Number', 'Custom Date'

		);
		fputcsv($fp, $header);
		foreach ($data as $obj) {
			fputcsv($fp, $obj);
		}
		fclose($fp);
		exit;
	}
}
