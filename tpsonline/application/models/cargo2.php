<?php

class Cargo2 extends CI_Model {

    private $_db;

    public function __construct() {
        parent::__construct();
        $this->_db = $this->load->database(ILCS_CARTOS, TRUE);
    }

    public function list_cargo($visit_id, $search, $sort, $order, $start, $end, $periode_start, $periode_end, $type = 'list') {
        $sql = $this->search_order_cargo($visit_id, $search, $sort, $order, $start, $end, $periode_start, $periode_end, $type);
        // die($sql);
        if($type == 'list') {
            return $this->_db->query($sql)
                            ->result();
        } else {
            return $this->_db->query($sql)
                            ->result_array();
        }

    }

    public function search_order_cargo($visit_id = null, $search = null, $sort = null, $order = null, $start = null, $end = null, $periode_start = null, $periode_end = null, $type) {
        if(empty($visit_id)) {
            $filterSearch = "";
            if(!empty($search)) {
                $filterSearch = " AND (
                		hu.code LIKE '%$search%' OR
                		hu.category1 LIKE '%$search%' OR
                        hu.category2 LIKE '%$search%' OR
                        hu.category3 LIKE '%$search%' OR
                        hu.category4 LIKE '%$search%' OR
                        CASE
                        	WHEN hu.holdcount > 0 THEN 'HOLD'
                			ELSE 'RELEASE'
                        END LIKE '%$search%'
                ) ";
            }

            // limit query based on rownum requested
            $limit = "";

            /* =======================================================================
             * Optionally use $date_limit to boost query for rownumber < 1000
             * remove this $date_limit if you get empty data
            */
            $date_limit = "";

            if(!empty($start) && !empty($end) && empty($seacrh) && empty($sort)) {
                $limit = " WHERE rn BETWEEN $start and $end ";
                if($end < 100) {
                  $date_limit = " AND ll.lastchange > (SYSDATE -7) ";
                }
                elseif($end < 1000) {
                  $date_limit = " AND ll.lastchange > ADD_MONTHS(SYSDATE, -1) ";
                }
            }
            // =======================================================================

            $range_filter = "";
            if(!empty($periode_start) && !empty($periode_end)) {
                $periode_start = date('Y/m/d 00:00:00', strtotime($periode_start));
                $periode_end = date('Y/m/d 23:59:59', strtotime($periode_end));

                $range_filter = " AND ll.lastchange
                    BETWEEN
                        to_date('$periode_start','YYYY/MM/DD HH24:MI:SS')
                    AND
                        to_date('$periode_end','YYYY/MM/DD HH24:MI:SS')";
            }

            if($type == 'list') {
                $select = 'vin, status, dtsonterminal, dtsloaded, dtsleft,
                        actual_position, direction, maker, model, jenis,
                        consignee,asal,final_location,vessel,visit_id,
                        customsno, customsdate, hold_status,
                        last_change2, voyage_in, voyage_out';
            } else {
                $select = 'vin, status, dtsonterminal, dtsloaded, dtsleft,
                        actual_position, direction, maker, model, jenis,
                        consignee,asal,final_location,vessel, hold_status,
                        visit_id, customsno, customsdate, voyage_in, voyage_out';
            }

            $sql = "SELECT
                        $select
                    FROM (
                        SELECT
                          cargo.rn,
                          cargo.activelocationlog_id,
                          cargo.id, cargo.orderinformation_id,
                          cargo.code vin,
                          cargo.category1 jenis,
                          cargo.category2 direction,
                          cargo.category3 maker,
                          cargo.category4 model,
                          CASE
                              WHEN cargo.holdcount > 0 THEN 'HOLD'
                              ELSE 'RELEASE'
                          END hold_status,
                          last_change2,
                          TO_CHAR( last_change2, 'DD-MON-YYYY HH24:MM' ) last_change,
                          oi.customsnumber customsno, TO_CHAR( oi.customsdate, 'DD-MON-YYYY HH24:MM' ) customsdate,
                          tloc.name final_location,
                          tpos.CODE actual_position,
                          torg2.name consignee,
                          getVesselName(cargo.id) vessel,
                          getStatusLocal(cargo.id, last_change2) status,
                          TO_CHAR(getWaktuLeft(cargo.id, last_change2), 'DD-MON-YYYY HH24:MM') dtsleft,
                          TO_CHAR(getWaktuLoaded(cargo.id, last_change2), 'DD-MON-YYYY HH24:MM') dtsloaded,
                          TO_CHAR(getWaktuTerminal(cargo.id, last_change2), 'DD-MON-YYYY HH24:MM') dtsonterminal,
                          getVisitID(cargo.id) visit_id,
                          getAsal(cargo.id) asal,
                          getVoyageIn(cargo.id) voyage_in,
                          getVoyageOut(cargo.id) voyage_out
                        FROM (
                          SELECT * FROM (
                            SELECT hu.*, ll.lastchange last_change2,
                              ROW_NUMBER() OVER (ORDER BY ll.lastchange DESC) rn
                            FROM t_handlingunit@CTOS_LINK hu
                            LEFT JOIN t_locationlog@CTOS_LINK ll on hu.activelocationlog_id = ll.id
                            WHERE hu.activelocationlog_id is not null and hu.category6 = 'INTERNATIONAL'
                              $date_limit
                              $range_filter
                              $filterSearch
                            --ORDER BY ll.lastchange DESC
                          ) $limit
                        ) cargo
                        LEFT JOIN t_orderinformation@CTOS_LINK oi ON cargo.orderinformation_id = oi.id
                        LEFT JOIN t_location@CTOS_LINK tloc ON oi.finallocation_id = tloc.id
                        LEFT JOIN t_organization@CTOS_LINK torg2 ON oi.consignee_id = torg2.id
                        LEFT JOIN t_position@CTOS_LINK tpos ON cargo.ACTUALPOSITION_ID = tpos.ID
                        ORDER BY last_change2 DESC
                    ) dashcargo
                    WHERE 1=1";

    		// if(!empty($search)) {
    		// 	$sql .= " and lower(vin || ' ' || status || last_change2 || ' ' || jenis || ' ' || direction || ' ' || maker || ' ' || model || ' ' || final_location || ' ' || logistic_company || ' ' || consignee || ' ' || customsno || ' ' || hold || ' ' || vessel || ' ' || truck || ' ' || driver || ' ' || visit_id) like lower('%".$search."%')";
    		// }
        } else {
            $sql ="SELECT * FROM (
                    	select ROW_NUMBER() OVER (ORDER BY hu.lastchange) nomor,
                    	    vs.nr visit_id_truck, ht.handlingunit_id,
                    	    hu.orderinformation_id, hu.code vin, hu.category1 jenis, hu.category2 direction, hu.category3 maker,hu.category4 model,
                    	    hu.ACTUALPOSITION_ID,
                    	        CASE
                    	            WHEN hu.holdcount > 0 THEN 'HOLD'
                    	            ELSE 'RELEASE'
                    	        END hold_status,
                    	        TO_CHAR( hu.lastchange, 'DD-MON-YYYY HH24:MM' ) last_change, hu.lastchange last_change2,
                    	    oi.customsnumber customsno,
                    		TO_CHAR( oi.CUSTOMSDATE, 'DD-MON-YYYY HH24:MM' ) customsdate,
                    		tloc.name final_location,
                    		tpos.CODE actual_position,
                    		torg.name logistic_company,
                    		torg2.name consignee,
                    	    getVesselName(hu.id) vessel,
                    		ll.LOCALSTATUS status,
                    		TO_CHAR(ll.DTSLEFT, 'DD-MON-YYYY HH24:MM') dtsleft,
                    		TO_CHAR(ll.DTSLOADED, 'DD-MON-YYYY HH24:MM') dtsloaded,
                    		TO_CHAR(ll.DTSONTERMINAL, 'DD-MON-YYYY HH24:MM') dtsonterminal,
                    		getVisitID(hu.id) visit_id,
                    		getAsal(hu.id) asal,
                            getVoyageIn(hu.id) voyage_in,
                            getVoyageOut(hu.id) voyage_out
                    	from t_visit@CTOS_LINK vs
                    	    left join t_trip@CTOS_LINK tp on vs.id = tp.visit_id
                          -- use inner join to avoid null trip
                    	    inner join t_handlingunitontrip@CTOS_LINK ht on tp.id = ht.trip_id
                    	    left join t_handlingunit@CTOS_LINK hu on ht.handlingunit_id = hu.id
                          left join t_locationlog@CTOS_LINK ll on hu.activelocationlog_id = ll.id
                    	    LEFT JOIN t_orderinformation@CTOS_LINK oi ON hu.orderinformation_id = oi.id
                    	    LEFT JOIN t_location@CTOS_LINK tloc ON oi.finallocation_id = tloc.id
                    	    LEFT JOIN t_organization@CTOS_LINK torg ON oi.controllingorganization_id = torg.id
                    	    LEFT JOIN t_organization@CTOS_LINK torg2 ON oi.consignee_id = torg2.id
                    	    LEFT JOIN t_position@CTOS_LINK tpos ON hu.ACTUALPOSITION_ID = tpos.ID
                    	where vs.TRANSPORTMEAN = 3 and vs.nr = '$visit_id'
                    	order by hu.LASTCHANGE
                    ) v_dash_cargo
                    WHERE 1=1";
        }
        // $sql = "select $start vin, $end status from dual";

        // if(!empty($start) && !empty($end)) {
            // $sql .= " and nomor between $start and $end";
        // }

		return $sql;
    }


    public function count_all_cargo($visit_id = null, $start = null, $end = null, $search = null) {
        if(empty($visit_id)) {
            $filterSearch = "";
            if(!empty($search)) {
                $filterSearch = " AND (
                		hu.code LIKE '%$search%' OR
                		hu.category1 LIKE '%$search%' OR
                        hu.category2 LIKE '%$search%' OR
                        hu.category3 LIKE '%$search%' OR
                        hu.category4 LIKE '%$search%' OR
                        CASE
                        	WHEN hu.holdcount > 0 THEN 'HOLD'
                			ELSE 'RELEASE'
                        END LIKE '%$search%'
                ) ";
            }

            if(empty($start) || empty($end)) {
              $sql = "SELECT
                        count(hu.id) jml
                      FROM
                        t_handlingunit@CTOS_LINK hu
                      WHERE
                        activelocationlog_id IS NOT NULL $filterSearch";

            } else {
                $start = date('Y/m/d 00:00:00', strtotime($start));
                $end = date('Y/m/d 23:59:59', strtotime($end));

                $sql = "SELECT count(hu.id) jml
                        FROM t_handlingunit@CTOS_LINK hu
                        LEFT JOIN t_locationlog@CTOS_LINK ll on hu.activelocationlog_id = ll.id
                        WHERE hu.activelocationlog_id IS NOT NULL $filterSearch
                          AND ll.lastchange
                          BETWEEN
                              to_date('$start','YYYY/MM/DD HH24:MI:SS')
                          AND
                              to_date('$end','YYYY/MM/DD HH24:MI:SS')";
            }

        }else{
            $sql = "select count(vs.id) jml from t_visit@CTOS_LINK vs
                    left join t_trip@CTOS_LINK tp on vs.id = tp.visit_id
                    left join t_handlingunitontrip@CTOS_LINK ht on tp.id = ht.trip_id
                    where vs.TRANSPORTMEAN = 3 and vs.nr = '$visit_id'";
        }

		return $this->_db->query($sql)
                        ->row()
                        ->JML;
    }
}
