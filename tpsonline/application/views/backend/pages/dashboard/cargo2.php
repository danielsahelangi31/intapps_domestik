<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<meta content="" name="description">
	<meta content="" name="author">
	<link rel="shortcut icon" href="<?php echo base_url('favicon.ico') ?>" type="image/x-icon">
	<link rel="icon" href="<?php echo base_url('favicon.ico') ?>" type="image/x-icon">
	<title>INTAPPS</title>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url('assets/vendor/bootstrap-3.3.7/css/bootstrap.min.css') ?>"/>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url('assets/vendor/bootstrap-table/bootstrap-table.css') ?>"/>
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url('assets/css/daterangepicker-bs3.css') ?>"/>
    <style>
    #wrap > .container {
        padding: 60px 15px 0;
    }
	.w100{
        overflow: hidden;
        width: 110px;
        min-width: 110px;
        max-width: 110px;
    }
    </style>
    <script type="text/javascript">
    	var bs = {
    		siteURL : '<?php echo site_url() ?>',
    		baseURL : '<?php echo base_url() ?>'
    	};
	</script>
</head>

<body>
    <div id="wrap">
        <?php $this->load->view('backend/components/header') ?>
        <div class="container">
            <h2>Data Kargo<?php if($visit_id != null || $visit_id != "") { echo "-".$visit_id; }; ?></h2>
            <hr>
            <form class="form-inline">
		  		<div class="form-group">
				    <label>Periode Tanggal : </label>
				</div>
			  <div class="form-group mx-sm-3 mb-2">
			    <input type="input" class="form-control" name="filter_date" id="filter_date">
			  </div>
			  <button type="button" class="btn btn-primary mb-2" id="btn_exp_excel">Export Excel</button>
			</form>
            <br>
            <table id="tblcargo" data-toggle="table"
                data-pagination="true"
                data-search="true"
		        data-show-toggle="false"
                data-show-refresh="true"
                data-show-columns="true"
                data-show-export="true"
                data-detail-view="true"
                data-detail-formatter="detailFormatter"
                data-minimum-count-columns="2"
                data-show-pagination-switch="true"
                data-show-footer="false"
                data-side-pagination="server"
                data-url="<?php echo site_url('dashboard/cargo_data'); ?>?visit_id=<?php echo $visit_id ?>"
                class="table table-striped table-bordered">
               <thead>
                <tr>
        			<th rowspan="2" data-field="VIN" data-visible="true" data-card-visible="false">Vin</th>
        			<th rowspan="2" data-field="STATUS" data-visible="true" data-card-visible="false">Status</th>
                    <th colspan="3" class="text-center">Waktu</th>
                    <th rowspan="2" data-field="ACTUAL_POSITION" data-visible="true" data-card-visible="true">Actual Position</th>
        			<th rowspan="2" data-field="DIRECTION" data-visible="true" data-card-visible="true">Direction</th>
        			<th rowspan="2" data-field="MAKER" data-visible="true" data-card-visible="true">Maker</th>
        			<th rowspan="2" data-field="MODEL" data-visible="true" data-card-visible="true">Model</th>
        			<th rowspan="2" data-field="JENIS" data-visible="true" data-card-visible="true">Jenis</th>
        			<th rowspan="2" data-field="CONSIGNEE" data-visible="true" data-card-visible="true">Consignee</th>
                    <th rowspan="2" data-field="ASAL" data-visible="true" data-card-visible="true">Asal</th>
        			<th rowspan="2" data-field="FINAL_LOCATION" data-visible="true" data-card-visible="true">Tujuan Terakhir</th>        			
                    <th rowspan="2" data-field="HOLD_STATUS" data-visible="true" data-card-visible="true">Status Custom</th>
		<th rowspan="2" data-field="VESSEL" data-visible="false" data-card-visible="true">Vessel</th>
                    <th rowspan="2" data-field="VISIT_ID" data-visible="false" data-card-visible="true">Visit ID (Vessel)</th>
                    <th rowspan="2" data-field="CUSTOMSNO" data-visible="false" data-card-visible="true">Custom Number</th>
                    <th rowspan="2" data-field="CUSTOMSDATE" data-visible="false" data-card-visible="true">Custom Date</th>
			<th rowspan="2" data-field="VOYAGE_IN" data-visible="false" data-card-visible="true">Voyage In</th>
			<th rowspan="2" data-field="VOYAGE_OUT" data-visible="false" data-card-visible="true">Voyage Out</th>
                 </tr>
                 <tr>
                    <th class="text-center w100" data-field="DTSONTERMINAL" data-visible="true" data-card-visible="false">On Terminal</th>
                    <th class="text-center w100" data-field="DTSLOADED" data-visible="true" data-card-visible="false">Loaded</th>
                    <th class="text-center w100" data-field="DTSLEFT" data-visible="true" data-card-visible="false">Left</th>
                </tr>
              </thead>
           </table>
		<iframe name="frmDownload" src="" border="0" width="1" height="1" style="border: none;"></iframe>
        </div>
    </div>

    <div id="footer">
      <div class="container">
        <div id="android_ready" class="android" data-toggle="tooltip" title="Android Device Ready"></div>
        <div id="html5_ready" class="html5" data-toggle="tooltip" title="HTML 5 Based"></div>

        <p class="text-muted credit">&copy; 2013 <a href="http://www.ilcs.co.id" target="_blank">PT. Integrasi Logistik Cipta Solusi</a></p>
      </div>
    </div>

    <div id="android_browser_ready"></div>
    <div id="powered_by_html5"></div>

    <script type="text/javascript" src="<?php echo base_url('assets/js/jquery-2.0.3.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/vendor/bootstrap-3.3.7/js/bootstrap.min.js') ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/vendor/bootstrap-table/bootstrap-table.js') ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/moment.js') ?>"></script>
    <script type="text/javascript" src="<?php echo base_url('assets/js/daterangepicker.js') ?>"></script>

    <script type="text/javascript">
        function detailFormatter(index, row) {
		if(row.VESSEL === null && typeof row.VESSEL === "object") {
                row.VESSEL = '-';
            }

            if(row.VISIT_ID === null && typeof row.VISIT_ID === "object") {
                row.VISIT_ID = '-';
            }
            if(row.CUSTOMSNO === null && typeof row.CUSTOMSNO === "object") {
                row.CUSTOMSNO = '-';
            }
            if(row.CUSTOMSDATE === null && typeof row.CUSTOMSDATE === "object") {
                row.CUSTOMSDATE = '-';
            }
	if(row.VOYAGE_IN === null && typeof row.VOYAGE_IN === "object") {
                row.VOYAGE_IN = '-';
            }
	
	if(row.VOYAGE_OUT === null && typeof row.VOYAGE_OUT === "object") {
                row.VOYAGE_OUT = '-';
            }

            var html = [];
	html.push('<p><b>Vessel:</b> ' + row.VESSEL || '-' + '</p>');
            html.push('<p><b>Visit ID (Vessel):</b> ' + row.VISIT_ID || '-' + '</p>');
            html.push('<p><b>Custom Number:</b> ' + row.CUSTOMSNO || '-' + '</p>');
            html.push('<p><b>Custom Date:</b> ' + row.CUSTOMSDATE || '-' + '</p>');
html.push('<p><b>Voyage In:</b> ' + row.VOYAGE_IN || '-' + '</p>');
html.push('<p><b>Voyage Out:</b> ' + row.VOYAGE_OUT || '-' + '</p>');

            return html.join('');
        }

        $(document).ready(function(){
            $('#android_ready, #html5_ready').tooltip().show();

            $('#filter_date').daterangepicker({
    		    "format": 'DD/MM/YYYY',
    		    "minDate": "<?php echo date('d/m/Y', strtotime('-1 months')) ?>",
                "maxDate": "<?php echo date('d/m/Y') ?>",
    		});

    		$('#filter_date').on('apply.daterangepicker', function(ev, picker) {
    		    tanggal_start 	 = $('#filter_date').data('daterangepicker').startDate.format('YYYY-MM-DD');
    			tanggal_end   	 = $('#filter_date').data('daterangepicker').endDate.format('YYYY-MM-DD');
                var params = {query: {start: tanggal_start, end: tanggal_end}};
    			$('#tblcargo').bootstrapTable('refresh', params);
    		});

    		$('#filter_date').on('cancel.daterangepicker', function(ev, picker) {
    			tanggal_start 	 = '';
    			tanggal_end   	 = '';
                var params = {query: {start: tanggal_start, end: tanggal_end}};
    			$('#filter_date').val('');
    			$('#tblcargo').bootstrapTable('refresh', params);
    		});

		$("#btn_exp_excel").click(function(){
    		    var tanggal_start = "";
    		    var tanggal_end   = "";
    			if($("#filter_date").val()!=""){
    			    tanggal_start = $('#filter_date').data('daterangepicker').startDate.format('YYYY-MM-DD');
    			    tanggal_end   = $('#filter_date').data('daterangepicker').endDate.format('YYYY-MM-DD');
    			}

                // console.log(tanggal_start + ' s/d ' + tanggal_end);

    			var url = '<?php echo base_url() ?>dashboard/export_cargo?start=' + tanggal_start + '&end=' + tanggal_end;
                document.getElementsByName('frmDownload')[0].src = url;
    		});

        });
    </script>


</body>
</html>
