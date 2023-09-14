<?php

class Internasional_outbound extends CI_Controller {

    private $local_db;

    public function __construct() {
        parent::__construct();

        // Dapatkan data login
        if (!$this->auth = $this->userauth->getLoginData()) {
            redirect(LOGIN_PAGE);
        }
    }

    private function get_db() {
        if (!$this->local_db) {
            $this->local_db = $this->load->database(ILCS_TPS_ONLINE, TRUE);
            $this->local_db->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
            $this->local_db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");
        }

        return $this->local_db;
    }

    /**
     * Index
     */
    public function index() {
        redirect('tps_online/internasional_outbound/listview');
    }

    /**
     * Listview
     * Halaman utama modul delivery request, menampilkan daftar delivery request yang sudah pernah
     * dilakukan dan sebagai launcher untuk membuat delivery request baru ataupun tindakan-tindakan
     * lain terhadap delivery request yang sudah dilakukan.
     */
    public function listview() {
        $num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/internasional_outbound_model');
        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // Content Data
        $res = $mod->select($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);

        // Layout Data
        $data = array(
            'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datasource' => $res->datasource,
        );

        $this->load->view('backend/pages/tps_online/internasional_outbound/listview', $data);
    }

    public function listview_history() {
        $num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/internasional_outbound_model');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // Content Data
        $res = $mod->selectHistory($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);

        // Layout Data
        $data = array(
            'history' => true,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datasource' => $res->datasource,
        );

        $this->load->view('backend/pages/tps_online/internasional_outbound/listview', $data);
    }

    public function view($id = NULL) {
        $num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {
            $grid_state = 'tps_online/internasional_outbound/listview';
        }

        $db = $this->get_db();

        $mod = model('tps_online/internasional_outbound_model');
        $mod->set_db($db);

        $view = array(
            'grid_state' => $grid_state
        );

        if ($row = $mod->get($id)) {
            if (is_post_request()) {
                $this->load->library('form_validation');

                $val = $this->form_validation;
                //$val->set_rules('INWARD_BC11', 'Nomor Inward BC 1.1', 'required');
                //$val->set_rules('INWARD_BC11_DATE', 'Tanggal Inward BC 1.1', 'required');
                $val->set_rules('OUTWARD_BC11', 'Nomor Outward BC 1.1', 'required');
                $val->set_rules('OUTWARD_BC11_DATE', 'Tanggal Outward BC 1.1', 'required');
                $val->set_rules('LOAD_PORT', 'Load Port', 'required');
                $val->set_rules('TRANSIT_PORT', 'Transit Port', 'required');
                $val->set_rules('DISCHARGER_PORT', 'Discharge Port', 'required');
                //$val->set_rules('NEXT_PORT', 'Next Port', 'required');

                if ($val->run()) {
                    $mod->update($id);
                    $view['info_msg'] = 'Sukses edit data kunjungan kapal';
                } else {
                    $view['error_msg'] = validation_errors();
                }

                //$row->INWARD_BC11 = post('INWARD_BC11');
                //$row->INWARD_BC11_DATE = post('INWARD_BC11_DATE');
                $row->OUTWARD_BC11 = post('OUTWARD_BC11');
                $row->OUTWARD_BC11_DATE = post('OUTWARD_BC11_DATE');
                $row->LOAD_PORT = post('LOAD_PORT');
                $row->TRANSIT_PORT = post('TRANSIT_PORT');
                $row->DISCHARGER_PORT = post('DISCHARGER_PORT');
                //$row->NEXT_PORT = post('NEXT_PORT');
            }

            $view['kunjungan'] = $row;

            $this->load->view('backend/pages/tps_online/internasional_outbound/view', $view);
        } else {
            redirect('tps_online/internasional_outbound/listview/404');
        }
    }

    public function finalize($id = NULL) {
        $db = $this->get_db();

        $mod = model('tps_online/internasional_outbound_model');
        $kargo = model('tps_online/cargo_internasional_outbound_model');

        $mod->set_db($db);
        $kargo->set_db($db);

        $view = array(
        );

        if ($row = $mod->get($id)) {
            if (is_post_request()) {
                $this->load->library('form_validation');

                $val = $this->form_validation;
                $val->set_rules('INWARD_BC11', 'Nomor Inward BC 1.1', 'required');
                $val->set_rules('INWARD_BC11_DATE', 'Tanggal Inward BC 1.1', 'required');
                $val->set_rules('OUTWARD_BC11', 'Nomor Outward BC 1.1', 'required');
                $val->set_rules('OUTWARD_BC11_DATE', 'Tanggal Outward BC 1.1', 'required');
                $val->set_rules('LOAD_PORT', 'Load Port', 'required');
                $val->set_rules('TRANSIT_PORT', 'Transit Port', 'required');
                $val->set_rules('DISCHARGER_PORT', 'Discharge Port', 'required');
                $val->set_rules('NEXT_PORT', 'Next Port', 'required');

                if ($val->run()) {
                    $mod->update($id);
                    $view['info_msg'] = 'Sukses edit data kunjungan kapal';
                } else {
                    $view['error_msg'] = validation_errors();
                }

                $row->INWARD_BC11 = post('INWARD_BC11');
                $row->INWARD_BC11_DATE = post('INWARD_BC11_DATE');
                $row->OUTWARD_BC11 = post('OUTWARD_BC11');
                $row->OUTWARD_BC11_DATE = post('OUTWARD_BC11_DATE');
                $row->LOAD_PORT = post('LOAD_PORT');
                $row->TRANSIT_PORT = post('TRANSIT_PORT');
                $row->DISCHARGER_PORT = post('DISCHARGER_PORT');
                $row->NEXT_PORT = post('NEXT_PORT');
            }

            $view['kunjungan'] = $row;
            $view['unsent'] = $kargo->select_unsent($id);

            $this->load->view('backend/pages/tps_online/internasional_outbound/finalize', $view);
        } else {
            redirect('tps_online/internasional_outbound/listview/404');
        }
    }

    public function get($token = NULL) {
        if ($this->auth->token == $token) {
            $out = new StdClass();

            $where = array(
                'VISIT_ID' => post('VISIT_ID')
            );

            $db = $this->get_db();

            $data = $db->select('VISIT_ID, VISIT_NAME, ETA, ETD, LOAD_PORT, DISCHARGER_PORT')->where($where)->get('CARTOS_SHIP_VISIT')->row();

            if ($data) {
                $data->ETA = $data->ETA ? date('d-M-Y H:i', strtotime($data->ETA)) : '-';
                $data->ETD = $data->ETD ? date('d-M-Y H:i', strtotime($data->ETD)) : '-';

                $out->success = true;
                $out->datasource = $data;
            } else {
                $out->success = false;
                $out->msg = 'Tidak dapat menemukan Visit ID: ' . post('VISIT_ID');
            }

            echo json_encode($out);
        } else {
            echo 'INVALID TOKEN';
        }
    }

    public function summary_detail($visit_id)
    {
        $num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {
            $grid_state = 'tps_online/internasional_outbound/listview';
        }

        $where = array(
            'VISIT_ID' => $visit_id
        );

        $db = $this->get_db();

        $data = $db->select('VISIT_ID, VISIT_NAME, VOYAGE_IN, VOYAGE_OUT, ETA, ETD, LOAD_PORT, DISCHARGER_PORT, VESSEL_STATUS')->where($where)->get('CARTOS_SHIP_VISIT')->row();

        if ($data) {
            $data->ETA = $data->ETA ? date('d-M-Y H:i', strtotime($data->ETA)) : '-';
            $data->ETD = $data->ETD ? date('d-M-Y H:i', strtotime($data->ETD)) : '-';

            $mod = model('tps_online/internasional_outbound_model');

            $mod->set_db($db);
            $result = $mod->getSummaryDetail($visit_id);

            $cargo = array();
            if(count($result) > 0) {
                foreach($result as $obj) {
                    $cargo[$obj->BL_NUMBER]['databl'] = array(
                        'BL_NUMBER' => $obj->BL_NUMBER,
                        'BL_NUMBER_DATE' => $obj->BL_NUMBER_DATE,
                        'MASTER_BL_NUMBER' => $obj->MASTER_BL_NUMBER,
                        'MASTER_BL_NUMBER_DATE' => $obj->MASTER_BL_NUMBER_DATE,
                        'CUSTOMS_NUMBER' => $obj->CUSTOMS_NUMBER
                    );

                    $cargo[$obj->BL_NUMBER]['datakargo'][] = $obj;
                }
            }

            // print_r($cargo);die;
            $params['cargo'] = $cargo;
            $params['grid_state'] = $grid_state;
            $params['data'] = $data;
            $this->load->view('backend/pages/tps_online/internasional_outbound/summary_detail', $params);
        } else {
            redirect('tps_online/internasional_outbound/listview/404');
        }
    }

}
