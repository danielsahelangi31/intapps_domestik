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

			<h1>KIRIM TPS ONLINE</h1>
			<p class="lead">
				<small></small>
			</p>

			<div class="row ct-listview-toolbar">
				<div class="col-md-6">
					<?php // $this->load->view('backend/components/searchform') ?>
                    <?php echo form_open('', array('class' => 'row form-inline', 'role' => 'form'));?>
                    <div class="form-group col-lg-4">
                        <select class="form-control" name="year">
                        <option value=""> - Year - </option>
                        <?php 
                        for($i=2010;$i<=2030;$i++){?>
                            <option value="<?php echo $i;?>" <?php if($year==$i) echo 'selected';?>><?php echo $i;?></option>
                        <?php }?>
                        </select>
                    </div>
                     <div class="form-group col-lg-4">
                        <select class="form-control" name="month">
                        <option value=""> - Month - </option>
                        <?php 
                        $month = array(1=>'Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Des');
                        for($j=1;$j<=12;$j++){?>
                            <option value="<?php echo $j;?>" <?php  if($bulan==$j) echo 'selected';?>><?php echo $month[$j]?></option>
                        <?php }?>
                        </select>
                    </div>
                    <div class="form-group col-lg-4">
                        <button type="submit" class="btn btn-default">Cari</button>
                        <?php
                        if(post('keyword')){
                        ?>
                        <a href="<?php echo site_url($target_url) ?>" class="btn btn-warning">Reset</a>
                        <?php
                        }
                        ?>
                    </div>
                    <?php echo form_close();?>
				</div>
				<div class="col-md-6">
					<div class="pull-right">

					</div>
				</div>
			</div>

			<hr />

			<div class="row ct-listview-toolbar">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-striped table-condensed">
							<thead>
								<tr>
									<th><?php echo gridHeader('VISIT_ID', 'VISIT ID', $cfg) ?></th>
									<th><?php echo gridHeader('VISIT_NAME', 'VISIT NAME', $cfg) ?></th>
									<th><?php echo gridHeader('VOYAGE_IN', 'VOYAGE IN', $cfg) ?></th>
									<th><?php echo gridHeader('ARRIVAL', 'ARRIVAL', $cfg) ?></th>
									<th><?php echo gridHeader('DEPARTURE', 'DEPARTURE', $cfg) ?></th>
									<th><?php echo gridHeader('JML_BL', 'JML BL', $cfg) ?></th>
									<th><?php echo gridHeader('JML_VIN', 'JML VIN', $cfg) ?></th>
                                    <th><?php echo gridHeader('JML_CAR', 'JML CAR', $cfg) ?></th>
                                    <th><?php echo gridHeader('JML_VALID_CAR', 'VALID CAR', $cfg) ?></th>
                                     <th><?php echo gridHeader('JML_KMS', 'JML KMS', $cfg) ?></th>
                                    <th><?php echo gridHeader('JML_VALID_KMS', 'VALID KMS', $cfg) ?></th>
									<th>STATUS</th>
									<th>LIST BL</th>
								</tr>
							</thead>
							<tbody>
								<?php
								$grid_state = $cfg->pagingURL.'/p:'.$cfg->currPage;

								if($datasource){
									foreach($datasource as $row){
								?>
								<tr>
									<td>
										<a href="<?php echo site_url('tps_online/notifikasi/list_bl/' . $row->VISIT_ID) ?>" class="edit_link">
											<?php echo $row->VISIT_ID ?>
										</a>
									</td>
									<td><?php echo $row->VISIT_NAME ?></td>
									<td><?php echo $row->VOYAGE_IN ?></td>
									<td><?php echo $row->ARRIVAL ?></td>
									<td><?php echo $row->DEPARTURE ?></td>
									<td><?php echo $row->JML_BL ?></td>
									<td><?php echo $row->JML_VIN ?></td>
                                    <td><?php echo $row->JML_CAR ?></td>
                                    <td><?php echo $row->JML_VALID_CAR ?></td>
                                    <td><?php echo $row->JML_KMS ?></td>
                                    <td><?php echo $row->JML_VALID_KMS ?></td>
									<td></td>
									<td>
                                    <a href="<?php echo site_url('tps_online/notifikasi/list_bl/' . $row->VISIT_ID) ?>" class="edit_link">
                                    VIEW LIST BL</a>
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

					<?php $this->load->view('backend/components/paging') ?>
				</div>

		</div><!-- /.container -->
	</div>

    <?php $this->load->view('backend/elements/footer') ?>

	<script src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
	<script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
	<script>	</script>
</body>
</html>
