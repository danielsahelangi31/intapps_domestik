<!DOCTYPE html>
<html lang="id">
<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
<div id="wrap">
    <?php $this->load->view('backend/components/header') ?>

    <div class="container">

        <h2>Truck ID List</h2>
        <p class="lead">
            <small>Data list truck id oleh perusahaan.</small>
        </p>

        <div class="row ct-listview-toolbar">
            <div class="col-md-6">
                <?php $this->load->view('backend/components/eticket/searchTruckID') ?>
            </div>
        </div>

        <hr />

        <table class="table table-striped table-condensed">
            <thead>
            <tr>
                <th>No</th>
                <th>Truck ID</th>
                <th>Carrier Company</th>
                <th>Action</th>
            </tr>
            </thead>
            <tbody>
                <?php
                    foreach ($datas as $index=>$item){
                    ?>
                        <tr>
                            <td><? echo $index+1; ?></td>
                            <td><? echo $item->license; ?></td>
                            <td><? echo  $item->carrier; ?></td>
                            <td><a href="<?php echo site_url('eticket/truck_id_detail/'.$item->code) ?>" class="btn btn-default">Detail</a></td>
                        </tr>
                    <?php
                    }

                ?>
            </tbody>
        </table>
    </div><!-- /.container -->
</div>

<?php $this->load->view('backend/elements/footer') ?>
</body>
</html>