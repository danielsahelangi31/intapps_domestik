<?php
//require_once('./application/models/base/modelbase.php');

// class Model_dashboard extends ModelBase{
class Model_dashboard extends CI_Model {

    private $_db;
  
        // Datagrid Sortable Fields
        public $sortable = array(
            'VESSEL_NAME' => 'Nama Kapal',
            'VOY_IN' => 'VOY_IN',
            'VOY_OUT' => 'Voyage Out',
            'CALL_SIGN' => 'Call Sign',     
    //        'VOYAGE_OUT' => 'Voyage Out',
            'ETA' => 'Estimated Time Arrival (mm-yyyy)',
            'ETD' => 'Estimated Time Time Departure (mm-yyyy)',
    //        'ETD' => 'Estimated Time Departure',
            //'VISIT_DIRECTION' => 'Visit Direction',
            // 'VESSEL_STATUS' => 'Visit Status',
        );
        // Datagrid Searchable Fields
        public $searchable = array(
            'VESSEL_NAME' => 'Nama Kapal',
            'VOY_IN' => 'VOY_IN',
            'VOY_OUT' => 'Voyage Out',
            'CALL_SIGN' => 'Call Sign',     
    //        'VOYAGE_OUT' => 'Voyage Out',
            'ETA' => 'Estimated Time Arrival (mm-yyyy)',
            'ETD' => 'Estimated Time Time Departure (mm-yyyy)',
    //        'ETD' => 'Estimated Time Departure',
            //'VISIT_DIRECTION' => 'Visit Direction',
        //     'VESSEL_STATUS' => 'Visit Status',
         );

    public function __construct() {
        parent::__construct();
        $this->_db = $this->load->database(ILCS_LOG_AUTOGATE, TRUE);
    }

    public function getAllVisit(){
        $con = $this->load->database('ilcs_cartos', TRUE);
        $data = $con->get('CARTOS_SHIP_VISIT')->result_array();

        return $data;
    }
    
    public function select_ds($where = array()){
        $con = $this->load->database('ilcs_cartos', TRUE);
    
        $datasource = $con->select('VISIT_ID, VISIT_NAME')->where($where)->where('ETA IS NOT NULL', NULL, FALSE)->like('VISIT_ID', 'VES', 'AFTER')->order_by("ETA", "desc")->get('CARTOS_SHIP_VISIT')->result();
    
        return $datasource;
    }
    
    public function insert($result){
		$this->_db->trans_start();
		
		
		
		$this->_db->trans_complete();
		
		return $this->_db->trans_status();
	}  		


    public function select_type_cargo($where = array()){
        $con = $this->load->database('ilcs_cartos', TRUE);
    
        $datasource = $con->select('CUSTOMS_CODE, DESCRIPTION')->where($where)->get('CUSTOMS_TYPE_MAPPING')->result();
    
        return $datasource;
    }
    
    public function getVisitKapal($where){
        $con = $this->load->database('ilcs_cartos', TRUE);
        $query = "SELECT  
                VISIT_NAME, VOYAGE_IN, INWARD_BC11, OUTWARD_BC11, 
                to_char(ARRIVAL,'dd-mm-yyyy') ARRIVAL, to_char(operational,'dd-mm-yyyy') OPERATIONAL, 
                to_char(COMPLETION,'dd-mm-yyyy') COMPLETION, to_char(DEPARTURE,'dd-mm-yyyy') DEPARTURE, count(distinct BL_NUMBER) JML_BL
                from CARTOS_SHIP_VISIT a join CARTOS_CARGO b on a.VISIT_ID = b.VISIT_ID
                where a.VISIT_ID = '$where'
                group by VISIT_NAME, VOYAGE_IN, INWARD_BC11, OUTWARD_BC11, ARRIVAL, OPERATIONAL, COMPLETION, DEPARTURE";

        $data = $con->query($query)->row();

        return $data;
    }

    public function getVINEI($where){
        $con = $this->load->database('ilcs_cartos', TRUE);
        $query = "SELECT EXPORT, IMPORT, SUM(JML_EX) JML_EX, SUM(JML_IM) JML_IM FROM(SELECT 
                CASE WHEN DIRECTION = 'EXPORT' then DIRECTION else 'EXPORT' end EXPORT,
                CASE WHEN DIRECTION = 'IMPORT' then DIRECTION else 'IMPORT' end IMPORT,
                CASE WHEN DIRECTION = 'EXPORT' then JML end JML_EX,
                CASE WHEN DIRECTION = 'IMPORT' then JML end JML_IM
                FROM (SELECT 
                                decode(DIRECTION,1,'IMPORT',2,'EXPORT','NOT DEFINED') DIRECTION, count(*) JML 
                                from CODECO_COARRI@ctos_link where  (visit_id_2 = '$where' or visit_id_1 = '$where')
                                and DIRECTION in (1,2) and trunc(DTS_ANNOUNCED) between trunc(sysdate-60) and trunc(sysdate)
                                group by decode(DIRECTION,1,'IMPORT',2,'EXPORT','NOT DEFINED')))
                                group by EXPORT, IMPORT";

        $data = $con->query($query)->row();

        return $data;
    }

    public function getTypeVin($where){
        $con = $this->load->database('ilcs_cartos', TRUE);
        $query = "SELECT HH, CBU, PARTS, SUM(JML_HH) JML_HH, sum(JML_CBU) JML_CBU, sum(JML_PARTS) JML_PARTS FROM (SELECT
                CASE WHEN TYPE_CARGO = 'HH' then TYPE_CARGO else 'HH' end HH,
                CASE WHEN TYPE_CARGO = 'CBU' then TYPE_CARGO else 'CBU' end CBU,
                CASE WHEN TYPE_CARGO = 'PARTS' then TYPE_CARGO else 'PARTS' end PARTS,
                CASE WHEN TYPE_CARGO = 'HH' then JML end JML_HH,
                CASE WHEN TYPE_CARGO = 'CBU' then JML end JML_CBU,
                CASE WHEN TYPE_CARGO = 'PARTS' then JML end JML_PARTS
                FROM(SELECT
                decode(TYPE,'CBU','CBU','HH','HH','PARTS','PARTS','KEMASAN') TYPE_CARGO, count(*) JML 
                from CODECO_COARRI@ctos_link where (VISIT_ID_2 = '$where' or VISIT_ID_1 = '$where')
                and direction in (1,2) and trunc(DTS_ANNOUNCED) between trunc(sysdate-60) and trunc(sysdate)
                group by decode(TYPE,'CBU','CBU','HH','HH','PARTS','PARTS','KEMASAN')))
                group by HH, CBU, PARTS";

        $data = $con->query($query)->row();

        return $data;
    }

    public function getNPE($where){
        $con = $this->load->database('ilcs_cartos', TRUE);
        $query = "SELECT NPE, NON_NPE, SUM(nvl(JML_NPE,0)) JML_NPE, SUM(nvl(JML_NON,0)) JML_NON FROM (SELECT 
                CASE WHEN NPE = 'NPE' THEN NPE ELSE 'NPE' end NPE,
                CASE WHEN NPE = 'NON NPE' THEN NPE ELSE 'NON_NPE' end NON_NPE,
                CASE WHEN NPE = 'NPE' THEN JML end JML_NPE,
                CASE WHEN NPE = 'NON NPE' THEN JML end JML_NON
                FROM (SELECT
                case when NO_NPE is not null then 'NPE' else 'NON NPE' end npe, count(*) JML 
                from CARTOS_CARGO where (VISIT_ID = '$where')
                and DIRECTION in (1,2) and trunc(DTS_ANNOUNCED) between trunc(sysdate-60) and trunc(sysdate)
                group by case when NO_NPE is not null then 'NPE' else 'NON NPE' end))
                group by NPE, NON_NPE";

        $data = $con->query($query)->row();

        return $data;
    }

    public function getJumlah($where){
        $con = $this->load->database('ilcs_cartos', TRUE);
        $query = "SELECT LEFT, IMPORT, EXPORT, LOADED, SUM(nvl(JML_LEFT,0)) JML_LEFT, SUM(nvl(JML_IM,0)) JML_IM, SUM(nvl(JML_EX,0)) JML_EX, SUM(nvl(JML_LOAD,0)) JML_LOAD FROM(SELECT 
                CASE WHEN STATUS = 'Left' THEN STATUS else 'Left' end LEFT,
                CASE WHEN STATUS = 'OnTerminal Import' THEN STATUS else 'OnTerminal Import' end IMPORT,
                CASE WHEN STATUS = 'OnTerminal Export' THEN STATUS else 'OnTerminal Export' end EXPORT,
                CASE WHEN STATUS = 'Loaded' THEN STATUS else 'Loaded' end LOADED,
                CASE WHEN STATUS = 'Left' then JML end JML_LEFT,
                CASE WHEN STATUS = 'OnTerminal Import' then JML end JML_IM,
                CASE WHEN STATUS = 'OnTerminal Export' then JML end JML_EX,
                CASE WHEN STATUS = 'Loaded' then JML end JML_LOAD
                FROM(select 
                case 
                when DIRECTION = 1 and DIRECTION_TYPE = 1 and DTS_LEFT is not null then 'Left' 
                when DIRECTION = 1 and DIRECTION_TYPE = 1 and DTS_ONTERMINAL is not null then 'OnTerminal Import' 
                when DIRECTION = 2 and DIRECTION_TYPE = 1 and DTS_ONTERMINAL is not null then 'OnTerminal Export' 
                when DIRECTION = 2 and DIRECTION_TYPE = 1 and DTS_LOADED is not null then 'Loaded' 
                end STATUS, count(*) JML 
                from CARTOS_CARGO where (VISIT_ID = '$where')
                and DIRECTION in (1,2) and trunc(DTS_ANNOUNCED) between trunc(sysdate-60) and trunc(sysdate)
                group by case 
                when DIRECTION = 1 and DIRECTION_TYPE = 1 and DTS_LEFT is not null then 'Left' 
                when DIRECTION = 1 and DIRECTION_TYPE = 1 and DTS_ONTERMINAL is not null then 'OnTerminal Import' 
                when DIRECTION = 2 and DIRECTION_TYPE = 1 and DTS_ONTERMINAL is not null then 'OnTerminal Export' 
                when DIRECTION = 2 and DIRECTION_TYPE = 1 and DTS_LOADED is not null then 'Loaded' 
                end))
                group by LEFT, IMPORT, EXPORT, LOADED";

        $data = $con->query($query)->row();

        return $data;
    }

    public function getDataSum($where){
        $con = $this->load->database('ilcs_cartos', TRUE);


        $sql = "SELECT vin, tgl_input FROM dashboard_update WHERE tgl_input IN (SELECT MAX(tgl_input) FROM dashboard_update) ORDER BY tgl_input";
        $vin = $con->query($sql)->result_array();
        $no_vin = $vin[0]['VIN'];
        
        $query = "SELECT VIN, nvl(NO_NPE,'Tidak Ada') NO_NPE, MODEL_NAME from CARTOS_CARGO where VIN = '$no_vin'";

        // $query = "SELECT VIN, nvl(NO_NPE,'Tidak Ada') NO_NPE, MODEL_NAME from CARTOS_CARGO where VIN = '$where'";
        

        $data = $con->query($query)->row();

        return $data;
    }


    // ==========================Dashboard Inventory=============================== //


    public function getDataDetail()
    {
        $con = $this->load->database('ilcs_cartos', TRUE);

        $sql = "SELECT * from dashboard_inventory_summary";

        $data = $con->query($sql)->result();

        return $data;

    }

    // data truck in 
    public function get_truk_in()
    {
        $con = $this->load->database('ilcs_cartos', TRUE);

        $sql = "SELECT 
                a.nr visit_id, a.transportmeanname plat, a.driver, a.visitstatus, count(vin) jml_vin from t_visit@ctos_link a
                join t_trip@ctos_link b on a.id = b.visit_id left join (select * from codeco_coarri@ctos_link where direction = 2 and direction_type = 1 and dts_loaded is not null) c on a.nr = c.visit_id_1
                where a.transportmean = 3 and a.visitstatus = 0 and a.lastchange between sysdate -30 and sysdate
                and b.incoming = 1
                group by a.nr, a.transportmeanname, a.driver, a.visitstatus";

        $data = $con->query($sql)->result();

        return $data;
    }

    public function get_vin_truk_in($visitID)
    {
        $con = $this->load->database('ilcs_cartos', TRUE);

        $sql = "SELECT 
                dts_onterminal, vin, type type_cargo, length, width, height, color, model_name, customs_number, customs_date, visit_name nama_kapal, voyage_in, transportmeanname_2 truk_pengangkut
                from codeco_coarri@ctos_link a left join vessel_schedule@ctos_link b on a.visit_id_1 = b.visit_id
                where visit_id_1 = '$visitID' order by 1";

        $data = $con->query($sql)->result();

        return $data;
        
    }

    public function get_vin_announced_ti()
    {
        $con = $this->load->database('ilcs_cartos', TRUE);

        $sql = "SELECT 
                dts_onterminal, vin, type type_cargo, length, width, height, color, model_name, customs_number, customs_date, transportmeanname nama_kapal, 
                externalreferencein voyage_in, transportmeanname_2 truk_pengangkut
                from 
                (select a.nr, externalreferencein, transportmeanname from t_visit@ctos_link a join t_trip@ctos_link b on a.id = b.visit_id
                where transportmean = 3 and visitstatus = 0 and a.lastchange between sysdate -30 and sysdate
                and incoming = 1) a join (select * from codeco_coarri@ctos_link where direction = 2 and direction_type = 1 and dts_loaded is not null) b
                on a.nr = b.visit_id_1";

        $data = $con->query($sql)->result();

        return $data;
    }
    // data truck in


    // data truck out
    public function get_truk_out()
    {
        $con = $this->load->database('ilcs_cartos', TRUE);

        $sql = "SELECT 
                a.nr visit_id, a.transportmeanname plat, a.driver, a.visitstatus, count(vin) jml_vin from t_visit@ctos_link a
                join t_trip@ctos_link b on a.id = b.visit_id left join (select * from codeco_coarri@ctos_link where direction = 1 and direction_type = 1 and dts_left is not null) c on a.nr = c.visit_id_2
                where a.transportmean = 3 and a.visitstatus = 5 and a.lastchange between sysdate -30 and sysdate
                and b.incoming = 0
                group by a.nr, a.transportmeanname, a.driver, a.visitstatus";

        $data = $con->query($sql)->result();

        return $data;
    }

    public function get_vin_truk_out($visitID)
    {
        $con = $this->load->database('ilcs_cartos', TRUE);

        $sql = "SELECT 
                dts_onterminal, vin, type type_cargo, length, width, height, color, model_name, customs_number, customs_date, visit_name nama_kapal, voyage_in, transportmeanname_2 truk_pengangkut
                from codeco_coarri@ctos_link a left join vessel_schedule@ctos_link b on a.visit_id_1 = b.visit_id
                where visit_id_2 ='$visitID' order by 1";

        $data = $con->query($sql)->result();

        return $data;
    }

    public function get_vin_announced_to()
    {
        $con = $this->load->database('ilcs_cartos', TRUE);

        $sql = "SELECT 
                dts_onterminal, vin, type type_cargo, length, width, height, color, model_name, customs_number, customs_date, transportmeanname nama_kapal, 
                externalreferencein voyage_in, transportmeanname_2 truk_pengangkut from 
                (select a.nr, externalreferencein, transportmeanname from t_visit@ctos_link a join t_trip@ctos_link b on a.id = b.visit_id
                where transportmean = 3 and visitstatus in 5 and a.lastchange between sysdate -30 and sysdate
                and incoming = 0) a join (select * from codeco_coarri@ctos_link where direction = 1 and direction_type = 1 and dts_left is not null) b
                on a.nr = b.visit_id_2";

        $data = $con->query($sql)->result();

        return $data;
    }
    // data truck out


    // data vessel in
    public function get_visit_vessel_export()
    {
        $con = $this->load->database('ilcs_cartos', TRUE);

        $sql = "SELECT eta, visit_id, nm_kapal, voy_in, voy_out, visitstatus, count(vin) jml_vin from
                (select eta, a.visit_id, a.visit_name nm_kapal, a.voyage_in voy_in, a.voyage_out voy_out, a.vessel_status visitstatus from vessel_schedule@ctos_link a
                where lastchange between sysdate -30 and sysdate     
                and visit_direction = 1) a left join (select distinct visit_id_2, vin from codeco_coarri@ctos_link where direction = 2 and direction_type = 1 and dts_loaded is not null) c on a.visit_id = c.visit_id_2
                group by eta, visit_id, nm_kapal, voy_in, voy_out, visitstatus order by eta";

        $data = $con->query($sql)->result();

        return $data;
    }

    public function get_vin_vessel_in($visitID)
    {
        $con = $this->load->database('ilcs_cartos', TRUE);

        $sql = "SELECT 
                dts_onterminal, vin, type type_cargo, length, width, height, color, model_name, customs_number, customs_date, visit_name nama_kapal, voyage_in, transportmeanname_2 truk_pengangkut
                from codeco_coarri@ctos_link a left join vessel_schedule@ctos_link b on a.visit_id_1 = b.visit_id
                where visit_id_2 ='$visitID' order by 1";

        $data = $con->query($sql)->result();

        return $data;
    }

    public function get_vin_vessel_export()
    {
        $con = $this->load->database('ilcs_cartos', TRUE);

        $sql = "SELECT 
                dts_onterminal, vin, type type_cargo, length, width, height, color, model_name, customs_number, customs_date, 
                visit_name nama_kapal, voyage_in, transportmeanname_2 truk_pengangkut from 
                (select * from vessel_schedule@ctos_link where lastchange between sysdate -30 and sysdate
                and visit_direction = 1) a join (select * from codeco_coarri@ctos_link where direction = 2 and direction_type = 1 and dts_loaded is not null) b
                on a.visit_id = b.visit_id_2 order by type_cargo";

        $data = $con->query($sql)->result();

        return $data;
    }
    // data vessel in


    // data vessel out
    public function get_visit_vessel_import()
    {
        $con = $this->load->database('ilcs_cartos', TRUE);

        $sql = "SELECT eta, visit_id, nm_kapal, voy_in, voy_out, visitstatus, count(vin) jml_vin from
                (select eta, a.visit_id, a.visit_name nm_kapal, a.voyage_in voy_in, a.voyage_out voy_out, a.vessel_status visitstatus from vessel_schedule@ctos_link a
                where lastchange between sysdate -30 and sysdate     
                and visit_direction = 1) a left join (select distinct visit_id_1, vin from codeco_coarri@ctos_link where direction = 1 and direction_type = 1 and dts_left is not null) c on a.visit_id = c.visit_id_1
                group by eta, visit_id, nm_kapal, voy_in, voy_out, visitstatus order by eta";

        $data = $con->query($sql)->result();

        return $data;
    }

    public function get_vin_vessel_out($visitID)
    {
        $con = $this->load->database('ilcs_cartos', TRUE);

        $sql = "SELECT 
                dts_onterminal, vin, type type_cargo, length, width, height, color, model_name, customs_number, customs_date, visit_name nama_kapal, voyage_in, transportmeanname_2 truk_pengangkut
                from codeco_coarri@ctos_link a left join vessel_schedule@ctos_link b on a.visit_id_1 = b.visit_id
                where visit_id_2 ='$visitID' order by 1";

        $data = $con->query($sql)->result();

        return $data;
    }

    public function get_vin_vessel_import()
    {
        $con = $this->load->database('ilcs_cartos', TRUE);

        $sql = "SELECT 
                dts_onterminal, vin, type type_cargo, length, width, height, color, model_name, customs_number, customs_date, 
                visit_name nama_kapal, voyage_in, transportmeanname_2 truk_pengangkut from 
                (select * from vessel_schedule@ctos_link where lastchange between sysdate -30 and sysdate
                and visit_direction = 1) a join (select * from codeco_coarri@ctos_link where direction = 1 and direction_type = 1 and dts_left is not null) b
                on a.visit_id = b.visit_id_1 order by type_cargo";

        $data = $con->query($sql)->result();

        return $data;
    }
    // data vessel out

    public function get_vin_terminal_in()
    {
        $con = $this->load->database('ilcs_cartos', TRUE);

        $sql = "SELECT 
                dts_onterminal, vin, type type_cargo, length, width, height, color, model_name, customs_number, customs_date, visit_name nama_kapal, voyage_in, transportmeanname_2 truk_pengangkut
                from codeco_coarri@ctos_link a left join vessel_schedule@ctos_link b on a.visit_id_1 = b.visit_id
                where direction = 2 and direction_type = 1 and dts_onterminal is not null and dts_loaded is null order by 1, type_cargo";

        $data = $con->query($sql)->result();

        return $data;
    }

    public function get_vin_terminal_out()
    {
        $con = $this->load->database('ilcs_cartos', TRUE);

        $sql = "SELECT 
                dts_onterminal, vin, type type_cargo, length, width, height, color, model_name, customs_number, customs_date, visit_name nama_kapal, voyage_in, transportmeanname_2 truk_pengangkut
                from codeco_coarri@ctos_link a left join vessel_schedule@ctos_link b on a.visit_id_1 = b.visit_id
                where direction = 1 and direction_type = 1 and dts_onterminal is not null and dts_left is null order by 1, type_cargo";

        $data = $con->query($sql)->result();

        return $data;
    }

    public function get_non_npe()
    {
        $con = $this->load->database('ilcs_cartos', TRUE);

        $sql = "SELECT 
                dts_onterminal, vin, type type_cargo, length, width, height, color, model_name, customs_number, customs_date, visit_name nama_kapal, voyage_in, transportmeanname_2 truk_pengangkut
                from codeco_coarri@ctos_link a left join vessel_schedule@ctos_link b on a.visit_id_1 = b.visit_id
                where direction = 2 and direction_type = 1 and dts_onterminal is not null and dts_loaded is null and customs_number is null order by 1";

        $data = $con->query($sql)->result();

        return $data;
    }

    public function get_npe()
    {
        $con = $this->load->database('ilcs_cartos', TRUE);

        $sql = "SELECT 
                dts_onterminal, vin, type type_cargo, length, width, height, color, model_name, customs_number, customs_date, visit_name nama_kapal, voyage_in, transportmeanname_2 truk_pengangkut
                from codeco_coarri@ctos_link a left join vessel_schedule@ctos_link b on a.visit_id_1 = b.visit_id
                where direction = 2 and direction_type = 1 and dts_onterminal is not null and dts_loaded is null and customs_number is not null order by 1";

        $data = $con->query($sql)->result();

        return $data;
    }

    // ===========data sppb========== 
    public function get_non_sppb()
    {
        $con = $this->load->database('ilcs_cartos', TRUE);


        $sql = "SELECT 
                dts_onterminal, vin, type type_cargo, length, width, height, color, model_name, customs_number, customs_date, visit_name nama_kapal, voyage_in, transportmeanname_2 truk_pengangkut
                from codeco_coarri@ctos_link a left join vessel_schedule@ctos_link b on a.visit_id_1 = b.visit_id
                where direction = 1 and direction_type = 1 and dts_onterminal is not null and dts_left is null and customs_number is null order by 1";

        $data = $con->query($sql)->result();

        return $data;
    }

    public function get_sppb()
    {
        $con = $this->load->database('ilcs_cartos', TRUE);


        $sql = "SELECT 
                dts_onterminal, vin, type type_cargo, length, width, height, color, model_name, customs_number, customs_date, visit_name nama_kapal, voyage_in, transportmeanname_2 truk_pengangkut
                from codeco_coarri@ctos_link a left join vessel_schedule@ctos_link b on a.visit_id_1 = b.visit_id
                where direction = 1 and direction_type = 1 and dts_onterminal is not null and dts_left is null and customs_number is not null order by 1";

        $data = $con->query($sql)->result();

        return $data;
    }




}
