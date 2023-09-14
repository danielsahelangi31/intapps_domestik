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

			<h1>Notifikasi</h1>
			<p class="lead">
				<small></small>
			</p>

		

			<div class="row ct-listview-toolbar">
				<div class="col-md-12">
					<div class="table-responsive" style="width:100%; height:430px; overflow:auto">
						<table class="table table-striped table-condensed">
							<thead>
								<tr>
                                    <th>VISIT ID</th>
                                    <th>VIN</th>
                                    <th>BL NUMBER</th>
                                    <th>BL NUMBER DATE</th>
                                    <th>CUSTOMS NUMBER</th>
                                    <th>CUSTOMS DATE</th>
                                    <th>TYPE CARGO</th>
                                    <th>WEIGHT</th>
                                    <th>DTS ONTERMINAL</th>
                                    <th>DTS LEFT</th>
                                    <th>NUMBER POLICE</th>
                                    <th>FLAG SEND CODECO</th>
                                    <th>FLAG SEND COARRI</th>
                                    <th>DTS ANNOUNCED</th>
                                    <th>DIRECTION</th>
                                    <th>DIRECTION TYPE</th>
                                    <th>NO SPPB</th>
                                    <th>JENIS KEMASAN</th>
                                    <th>JUMLAH</th>
                                    <th>INWARD BC11</th>
                                    <th>INWARD BC11 DATE</th>
                                    <th>OUTWARD BC11</th>
                                    <th>OUTWARD BC11 DATE</th>
                                    <th>REMARK</th>
                                    <th>NO NPE</th>
                                    <th>NPE DATE</th>
                                    <th>CONSIGNEE ID</th>
                                    <th>CONSIGNEE NAME</th>
                                    <th>MERK</th>
                                    <th>BRUTO</th>
                                    <th>IN_OUT_DOC</th>
                                    <th>IN OUT DOC DATE</th>
                                    <th>KD DOK</th>
                                    <th>DISCHARGER PORT</th>
                                    <th>NEXT PORT</th>

								</tr>
							</thead>
							<tbody>
								<?php
								$grid_state = $cfg->pagingURL.'/p:'.$cfg->currPage;

								if($datasource){
									foreach($datasource as $row){
										//echo '<pre>';
										//print_r($row);
										//echo '</pre>';
								?>
								<tr>
                                	<td><?php echo $row->VISIT_ID ?></td>
                                    <td>
									<a href="<?php echo site_url('tps_online/notifikasi/view_by_vin/' . $row->VIN.'/'.$row->BL_NUMBER) ?>" class="edit_link">
									<?php echo $row->VIN ?>
                                    </a></td>
                                    <td align="center">
									<?php if($row->BL_NUMBER==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                    <?php if($row->BL_NUMBER_DATE==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->CUSTOMS_NUMBER==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->CUSTOMS_DATE==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
									</td>
                                    <td>
                                     <?php if($row->TYPE_CARGO==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->WEIGHT==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->DTS_ONTERMINAL==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>	
                                    <td>
                                     <?php if($row->DTS_LEFT==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>	
                                    <td>
                                     <?php if($row->NUMBER_POLICE==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->FLAG_SEND_CODECO==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>	
                                    <td>
                                     <?php if($row->FLAG_SEND_COARRI==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>	
                                    <td>
                                     <?php if($row->DTS_ANNOUNCED==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->DIRECTION==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>	
                                    <td>
                                     <?php if($row->DIRECTION_TYPE==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->NO_SPPB==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>	
                                    <td>
                                     <?php if($row->JNS_KMS==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->JUMLAH==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>	
                                    <!-- <td></td> -->	
                                    <td>
                                     <?php if($row->INWARD_BC11==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>	
                                    <td>
                                     <?php if($row->INWARD_BC11_DATE==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->OUTWARD_BC11==""){ ?>
                                    <i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
                                    <?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->OUTWARD_BC11_DATE==""){ ?>
                                    <i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
                                    <?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->REMARK==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->NO_NPE==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->NPE_DATE==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->CONSIGNEE_ID==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->CONSIGNEE_NAME==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->MERK==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->BRUTO==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->IN_OUT_DOC==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->IN_OUT_DOC_DATE==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->KD_DOK==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->DISCHARGER_PORT==""){ ?>
									<i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
									<?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
                                    </td>
                                    <td>
                                     <?php if($row->NEXT_PORT==""){ ?>
                                    <i class="glyphicon glyphicon-remove-sign text-danger text-center"></i>
                                    <?php } else{ ?>
                                    <i class="glyphicon glyphicon-ok-sign text-success text-center"></i>
                                    <?php } ?>
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
