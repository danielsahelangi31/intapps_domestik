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
        <?php
        if($this->session->flashdata('stats')) {
            ?>
            <div class="alert alert-success fade in">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                <h4><?php echo 'Status : '.$this->session->flashdata('stats') ?></h4>
            </div>
            <?php
        }
        ?>
        <hr />

        <div class="row">
            <form role="form" class="form-horizontal" action="" method="post">
                <div class="col-lg-6">

                    <div class="col-lg-12">
                        <div class="form-group">
                            <input type="hidden" name="truck_code" value="<?php echo $datas->TRUCK_CODE; ?>">
                            <label class="text-left">License Plate * </label>
                            <input type="text" class="form-control"
                                   name="license_plate" placeholder=""
                                   value="<?php echo $datas->LICENSEPLATE; ?>" />
                            <?php echo form_error('license_plate', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="text-left">Carrier Company</label>
                            <select class="form-control js-example-basic-single" name="carrier_code" >
                                <option value="">-- Select --</option>
                                <?php
                                foreach ($carriers as $carrier){
                                    ?>
                                    <option value="<? echo $carrier['id']; ?>" <?php echo $datas->CARRIER_CODE == $carrier['id'] ? 'selected' : '' ;  ?> ><? echo $carrier['id'].' - '.$carrier['text']; ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="text-left">Driver Name</label>
                            <input type="text" class="form-control" name="driver_name"
                                   placeholder="" value="<?php echo $datas->DRIVER_NAME; ?>" />
                            <?php echo form_error('driver_name', '<div class="error">', '</div><br/>'); ?>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="text-left">Company Owner</label>
                            <select class="form-control js-example-basic-single" name="owner_code" >
                                <option value="">-- Select --</option>
                                <?php
                                foreach ($owners as $owner){
                                    ?>
                                    <option value="<? echo $owner['id'] ?>" <?php echo $datas->CARRIER_CODE == $owner['id'] ? 'selected' : '' ;  ?> ><? echo $owner['id'].' - '.$owner['text'] ?></option>
                                    <?php
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <label class="text-left">Truck Type *</label>
                            <select class="form-control" name="desc_type">
                                <option value="">-- Select --</option>
                                <option value="TOWING" <? echo $datas->DESC_TYPE == 'TOWING' ? 'selected' : ''; ?> >TOWING</option>
                                <option value="CC" <? echo $datas->DESC_TYPE == 'CC' ? 'selected' : ''; ?> >CC</option>
                                <option value="SELFDRIVE" <? echo $datas->DESC_TYPE == 'SELFDRIVE' ? 'selected' : ''; ?> >SELFDRIVE</option>
                                <option value="DINAS" <? echo $datas->DESC_TYPE == 'DINAS' ? 'selected' : ''; ?> >DINAS</option>
                            </select>
                            <?php echo form_error('desc_type', '<div class="error">', '</div><br/>'); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group text-center">
                    <button type="submit" class="btn btn-primary">Save Data</button>
                </div>
            </form>
        </div>


    </div><!-- /.container -->
</div>

<?php $this->load->view('backend/elements/footer') ?>

<script type="text/javascript">

    $(document).ready(function () {
        $('select.js-example-basic-single').select2();
    });


</script>

</body>
</html>