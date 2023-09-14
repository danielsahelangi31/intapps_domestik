<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>
		<div class="container">
			<h2>Data Truck</h2>
            <hr>
			<table id="example" class="table table-striped table-bordered" style="width:100%">
    	       <thead>
                <tr>
                    <th>Visit ID</th>
                    <th>Trucking</th>
                    <th>No. Plat</th>
                    <th>Supir</th>
                    <th>Gate In</th>
                    <th>Gate Out</th>
                    <th>Waktu</th>
                    <th>Status SPPB</th>
                    <th>Status</th>
                    <th>Bentuk</th>
                    <th>NPE Export</th>
                    <th>Hold Export</th>
                    <th>Kargo Export</th>
                    <th>Status NPE</th>
                    <th>Kargo Import</th>
                    <th>Hold Import</th>
                    <th>SPPB Import</th>
                </tr>
			  </thead>
			  <tbody>
			  </tbody>
	       </table>
		</div>
	</div>
    <?php $this->load->view('backend/elements/footer') ?>
</body>

<script type="text/javascript">

$(document).ready(function() {

	$('#example').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
          "url": "<?php echo base_url(); ?>dashboard/truck_data",
          "type": "POST"
        }
    });

});
</script>
</html>