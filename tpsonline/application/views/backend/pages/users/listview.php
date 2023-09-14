<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Users</h1>
			<p class="lead">
				<small>Data users yang terdaftar untuk akses SmartCargo.</small>
			</p>
			
			<div class="row ct-listview-toolbar">
				<div class="col-md-6">
                	<?php $this->load->view('backend/components/searchform') ?>
				</div>
				<div class="col-md-6">
					<div class="pull-right">
						<a href="<?php echo site_url('users/add') ?>" class="btn btn-primary">User Baru</a>
					</div>
				</div>
			</div>

			<hr />

			<table class="table table-striped table-condensed">
				<thead>
					<tr>
						<th>Username</th>
						<th>Perusahaan</th>
						<th>Nama Lengkap</th>
						<th>Email</th>
						<th>Telepon</th>
						<th>Handphone</th>
                        <th>Tindakan</th>
					</tr>
				</thead>
				<tbody>
					<?php
					if(isset($datasource)):
					foreach($datasource as $row){
					?>
					<tr>
						<td><?php echo $row->username ?></td>
                        <td><?php echo $row->nama_perusahaan ?></td>
                        <td><?php echo $row->nama_lengkap ?></td>
                        <td><?php echo $row->email ?></td>
                        <td><?php echo $row->telepon ?></td>
                        <td><?php echo $row->handphone ?></td>
                        <td>
                        	<a href="<?php echo site_url('users/edit/'.$row->id) ?>" class="edit_link">Edit</a> |
                            <a href="<?php echo site_url('users/delete/'.$row->id) ?>" class="delete_link" onClick="alert('Yakin nih data mau dihapus?');">Hapus</a>
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