<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<div class="container">
			
			<h1>Error Create E-Ticket</h1>
			<p class="lead">
				<table border="0">
					<tr>
						<td><b>Error Code</b></td>
						<td> : </td>
						<td><?= $status ?></td>
					</tr>
					<?php if($status == '404'){ ?>
					<tr>
						<td><b>Penyebab</b></td>
						<td> : </td>
						<td>Truck Tidak ditemukan, Biasanya terdapat spasi pada plat nomor atau data master truck dicartos yang tidak lengkap.</td>
					</tr>
					<tr>
						<td><b>Solusi</b></td>
						<td> : </td>
						<td>Koordinasi dengan pihak service desk untuk pengecekan data master truck pada aplikasi cartos.</td>
					</tr>
					<tr>
						<td><b>Saran</b></td>
						<td> : </td>
						<td>Dilakukan singkronisasi data master truck antara carmaker dan IKT.</td>
					</tr>
					<?php }else if($status == '402'){ ?>
					<tr>
						<td><b>Penyebab</b></td>
						<td> : </td>
						<td>Data belum online online.</td>
					</tr>
					<tr>
						<td><b>Saran</b></td>
						<td> : </td>
						<td>Koordinasi dengan pengurus untuk memastikan dokumen yang dibawa tidak salah.</td>
					</tr>
					<?php }else if($status == '399'){ ?>
					<tr>
						<td><b>Penyebab</b></td>
						<td> : </td>
						<td>Dokumen telah lewat 1 bulan.</td>
					</tr>
					<tr>
						<td><b>Solusi</b></td>
						<td> : </td>
						<td>Adakan sosialisasi kepada pengurus perihal masa berlaku dokumen SPPB.</td>
					</tr>
					<?php }else if($status == '652'){ ?>
					<tr>
						<td><b>Penyebab</b></td>
						<td> : </td>
						<td>Vin sudah di asosiasi ke TRK yang lain.</td>
					</tr>
					<tr>
						<td><b>Solusi</b></td>
						<td> : </td>
						<td>Pastikan data vin sudah benar, jika sudah benar dan tetep error maka dilakukan remove asosiasi.</td>
					</tr>
					<?php }else if($status == '350'){ ?>
					<tr>
						<td><b>Penyebab</b></td>
						<td> : </td>
						<td>Truck masih berada di lungkungan IKT / truck belum keluar IKT.</td>
					</tr>
					<tr>
						<td><b>Saran</b></td>
						<td> : </td>
						<td>Dilakukan sosialisasi Kembali kepada carmaker atau pengurus agar melalukan announce truck setelah truck keluar dari IKT.</td>
					</tr>
					<?php }else if($status == '654'){ ?>
					<tr>
						<td><b>Penyebab</b></td>
						<td> : </td>
						<td>Gagal melakukan asosiasi, karena VIN sudah diasosiasi dengan trip yang lain.</td>
					</tr>
					<tr>
						<td><b>Solusi</b></td>
						<td> : </td>
						<td>Pastikan data vin sudah benar, jika sudah benar dan tetep error maka dilakukan remove asosiasi.</td>
					</tr>
					<?php }else if($status == '660'){ ?>
					<tr>
						<td><b>Penyebab</b></td>
						<td> : </td>
						<td>Gagal asosiasi karena VIN sudah onterminal.</td>
					</tr>
					<tr>
						<td><b>Solusi</b></td>
						<td> : </td>
						<td>Dilakukan proses pengembalian VIN ke posisi Announce.</td>
					</tr>
					<tr>
						<td><b>Saran</b></td>
						<td> : </td>
						<td>agar pihak IKT maupun carmaker jangan melakukan scan unit jika fisik unit belum ada di IKT.</td>
					</tr>
					<?php }else if($status == '397'){ ?>
					<tr>
						<td><b>Penyebab</b></td>
						<td> : </td>
						<td>Data tidak sama dengan yang ada di CEISA.</td>
					</tr>
					<tr>
						<td><b>Solusi</b></td>
						<td> : </td>
						<td>Pastikan pengurus selalu membawa dokumen yang benar.</td>
					</tr>
				<?php } ?>
				</table>
			</p>
			
			<hr />

			<div class="row">
	          <div class="col-lg-12">
	            <div class="card">
	              <div class="card-header border-0">
	                <div class="d-flex justify-content-between">
	                  <h3 class="card-title"></h3>
	                </div>
	              </div>
	              <div class="card-body">
	          	<table class="table table-striped table-condensed" id="t_error_eticket">
	                <thead>
	                    <tr>
	                        <th>No</th>
	                        <th>License Plate</th>
	                        <th>Maker</th>
	                        <th>Response Msg</th>
	                        <th>Record Time</th>
	                    </tr>
	                </thead>
	                <tbody>
	                </tbody>
	            </table>
	              </div>
	            </div>
	          </div>
			</div>
		</div><!-- /.container -->
	</div>
	
    <?php $this->load->view('backend/elements/footer') ?>

<script type="text/javascript">
$(document).ready(function() { 

	$('#t_error_eticket').DataTable({
		"processing": true,
		"serverSide": true,
		"deferRender": true,
		"dom": 'Bfrtip',
		"buttons": [
		    'colvis',
		    'pageLength'
		],
		"order": [],
		"ajax": {
		    "url": '<?= site_url() ?>dashboard_eticket/get_eticket/<?= $status ?>/<?= $bulan ?>/<?= $tahun ?>',
		    "type": "POST"
		},
		"columnDefs": [{
		        "targets": [0, 2],
		        "orderable": false,
		    },
		    {
		        "targets": [1, 2, 3],
		        "visible": true,
		        "searchable": true
		    },
		]
		});
    
});
</script>
</body>
</html>