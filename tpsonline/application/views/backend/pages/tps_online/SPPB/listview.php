<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Data SPPB</h1>
			<p class="lead">
				<small></small>
			</p>
			
			<div class="row ct-listview-toolbar">
				<div class="col-md-6">
					<?php $this->load->view('backend/components/searchform') ?>
				</div>
				<div class="col-md-6">
					<div class="pull-right">
						
					</div>
				</div>
			</div>
			
			<hr />
			
			<div class="table-responsive">
				<table class="table table-striped table-condensed">
					<thead>
						<tr>
							<th><?php echo gridHeader('NM_ANGKUT', 'Vessel', $cfg) ?></th>
							<th><?php echo gridHeader('NO_VOY_FLIGHT', 'Voyage', $cfg) ?></th>
							<th><?php echo gridHeader('NO_BL_AWB', 'Nomor BL', $cfg) ?></th>
							<th><?php echo gridHeader('TGL_BL_AWB', 'Tanggal BL', $cfg) ?></th>
							<th><?php echo gridHeader('NO_SPPB', 'Nomor SPPB', $cfg) ?></th>
							<th><?php echo gridHeader('NAMA_IMP', 'Importir', $cfg) ?></th>
							<th><?php echo gridHeader('JUMLAH_CARGO', 'Jumlah Cargo', $cfg) ?></th>
							<th><?php echo gridHeader('SUM_RELEASE_CARGO', 'Cargo Release', $cfg) ?></th>
							<th>Tindakan</th>
						</tr>
					</thead>
					<tbody>
						<?php
						$grid_state = $cfg->pagingURL.'/p:'.$cfg->currPage;
						
						if($datasource){
							foreach($datasource as $row){
						?>
						<tr>
							<td><?php echo $row->NM_ANGKUT ?></td>
							<td><?php echo $row->NO_VOY_FLIGHT ?></td>
							<td><?php echo $row->NO_BL_AWB ?></td>
							<td><?php echo $row->TGL_BL_AWB ?></td>
							<td><?php echo $row->NO_SPPB ?></td>
							<td><?php echo $row->NAMA_IMP ?></td>
							<td><?php echo $row->JUMLAH_CARGO ?></td>
							<td><?php 
							if ($row->SUM_RELEASE_CARGO == 0)
									{echo "0";}
							else 
									{echo $row->SUM_RELEASE_CARGO ;}
							?></td>
							<td>
								<a href="<?php echo site_url('tps_online/sppb/view/'.$row->NO_BL_AWB.'/'.$grid_state) ?>" class="edit_link">Lihat</a>
							</td>
						</tr>
						<?php
							}
						}else{
						?>
						<tr><td colspan="7"><em>Tidak ada data</em></td></tr>
						<?php	
						}
						?>
					</tbody>
				</table>
			</div>
			
			<?php $this->load->view('backend/components/paging') ?>
		</div><!-- /.container -->
	</div>

    <?php $this->load->view('backend/elements/footer') ?>
</body>
</html>