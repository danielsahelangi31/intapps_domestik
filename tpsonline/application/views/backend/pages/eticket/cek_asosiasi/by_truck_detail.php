<!DOCTYPE html>
<html lang="id">
<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
<div id="wrap">
    <?php $this->load->view('backend/components/header') ?>

    <div class="container">

        <div class="row">
            <div class="col-md-8">
                <h2>Asosiasi Check by Truck Code</h2>
                <?php
                if($response){
                    if($response->code !=10 ){
                        ?>
                        <h4><?php echo $response->message; ?></h4>
                        <?php
                    }
                }
                ?>
            </div>
            <div class="col-md-4">
                <div class="pull-right back_list">

                </div>
            </div>
        </div>

        <?php echo form_open('#', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>


        <div class="row">
            <div class="col-lg-6">
                <fieldset class="delivery-request-border">
                    <legend class="delivery-request-border">Truck Information</legend>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Truck Code</label>
                        <div class="col-lg-8">
                            <p class="form-control-static"><?php echo $InfoTruck->TruckCode ? $InfoTruck->TruckCode : 'No Data'; ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Truck License Plate</label>
                        <div class="col-lg-8">
                            <p class="form-control-static"><?php echo $InfoTruck->licenseplate ? $InfoTruck->licenseplate : 'No Data' ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Truck Type</label>
                        <div class="col-lg-8">
                            <p class="form-control-static"><?php echo $InfoTruck->TruckType ? $InfoTruck->TruckType : 'No Data' ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Driver</label>
                        <div class="col-lg-8">
                            <p class="form-control-static"><?php echo $InfoTruck->TruckDriver ? $InfoTruck->TruckDriver : 'No Data' ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 control-label visit_id_loading">Visit ID</label>
                        <div class="col-lg-8">
                            <p class="form-control-static"><?php echo $InfoTruck->VisitID ? $InfoTruck->VisitID : 'No Data' ?></p>
                        </div>
                    </div>
                </fieldset>
                <fieldset class="delivery-request-border">
                    <legend class="delivery-request-border">Trip Information</legend>
                    <div class="form-group">
                        <label class="col-lg-4 control-label">Trip</label>
                        <div class="col-lg-8">
                            <p class="form-control-static"><?php echo $InfoTrip->Trip ? $InfoTrip->Trip : 'No Data'; ?></p>
                        </div>
                    </div>
                    <?php

                    if($InfoTrip->Trip){
                        foreach ($InfoTrip->TripID as $index => $item){
                            ?>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Trip ID <?php echo ($index+1); ?> </label>
                                <?php
                                foreach ($item->ID as $data){
                                    ?>
                                    <div class="col-lg-8">
                                        <p class="form-control-static"><?php echo $data ? $data : 'No Data'; ?></p>
                                    </div>
                                    <?php
                                }
                                ?>
                            </div>
                            <?php
                        }
                    }

                    ?>
                </fieldset>
            </div>
            <div class="col-lg-6">
                <fieldset class="delivery-request-border">
                    <legend class="delivery-request-border">List VIN</legend>
                    <?php
                    if (isset($ListVIN[0]->VIN)){
                        foreach ($ListVIN[0]->VIN as $indexed => $vin){
                            ?>
                            <div class="form-group">
                                <div class="col-lg-12">
                                    <input type="text" readonly class="form-control" value="<?php echo $vin; ?>" placeholder="<?php echo $vin; ?>" />
                                </div>
                            </div>
                            <?php
                        }
                    }

                    ?>
                </fieldset>
            </div>
        </div>

        <?php echo form_close() ?>

    </div><!-- /.container -->
</div>

<?php $this->load->view('backend/elements/footer') ?>


<script type="text/javascript">

</script>

<script type="text/javascript">


</script>
</body>
</html>