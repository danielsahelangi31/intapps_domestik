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

			<h1>Notifikasi Export</h1>
			<p class="lead">
				<small></small>
			</p>

			<!--<div class="row ct-listview-toolbar">
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

			<hr />-->

			<div class="row ct-listview-toolbar">
				<div class="col-md-12">
					<div class="table-responsive">
						<table class="table table-striped table-condensed">
							<thead>
								<tr>
									<th>NO</th>
                                	<th rowspan="2"><?php echo gridHeader('BL_NUMBER', 'BL NUMBER', $cfg) ?></th>
									<th rowspan="2"><?php echo gridHeader('TYPE_CARGO', 'TYPE CARGO', $cfg) ?></th>
									<th rowspan="2"><?php echo gridHeader('JML_VIN', 'JML VIN', $cfg) ?></th>
									<th><?php echo gridHeader('JML_SUKSES_CODECO', 'JML SUKSES GATE IN/OUT', $cfg) ?></th>
									<th><?php echo gridHeader('JML_SUKSES_CODECO', 'JML SELISIH GATE IN/OUT', $cfg) ?></th>
									<th><?php echo gridHeader('JML_SUKSES_COARRI', 'JML SUKSES LOAD/DISCHARGE', $cfg) ?></th>
									<th><?php echo gridHeader('JML_SUKSES_CODECO', 'JML SELISIH LOAD/DISCHARGE', $cfg) ?></th>
									<th rowspan="2"><?php echo gridHeader('REMARK2',  'REMARK CODECO', $cfg) ?></th>
									<th rowspan="2"><?php echo gridHeader('REMARK', 'REMARK COARRI', $cfg) ?></th>
								</tr>
							</thead>
							<tbody>
								<?php
								$grid_state = $cfg->pagingURL.'/p:'.$cfg->currPage;
								
								$no = 1;
								// count total
								$total_vin 			 = 0;
								// codeco
								$total_sukses_codeco = 0; 
								$total_selisih_codeco = 0;
								// coarri
								$total_sukses_coarri  = 0;
								$total_selisih_coarri = 0;
								// count total
								
								if($datasource){
									
									foreach($datasource as $row){

										//print_r($row);
								?>
								<tr>
									<td><?php echo $no++ ?></td>
                                	<td>
									<a href="<?php echo site_url('tps_online/notifikasi/bl_detail/' . $row->BL_NUMBER.'/'.$row->VISIT_ID.'/'.$row->TYPE_CARGO) ?>" class="edit_link">
									<?php echo $row->BL_NUMBER ?>
                                    </a></td>
									<td><?php echo $row->TYPE_CARGO ?></td>
									<td><?php echo $row->JML_VIN ?></td>
									<td><?php echo $row->JML_SUKSES_CODECO ?></td>
									<td><?php echo $row->JML_VIN - $row->JML_SUKSES_CODECO ?></td>
									<td><?php echo $row->JML_SUKSES_COARRI ?></td>
									<td><?php echo $row->JML_VIN - $row->JML_SUKSES_COARRI ?></td>
									<td><?php echo $row->REMARK2 ?></td>
									<td><?php echo $row->REMARK ?></td>

									<!-- count total -->
									<?php $total_vin += $row->JML_VIN ?>
									<!-- codeco -->
									<?php $total_sukses_codeco += $row->JML_SUKSES_CODECO ?>
									<?php $total_selisih_codeco += $row->JML_VIN - $row->JML_SUKSES_CODECO ?>
									<!-- coarri -->
									<?php $total_sukses_coarri += $row->JML_SUKSES_COARRI ?>
									<?php $total_selisih_coarri += $row->JML_VIN - $row->JML_SUKSES_COARRI ?>

									<!-- count total -->
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
							
							<tfoot>
								<tr style="background-color: yellow;">
									<th colspan="2" class="text-center" style="background-color: red;">TOTAL</th>
									<th><?php echo $total_vin ?></th>
									<!-- codeco -->
									<th><?php echo $total_sukses_codeco ?></th>
									<th><?php echo $total_selisih_codeco ?></th>
									<!-- coarri -->
									<th><?php echo $total_sukses_coarri ?></th>
									<th><?php echo $total_selisih_coarri ?></th>
									<th></th>
									<th></th>
									<th></th>
								</tr>
							</tfoot>
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
