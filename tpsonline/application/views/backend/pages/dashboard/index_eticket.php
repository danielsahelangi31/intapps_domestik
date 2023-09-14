<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>

</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Selamat Datang di Eticket</h1>
			<p class="lead">
				<small>Informasi terkait E-Ticket.</small>
			</p>
			
			<hr />
			<?php if($this->userauth->getLoginData()->sender == 'IKT'){ ?>
			<div class="row">
	          <div class="col-lg-12">
	            <div class="card">
	              <div class="card-header border-0">
	                <div class="d-flex justify-content-between">
	                  <h3 class="card-title"></h3>
	                    <div class="row" style="margin-bottom: 20px;">
	                    	<div class="col-lg-6">
		                      <select class="form-control mr-2" name="bulan" id="bulan">
		                        <option value="ALL">--All Month--</option>
		                        <?php for($i = 1; $i <= 12; $i++){ ?>
		                        <option value="<?= $i ?>"><?= $bulan[$i-1] ?></option>
		                        <?php } ?>
		                      </select>
	                    	</div>
	                    	<div class="col-lg-6">
		                      <select class="form-control" name="tahun" id="tahun">
		                        <option value="ALL">--All Year--</option>
		                        <?php for($i = 2020; $i <= date('Y'); $i++){ ?>
		                        <option value="<?= $i ?>"><?= $i ?></option>
		                        <?php } ?>
		                      </select>
	                    	</div>
	                    </div>
	                </div>
	              </div>
	            </div>
	          </div>
			</div>
			<div class="row">
	          <div class="col-lg-6">
	            <div class="card">
	              <div class="card-header border-0">
	                <div class="d-flex justify-content-between">
	                  <h3 class="card-title"></h3>
	                </div>
	              </div>
	              <div class="card-body">
	                <div id="chart_eticket" style="height: 370px; width: 100%;"></div>
	              </div>
	            </div>
	          </div>
	          <div class="col-lg-6">
	          	<div class="card">
	              <div class="card-header border-0">
	                <div class="d-flex justify-content-between">
	                  <h3 class="card-title"></h3>
	                    
	                </div>
	              </div>
	              <div class="card-body">
	                <div id="chart_visit_id" style="height: 370px; width: 100%;"></div>
	              </div>
	            </div>
	          </div>
			</div>
			<div class="row">
	          <div class="col-lg-6">
	            <div class="card">
	              <div class="card-header border-0">
	                <div class="d-flex justify-content-between">
	                  <h3 class="card-title"></h3>
	                    
	                </div>
	              </div>
	              <div class="card-body">
	                <div id="chart_rc" style="height: 370px; width: 100%;"></div>
	              </div>
	            </div>
	          </div>
	          <div class="col-lg-6">
	          	<div class="card">
	              <div class="card-header border-0">
	                <div class="d-flex justify-content-between">
	                  <h3 class="card-title"></h3>
	                    
	                </div>
	              </div>
	              <div class="card-body">
	                <div id="chart_truck" style="height: 370px; width: 100%;"></div>
	              </div>
	            </div>
	          </div>
			</div>
			<div class="row">
	          <div class="col-lg-6">
	            <div class="card">
	              <div class="card-header border-0">
	                <div class="d-flex justify-content-between">
	                  <h3 class="card-title"></h3>
	                    
	                </div>
	              </div>
	              <div class="card-body">
	                <div id="chart_vin" style="height: 370px; width: 100%;"></div>
	              </div>
	            </div>
	          </div>
	          <div class="col-lg-6">
	          	<div class="card">
	              <div class="card-header border-0">
	                <div class="d-flex justify-content-between">
	                  <h3 class="card-title"></h3>
	                    
	                </div>
	              </div>
	              <div class="card-body">
	              </div>
	            </div>
	          </div>
			</div>
			<?php } ?>
		</div><!-- /.container -->
	</div>
	
    <?php $this->load->view('backend/elements/footer') ?>

<script type="text/javascript">
$(window).bind("pageshow", function() {
    $('#bulan').val('ALL');
    $('#tahun').val('ALL');
});
$(document).ready(function() { 
	function chart_eticket(bulan, tahun) {
	$.ajax({
	    url : "<?php echo site_url('dashboard_eticket/chart_eticket'); ?>",
	    method : "POST",
	    data : {bulan: bulan, tahun: tahun},
	    async : true,
	    dataType : 'json',
	    success: function(data){  
	    	Highcharts.chart('chart_eticket', {
            chart: {
                type: 'column',
            },
            title: {
                text: 'Error Create E-Ticket'
            },
            xAxis: {
                categories: ["404", "402", "399", "652", "350", "654", "660", "397"],
            },
            yAxis: {
                min: 0,
                title: {
                    text: 'Jumlah'
                }
            },
            tooltip: {
                headerFormat: '<span style="font-size:10px">Error Code: {point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">Jumlah: </td>' +
                    '<td style="padding:0"><b>{point.y:f}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                series: {
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function () {
                            	window.location.href = '<?= base_url() ?>'+'dashboard_eticket/report_eticket/'+this.status+'/'+bulan+'/'+tahun;
                            }
                        }
                    }
                }
            },
            exporting: { enabled: false },
            
            series: [{
                name: 'Error Code',
                data: [{
                        y: Number(data.ERR404),
                        status: 404,
                      },
                      {
                        y: Number(data.ERR402),
                        status: 402,
                      },
                      {
                        y: Number(data.ERR399),
                        status: 399,
                      },
                      {
                        y: Number(data.ERR652),
                        status: 652,
                      },
                      {
                        y: Number(data.ERR350),
                        status: 350,
                      },
                      {
                        y: Number(data.ERR654),
                        status: 654,
                      },
                      {
                        y: Number(data.ERR660),
                        status: 660,
                      },
                      {
                        y: Number(data.ERR397),
                        status: 397,
                      }
                ]

            }]
        });

	    }
	});
	return false;
	} 
	function chart_rc(bulan, tahun) {
	$.ajax({
	    url : "<?php echo site_url('dashboard_eticket/chart_rc'); ?>",
	    method : "POST",
	    data : {bulan: bulan, tahun: tahun},
	    async : true,
	    dataType : 'json',
	    success: function(data){  
	    	Highcharts.chart('chart_rc', {
            chart: {
                type: 'column',
            },
            title: {
                text: 'Return Cargo'
            },
            xAxis: {
                categories: ["Request", "Approved", "Reject"],
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
                    point: {
                        events: {
                            click: function () {
                            	window.location.href = '<?= base_url() ?>'+'dashboard_eticket/report_rc/'+this.status+'/'+bulan+'/'+tahun;
                            }
                        }
                    }
                }
            },
            exporting: { enabled: false },
            
            series: [{
                name: 'Status',
                data: [{
                        y: Number(data.REQUEST),
                        status: 1,
                      },
                      {
                        y: Number(data.APPROVED),
                        status: 2,
                      },
                      {
                        y: Number(data.REJECT),
                        status: 3,
                      },
                ]

            }]
        });
	    }
	});
	return false;
	}

	function chart_visit_id(bulan, tahun) {
	$.ajax({
	    url : "<?php echo site_url('dashboard_eticket/chart_visit_id'); ?>",
	    method : "POST",
	    data : {bulan: bulan, tahun: tahun},
	    async : true,
	    dataType : 'json',
	    success: function(data){  
	    	Highcharts.chart('chart_visit_id', {
            chart: {
                type: 'column',
            },
            title: {
                text: 'Status Visit ID'
            },
            xAxis: {
                categories: ["Announce", "Arrived", "Operational", "Completed", "Deleted"],
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
                    point: {
                        events: {
                            click: function () {
                            	window.location.href = '<?= base_url() ?>'+'dashboard_eticket/report_visit_id/'+this.status+'/'+bulan+'/'+tahun;
                            }
                        }
                    }
                }
            },
            exporting: { enabled: false },
            
            series: [{
                name: 'Status',
                data: [{
                        y: Number(data.ANNOUNCE),
                        status: 0,
                      },
                      {
                        y: Number(data.ARRIVED),
                        status: 2,
                      },
                      {
                        y: Number(data.OPERATIONAL),
                        status: 3,
                      },
                      {
                        y: Number(data.COMPLETED),
                        status: 4,
                      },
                      {
                        y: Number(data.DELETED),
                        status: 10,
                      },
                ]

            }]
        });
	    }
	});
	return false;
	} 
	
	function chart_truck(bulan, tahun) {
	$.ajax({
	    url : "<?php echo site_url('dashboard_eticket/chart_truck'); ?>",
	    method : "POST",
	    data : {bulan: bulan, tahun: tahun},
	    async : true,
	    dataType : 'json',
	    success: function(data){  
	    	Highcharts.chart('chart_truck', {
            chart: {
                type: 'column',
            },
            title: {
                text: 'Announce Truck'
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
                headerFormat: '<span style="font-size:10px">Maker: {point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">Jumlah: </td>' +
                    '<td style="padding:0"><b>{point.y:f}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                series: {
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function () {
                            	window.location.href = '<?= base_url() ?>'+'dashboard_eticket/report_truck/'+this.maker+'/'+bulan+'/'+tahun;
                            }
                        }
                    }
                }
            },
            exporting: { enabled: false },
            
            series: [{
                name: 'Maker',
                data: [{
                        y: Number(data.TOYOTA),
                        maker: "EVLS",
                      },
                      {
                        y: Number(data.DAIHATSU),
                        maker: "ADLES",
                      },
                      {
                        y: Number(data.MITSUBISHI),
                        maker: "MMKI",
                      },
                      {
                        y: Number(data.SUZUKI),
                        maker: "NSDS",
                      },
                      {
                        y: Number(data.OTHER),
                        maker: "OTHER",
                      },
                ]

            }]
        });
	    }
	});
	return false;
	} 
	function chart_vin(bulan, tahun) {
	$.ajax({
	    url : "<?php echo site_url('dashboard_eticket/chart_vin'); ?>",
	    method : "POST",
	    data : {bulan: bulan, tahun: tahun},
	    async : true,
	    dataType : 'json',
	    success: function(data){  
	      Highcharts.chart('chart_vin', {
            chart: {
                type: 'column',
            },
            title: {
                text: 'Announce VIN'
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
                headerFormat: '<span style="font-size:10px">Maker: {point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">Jumlah: </td>' +
                    '<td style="padding:0"><b>{point.y:f}</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },
            plotOptions: {
                series: {
                    cursor: 'pointer',
                    point: {
                        events: {
                            click: function () {
                            	window.location.href = '<?= base_url() ?>'+'dashboard_eticket/report_vin/'+this.maker+'/'+bulan+'/'+tahun;
                            }
                        }
                    }
                }
            },
            exporting: { enabled: false },
            
            series: [{
                name: 'Maker',
                data: [{
                        y: Number(data.TOYOTA),
                        maker: "EVLS",
                      },
                      {
                        y: Number(data.DAIHATSU),
                        maker: "ADLES",
                      },
                      {
                        y: Number(data.MITSUBISHI),
                        maker: "MMKI",
                      },
                      {
                        y: Number(data.SUZUKI),
                        maker: "NSDS",
                      },
                      {
                        y: Number(data.OTHER),
                        maker: "OTHER",
                      },
                ]

            }]
        });
	    }
	});
	return false;
	} 

	$('#t_return_cargo').DataTable({
		"processing": true,
		"serverSide": true,
		"deferRender": true,
		"pageLength": 5,
		"dom": 'Bfrtip',
		"buttons": [
		    'colvis',
		],
		"order": [],
		"ajax": {
		    "url": '<?= site_url() ?>'+'dashboard_eticket/get_rc/',
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
	
    var bulan=$('#bulan').val();
    var tahun=$('#tahun').val();
	chart_eticket(bulan, tahun);
	chart_visit_id(bulan, tahun);
	chart_rc(bulan, tahun);
	chart_truck(bulan, tahun);
	chart_vin(bulan, tahun);
	$('#bulan, #tahun').change(function() {
       var bulan=$('#bulan').val();
       var tahun=$('#tahun').val();
	   chart_eticket(bulan, tahun);
	   chart_visit_id(bulan, tahun);
	   chart_rc(bulan, tahun);
	   chart_truck(bulan, tahun);
	   chart_vin(bulan, tahun);
	});

	function onClick_eticket(e){
		var bulan=$('#bulan').val();
		var tahun=$('#tahun').val(); 
		window.location.href = '<?= base_url() ?>'+'dashboard_eticket/report_eticket/'+e.dataPoint.status+'/'+bulan+'/'+tahun;  
	}

	function onClick_visit_id(e){
		var bulan=$('#bulan').val();
		var tahun=$('#tahun').val(); 
		window.location.href = '<?= base_url() ?>'+'dashboard_eticket/report_visit_id/'+e.dataPoint.status+'/'+bulan+'/'+tahun;  
	}

	function onClick_truck(e){
		var bulan=$('#bulan').val();
		var tahun=$('#tahun').val(); 
		window.location.href = '<?= base_url() ?>'+'dashboard_eticket/report_truck/'+e.dataPoint.maker+'/'+bulan+'/'+tahun;  
	}

	function onClick_vin(e){
		var bulan=$('#bulan').val();
		var tahun=$('#tahun').val(); 
		window.location.href = '<?= base_url() ?>'+'dashboard_eticket/report_vin/'+e.dataPoint.maker+'/'+bulan+'/'+tahun;  
	}
    
});
</script>
</body>
</html>