<?php

class Kargo_internasional_outbound extends CI_Controller {

    private $local_db;

    public function __construct() {
        parent::__construct();

        // Dapatkan data login
        if (!$this->auth = $this->userauth->getLoginData()) {
            redirect(LOGIN_PAGE);
        }
    }

    private function get_db() {
        if (!$this->local_db)
            $this->local_db = $this->load->database(ILCS_TPS_ONLINE, TRUE);
        $this->local_db->query("ALTER SESSION SET NLS_TIMESTAMP_FORMAT = 'YYYY-MM-DD HH24:MI:SS'");
        $this->local_db->query("ALTER SESSION SET NLS_DATE_FORMAT = 'YYYY-MM-DD'");

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
        $mod = model('tps_online/kargo_internasional_outbound_model');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // Content Data
        $res = $mod->select($this->auth->id,$get_args,$this->get_db());
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);

        //echo $res->actualRows;
        //print_r ($res);
        //die;
        // Layout Data
        $data = array(
            'history' => false,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datasource' => $res->datasource
        );

        $this->load->view('backend/pages/tps_online/kargo_internasional_outbound/listview', $data);
    }

    public function listview_history() {
        $num_args = func_num_args();
        $get_args = func_get_args();

        // Load Model & Parsing Parameter untuk sorting, searching dan paging
        $mod = model('tps_online/kargo_internasional_outbound_model');

        $mod->set_db($this->get_db());

        $cfg = $mod->parseParameter($num_args, $get_args);

        // Apply Config
        $mod->terapkanConfig($cfg);

        // Content Data
        $res = $mod->selectHistory($this->auth->id);
        $cfg->totalPage = (int) ceil($res->actualRows / $cfg->rowPerPage);

        //echo $res->actualRows;
        //print_r ($res);
        //die;
        // Layout Data
        $data = array(
            'history' => true,
            'cfg' => $cfg,
            'searchable' => $mod->searchable,
            'sortable' => $mod->sortable,
            'datasource' => $res->datasource
        );

        $this->load->view('backend/pages/tps_online/kargo_internasional_outbound/listview', $data);
    }

    public function add() {
        $db = $this->get_db();

        $mod = model('manifest/manifest_model');
        $mod->set_db($db);

        $view = array();

        if (is_post_request()) {
            $parser = model('manifest/parser/excel_parser_manifest');
            $result = $parser->parse('manifest_file');

            // Enhance Result
            $auth = $this->auth;
            $result->user_id = $auth->id;
            $result->username = $auth->username;
            $result->cuscar_request_id = uniqid();

            if ($result->status) {
                $thrower = model('manifest/api/edifact_message_hub');
                $throw_result = $thrower->throw_cuscar($result);

                if ($throw_result->success) {
                    $result->generated_filename = uniqid();
                    move_uploaded_file($_FILES['manifest_file']['tmp_name'], MANIFEST_FILE_STORE_BASE . '/' . $result->generated_filename);

                    $mod->insert($result);

                    $view['success_msg'] = 'Sukses kirim file';
                } else {
                    $view['error_header'] = 'Kami Sudah Berusaha :(';
                    $view['error_msg'] = $throw_result->msg;
                }
            } else {
                if (isset($result->parser_error)) {
                    if (isset($result->error_header)) {
                        $view['error_header'] = $result->error_header;
                    }
                    $view['parser_error'] = $result->parser_error;
                } else {
                    $view['error_msg'] = 'Kami harus memastikan datanya memenuhi standar. Harap perbaiki hal-hal yang tercantum dalam rincian.';
                }
            }

            $view['result'] = $result;
        }

        $this->load->helper('inflector');
        $this->load->view('backend/pages/tps_online/internasional_outbound/add', $view);
    }

    public function view($id = NULL) {
        $num_args = func_num_args();
        $get_args = func_get_args();

        $grid_state = '';
        for ($i = 1; $i < $num_args; $i++) {
            $grid_state .= $get_args[$i] . '/';
        }

        if (!$grid_state) {
            $grid_state = 'tps_online/kargo_internasional_outbound/listview';
        }

        $db = $this->get_db();

        $mod = model('tps_online/kargo_internasional_outbound_model');
        $mod->set_db($db);

        $view = array(
            'grid_state' => $grid_state
        );

        $row = $mod->get($id);
        //print_r ($row);
        //ei
        if ($row = $mod->get($id)) {
            $view['kargo'] = $row;

            $this->load->view('backend/pages/tps_online/kargo_internasional_outbound/view', $view);
        } else {
            redirect('tps_online/kargo_internasional_outbound/listview/404');
        }
    }

    public function unduh_manifest($id) {
        $db = $this->get_db();

        $mod = model('manifest/manifest_model');
        $mod->set_db($db);

        if ($row = $mod->get($id, $this->auth->id)) {
            header("Content-disposition: attachment; filename=\"" . $row->nama_file_asli . '"');
            header("Content-type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
            readfile(MANIFEST_FILE_STORE_BASE . '/' . $row->filename);
        } else {
            redirect('tps_online/kunjungan_kapal/listview/405');
        }
    }

    public function test() {
        // Array Access Time
        define('SPREAD', 100000000);

        $start = microtime(true);

        $x = 0;
        $stack = array();
        for ($i = 0; $i < SPREAD; $i++) {
            $x = $x + 2;
        }

        $end = microtime(true);

        echo '<p>TIME1:' . round((($end - $start) * 1000)) . 'ms';
        return;

        // Container Number Checking
        $mod = model('manifest/manifest_model');
        $container_number = 'CSQU3054387';

        $targets = array();
        for ($i = 0; $i < 10; $i++) {
            $targets[rand(0, SPREAD - 1)] = true;
        }

        $start = microtime(true);

        $stack = array();
        for ($i = 0; $i < SPREAD; $i++) {
            $string = '';

            for ($j = 0; $j < 5; $j++) {
                // this numbers refer to numbers of the ascii table (lower case)
                $string .= chr(rand(97, 122));
            }

            if (isset($targets[$i])) {
                $targets[$i] = $string;
            }

            $stack[$string] = true;
        }

        $end = microtime(true);

        echo '<p>TIME1:' . round((($end - $start) * 1000)) . 'ms';

        $start = microtime(true);

        foreach ($targets as $key => $val) {
            echo '<h2>' . $stack[$val];
        }

        $end = microtime(true);

        echo '<p>TIME2:' . round((($end - $start) * 1000)) . 'ms';
    }

    public function test_curl() {
        //extract data from the post
        extract($_POST);

        //set POST variables
        $url = 'http://domain.com/get-post.php';
        $fields = array(
            'lname' => urlencode($last_name),
            'fname' => urlencode($first_name),
            'title' => urlencode($title),
            'company' => urlencode($institution),
            'age' => urlencode($age),
            'email' => urlencode($email),
            'phone' => urlencode($phone)
        );

        //url-ify the data for the POST
        foreach ($fields as $key => $value) {
            $fields_string .= $key . '=' . $value . '&';
        }
        rtrim($fields_string, '&');

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);

        //execute post
        $result = curl_exec($ch);

        //close connection
        curl_close($ch);
    }

    public function time_test() {
        // echo date_default_timezone_get();exit();

        echo '<h2>TZ DEFAULT</h2>';
        echo gmdate('Y-m-d H:i:s', 0);
        echo '<BR>';
        echo date('Y-m-d H:i:s', 0);


        date_default_timezone_set('UTC');

        echo '<h2>TIMEZONE CHANGED</h2>';
        echo gmdate('Y-m-d H:i:s', 0);
        echo '<BR>';
        echo date('Y-m-d H:i:s', 0);
    }

    public function test_soap() {
        $mod = model('manifest/api/edifact_message_hub');
        $test = new StdClass();
        $mod->throw_cuscar($test);
    }

}
