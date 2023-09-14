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
    .imagenya{
        width: 200px;
        height: 100px;
        padding: 5px;
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
            <h2>Data Truck</h2>
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
                data-url="<?php echo site_url('dashboard/truck_data'); ?>"
                class="table table-striped table-bordered">
               <thead>
                <tr>
        			<th rowspan="2" data-field="VISIT_ID" data-visible="true" data-card-visible="false">Visit ID</th>
        			<th rowspan="2" data-field="TRUCKING" data-visible="true" data-card-visible="false">Perusahaan Truk</th>
                    <th rowspan="2" data-field="PLAT_NO" data-visible="true" data-card-visible="false">Plat Nomer</th>
                    <th rowspan="2" data-field="DRIVER" data-visible="true" data-card-visible="false">Supir</th>
                    <th colspan="3" class="text-center">Waktu</th>
        			<th rowspan="2" data-field="DIRECTION" data-visible="true" data-card-visible="false">Direction</th>
                    <th rowspan="2" data-field="STATUS_SPPB" data-visible="true" data-card-visible="false">Custom Status</th>
                    <th rowspan="2" data-field="SEGEL" data-visible="true" data-card-visible="false">Segel</th>
        			<!-- <th rowspan="2" data-field="VISIT_ID" data-visible="false" data-card-visible="true">Gate In</th>
                    <th rowspan="2" data-field="CUSTOMSNO" data-visible="false" data-card-visible="true">Gate Out</th> -->
                 </tr>
                 <tr>
                    <th class="text-center" data-field="GATE_IN" data-visible="true" data-card-visible="false">Operational</th>
                    <th class="text-center" data-field="COMPLETION" data-visible="true" data-card-visible="false">Complete</th>
                    <th class="text-center" data-field="GATE_OUT" data-visible="true" data-card-visible="false">Left</th>
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
            var html = [];
            // row.VISIT_ID = 'http://sunraysia.co.in/wp-content/uploads/2018/01/No_Image_Available-e1516101027970.jpg';
            // console.log(row);
            html.push('<p><b>Gate In:</b><p><br/><a target="_blank" href="' + row.CAM_1IN + '"><img class="imagenya" src="' + row.CAM_1IN + '"></a><a target="_blank" href="' + row.CAM_2IN + '"><img class="imagenya" src="' + row.CAM_2IN + '"></a><a target="_blank" href="' + row.CAM_3IN + '"><img class="imagenya" src="' + row.CAM_3IN + '"></a>');
            html.push('<p><b>Gate Out:</b><p><br/><a target="_blank" href="' + row.CAM_1OUT + '"><img class="imagenya" src="' + row.CAM_1OUT + '"></a><a target="_blank" href="' + row.CAM_2OUT + '"><img class="imagenya" src="' + row.CAM_2OUT + '"></a><a target="_blank" href="' + row.CAM_3OUT + '"><img class="imagenya" src="' + row.CAM_3OUT + '"></a>');
            return html.join('');
        }

        $(document).ready(function(){
            $('#android_ready, #html5_ready').tooltip().show();

            $('#filter_date').daterangepicker({
    		    format: 'DD/MM/YYYY'
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
                // alert('to be continue...');
    		    var tanggal_start = "";
    		    var tanggal_end   = "";
                
    			if($("#filter_date").val()!=""){
    			    tanggal_start = $('#filter_date').data('daterangepicker').startDate.format('YYYY-MM-DD');
    			    tanggal_end   = $('#filter_date').data('daterangepicker').endDate.format('YYYY-MM-DD');
    			}
                
                var url = '<?php echo base_url() ?>dashboard/export_truck?start=' + tanggal_start + '&end=' + tanggal_end;
                document.getElementsByName('frmDownload')[0].src = url;
    		});
        });
    </script>


</body>
</html>
