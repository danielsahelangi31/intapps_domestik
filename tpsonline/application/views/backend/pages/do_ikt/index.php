<!DOCTYPE html>
<html lang="id">

<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
    <div id="wrap">
        <?php $this->load->view('backend/components/header') ?>
        <div class="container">
            <h2>DO IKT</h2>
            <hr />
            <table class="table table-striped table-condensed" id="do_ikt">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Message ID</th>
                        <th>No DO</th>
                        <th>No BL</th>
                        <th>VIN</th>
                        <th>Consignee</th>
                        <th>Customer</th>
                        <th>Carrier</th>
                        <th>Vessel Name</th>
                        <th>Vessel Voyage In</th>
                        <th>Vessel Call Sign</th>
                        <th>Vessel Voyage Out</th>
                        <th>Port Loading</th>
                        <th>Port Discharge</th>
                        <th>ATA</th>
                        <th>VIN Description</th>
                        <th>Record Time</th>
                        <th>Gross Weight</th>
                        <th>Do Release Date</th>
                        <th>Do Expired Date</th>
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
        var table = $('#do_ikt').DataTable({
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
                "url": '<?php echo site_url('do_ikt/doikt/getDoIkt'); ?>',
                "type": "POST"
            },
            "columnDefs": [{
                    "targets": [0],
                    "orderable": false,
                },
                {
                    "targets": [1, 5, 6, 7, 11, 12, 13, 14, 15, 16, 17, 18, 19],
                    "visible": false,
                    "searchable": true
                },
            ],
            //Set column definition initialisation properties.
        });
    });
    </script>
</body>

</html>