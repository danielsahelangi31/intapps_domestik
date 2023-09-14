<!DOCTYPE html>
<html lang="id">
<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
<div id="wrap">
    <?php $this->load->view('backend/components/header') ?>

    <div class="container">

        <h2>Truck Detail</h2>

        <hr />

        <div class="row">
            <form role="form" class="form-horizontal" action="" method="post">
                <div class="col-lg-6">

                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="text-left">License Plate</label>
                            <input readonly type="text" class="form-control" id="username"
                                   name="username" placeholder=""
                                   value="<?php echo $datas->license; ?>" />
                            <?php echo form_error('username', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="text-left">Carrier Company</label>
                            <input readonly type="text" class="form-control" name="password"
                                   placeholder="" value="<?php echo $datas->carrier; ?>" />
                            <?php echo form_error('password', '<div class="error">', '</div><br/>'); ?>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="text-left">Driver Name</label>
                            <input readonly type="text" class="form-control" name="passconf"
                                   placeholder="" value="<?php echo $datas->driver; ?>" />
                            <?php echo form_error('passconf', '<div class="error">', '</div><br/>'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="text-left">Company Owner</label>
                            <input readonly type="text" class="form-control" name="nama_lengkap"
                                   placeholder="" value="<?php echo $datas->company; ?>" />
                            <?php echo form_error('nama_lengkap', '<div class="error">', '</div><br/>'); ?>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="text-left">Truck Type</label>
                            <input readonly type="text" class="form-control" name="handphone"
                                   placeholder="" value="<?php echo $datas->truck_type; ?>" />
                            <?php echo form_error('handphone', '<div class="error">', '</div><br/>'); ?>
                        </div>
                    </div>
                </div>

            </form>
        </div>


    </div><!-- /.container -->
</div>

<?php $this->load->view('backend/elements/footer') ?>
</body>
</html>