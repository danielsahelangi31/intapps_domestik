<!DOCTYPE html>
<html lang="id">
<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
<div id="wrap">
    <?php $this->load->view('backend/components/header') ?>

    <div class="container">

        <h2>Document Inquiry List</h2>
        <hr />
        <table class="table table-striped table-condensed" id="inquiry_doc">
            <thead>
            <tr>
                <th>No</th>
                <th>VIN</th>
                <th>MAKER</th>
                <th>DESTINATION</th>
                <th>MODEL</th>
                <th>ENGINE NO</th>
                <th>VESSEL NAME</th>
                <th>ETD</th>
                <th>PEB NO</th>
                <th>PEB DATE</th>
                <th>BC DOC NO</th>
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
        var table = $('#inquiry_doc').DataTable({
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
                "url": '<?php echo site_url('doc_inquiry/inquiry/get_docs'); ?>',
                "type": "POST"
            },
            "columnDefs": [
                {
                    "targets": [ 0 ],
                    "orderable": false,
                },
                {
                    "targets": [ 3,4,5,6,7 ],
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