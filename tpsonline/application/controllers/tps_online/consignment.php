<?php
class Consignment extends CI_Controller
{
	private $local_db;

	public function __construct()
	{
		parent::__construct();
		// Dapatkan data login
		// if(!$this->auth = $this->userauth->getLoginData()){
		// 	redirect(LOGIN_PAGE);
		// }

		$this->load->library('logger');
	}

	private function get_db()
	{
		if (!$this->auth = $this->userauth->getLoginData()) {
			redirect(LOGIN_PAGE);
		}
		if (!$this->local_db) {
			$this->local_db = $this->load->database(ILCS_TPS_ONLINE, TRUE);
			$this->local_db->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
			$this->local_db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
		}

		return $this->local_db;
	}

	private function get_ctos()
	{
		if (!$this->auth = $this->userauth->getLoginData()) {
			redirect(LOGIN_PAGE);
		}
		if (!$this->local_db) {
			$this->local_db = $this->load->database(ILCS_CTOS_QAS, TRUE);
		}

		return $this->local_db;
	}

	/**
	 * Index
	 */
	public function index()
	{
		if (!$this->auth = $this->userauth->getLoginData()) {
			redirect(LOGIN_PAGE);
		}
		redirect('tps_online/consignment/listview');
	}

	public function pdfGateOut($visitID)
	{
		$this->load->library('M_pdf');
		$mod = model('etickets');
		$res = $mod->getGateOut($visitID);
		$datas = $mod->getEntryTicketInfo($visitID);
		$count['COUNT'] = strval(count($res));

		if ($datas) {
			foreach ($res as $row) {
				$gateOut[] = $row;
			}
		}

		$data = array(
			'vehicles' => $count,
			'inform'   => $datas ? $datas[0] : null,
			'title'    => $visitID,
			'gateOut'  => $gateOut
		);

		ini_set('memory_limit', '256M');
		$html = $this->load->view('backend/reports/getPdf/gate_out', $data, true);
		$this->m_pdf->pdf->WriteHTML($html);
		$output = $datas[0]->TNR . '.pdf';
		$this->m_pdf->pdf->Output($output, "I");
		$this->logger
			->user($this->userauth->getLoginData()->username)
			->function_name($this->router->fetch_method())
			->comment('print ' . $visitID)
			->log();
	}

	public function downloadPdf($TRKVisitID)
	{
		if (!$this->auth = $this->userauth->getLoginData()) {
			redirect(LOGIN_PAGE);
		}
		$this->load->library('M_pdf');
		$mod = model('etickets');
		$res = $mod->getAsosiasiByTruckVisitID($TRKVisitID);

		$import = [];
		$export = [];
		$datas = $mod->getEntryTicketInfo($TRKVisitID);

		if ($datas) {
			if ($datas[0]->DESCRIPTION == 'SELFDRIVE') {
				foreach ($res as $item) {
					foreach ($res as $item) {
						if ($item->DIRECTION == '1') {
							array_push($import, $item);
						} else {
							array_push($export, $item);
						}
					}
				}
			} else {
				$imp = $mod->getImport($TRKVisitID);
				foreach ($imp as $items) {
					array_push($import, $items);
				}
				foreach ($res as $item) {
					if ($item->DIRECTION != '1') {
						array_push($export, $item);
					}
				}
			}
		}

		$data = array(
			'inform' => $datas ? $datas[0] : null,
			'title' => $TRKVisitID,
			'imports' => $import,
			'exports' => $export
		);

		ini_set('memory_limit', '256M');
		$html = $this->load->view('backend/reports/getPdf/entry_ticket', $data, true);
		$this->m_pdf->pdf->WriteHTML($html);
		$output = $datas[0]->TNR . '.pdf';
		$this->m_pdf->pdf->Output($output, "I");
		$this->logger
			->user($this->userauth->getLoginData()->username)
			->function_name($this->router->fetch_method())
			->comment('print ' . $TRKVisitID)
			->log();
	}

	public function downloadPdf_public($TRKVisitID)
	{
		$this->load->library('M_pdf');

		$mod = model('etickets');
		$res = $mod->getAsosiasiByTruckVisitID($TRKVisitID);

		$import = [];
		$export = [];
		$datas = $mod->getEntryTicketInfo($TRKVisitID);

		if ($datas) {
			if ($datas[0]->DESCRIPTION == 'SELFDRIVE') {
				foreach ($res as $item) {
					foreach ($res as $item) {
						if ($item->DIRECTION == '1') {
							array_push($import, $item);
						} else {
							array_push($export, $item);
						}
					}
				}
			} else {
				$imp = $mod->getImport($TRKVisitID);
				foreach ($imp as $items) {
					array_push($import, $items);
				}
				foreach ($res as $item) {
					if ($item->DIRECTION != '1') {
						array_push($export, $item);
					}
				}
			}
		}


		$data = array(
			'inform' => $datas ? $datas[0] : null,
			'title' => $TRKVisitID,
			'imports' => $import,
			'exports' => $export
		);

		ini_set('memory_limit', '256M');
		$html = $this->load->view('backend/reports/getPdf/entry_ticket', $data, true);
		$this->m_pdf->pdf->WriteHTML($html);
		$output = $datas[0]->TNR . '.pdf';
		$this->m_pdf->pdf->Output($output, "I");
		$this->logger
			->user($this->userauth->getLoginData()->username)
			->function_name($this->router->fetch_method())
			->comment('print ' . $TRKVisitID)
			->log();
	}

	/**
	 * Listviews
	 * Halaman utama modul delivery request, menampilkan daftar delivery request yang sudah pernah
	 * dilakukan dan sebagai launcher untuk membuat delivery request baru ataupun tindakan-tindakan
	 * lain terhadap delivery request yang sudah dilakukan.
	 */
	public function listview()
	{
		if (!$this->auth = $this->userauth->getLoginData()) {
			redirect(LOGIN_PAGE);
		}
		$num_args = func_num_args();
		$get_args = func_get_args();

		// Load Model & Parsing Parameter untuk sorting, searching dan paging
		$mod = model('tps_online/kargo_model');

		$mod->set_db($this->get_db());

		$cfg = $mod->parseParameter($num_args, $get_args);

		// Apply Config
		$mod->terapkanConfig($cfg);

		// Content Data
		$res = $mod->select($this->auth->id);
		$cfg->totalPage		= (int) ceil($res->actualRows / $cfg->rowPerPage);

		// Layout Data
		$data = array(
			'cfg' => $cfg,
			'searchable' => $mod->searchable,
			'sortable' => $mod->sortable,
			'datasource' => $res->datasource
		);

		$this->load->view('backend/pages/tps_online/kargo/listview', $data);
	}

	public function assign_bl($visit_id = NULL)
	{
		if (!$this->auth = $this->userauth->getLoginData()) {
			redirect(LOGIN_PAGE);
		}
		$db = $this->get_db();

		$kunjungan_kapal = model('tps_online/kunjungan_kapal_model');
		$kunjungan_kapal->set_db($db);

		$data = array(
			'VISIT_ID' => $visit_id,
			'VISIT_ID_DS' => $kunjungan_kapal->select_ds(array('FLAG_SEND' => 0)),
			'TYPE_CARGO_DS' => $kunjungan_kapal->select_type_cargo(array('STATUS' => 'Y'))
		);
		$this->load->view('backend/pages/tps_online/consignment/assign_bl', $data);
	}

	public function get_visit_id($token, $chars)
	{
		if (!$this->auth = $this->userauth->getLoginData()) {
			redirect(LOGIN_PAGE);
		}
		$db = $this->get_db();

		$kunjungan_kapal = model('tps_online/kunjungan_kapal_model');
		$kunjungan_kapal->set_db($db);

		$chars = str_replace('%20', ' ', $chars);
		$params = "FLAG_SEND = 0 and (VISIT_ID like '%$chars')";
		$data = $kunjungan_kapal->select_ds($params);

		$datasource = array();
		foreach ($data as $d) {
			$datasource[] = $d->VISIT_ID . '-' . $d->VISIT_NAME;
		}

		echo json_encode($datasource);
	}


	public function get_pelanggan($token, $chars)
	{
		if (!$this->auth = $this->userauth->getLoginData()) {
			redirect(LOGIN_PAGE);
		}
		$db = $this->get_db();
		$datasource = array();
		$chars = str_replace('%20', ' ', $chars);
		$data = $db->select('ID')->select('NAMA_PERUSAHAAN')->like('NAMA_PERUSAHAAN', $chars)->where('FLAG_DELETED', 0)->limit(20)->get('MST_PELANGGAN')->result();

		foreach ($data as $d) {
			$datasource[] = $d->ID . '-' . $d->NAMA_PERUSAHAAN;
		}

		echo json_encode($datasource);
	}

	public function get_npwp($token, $chars)
	{
		if (!$this->auth = $this->userauth->getLoginData()) {
			redirect(LOGIN_PAGE);
		}
		$db = $this->get_db();
		$datasource = array();
		$data = $db->select('NPWP')->where('ID', $chars)->get('MST_PELANGGAN')->result();

		foreach ($data as $d) {
			$datasource[] = $d->NPWP;
		}

		echo json_encode($datasource);
	}

	public function get_npe($token)
	{
		if (!$this->auth = $this->userauth->getLoginData()) {
			redirect(LOGIN_PAGE);
		}

		$out = new StdClass();
		$db = $this->get_db();

		$NPWP = preg_replace('/[^0-9]/', '', post('NPWP'));
		$CUSTOMS_NUMBER = post('CUSTOMS_NUMBER');
		$CUSTOMS_DATE = post('CUSTOMS_DATE');
		$datasource = array();

		if (count(array_unique($CUSTOMS_NUMBER)) === 1 && count(array_unique($CUSTOMS_DATE)) === 1) {

			libxml_disable_entity_loader(false);
			$soapclient = new SoapClient('http://103.29.187.72:80/tps_test/tpsService?wsdl', array(
				'location' => 'http://103.29.187.72:80/tps_test/tpsService',
				'exceptions' => true,
				'cache_wsdl' => WSDL_CACHE_NONE,
				'trace' => 1
			));

			$params = array(
				"Username" => "CART",
				"Password" => "CARTERMINAL",
				"No_PEB" => $CUSTOMS_NUMBER[0],
				"Tgl_PEB" => date("dmY", strtotime($CUSTOMS_DATE[0])),
				"npwp" => $NPWP
			);



			$response = $soapclient->GetEkspor_PEB($params);

			$xml_return = simplexml_load_string((string)$response->return);
			//print_r($xml_return);
			if (isset($xml_return->NPE)) {
				$out->success = true;
				$out->datasource =  $xml_return->NPE->HEADER;
			} else {
				$out->success = false;
				$message = (string) $xml_return;
				$out->errors =  array($message);
			}
		} else {
			$out->success = false;
			$out->errors = array('Nomor atau Tanggal PEB Tidak sama');
		}
		echo @json_encode($out);
	}




	public function get_bulk_vin($token)
	{
		if (!$this->auth = $this->userauth->getLoginData()) {
			redirect(LOGIN_PAGE);
		}
		if ($this->auth->token == $token) {
			$out = new StdClass();

			$VISIT_ID = post('VISIT_ID');
			$VIN = post('VIN');
			$vins = array_unique(explode(" ", str_replace("\n", ' ', $VIN)));

			$db = $this->get_db();

			$data = $db->select('VIN, VISIT_ID, MODEL_NAME, MAKE_NAME, DIRECTION, CUSTOMS_NUMBER, CUSTOMS_DATE')
				->where_in('VIN', $vins)
				->where('VISIT_ID', $VISIT_ID)
				->get('CARTOS_CARGO')->result(); //->result()
			// echo "<pre>";
			// print_r($db->last_query());
			// ->where('VISIT_ID', $VISIT_ID)
			if ($data) {
				$errors = array();
				$founded = array();
				$datasource = array();

				foreach ($data as $row) {
					$founded[] = $row->VIN;

					if ($row->VISIT_ID == $VISIT_ID || $row->VISIT_ID != NULL) {
						$datasource[] = $row;
					} else {
						$errors[] = 'VIN ' . $row->VIN . ' tidak ada dalam VISIT_ID ' . $VISIT_ID . ' silakan cek kembali';
					}
				}
				// echo "<pre>";
				// print_r($datasource);die();

				if (count($vins) == count($datasource)) {
					$out->success = true;
					$out->datasource = $datasource;
				} else {
					foreach ($vins as $vin) {
						if (!in_array($vin, $founded)) {
							$errors[] = 'VIN : <b>' . $vin . '</b> tidak ditemukan dalam VISIT ID : <b>' . $row->VISIT_ID . '</b>';
							//$row->VIN                  //dalam database;
						}
					}
					// echo "<pre>";
					// print_r($datasource);
					// print_r($vins);
					// print_r($errors);die();
					$out->success = false;
					$out->errors = $errors;
					$out->datasource = $datasource;
				}
			} else {
				$out->success = false;
				$out->errors = array('Tidak ada satupun VIN yang ditemukan. Cek kembali VISIT ID dan VIN yang anda masukkan.');
			}

			echo @json_encode($out);
		} else {
			echo 'INVALID TOKEN';
		}
	}

	public function get_vin($token)
	{
		if (!$this->auth = $this->userauth->getLoginData()) {
			redirect(LOGIN_PAGE);
		}
		if ($this->auth->token == $token) {
			$out = new StdClass();

			$VISIT_ID = post('VISIT_ID');
			$VIN = post('VIN');


			$where = array(
				'VIN' => substr($VIN, 0, 128),
				'VISIT_ID' => $VISIT_ID
			);
			$db = $this->get_db();
			$data = $db->select('VIN, VISIT_ID, MODEL_NAME, MAKE_NAME, DIRECTION, CUSTOMS_NUMBER, CUSTOMS_DATE')
				->where($where)
				->get('CARTOS_CARGO')->row();
			if ($data) {
				if ($data->VISIT_ID != NULL || $data->VISIT_ID == $VISIT_ID) {
					$out->success = true;
					$out->datasource = $data;
				} else {
					$out->success = false;
					$out->msg = 'VIN ' . $VIN . ' tidak ada dalam VISIT_ID ' . $VISIT_ID . ' silakan cek kembali';
				}
			} else {
				$out->success = false;
				$out->msg = 'Data VIN yang anda cari tidak ada. Cek VISIT ID dan VIN yang anda masukkan.';
			}

			echo @json_encode($out);
		} else {
			echo 'INVALID TOKEN';
		}
	}

	public function simpan($token)
	{
		if (!$this->auth = $this->userauth->getLoginData()) {
			redirect(LOGIN_PAGE);
		}
		if ($this->auth->token == $token) {
			$out = new StdClass();

			//if(is_post_request()){
			$this->load->library('form_validation');

			$val = $this->form_validation;
			$val->set_rules('VISIT_ID', 'Visit ID', 'required');
			// $val->set_rules('BL_NUMBER', 'Nomor BL', 'required');
			// $val->set_rules('BL_NUMBER_DATE', 'Tanggal BL', 'required');
			$val->set_rules('BL_NUMBER', 'Nomor Master BL', 'required');
			$val->set_rules('BL_NUMBER_DATE', 'Tanggal Master BL', 'required');
			$val->set_rules('HOUSE_BL_NUMBER', 'Nomor House BL', 'required');
			$val->set_rules('HOUSE_BL_NUMBER_DATE', 'Tanggal House BL', 'required');

			$val->set_rules('TYPE_CARGO', 'Jenis Cargo', 'required');
			$val->set_rules('BRUTO', 'Bruto Cargo');
			$val->set_rules('JUMLAH', 'Jumlah', 'required');
			$val->set_rules('VIN[]', 'VIN', 'required');

			if ($val->run()) {
				$db = $this->get_db();

				$vin = post('VIN');
				$visitid = post('VISIT_ID');
				$str = post('NPE_DATE');
				//$upd = array();
				$bl_number =  post('BL_NUMBER');



				//echo $date->format('Y-m-d'); // => 2013-12-24
				/**
						,
							'NO_NPE' => post('NO_NPE'),
							'NPE_DATE' => $tanggal_npe
				 */

				if (is_array($vin)) {
					$upd = array(
						// 'BL_NUMBER' => post('BL_NUMBER'),
						// 'BL_NUMBER_DATE' => date('Y-m-d', strtotime(post('BL_NUMBER_DATE'))),
						'BL_NUMBER' => trim($bl_number, " "),
						'BL_NUMBER_DATE' => date('Y-m-d', strtotime(post('BL_NUMBER_DATE'))),
						'HOUSE_BL_NUMBER' => trim(post('HOUSE_BL_NUMBER'), " "),
						'HOUSE_BL_NUMBER_DATE' => date('Y-m-d', strtotime(post('HOUSE_BL_NUMBER_DATE'))),

						'DTS_SET_CONSIGNMENT' => date('Y-m-d H:i:s'),
						'VISIT_ID' => post('VISIT_ID'),
						'NO_NPE' => post('NO_NPE'),
						'FLAG_SEND_CODECO' => 0,
						'FLAG_SEND_COARRI' => 0,
						'DATE_SEND_CODECO' => null,
						'DATE_SEND_COARRI' => null
					);

					if (!empty($str)) {

						$tgl_npe = DateTime::createFromFormat('Ymd', $str);

						$tanggal_npe = $tgl_npe->format('Y-m-d');
						$upd['NPE_DATE'] = date('Y-m-d', strtotime($tanggal_npe));
					}

					$db->trans_start();

					$vin_berhasil = array();
					$vin_gagal 	 = array();

					$count_update = 0;
					$gagal_update = 0;


					foreach ($vin as $key => $item) {

						$row = $db->where('VIN', $item)
							->where('VISIT_ID', post('VISIT_ID'))
							->get('CARTOS_CARGO')->row();

						if ($row) {

							$vin_berhasil[] = $item;

							$multipleWhere = array(
								'VIN'	  => $item,
								'VISIT_ID' => post('VISIT_ID')
							);

							$db->where($multipleWhere)->update('CARTOS_CARGO', $upd);

							$count_update++;
						} else {
							$vin_gagal[] = $item;
							$gagal_update++;
						}
					}


					// foreach($vin as $item){
					// 	if($row = $db->where('VIN', $item)->get('CARTOS_CARGO')->row()){
					// 		if($row->VISIT_ID == post('VISIT_ID') || $row->VISIT_ID == NULL || $row->VISIT_ID == ''){
					// 			$db->where('VIN', $item)->update('CARTOS_CARGO', $upd);
					// 		}
					// 	}
					// }					

					$bruto = post('BRUTO');

					$insrt = array(
						// 'BL_NUMBER' => post('BL_NUMBER'),
						'BL_NUMBER' => post('BL_NUMBER'),
						'HOUSE_BL_NUMBER' => post('HOUSE_BL_NUMBER'),

						'CUSTOMS_CARGO_CODE' => post('TYPE_CARGO'),
						'BRUTO' => preg_replace("/\D/", "", $bruto),
						// 'JUMLAH' => post('JUMLAH'),
						// 'RECORD_TIME' => date('Y-m-d H:i:s'),
						'RECORD_TIME' => date('Y-m-d H:i:s'),
						'JUMLAH' => post('JUMLAH')
					);
					$db->insert('BL_CARGO_TYPE_MAPPING', $insrt);

					$db->trans_complete();

					if ($db->trans_status()) {

						$data = $db->select('BL_NUMBER, STATUS_HOLD, STATUS_RELEASE')
							->where('BL_NUMBER', post('BL_NUMBER'))
							->where('STATUS_HOLD', 1)
							->where('STATUS_RELEASE', 0)
							->get('ATENSI_P2')->result();
							$xml_return = null;

							if(count($data) > 0){
								libxml_disable_entity_loader(false);
								$soapclient = new SoapClient(Report_Cartos_Autogate.'?wsdl', array(
									'location' => Report_Cartos_Autogate,
									'exceptions' => true,
									'cache_wsdl' => WSDL_CACHE_NONE,
									'trace' => 1,
								));

								$params = array(
									"username" => 'intapps_'.$this->userauth->getLoginData()->username,
									"password" => "admin@ilcs",
									"trip" => "IMPORT",
									"houseBL" => post('HOUSE_BL_NUMBER'),
								);

								$response = $soapclient->op_CreateHoldP2($params);
							}
							
						$out->success = true;
						$out->msg = '<b> VIN Berhasil update   : </b>' . $count_update . ' Pada BL Number : ' . $bl_number .
							'<br>' . implode(' , ', $vin_berhasil) .
							'<br><br><b> VIN Gagal Update  : </b>' . $gagal_update . ' Pada BL Number  : ' . $bl_number .
							'<br>' . implode(' , ', $vin_gagal).
							'<br><br><b>P2 HOLD</b>'.
							'<br><br><b> Total Vin Success Hold  : </b>' . $response->TotalVinSuccessHold.
							'<br><br><b> Total Vin Failed Hold  : </b>' . $response->TotalVinFailHold
							;
					} else {
						$out->success = false;
						$out->msg = 'Gagal input ke database, tidak ada data yang di update';
					}
				} else {
					$out->success = false;
					$out->msg = 'VIN harus berupa array';
				}
			} else {
				$out->success = false;
				$out->msg = validation_errors();
			}
			/**}else{
				$out->success = false;
				$out->msg = 'Anda harus menggunakan POST request';
			}*/

			echo @json_encode($out);
		} else {
			echo 'INVALID TOKEN';
		}
	}
}
