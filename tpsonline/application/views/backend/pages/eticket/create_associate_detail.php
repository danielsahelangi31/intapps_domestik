<!DOCTYPE html>
<html lang="id">
<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
<div id="wrap">
    <?php $this->load->view('backend/components/header') ?>

    <div class="container">

        <h2>Create Associate</h2>

        <hr />

        <div class="row">
            <form role="form" class="form-horizontal" action="<? echo site_url('eticket/submit_create_associate'); ?>" method="post">
                <div class="col-lg-6">

                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="text-left">Truck ID</label>
                            <input type="hidden" name="truck_id" value="<?php echo $datas->code; ?>">
                            <input readonly type="text" class="form-control"
                                   name="license_plate" placeholder=""
                                   value="<?php echo $datas->license; ?>" />
                            <?php echo form_error('username', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="text-left">Ticket Number</label>
                            <input readonly type="text" class="form-control" name="ticket_number"
                                   placeholder="" value="<?php echo $datas->eticket; ?>" />
                            <?php echo form_error('password', '<div class="error">', '</div><br/>'); ?>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="text-left">Plan Date <?php echo date('m/d/Y',strtotime($datas->plan_date)); ?></label>
                            <input type="text" class="form-control date"  name="plan_date" value="<?php echo $datas->plan_date;?>"  />

                            <?php echo form_error('passconf', '<div class="error">', '</div><br/>'); ?>
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Save</button>
                        </div>
                    </div>

                </div>
                <div class="col-lg-6">
                    <table class="table table-striped table-condensed">
                        <thead>
                        <tr>
                            <th>Status</th>
                            <th>VIN Number</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                        foreach ($datas->vin as $index=>$item){
                            ?>
                            <tr>
                                <td><? echo $item->status; ?></td>
                                <td><? echo $item->vin_number; ?></td>
                            </tr>
                            <?php
                        }

                        ?>
                        </tbody>
                    </table>
                </div>

            </form>
        </div>


    </div><!-- /.container -->
</div>

<?php $this->load->view('backend/elements/footer') ?>
</body>
</html>