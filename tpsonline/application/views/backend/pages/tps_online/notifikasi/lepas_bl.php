<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
<?php
$target_url = $this->router->fetch_class().'/'.$this->router->fetch_method();
if($this->router->fetch_directory()){
	$target_url = $this->router->fetch_directory().'/'.$target_url;
}
?>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">

			<h1>Lepas BL</h1>
			<p class="lead">
				<small></small>
			</p>
			<div class="row ct-listview-toolbar">
				<div class="col-md-6">
					<?php // $this->load->view('backend/components/searchform') ?>
                    <?php echo form_open('', array('class' => 'row form-inline', 'role' => 'form'));?>
                    <div class="form-group col-lg-4">
                       <input name="bl_no" class="form-control" placeholder="Masukkan No BL" value="<?php echo @$bl_no;?>">
                    </div>
                  
                    <div class="form-group col-lg-4">
                        <button type="submit" class="btn btn-default">Cari</button>
                       
                    </div>
                    <?php echo form_close();?>
				</div>
				<div class="col-md-6">
					<div class="pull-right">

					</div>
				</div>
			</div>
			
			<div class="row ct-listview-toolbar">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-striped table-condensed">
							<thead>
								<tr>
									<th>VISIT ID</th>
									<th>TYPE CARGO</th>
									<th>VIN</th>
									<th>MODEL NAME</th>
									<th>NUMBER POLICE</th>
									<th>ACTION</th>
								</tr>
							</thead>
							<tbody>
								<?php
								//$grid_state = $cfg->pagingURL.'/p:'.$cfg->currPage;

								if(@$list){
									foreach($list as $row){
								?>
								<tr>
									<td><?php echo $row->VISIT_ID ?></td>
									<td><?php echo $row->TYPE_CARGO ?></td>
                                    <td><?php echo $row->VIN ?></td>
									<td><?php echo $row->MODEL_NAME ?></td>
									<td><?php echo $row->NUMBER_POLICE ?></td>
									<td>
									<a href="<?php echo base_url('tps_online/notifikasi/lepas_bl/'.$row->BL_NUMBER.'/'.$row->VIN);?>">REMOVE FROM BL</a>
									</td>
								</tr>
								<?php
									}
								}else{
								?>
								<tr><td colspan="9"><em>Tidak ada data</em></td></tr>
								<?php
								}
								?>
							</tbody>
						</table>
					</div>

					<?php //$this->load->view('backend/components/paging') ?>
				</div>

		</div><!-- /.container -->
	</div>

    <?php $this->load->view('backend/elements/footer') ?>

	<script src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
	<script>	</script>
</body>
</html>
