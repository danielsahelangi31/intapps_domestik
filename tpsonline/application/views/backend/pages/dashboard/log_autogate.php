<!DOCTYPE html>
<html lang="id">
<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
    <div id="wrap">
        <?php $this->load->view('backend/components/header') ?>
        <div class="container">
            <h2>Log Autogate</h2>
            <table id="example" class="table table-striped table-bordered" style="width:100%">
               <thead>
                <tr>
                    <th>LOG_TIME</th>
                    <th>GATE_TYPE</th>
                    <th>LICENSE_PLATE</th>
                    <th>CODE</th>
                    <th>MESSAGE</th>
                    <th>VISIT_ID</th>
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
          "url": "<?php echo base_url(); ?>dashboard/log_autogate_data",
          "type": "POST"
        }
    });

});
</script>
</html>