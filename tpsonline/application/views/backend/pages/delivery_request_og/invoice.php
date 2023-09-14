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
									<table>
										<tr>
											<td>No. Nota</td>
											<td><?php echo $invoice->nomor_faktur_pajak ?>
										</tr>
										<tr>
											<td>No. Doc</td>
											<td><?php echo $del_req->nomor_request_inhouse ?>
										</tr>
										<tr>
											<td>Tgl. Proses</td>
											<td><?php echo date('d-m-Y H:i:s', strtotime($invoice->tanggal_terbit)) ?>
										</tr>
										<tr>
											<td>Halaman</td>
											<td>1/1
										</tr>
									</table>
								</div>
								
								<div style="clear:both"></div>
							</div>
							<div id="sub_header">
								NOTA DAN PENGHITUNGAN PELAYANAN JASA: PENUMPUKAN / GERAKAN (DELIVERY PETIKEMAS)
							</div>
							<div id="invoice_header">
								<table id="invoice_header_data">
									<tr>
										<td style="width:15%">Untuk Perusahaan</td>
										<td style="width:2%">:</td>
										<td style="width:23%"><?php echo $member->nama_perusahaan ?></td>
										
										<td style="width:15%">Nomor D.O</td>
										<td style="width:2%">:</td>
										<td style="width:23%"><?php echo $del_req->nomor_do ?></td>
									</tr>
									<tr>
										<td>NPWP</td>
										<td>:</td>
										<td><?php echo $member->npwp ?></td>
										
										<td>Nomor B.L</td>
										<td>:</td>
										<td><?php echo $del_req->nomor_bl ?></td>
									</tr>
									<tr>
										<td>Alamat</td>
										<td>:</td>
										<td><?php echo $member->alamat ?></td>
										
										<td>Bongkar / Muat</td>
										<td>:</td>
										<td>BONGKAR</td>
									</tr>
									<tr>
										<td>Nama Kapal/Voyage</td>
										<td>:</td>
										<td><?php echo $del_req->call_sign ?> / <?php echo $del_req->voyage ?></td>
										
										<td>Tanggal Tiba</td>
										<td>:</td>
										<td><?php echo $del_req->tanggal_datang ?></td>
									</tr>
								</table>
							</div>
							<div id="invoice_detail">
								<div id="detail_wrap">
									<table id="invoice_detail_table">
										<thead>
											<tr class="title">
												<th class="title" width="5%">No</th>
												<th class="title" width="20%">Keterangan</th>
												<th class="title" width="10%">Tgl Awal</th>
												<th class="title" width="10%">Tgl Akhir</th>
												<th class="title" width="5%">Box</th>
												<th class="title" width="3%">Size</th>
												<th class="title" width="3%">Type</th>
												<th class="title" width="3%">Status</th>
												<th class="title" width="3%">HZ</th>
												<th class="title" width="3%">Hari</th>
												<th class="title" width="15%">Unit Price</th>
												<th class="title" width="15%">Total</th>
											</tr>
										</thead>
										<tbody>
										<?php
										$i = 1;
										foreach ($invoice->detail as $detail){
										?>
										<tr class="item">
											<td class="left"><?php echo $i++ ?></td>
											<td class="left"><?php echo $detail->uraian ?>&nbsp;</td>
											<td class="center"><?php echo $detail->tanggal_awal ? date('d-M-Y', strtotime($detail->tanggal_awal)) : '' ?>&nbsp;</td>
											<td class="center"><?php echo $detail->tanggal_akhir ? date('d-M-Y', strtotime($detail->tanggal_akhir)) : '' ?>&nbsp;</td>
											<td class="center"><?php echo $detail->qty ?>&nbsp;</td>
											<td class="center"><?php echo $detail->size_cont ?>&nbsp;</td>
											<td class="center"><?php echo $detail->sty_name ?>&nbsp;</td>
											<td class="center"><?php echo $detail->status_cont ?>&nbsp;</td>
											<td class="center"><?php echo $detail->hazard ? 'Y' : 'T' ?>&nbsp;</td>
											<td class="center"><?php echo $detail->total_hari ?>&nbsp;</td>
											<td class="right"><?php echo number_format($detail->tarif, 2)?>&nbsp;</td>
											<td class="right"><?php echo number_format($detail->total, 2)?>&nbsp;</td>
										</tr>
										<?php
										}
										?>
										</tbody>
									</table>
									<table class="total" align="right" border="0" width="560px"> 
										<tr>
											<td class="item_total_detail" align="right" width="460px" ><b>Diskon</b></td>
											<td class="item_total" align="right" width="97px">&nbsp;<?php echo number_format($invoice->discount, 2) ?></td>
										</tr>
										<tr>
											<td class="item_total_detail" align="right"><b>Administrasi</b></td>
											<td class="item_total" align="right">&nbsp;<?php echo number_format($invoice->administrasi, 2) ?></td>
										</tr>
										<tr>
											<td class="item_total_detail" align="right"><b>Dasar Pengenaan</b></td>
											<td class="item_total_project" align="right">&nbsp;<?php echo number_format($invoice->total, 2)?></td>
										</tr>
										<tr>
											<td class="item_total_detail" align="right"><b>Jumlah PPN</b></td>
											<td class="item_total_project" align="right">&nbsp;<?php echo number_format($invoice->ppn, 2)?></td>
										</tr>
										<tr>
											<td class="item_total_detail" align="right"><b>Materai</b></td>
											<td class="item_total_project" align="right">&nbsp;<?php echo number_format($invoice->materai, 2)?></td>
										</tr>
										<tr>
											<td class="item_total_detail" align="right"><b>Jumlah Dibayar</b></td>
											<td class="item_total_project" align="right">&nbsp;<?php echo number_format($invoice->kredit, 2) ?></td>
										</tr>
									</table>
								</div>
								<?php
								if($invoice->flag_lunas){
								?>								
								<div id="detail_footer">
									<div id="signature">
										<p>TANJUNG PRIOK, <?php echo date('d F Y', strtotime($invoice->tanggal_terbit)) ?></p>
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
								<?php
								}
								?>
							</div>

							<?php
							if($invoice->flag_lunas){
							?>
							<div id="kwitansi_wrap">
								<div id="no_kwitansi">NOMOR: <?php echo  $invoice->kode_uper ?></div>
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
											<td><b><i># <?php echo  getBilangan($invoice->kredit) ?> #</i></b></td>
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
									<div id="kwitansi_sejumlah">Rp.<?php echo number_format($invoice->kredit, 2)?>,-</div>
									<p style="font-size:6pt">NOMOR SERI : <?php echo date('Y') ?> - </p>
								</div>
								<div id="kwitansi_footer_right">
									<p>JAKARTA, </p>
									<p style="border-bottom:1px solid #000; height:12mm"></p>
								</div>
								
								<div class="clear"></div>
							</div>
							<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
			<div class="row">
				<div class="col-lg-12">
					<div class="pull-left">
						<a href="<?php echo site_url('delivery_request_og/invoice/'.$del_req->id.'/print') ?>" class="btn btn-success fr" target="_blank">Cetak Invoice</a>
					</div>
					<div class="pull-right">
						<?php
						if($invoice->flag_lunas){
						?>
						<a href="<?php echo site_url('delivery_request_og/assign_truck/'.$del_req->id) ?>" class="btn btn-primary fr">Pilih Trucking</a>
						<?php
						}else{
						?>
						<a href="#" class="btn btn-default disabled fr" title="Harap lunasi invoice terlebih dahulu">Pilih Trucking</a>
						<?php
						}
						?>
						<a href="<?php echo site_url('delivery_request_og/listview') ?>" class="btn btn-default">Kembali</a>
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