<!DOCTYPE html>
<html lang="id">
<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
    <div id="wrap">
        <?php $this->load->view('backend/components/header') ?>
        <div class="container">
            <h2>Data Kargo<?php if($VISIT_ID != null || $VISIT_ID != "") { echo "-".$VISIT_ID; }; ?></h2>
            <hr>
            <table id="example" class="table table-striped table-bordered" style="width:100%">
               <thead>
                <tr>
                    <th>Perusahaan Logistik</th>
                    <th>Truck</th>
                    <th>Supir</th>
                    <th>Waktu</th>
                    <th>No. Vin</th>
                    <th>No. SPPB</th>
                    <th>Hold Status</th>
                    <th>Status</th>
                    <th>Visit ID</th>
                    <th>Vessel</th>
                    <th>Bentuk</th>
                    <th>Jenis</th>
                    <th>Pembuat</th>
                    <th>Model</th>
                    <th>Penerima</th>
                    <th>Tujuan Terakhir</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
           </table>
        </div>
    </div>
    <?php $this->load->view('backend/elements/footer') ?>
</body>

<script type="text/javascript">

$(document).ready(function() {

    $('#example').DataTable({
        "responsive": true,
        "processing": true,
        "serverSide": true,
        "order": [],
        "ajax": {
          "url": "<?php echo base_url(); ?>dashboard/cargo_data",
          "type": "POST",
          "data": {
            "VISIT_ID": "<?php echo $VISIT_ID; ?>"
          }
        }
    });

});
</script>
</html>