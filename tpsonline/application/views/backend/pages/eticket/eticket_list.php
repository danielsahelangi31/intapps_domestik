<!DOCTYPE html>
<html lang="id">
<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
<div id="wrap">
    <?php $this->load->view('backend/components/header') ?>

    <div class="container">

        <h2>E-Ticket List</h2>
        <hr />
        <table class="table table-striped table-condensed" id="eticket_list">
            <thead>
            <tr>
                <th>No</th>
                <th>Truck Code</th>
                <th>License Plate</th>
                <th>Driver Name</th>
                <th>KTP / SIM</th>
                <th>Truck Visit ID</th>
                <th>Trip</th>
                <th>Maker</th>
                <th>Last Change</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div><!-- /.container -->
</div>

<?php $this->load->view('backend/elements/footer') ?>

<script type="text/javascript">
    var table;

    $(document).ready(function() {

        //datatables
        var table = $('#eticket_list').DataTable({
            "processing": true,
            "serverSide": true,
            "deferRender": true,
            "bInfo" : false,
            "dom": 'Bfrtip',
            "buttons": [
                'colvis',
                'pageLength'
            ],
            "order": [],
            "ajax": {
                "url": '<?php echo site_url('eticket/eticket_list/get_items'); ?>',
                "type": "POST"
            },
            "columnDefs": [
                {
                    "targets": [0,2,9],
                    "orderable": false,
                    "searchable": false,
                },
                {
                    "targets": [3,4],
                    "orderable": false,
                    "visible": false,
                    "searchable": false,
                },
            ],
            //Set column definition initialisation properties.

        });

    });

</script>

</body>
</html>