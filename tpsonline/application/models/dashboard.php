<?php

class Dashboard extends CI_Model {

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $this->db2 = $this->load->database(ILCS_TPS_ONLINE, TRUE);
    }

    function chart_rc($bulan, $tahun){
        if($bulan != 'ALL') $where_bulan = " AND TO_DATE(TO_CHAR(CREATED_DT, 'MM'),'MM') = TO_DATE('".$bulan."','MM') ";
        if($tahun != 'ALL') $where_tahun = " AND TO_DATE(TO_CHAR(CREATED_DT, 'YYYY'),'YYYY') = TO_DATE('".$tahun."','YYYY') ";
        $query = "SELECT DISTINCT (SELECT COUNT(*) FROM RETURN_CARGO WHERE RC_STATUS = '1' ".$where_bulan." ".$where_tahun.") AS REQUEST, 
                    (SELECT COUNT(*) FROM RETURN_CARGO WHERE RC_STATUS = '2' ".$where_bulan." ".$where_tahun.") AS APPROVED, 
                    (SELECT COUNT(*) FROM RETURN_CARGO WHERE RC_STATUS = '3' ".$where_bulan." ".$where_tahun.") AS REJECT
                    FROM RETURN_CARGO";
        $exec  = $this->db2->query($query);
        return $exec->row();
    }

    function chart_eticket($bulan, $tahun){
        if($bulan != 'ALL') $where_bulan = " AND TO_DATE(TO_CHAR(RECORD_TIME, 'MM'),'MM') = TO_DATE('".$bulan."','MM') ";
        if($tahun != 'ALL') $where_tahun = " AND TO_DATE(TO_CHAR(RECORD_TIME, 'YYYY'),'YYYY') = TO_DATE('".$tahun."','YYYY') ";
        $query = "SELECT DISTINCT (SELECT COUNT(*) FROM ANNOUNCETRUCK_REQ WHERE RESPON_CODE = '404' ".$where_bulan." ".$where_tahun.") AS ERR404, 
                    (SELECT COUNT(*) FROM ANNOUNCETRUCK_REQ WHERE RESPON_CODE = '402' ".$where_bulan." ".$where_tahun.") AS ERR402, 
                    (SELECT COUNT(*) FROM ANNOUNCETRUCK_REQ WHERE RESPON_CODE = '399' ".$where_bulan." ".$where_tahun.") AS ERR399, 
                    (SELECT COUNT(*) FROM ANNOUNCETRUCK_REQ WHERE RESPON_CODE = '652' ".$where_bulan." ".$where_tahun.") AS ERR652, 
                    (SELECT COUNT(*) FROM ANNOUNCETRUCK_REQ WHERE RESPON_CODE = '652' ".$where_bulan." ".$where_tahun.") AS ERR652, 
                    (SELECT COUNT(*) FROM ANNOUNCETRUCK_REQ WHERE RESPON_CODE = '350' ".$where_bulan." ".$where_tahun.") AS ERR350, 
                    (SELECT COUNT(*) FROM ANNOUNCETRUCK_REQ WHERE RESPON_CODE = '654' ".$where_bulan." ".$where_tahun.") AS ERR654, 
                    (SELECT COUNT(*) FROM ANNOUNCETRUCK_REQ WHERE RESPON_CODE = '660' ".$where_bulan." ".$where_tahun.") AS ERR660, 
                    (SELECT COUNT(*) FROM ANNOUNCETRUCK_REQ WHERE RESPON_CODE = '397' ".$where_bulan." ".$where_tahun.") AS ERR397
                    FROM ANNOUNCETRUCK_REQ";
        $exec  = $this->db2->query($query);
        return $exec->row();
    }

    function chart_visit_id($bulan, $tahun){
        $join = " LEFT JOIN T_TRIP ON T_VISIT.ID = T_TRIP.VISIT_ID
                    LEFT JOIN T_HANDLINGUNITONTRIP ON T_TRIP.ID = T_HANDLINGUNITONTRIP.TRIP_ID
                    LEFT JOIN T_HANDLINGUNIT ON T_HANDLINGUNIT.ID = T_HANDLINGUNITONTRIP.HANDLINGUNIT_ID ";
        if($bulan != 'ALL') $where_bulan = " AND TO_DATE(TO_CHAR(T_VISIT.LASTCHANGE, 'MM'),'MM') = TO_DATE('".$bulan."','MM') ";
        if($tahun != 'ALL') $where_tahun = " AND TO_DATE(TO_CHAR(T_VISIT.LASTCHANGE, 'YYYY'),'YYYY') = TO_DATE('".$tahun."','YYYY') ";
        $query = "SELECT DISTINCT (SELECT COUNT(*) FROM T_VISIT ".$join." WHERE T_VISIT.VISITSTATUS = '0' ".$where_bulan." ".$where_tahun.") AS ANNOUNCE, 
                    (SELECT COUNT(*) FROM T_VISIT ".$join." WHERE T_VISIT.VISITSTATUS = '2' ".$where_bulan." ".$where_tahun.") AS ARRIVED, 
                    (SELECT COUNT(*) FROM T_VISIT ".$join." WHERE T_VISIT.VISITSTATUS = '3' ".$where_bulan." ".$where_tahun.") AS OPERATIONAL, 
                    (SELECT COUNT(*) FROM T_VISIT ".$join." WHERE T_VISIT.VISITSTATUS = '4' ".$where_bulan." ".$where_tahun.") AS COMPLETED, 
                    (SELECT COUNT(*) FROM T_VISIT ".$join." WHERE T_VISIT.VISITSTATUS = '10' ".$where_bulan." ".$where_tahun.") AS DELETED
                    FROM T_VISIT";
        $exec  = $this->db->query($query);
        return $exec->row();
    }

    function chart_truck($bulan, $tahun){
        if($bulan != 'ALL') $where_bulan = " AND TO_DATE(TO_CHAR(RECORD_TIME, 'MM'),'MM') = TO_DATE('".$bulan."','MM') ";
        if($tahun != 'ALL') $where_tahun = " AND TO_DATE(TO_CHAR(RECORD_TIME, 'YYYY'),'YYYY') = TO_DATE('".$tahun."','YYYY') ";
        $query = "SELECT DISTINCT (SELECT COUNT(*) FROM ANNOUNCETRUCK_REQ WHERE SENDER LIKE '%EVLS%' ".$where_bulan." ".$where_tahun.") AS TOYOTA, 
                    (SELECT COUNT(*) FROM ANNOUNCETRUCK_REQ WHERE SENDER LIKE '%ADLES%' ".$where_bulan." ".$where_tahun.") AS DAIHATSU, 
                    (SELECT COUNT(*) FROM ANNOUNCETRUCK_REQ WHERE SENDER LIKE '%MMKI%' ".$where_bulan." ".$where_tahun.") AS MITSUBISHI, 
                    (SELECT COUNT(*) FROM ANNOUNCETRUCK_REQ WHERE SENDER LIKE '%NSDS%' ".$where_bulan." ".$where_tahun.") AS SUZUKI,
                    (SELECT COUNT(*) FROM ANNOUNCETRUCK_REQ WHERE (SENDER NOT LIKE '%NSDS%' AND SENDER NOT LIKE '%EVLS%' AND SENDER NOT LIKE '%MMKI%' AND SENDER NOT LIKE '%ADLES%') ".$where_bulan." ".$where_tahun.") AS OTHER
                    FROM ANNOUNCETRUCK_REQ";
        $exec  = $this->db2->query($query);
        return $exec->row();
    }

    function chart_vin($bulan, $tahun){
        if($bulan != 'ALL') $where_bulan = " AND TO_DATE(TO_CHAR(RECORD_TIME, 'MM'),'MM') = TO_DATE('".$bulan."','MM') ";
        if($tahun != 'ALL') $where_tahun = " AND TO_DATE(TO_CHAR(RECORD_TIME, 'YYYY'),'YYYY') = TO_DATE('".$tahun."','YYYY') ";
        $query = "SELECT DISTINCT (SELECT COUNT(*) FROM ANNOUNCEVIN_REQ WHERE SENDER LIKE '%EVLS%' ".$where_bulan." ".$where_tahun.") AS TOYOTA, 
                    (SELECT COUNT(*) FROM ANNOUNCEVIN_REQ WHERE SENDER LIKE '%ADLES%' ".$where_bulan." ".$where_tahun.") AS DAIHATSU, 
                    (SELECT COUNT(*) FROM ANNOUNCEVIN_REQ WHERE SENDER LIKE '%MMKI%' ".$where_bulan." ".$where_tahun.") AS MITSUBISHI, 
                    (SELECT COUNT(*) FROM ANNOUNCEVIN_REQ WHERE SENDER LIKE '%NSDS%' ".$where_bulan." ".$where_tahun.") AS SUZUKI,
                    (SELECT COUNT(*) FROM ANNOUNCEVIN_REQ WHERE (SENDER NOT LIKE '%NSDS%' AND SENDER NOT LIKE '%EVLS%' AND SENDER NOT LIKE '%MMKI%' AND SENDER NOT LIKE '%ADLES%') ".$where_bulan." ".$where_tahun.") AS OTHER
                    FROM ANNOUNCEVIN_REQ";
        $exec  = $this->db2->query($query);
        return $exec->row();
    }

}