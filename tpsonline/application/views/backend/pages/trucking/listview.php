<!DOCTYPE html>
<html lang="id">
<head>
	<?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
    <div id="wrap">
        <?php $this->load->view('backend/components/header') ?>

        <div class="container">

            <h1>Pesanan Trucking Delivery</h1>
			<p class="lead">
				<small>Data pesanan dari Freight Forwarder melalui Smart Cargo</small>
			</p>
			
            <div class="row ct-listview-toolbar">
                <div class="col-md-6">
                    <?php $this->load->view('backend/components/searchform') ?>
                </div>
            </div>

            <hr />

            <table class="table table-striped table-condensed">
                <thead>
					<tr>
						<th>No. Container</th>
						<th>Tanggal</th>
						<th>Terminal</th>
						<th>Consignee</th>
						<th>Tanggal Ambil</th>
						<th>Jenis</th>
						<th>Tindakan</th>
					</tr>
                </thead>
                <tbody>
                <?php
                if($datasource){
                    foreach($datasource as $row){

                ?>
                <tr>
                    <td><?php echo $row->container_number ?></td>
                    <td><?php echo date('d-M-Y H:i:s', strtotime($row->waktu_input)) ?></td>
                    <td><?php echo $row->nama_terminal_petikemas ?></td>
                    <td><?php echo $row->consignee ?></td>
                    <td><?php echo date('d-M-Y', strtotime($row->rencana_ambil)) ?></td>
                    <td><?php echo $row->iso_code ? $row->iso_code : $row->container_size.$row->container_type ?></td>
                    <td>
						<?php
						if($row->truck_id){
						?>
						<a href="<?php echo site_url('trucking/driver_reset/'. $row->id) ?>" class="edit_link">Reset Supir</a> |
						<a href="#" class="edit_link view-ticket" data-id="<?php echo $row->id?>">Lihat Tiket</a>
						<?php
						}else{
						?>
                        <a href="<?php echo site_url('trucking/driver_assign/'. $row->id) ?>" class="edit_link">Pilih Supir</a>
						<?php
						}
						?>
                    </td>
                </tr>
                <?php
                    }
                }else{
                ?>
                <tr><td colspan="8"><em>Tidak ada data</em></td></tr>
                <?php
                }
                ?>
                </tbody>
            </table>

            <?php $this->load->view('backend/components/paging') ?>
        </div><!-- /.container -->
    </div>	

    <?php $this->load->view('backend/elements/footer') ?>

<!-- Modal -->
    <div class="modal fade" id="ticket-preview">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <label class="text-left">No Tiket : </label>
                    <label id="nomor-tiket"></label>
                    <br/>
                    <label class="text-left">No HP : </label>
                    <label id="nomor-hp"></label>
                    <br/>
                    <label class="text-left">Nama Supir  : </label>
                    <label id="nama-supir"></label>
                    <br/>
                    <label class="text-left">Security Code : </label>
                    <label id="security-code"></label>
                    <br/>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

<script type="text/javascript">
$('.view-ticket').on('click', function(e) {
    e.preventDefault();
    var id = $(this).data('id');
    $('#ticket-preview').data('id', id).modal('show');

    $.post('<?php echo site_url("trucking_aux/retrieve_trucking_contact_data")?>', 
            {
                id : $(this).data('id')
            },
            function(data) {
                $("#nomor-tiket").html(data.nomor_tiket);
                $("#nomor-hp").html(data.nomor_hp);
                $("#nama-supir").html(data.nama_supir);
                $("#security-code").html(data.security_code);
            },
            'JSON'
    );
});

</script>
</body>
</html>