<!DOCTYPE html>
<html lang="id">
<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
<div id="wrap">
    <?php $this->load->view('backend/components/header') ?>

    <div class="container">

        <h2>Asosiasi Check by Truck Code</h2>

        <hr />

        <div class="row">
            <form role="form" class="form-horizontal" action="" method="post">
                <div class="col-md-4 col-md-offset-4">
                    <div class="form-group text-center">
                        <label >Truck Code *</label>
                        <input type="text" class="form-control" id="truck_code"
                               name="truck_code" placeholder="Enter Truck Code (without space)"
                        />
                        <?php echo form_error('truck_code', '<div class="error">', '</div><br/>'); ?>
                        <div class="error"></div>
                    </div>
                    <div class="form-group text-center">
                        <label class="text-left">Gate *</label>
                        <select class="form-control" name="type_gate">
                            <option value="">-- Select --</option>
                            <option value="IN" >IN</option>
                            <option value="OUT" >OUT</option>
                        </select>
                        <?php echo form_error('type_gate', '<div class="error">', '</div><br/>'); ?>
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