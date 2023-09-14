<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
	<link href="<?php echo base_url('assets/css/std_invoice.css') ?>" rel="stylesheet">
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<div class="container">
			<div class="row">
            	<div class="col-md-8">
                	<h2>Invoice</h2>
                </div>
				<div class="col-md-4">
					<div class="pull-right back_list">
						
					</div>
				</div>
			</div>

			<form role="form" class="form-horizontal" action="" method="post">
			<input type="hidden" id="id" value="1">
			
			<div class="row">
				<div class="col-lg-6">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Nomor Delivery Order / Tanggal Kadaluarsa</legend>
						<div class="col-lg-12">
							<label><?php echo $del_req->nomor_do ?> / EXP. <?php echo $del_req->expired_do ?></label>
						</div>
					</fieldset>
				</div>

				<div class="col-lg-6">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Terminal</legend>
						<div class="col-lg-12">
							<label class="text-left"><?php echo $del_req->nama_terminal_petikemas ?>, <?php echo $del_req->nama_pelabuhan ?></label>
						</div>
					</fieldset>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<div class="proforma-preview-window">
						<div class="preview-button">
							<button class="btn btn-primary fr" onclick="alert('ucuy');return false;">Cetak Invoice</button>
						</div>
						
						<?php
						$rowh = array(
							'KD_UPER_LUNAS' => '010.010-13.09000002',
							'NO_DO' => '10882302',
							'NM_PBM' => 'PT. MERCEDES-BENZ INDONESIA',
							'NO_NPWP_PBM' => '01.000.082.6-092.000',
							'NO_BL' => 'HLCUATL130344034',
							'ALMT_PBM' => 'DESA WANAHERANG GUNUNG PUTRI BOGOR 16965',
							'NM_KAPAL' => 'CAPE FULMAR. MV',
							'VOYAGE_IN' => '093E',
							'TGL_JAM_TIBA' => '2013-09-08 10:14',
							'DISCOUNT' => '0',
							'ADMINISTRASI' => '20000',
							'TOTAL' => '739500',
							'PPN' => '73950',
							'KREDIT' => '813450',
							'TGL_SIMPAN' => '2013-09-08 10:14',
						);
						?>
						
						<div class="viewport" style="font-size:10pt">
							<div id="header">
								<div id="main_logo">
									<img src="<?php echo base_url('assets/img/ipc_logo.jpg') ?>" alt="IPC Logo" width="80px" />
								</div>
								<div id="kop">
									<h1 style="font-size:10pt; text-align:center; ">PT. PELABUHAN INDONESIA II (PERSERO)<br />CABANG TANJUNG PRIOK</h1>
									<table style="width:100%; font-size:8pt;">
										<tr>
											<td style="width:70px">ALAMAT</td>
											<td>: Jl. Raya Pelabuhan No. 9 Tanjung Priok Jakarta 14310</td>
										</tr>
										<tr>
											<td>NPWP</td>
											<td>: 0 1 . 0 6 1 . 0 0 5 . 3 - 0 9 3 . 0 0 0</td>
										</tr>
									</table>
								</div>
								<div id="nomor_invoice">
									NOMOR: <?php echo  $rowh['KD_UPER_LUNAS'] ?>
								</div>
							</div>
							<div id="sub_header">
								NOTA DAN PENGHITUNGAN PELAYANAN JASA: 
							</div>
							<div id="invoice_header">
								<table id="invoice_header_data">
									<tr>
										<td style="width:15%">Untuk Perusahaan</td>
										<td style="width:2%">:</td>
										<td style="width:23%"><?php echo $rowh["NM_PBM"]?></td>
										
										<td style="width:15%">Nomor D.O</td>
										<td style="width:2%">:</td>
										<td style="width:23%"><?php echo $rowh["NO_DO"]?></td>
									</tr>
									<tr>
										<td>NPWP</td>
										<td>:</td>
										<td><?php echo $rowh["NO_NPWP_PBM"]?></td>
										
										<td>Nomor B.L</td>
										<td>:</td>
										<td><?php echo $rowh["NO_BL"]?></td>
									</tr>
									<tr>
										<td>Alamat</td>
										<td>:</td>
										<td><?php echo $rowh["ALMT_PBM"]?></td>
										
										<td>Bongkar / Muat</td>
										<td>:</td>
										<td>BONGKAR</td>
									</tr>
									<tr>
										<td>Nama Kapal/Voyage</td>
										<td>:</td>
										<td><?php echo $rowh["NM_KAPAL"]?> / <?php echo $rowh["VOYAGE_IN"]?></td>
										
										<td>Tanggal Tiba</td>
										<td>:</td>
										<td><?php echo $rowh["TGL_JAM_TIBA"]?></td>
									</tr>
								</table>
							</div>
							<div id="invoice_detail">
								<div id="detail_wrap">
									<table id="invoice_detail_table">
										<thead>
											<tr class="title">
												<th class="title" width="5%">No</th>
												<th class="title" width="35%">Description</th>
												<th class="title" width="10%">Qty</th>
												<th class="title" width="10%">HZ</th>
												<th class="title" width="10%">Day</th>
												<th class="title" width="15%">Unit Price</th>
												<th class="title" width="15%">Total</th>
											</tr>
										</thead>
										<tbody>
										<?php
										$j=0;
										$rowsss = array();
										
										$rowsss['data'][] = array(
											'URAIAN_1' => 'TAMBAHAN SPPB MASA I-2 # 40 DRY FCL',
											'SIZE_CONT' => '40',
											'STY_NAME' => 'DRY',
											'NM_JENIS_PEMILIK' => 'FCL',
											'QTY' => 1,
											'HZ' => 'T',
											'TOTHARI' => 1,
											'TARIF' => 217600,
											'TOTTARIF' => 217600
										);
										
										$rowsss['data'][] = array(
											'URAIAN_1' => 'TAMBAHAN SPPB MASA I-2 # 40 DRY FCL',
											'SIZE_CONT' => '40',
											'STY_NAME' => 'DRY',
											'NM_JENIS_PEMILIK' => 'FCL',
											'QTY' => 1,
											'HZ' => 'T',
											'TOTHARI' => 1,
											'TARIF' => 217600,
											'TOTTARIF' => 217600
										);
										
										$rowsss['data'][] = array(
											'URAIAN_1' => 'TAMBAHAN SPPB MASA I-2 # 40 DRY FCL',
											'SIZE_CONT' => '40',
											'STY_NAME' => 'DRY',
											'NM_JENIS_PEMILIK' => 'FCL',
											'QTY' => 1,
											'HZ' => 'T',
											'TOTHARI' => 1,
											'TARIF' => 217600,
											'TOTTARIF' => 217600
										);
										
										foreach ($rowsss['data'] as $rowds){
											
											
											$j++ ;
										?>
										<tr class="item">
											<td class="left"><?php echo $j?></td>
											<td class="left"><?php echo $rowds["URAIAN_1"] ." # ". $rowds["SIZE_CONT"] ." ". $rowds["STY_NAME"] ." ". $rowds["NM_JENIS_PEMILIK"]    ?>&nbsp;</td>
											<td class="center"><?php echo $rowds["QTY"]?>&nbsp;</td>
											<td class="center"><?php echo $rowds["HZ"]?>&nbsp;</td>
											<td class="center"><?php echo $rowds["TOTHARI"]?>&nbsp;</td>
											<td class="right"><?php echo number_format($rowds["TARIF"],2)?>&nbsp;</td>
											<td class="right"><?php echo number_format($rowds["TOTTARIF"],2)?>&nbsp;</td>
										</tr>
										<?php
										}
										?>
										</tbody>
									</table>
									<table class="total" align="right" border="0" width="560px"> 
										<tr>
											<td class="item_total_detail" align="right" width="460px" ><b>Discount</b></td>
											<td class="item_total" align="right" width="97px">&nbsp;<?php echo number_format($rowh["DISCOUNT"],2)?></td>
										</tr>
										<tr>
											<td class="item_total_detail" align="right"><b>Administration</b></td>
											<td class="item_total" align="right">&nbsp;<?php echo number_format($rowh["ADMINISTRASI"],2)?></td>
										</tr>
										<tr>
											<td class="item_total_detail" align="right"><b>Total</b></td>
											<td class="item_total_project" align="right">&nbsp;<?php echo number_format($rowh["TOTAL"],2)?></td>
										</tr>
										<tr>
											<td class="item_total_detail" align="right"><b>Tax</b></td>
											<td class="item_total_project" align="right">&nbsp;<?php echo number_format($rowh["PPN"],2)?></td>
										</tr>
										<tr>
											<td class="item_total_detail" align="right"><b>Grand Total</b></td>
											<td class="item_total_project" align="right">&nbsp;<?php echo number_format($rowh["KREDIT"],2)?></td>
										</tr>
									</table>
								</div>
								<div id="detail_footer">
									<div id="signature">
										<p>TANJUNG PRIOK, <?php echo $rowh["TGL_SIMPAN"]?></p>
										<p>CABANG PELABUHAN TANJUNG PRIOK</p>
										<p class="center">MANAGER KEUANGAN</p>
										<div style="height:15mm" id="signature_image">
										
										</div>
										<p class="center" style="font-size:14px; text-decoration:underline;">TRY DJUNAIDY</p>
										<p class="center" style="font-size:14px;">NIPP.270066463</p>
									</div>
									<div style="clear:both"></div>
									<div id="invoice_note">
										<p>Tanggal Cetak: <?php echo date('d-m-Y H:i:s') ?></p>
										<p>Berlaku Sebagai Faktur Pajak berdasarkan peraturan dirjen pajak<br />No 10/PJ/2010 Tanggal 9 Maret 2010</p>
									</div>
								</div>
							</div>
							
							<div id="kwitansi_wrap">
								<div id="no_kwitansi">NOMOR: <?php echo  $rowh['KD_UPER_LUNAS'] ?></div>
								<div id="kwitansi_header">BUKTI PEMBAYARAN</div>
								
								<div id="kwitansi_data">
									<table>
										<tr>
											<td style="width:160px">SUDAH TERIMA DARI</td>
											<td style="width:10px">:</td>
											<td style="width:600px">&nbsp;</td>
										</tr>
										<tr>
											<td>BANYAKNYA UANG</td>
											<td>:</td>
											<td><b><i># <?php echo  getBilangan($rowh["KREDIT"])?> #</i></b></td>
										</tr>
										<tr>
											<td>UNTUK PEMBAYARAN</td>
											<td>:</td>
											<td>Penumpukan / Gerakan (DELIVERY)</td>
										</tr>
									</table>
								</div>
								<div style="height:2mm"></div>
								<div id="kwitansi_footer_left">
									<div id="kwitansi_sejumlah">Rp.<?php echo number_format($rowh["KREDIT"], 2)?>,-</div>
									<p style="font-size:6pt">NOMOR SERI : 2012 - </p>
								</div>
								<div id="kwitansi_footer_right">
									<p>JAKARTA, <?php echo $rowh["TGL_SIMPAN"]?></p>
									<p style="border-bottom:1px solid #000; height:12mm"></p>
								</div>
								
								<div class="clear"></div>
							</div>
						</div>	

					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<div class="pull-left">
						<button class="btn btn-success fr" onclick="alert('ucuy');return false;">Cetak Invoice</button>
					</div>
					<div class="pull-right">
						<a href="<?php echo site_url('delivery_request_og/assign_truck/'.$del_req->id) ?>" class="btn btn-primary fr">Pilih Trucking</a>
						<a href="<?php echo site_url('delivery_request_og/listview') ?>" class="btn btn-default">Batal</a>
					</div>
				</div>
			</div>
			</form>

		</div><!-- /.container -->
	</div>
	
	</div>
    <?php $this->load->view('backend/elements/footer') ?>

<script type="text/javascript">

</script>
</body>
</html>