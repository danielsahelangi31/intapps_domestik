	<div id="footer">
      <div class="container">
		<div id="android_ready" class="android" data-toggle="tooltip" title="Android Device Ready"></div>
		<div id="html5_ready" class="html5" data-toggle="tooltip" title="HTML 5 Based"></div>
        
		<p class="text-muted credit">&copy; 2013 <a href="http://www.ilcs.co.id" target="_blank">PT. Integrasi Logistik Cipta Solusi</a></p>
		
	  </div>
    </div>
    
	<div id="android_browser_ready"></div>
	<div id="powered_by_html5"></div>
	
	
	<!-- Bootstrap core JavaScript
	<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	
	================================================== -->
	<!-- Placed at the end of the document so the pages load faster -->
	<script src="<?php echo base_url('assets/js/jquery-2.0.3.min.js') ?>"></script>
	
	<script src="<?php echo base_url('assets/js/bootstrap.js') ?>"></script>
	<script src="<?php echo base_url('assets/js/jquery-ui.js') ?>"></script>
	<script src="<?php echo base_url('assets/js/jquery.maskedinput.js') ?>"></script>
	<script src="<?php echo base_url('assets/js/bootstrap-datepicker.js') ?>"></script>
	<!-- <script src="<?php echo base_url('assets/js/jquery-3.3.1.js') ?>"></script> -->
	<script src="<?php echo base_url('assets/js/jquery.dataTables.min.js') ?>"></script>
	<script src="<?php echo base_url('assets/js/dataTables.bootstrap.min.js') ?>"></script>
  	<script type="text/javascript" src="<?php echo base_url('assets/js/moment.js') ?>"></script>
  	<script type="text/javascript" src="<?php echo base_url('assets/js/daterangepicker.js') ?>"></script>
	<!-- Select2 -->
	<script src="<?=base_url()?>assets/select2/dist/js/select2.full.min.js"></script>
	<script src="<?=base_url()?>assets/colvis/dataTables.buttons.min.js"></script>
	<script src="<?=base_url()?>assets/colvis/buttons.colVis.min.js"></script>
	<script src="<?=base_url()?>assets/js/sweetalert.min.js"></script>

	
	<script type="text/javascript">
	var datepickerBase = {
		autoclose: true,
		weekStart : 1,
		forceParse : true,
		language : 'id',
		orientation : 'top auto',
		format : 'dd-mm-yyyy'
	}
	
	$(document).ready(function(){
        $(".alert-success").delay(5000).slideUp(200, function() {
            $(this).alert('close');
        });

		$('#android_ready, #html5_ready').tooltip().show();
		
	
		$.fn.datepicker.dates['id'] = {
			days: ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu", "Minggu"],
			daysShort: ["Min", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab", "Min"],
			daysMin: ["Mg", "Sn", "Se", "Rb", "Km", "Jm", "Sa", "Mg"],
			months: ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "November", "Desember"],
			monthsShort: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
			today: "Hari Ini",
			clear: "Bersihkan"
		};
		
		$('.date').datepicker(datepickerBase);
	});
	</script>
	
	