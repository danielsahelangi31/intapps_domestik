<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Aktivasi Member Baru</h1>
			<p class="lead">
				<small>Data perusahaan baru yang belum diperiksa.</small>
			</p>
			
			<div class="row ct-listview-toolbar">
				<div class="col-md-8">
                	<?php $this->load->view('backend/components/searchform') ?>
				</div>
			</div>

			<hr />

			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th>ID</th>
						<th>Nama Perusahaan</th>
						<th>NPWP</th>
						<th>Alamat</th>
						<th>Terdaftar Sebagai</th>
						<th>Tanggal Bergabung</th>
                        <th>Tindakan</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if(isset($datasource)):
					foreach($datasource as $row){
						$terdaftar_sebagai = array();
						if($row->freight_forwarder_id){
							$terdaftar_sebagai[] = 'Freight Forwarder';
						}
						
						if($row->trucking_company_id){
							$terdaftar_sebagai[] = 'Trucking Company';
						}
					?>
					<tr>
						<td><?php echo $row->id ?></td>
                        <td><?php echo $row->nama_perusahaan ?></td>
                        <td><?php echo $row->npwp ?></td>
                        <td><?php echo $row->alamat ?></td>
                        <td><?php echo implode(', ', $terdaftar_sebagai) ?></td>
                        <td><?php echo date('d-M-Y H:i:s', strtotime($row->waktu_bergabung)) ?></td>
                        <td>
                        	<a href="#" class="confirm-approval" data-id="<?php echo $row->id?>" >Approval</a>
                        </td>
					</tr>
					<?php
					}
					endif;
					?>
				</tbody>
			</table>
			<?php $this->load->view('backend/components/paging') ?>
			
			<!-- Modal -->
	  		<div class="modal fade" id="approval">
	    		<div class="modal-dialog">
	      			<div class="modal-content">
	        			<div class="modal-body">
							<label class="text-left">Nama Perusahaan : </label>
							<label id="nama-perusahaan"></label>
							<br/>
							<label class="text-left">NPWP : </label>
							<label id="nomor-npwp"></label>
							<br/>
							<label class="text-left">Alamat : </label>
							<label id="alamat-perusahaan"></label>	
							<br/>
							<label class="text-left">Telepon : </label>
							<label id="telepon-perusahaan"></label>	
							<br/>
							<label class="text-left">Fax : </label>
							<label id="fax-perusahaan"></label>	
							<br/>

	          				Approve atau Reject member ini ?
	        			</div>
	        			<div class="modal-footer">
							<button type="button" class="btn btn-primary" id="btn-approve">Approve</button>
	        				<button type="button" class="btn btn-primary" id="btn-reject">Reject</button>
	          				<button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
	        			</div>
	      			</div><!-- /.modal-content -->
	    		</div><!-- /.modal-dialog -->
	  		</div><!-- /.modal -->

		</div><!-- /.container -->
	</div>
	
    <?php $this->load->view('backend/elements/footer') ?>

<script type="text/javascript">
$('.confirm-approval').on('click', function(e) {
	e.preventDefault();
	var id = $(this).data('id');
    $('#approval').data('id', id).modal('show');

	$.post('<?php echo site_url("member_aux/retrieve")?>', 
    		{
				id : $(this).data('id')
			},
    		function(data) {
				if(data.status){
					$("#nama-perusahaan").html(data.nama_perusahaan);
					$("#nomor-npwp").html(data.npwp);
					$("#alamat-perusahaan").html(data.alamat);
					$("#telepon-perusahaan").html(data.telepon);
					$("#fax-perusahaan").html(data.fax);
				}else{}
			},
			'JSON'
	);
});



$('#btn-approve').click(function() {
    // handle approval here
  	var id = $('#approval').data('id');
	$('#approval').modal('hide');
	window.parent.location.href = '<?php echo site_url("member/approve")?>' + '/' + id;
  	//$('[data-id='+id+']').remove();
});

$('#btn-reject').click(function() {
    // handle rejection here
  	var id = $('#approval').data('id');
	$('#approval').modal('hide');
	window.parent.location.href = '<?php echo site_url("member/reject")?>' + '/' + id;
});

</script>
</body>
</html>