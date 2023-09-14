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
			<div class="row">
            	<div class="col-md-8">
                	<h2>Pembayaran / Lihat Pra Nota</h2>
                </div>
				<div class="col-md-4">
					<div class="pull-right back_list">
						
					</div>
				</div>
			</div>

			<?php echo form_open(null, array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
			<input type="hidden" id="id" name="id" value="<?php echo $del_req->id ?>">
			
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
						<?php						
						$header = $invoice->header;
						?>
						<div class="viewport" style="font-size:10pt">
							<div id="header">
								<div id="main_logo">
									<img src="<?php echo base_url('assets/img/ipc_logo.jpg') ?>" alt="IPC Logo" width="80px" />
								</div>
								<div id="kop">
									<h1 style="font-size:10pt; text-align:center;">PT. PELABUHAN INDONESIA II (PERSERO)<br />CABANG TANJUNG PRIOK</h1>
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
									NOMOR: <?php echo $header->kdUper ?>
								</div>
								<div style="clear:both"></div>
							</div>
							<div id="sub_header">
								NOTA DAN PENGHITUNGAN PELAYANAN JASA: 
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
										$i = 1;
										if(is_array($invoice->detail)){
											$invoice_detail = $invoice->detail;
										}else{
											$invoice_detail = array($invoice->detail);
										}
										
										foreach ($invoice_detail as $detail){										
										?>
										<tr class="item">
											<td class="left"><?php echo $i++ ?></td>
											<td class="left"><?php echo $detail->uraian ." # ". 
											$detail->sizeCont ." ". 
											$detail->styName ." ". 
											$detail->nmJenisPemilik ?>&nbsp;</td>
											<td class="center"><?php echo $detail->qty ?>&nbsp;</td>
											<td class="center"><?php echo $detail->hazard ?>&nbsp;</td>
											<td class="center"><?php echo $detail->hazard ?>&nbsp;</td>
											<td class="right"><?php echo ($detail->tarif) ?>&nbsp;</td>
											<td class="right"><?php echo ($detail->total) ?>&nbsp;</td>
										</tr>
										<?php
										}
										?>
										</tbody>
									</table>
									<table class="total" align="right" border="0" width="560px"> 
										<tr>
											<td class="item_total_detail" align="right" width="460px" ><b>Discount</b></td>
											<td class="item_total" align="right" width="97px">&nbsp;<?php echo number_format((float) $header->discount, 2) ?></td>
										</tr>
										<tr>
											<td class="item_total_detail" align="right"><b>Administration</b></td>
											<td class="item_total" align="right">&nbsp;<?php echo number_format((float) $header->administrasi, 2) ?></td>
										</tr>
										<tr>
											<td class="item_total_detail" align="right"><b>Total</b></td>
											<td class="item_total_project" align="right">&nbsp;<?php echo number_format((float) $header->total, 2) ?></td>
										</tr>
										<tr>
											<td class="item_total_detail" align="right"><b>Tax</b></td>
											<td class="item_total_project" align="right">&nbsp;<?php echo number_format((float) $header->ppn, 2) ?></td>
										</tr>
										<tr>
											<td class="item_total_detail" align="right"><b>Grand Total</b></td>
											<td class="item_total_project" align="right">&nbsp;<?php echo number_format((float) $header->kredit, 2) ?></td>
										</tr>
									</table>
								</div>
							</div>
							
								

					</div>
					</div>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Metode Pembayaran</legend>
						<input type="radio" name="payment_channel" id="payment_channel_1" value="CP_MANDIRI"><label for="payment_channel_1">Mandiri Clickpay</label><br>
						<input type="radio" name="payment_channel" id="payment_channel_2" value="OTHER_NOT_CP"><label for="payment_channel_2">ATM / Teller / Internet Banking (Generate Invoice)</label>
					</fieldset>
				</div>
			</div>

			<div class="row">
				<div class="col-lg-12">
					<!--
					<div class="pull-left">
						<button class="btn btn-success fr" onclick="alert('ucuy');return false;">Cetak Proforma</button>
					</div>
					-->
					<div class="pull-right">
						<button class="btn btn-primary fr" type="submit">Bayar</button>
						<a href="<?php echo site_url('delivery_request_og/listview') ?>" class="btn btn-default">Kembali</a>
					</div>
				</div>
			</div>
			<?php echo form_close() ?>

		</div><!-- /.container -->
	</div>
	
    <?php $this->load->view('backend/elements/footer') ?>

<script type="text/javascript">

</script>
</body>
</html>