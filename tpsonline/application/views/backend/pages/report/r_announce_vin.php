<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Announce VIN</h1>
			<p class="lead">
				<small>Maker: <?= $ket ?></small>
			</p>
			
			<hr />

			<div class="row">
	          <div class="col-lg-12">
	            <div class="card">
	              <div class="card-header border-0">
	                <div class="d-flex justify-content-between">
	                  <h3 class="card-title"></h3>
	                </div>
	              </div>
	              <div class="card-body">
	              	<div id="chart_maker" style="height: 370px; width: 100%;"></div>
		          	<table class="table table-striped table-condensed" id="t_return_cargo">
		                <thead>
		                    <tr>
		                        <th>No</th>
		                        <th>Document Transfer ID</th>
		                        <th>Fuel</th>
		                        <th>Status</th>
		                        <th>Maker</th>
		                        <th>Record Time</th>
		                    </tr>
		                </thead>
		                <tbody>
		                </tbody>
		            </table>
	              </div>
	            </div>
	          </div>
			</div>
		</div><!-- /.container -->
	</div>
	
    <?php $this->load->view('backend/elements/footer') ?>

<script type="text/javascript">
$(document).ready(function() { 

	$('#t_return_cargo').DataTable({
		"processing": true,
		"serverSide": true,
		"deferRender": true,
		"dom": 'Bfrtip',
		"buttons": [
		    'colvis',
		    'pageLength'
		],
		"order": [],
		"ajax": {
		    "url": '<?= site_url() ?>dashboard_eticket/get_vin/<?= $maker ?>/<?= $bulan ?>/<?= $tahun ?>',
		    "type": "POST"
		},
		"columnDefs": [{
		        "targets": [0],
		        "orderable": false,
		    },
		    {
		        "targets": [1, 2, 3],
		        "visible": true,
		        "searchable": true
		    },
		]
		});

	function chart_maker() {
	var bulan=$('#bulan').val();
	var tahun=$('#tahun').val();
	$.ajax({
	    url : "<?= site_url() ?>dashboard_eticket/chart_maker_vin/<?= $maker ?>/<?= $bulan ?>/<?= $tahun ?>",
	    method : "POST",
	    data : {bulan: bulan, tahun: tahun},
	    async : true,
	    dataType : 'json',
	    success: function(data){ 
	    	Highcharts.chart('chart_maker', {
            chart: {
                type: 'pie',
            },
            title: {
                text: 'Status Transaksi'
            },
            xAxis: {
                categories: ["Toyota", "Daihatsu", "Mitsubishi", "Suzuki", "Other"],
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Jumlah'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">Status: {point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">Jumlah: </td>' +
                    '<td style="padding:0"><b>{point.y:f}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                series: {
                    cursor: 'pointer',
                   
                },
                pie: {
                    allowPointSelect: true,
                    cursor: 'pointer',
                    dataLabels: {
                        enabled: false
                    },
                    showInLegend: true
                }
            },
            exporting: { enabled: false },
            
            series: [{
                name: 'Status',
                data: [{
                        y: Number(data.SUKSES),
                        name: "Sukses",
                      },
                      {
                        y: Number(data.GAGAL),
                        name: "Gagal",
                      },
                ]

            }]
        }); 
	    }
	});
	return false;
	} 
	chart_maker();

    
});
</script>
</body>
</html>