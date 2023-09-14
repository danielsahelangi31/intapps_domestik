<!DOCTYPE html>
<html lang="id">
<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
<div id="wrap">
    <?php $this->load->view('backend/components/header') ?>

    <div class="container">

        <h2>Update VIN</h2>

        <hr />

        <div class="row">
            <form role="form" class="form-horizontal" action="" method="post">
                <div class="col-md-4 col-md-offset-4">
                        <div class="form-group text-center">
                            <label >Truck Visit ID</label>
                            <input type="text" class="form-control" id="truck_id"
                                   name="truck_id" placeholder="Enter Truck Visit ID"
                                    />
                            <?php echo form_error('truck_id', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div>
                        </div>
                    <div class="form-group text-center">
                        <button type="submit" class="btn btn-primary">Next > </button>
                    </div>
                </div>

            </form>
        </div>


    </div><!-- /.container -->
</div>

<?php $this->load->view('backend/elements/footer') ?>
</body>
</html>