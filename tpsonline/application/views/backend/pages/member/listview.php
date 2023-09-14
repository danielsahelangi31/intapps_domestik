<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Member</h1>
			<p class="lead">
				<small>Data perusahaan yang terdaftar untuk akses SmartCargo.</small>
			</p>
			
			<div class="row ct-listview-toolbar">
				<div class="col-md-6">
                	<?php $this->load->view('backend/components/searchform') ?>
					
				</div>
				<div class="col-md-6">
					<div class="pull-right">
						<a href="<?php echo site_url('member/add') ?>" class="btn btn-primary">Member Baru</a>
					</div>
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
                        <td>
                        	<a href="<?php echo site_url('member/edit/'.$row->id) ?>" class="edit_link">Edit</a>
                            <!--a href="<?php //echo site_url('member/delete/'.$row->id) ?>" class="delete_link" onClick="alert('Yakin nih data mau dihapus?');">Hapus</a-->
                        </td>
					</tr>
					<?php
					}
					endif;
					?>
				</tbody>
			</table>
			<?php $this->load->view('backend/components/paging') ?>
		</div><!-- /.container -->
	</div>
	
    <?php $this->load->view('backend/elements/footer') ?>
</body>
</html>