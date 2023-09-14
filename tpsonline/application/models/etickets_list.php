<?php

class Etickets_List extends CI_Model {

    var $column_order = array(null,'A.CODE','A.NR','A.CATEGORY2','A.CATEGORY3');

    public function __construct()
    {
        parent::__construct();
        $this->db = $this->load->database(ILCS_CTOS_QAS, TRUE);
        $this->db2 = $this->load->database(ILCS_TPS_ONLINE, TRUE);
    }


    function get_datatables($sender)
    {
        $maker = [];
        $get_maker = $this->db2->select('MAKE')
            ->from('AUTOGATE_MAKER')
            ->where('SENDER', $sender)
            ->get()
            ->result();

        foreach ($get_maker as $data) {
            array_push($maker, $data->MAKE);
        }

        $maker_string= implode("','", $maker);
        $add_payload = "
        select *
            from ( select a.*, rownum as rnum
                    from (SELECT
                            A.CATEGORY3,
                            (case when count( DISTINCT CATEGORY2)>1 then 'BACKLOAD'
                                ELSE MAX(CATEGORY2) END)  CATEGORY2,
                            A.NR, A.VISITSTATUS,
                            A.LASTCHANGE,
                            A.DRIVER,
                            A.LICENSEPLATE,
                            A.CODE,
                            A.DESCRIPTION
                        FROM (
                                select DISTINCT
                                    T_HANDLINGUNIT.CATEGORY3,
                                    T_HANDLINGUNIT.CATEGORY2,
                                    T_VISIT.NR, T_VISIT.VISITSTATUS,
                                    T_VISIT.LASTCHANGE,
                                    T_VISIT.DRIVER,
                                    T_TRUCK.LICENSEPLATE,
                                    T_TRUCK.DESCRIPTION,
                                    T_TRUCK.CODE
                                from T_HANDLINGUNIT
                                        join T_HANDLINGUNITONTRIP ON T_HANDLINGUNITONTRIP.HANDLINGUNIT_ID = T_HANDLINGUNIT.ID
                                        join T_TRIP ON T_TRIP.ID = T_HANDLINGUNITONTRIP.TRIP_ID
                                        join T_VISIT ON T_VISIT.ID = T_TRIP.VISIT_ID
                                        join T_TRUCK ON T_VISIT.TRUCK_ID = T_TRUCK.ID
                                where T_VISIT.LASTCHANGE > current_timestamp -3
                                --AND t_visit.VISITSTATUS < 5
                                AND T_HANDLINGUNITONTRIP.DTSDELETED is NULL
                                AND T_VISIT.NR LIKE 'TRK%' --AND T_TRUCK.CODE='B9783FEH'
                                union
                                select DISTINCT
                                    T_HANDLINGUNIT.CATEGORY3,
                                    T_HANDLINGUNIT.CATEGORY2,
                                    T_VISIT.NR, T_VISIT.VISITSTATUS,
                                    T_VISIT.LASTCHANGE,
                                    T_VISIT.DRIVER,
                                    T_TRUCK.LICENSEPLATE,
                                    T_TRUCK.DESCRIPTION,
                                    T_TRUCK.CODE
                                from T_HANDLINGUNIT
                                        join T_HANDLINGUNITONTRIP ON T_HANDLINGUNITONTRIP.HANDLINGUNIT_ID = T_HANDLINGUNIT.ID
                                        join T_TRIP ON T_TRIP.ID = T_HANDLINGUNITONTRIP.TRIP_ID
                                        join T_VISIT ON T_VISIT.ID = T_TRIP.VISIT_ID
                                        join T_TRUCK ON T_VISIT.TRUCK_ID = T_TRUCK.ID
                                where T_VISIT.LASTCHANGE > current_timestamp -3
                                AND t_visit.additionalinformation ='SELFDRIVE'
                                --AND t_visit.VISITSTATUS<5
                                AND T_HANDLINGUNITONTRIP.DTSDELETED is NULL
                                AND T_VISIT.NR LIKE 'TRK%' 
                                union
                                select
                                    t_visit.categoryfield5 AS CATEGORY3,
                                    'IMPORT' AS CATEGORY2,
                                    t_visit.nr AS NR, T_VISIT.VISITSTATUS AS VISITSTATUS,
                                    t_visit.lastchange AS LASTCHANGE,
                                    t_visit.DRIVER AS DRIVER,
                                    T_TRUCK.LICENSEPLATE AS LICENSEPLATE,
                                    T_TRUCK.DESCRIPTION AS DESCRIPTION,
                                    T_TRUCK.CODE AS CODE 
                                from 
                                    t_visit
                                    join T_TRUCK ON T_VISIT.TRUCK_ID = T_TRUCK.ID
                                where T_VISIT.LASTCHANGE > current_timestamp -3 
                                AND t_visit.additionalinformation  LIKE '%IMPORT%'
                                --and t_visit.VISITSTATUS < 5
                            ) A
                            ".(($_POST["search"]["value"] ? " WHERE UPPER(A.CODE) like '%".strtoupper($_POST["search"]["value"])."%' OR UPPER(A.NR) like '%".strtoupper($_POST["search"]["value"])."%' OR UPPER(A.CATEGORY2) like '%".strtoupper($_POST["search"]["value"])."%' OR UPPER(A.CATEGORY3) like '%".strtoupper($_POST["search"]["value"])."%'" : "") )."
                        ".( $sender != 'IKT' && $sender != 'EMERGENCY' ? $_POST["search"]["value"] ? " AND A.CATEGORY3 in ('$maker_string') " : " WHERE A.CATEGORY3 in ('$maker_string') " :"" )."
                        group by
                            A.CATEGORY3,
                            A.NR, A.VISITSTATUS,
                            A.LASTCHANGE,
                            A.LICENSEPLATE,
                            A.CODE, 
                            A.DRIVER, 
                            A.DESCRIPTION 
                            ORDER BY ".( $_POST['order'] ? $this->column_order[$_POST['order']['0']['column']]." ".$_POST['order']['0']['dir'] : "A.LASTCHANGE DESC" )."
                            ) a
                    where rownum <= ".( $_POST['start'] == 0 ? $_POST['length']+$_POST['start'] : $_POST['length']+$_POST['start']-1)." )
            where rnum >= ".($_POST['start'])."
            ";

        return $this->db->query($add_payload);
    }

    function count_total($sender)
    {
        $maker = [];
        $get_maker = $this->db2->select('MAKE')
            ->from('AUTOGATE_MAKER')
            ->where('SENDER', $sender)
            ->get()
            ->result();

        foreach ($get_maker as $data) {
            array_push($maker, $data->MAKE);
        }

        $maker_string= implode("','", $maker);
        $query = "select count(a.CODE) as tots from (SELECT
                A.CATEGORY3,
                (case when count( DISTINCT CATEGORY2)>1 then 'BACKLOAD'
                      ELSE MAX(CATEGORY2) END)  CATEGORY2,
                A.NR,
                A.LASTCHANGE,
                A.LICENSEPLATE,
                A.CODE
            FROM (
                     select DISTINCT
                         T_HANDLINGUNIT.CATEGORY3,
                         T_HANDLINGUNIT.CATEGORY2,
                         T_VISIT.NR,
                         T_VISIT.LASTCHANGE,
                         T_TRUCK.LICENSEPLATE,
                         T_TRUCK.CODE
                     from T_HANDLINGUNIT
                              join T_HANDLINGUNITONTRIP ON T_HANDLINGUNITONTRIP.HANDLINGUNIT_ID = T_HANDLINGUNIT.ID
                              join T_TRIP ON T_TRIP.ID = T_HANDLINGUNITONTRIP.TRIP_ID
                              join T_VISIT ON T_VISIT.ID = T_TRIP.VISIT_ID
                              join T_TRUCK ON T_VISIT.TRUCK_ID = T_TRUCK.ID
                     where T_VISIT.LASTCHANGE > current_timestamp -3
                       AND          T_VISIT.ARRIVAL is NULL
                       AND T_HANDLINGUNITONTRIP.DTSDELETED is NULL
                       AND T_VISIT.NR LIKE 'TRK%' --AND T_TRUCK.CODE='B9783FEH'
                     union
                     select
                         t_visit.categoryfield5 AS CATEGORY3,
                         'IMPORT' AS CATEGORY2,
                         t_visit.nr AS NR,
                         t_visit.lastchange AS LASTCHANGE,
                         T_TRUCK.LICENSEPLATE AS LICENSEPLATE,
                         T_TRUCK.CODE AS CODE  from t_visit
                                                        join T_TRUCK ON T_VISIT.TRUCK_ID = T_TRUCK.ID
                     where T_VISIT.LASTCHANGE > current_timestamp -3 AND t_visit.additionalinformation  LIKE '%IMPORT%'
                       --and t_visit.VISITSTATUS<5
                 ) A
            ".( $sender != 'IKT' && $sender != 'EMERGENCY' ? " WHERE A.CATEGORY3 in ('$maker_string') ":"" )."
            group by
                A.CATEGORY3,
                A.NR,
                A.LASTCHANGE,
                A.LICENSEPLATE,
                A.CODE ORDER BY A.LASTCHANGE DESC) a";
        return $this->db->query($query);
    }

}