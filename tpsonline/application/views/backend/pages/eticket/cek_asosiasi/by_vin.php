<!DOCTYPE html>
<html lang="id">
<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
    <style>
        .extraVIN {
            display:none;
        }
    </style>
</head>

<body>
<div id="wrap">
    <?php $this->load->view('backend/components/header') ?>

    <div class="container">

        <h2>Asosiasi Check by VIN</h2>

        <hr />

        <?php

        if($this->session->flashdata('responses')) {
            ?>
                <div class="row">
                    <div class="col-lg-12">

                        <?php
                        foreach ($this->session->flashdata('responses')->InquiryBC->InfoVIN as $successs){
                            ?>
                                <fieldset class="delivery-request-border">
                                    <legend class="delivery-request-border">VIN Information</legend>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">VIN</label>
                                        <div class="col-lg-8">
                                            <p class="form-control-static"><?php echo $successs->VIN ? $successs->VIN : 'No Data'; ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">Trip Type</label>
                                        <div class="col-lg-8">
                                            <p class="form-control-static"><?php echo $successs->Exportimport ? $successs->Exportimport :  'No Data' ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">Cargo Type</label>
                                        <div class="col-lg-8">
                                            <p class="form-control-static"><?php echo $successs->TypeCargo ? $successs->TypeCargo : 'No Data' ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label">Announce Date</label>
                                        <div class="col-lg-8">
                                            <p class="form-control-static"><?php echo $successs->dts_announced ? $successs->dts_announced : 'No Data' ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label visit_id_loading">Model</label>
                                        <div class="col-lg-8">
                                            <p class="form-control-static"><?php echo $successs->Model ? $successs->Model : 'No Data' ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label visit_id_loading">Maker</label>
                                        <div class="col-lg-8">
                                            <p class="form-control-static"><?php echo $successs->Maker ? $successs->Maker : 'No Data' ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label visit_id_loading">Consignee</label>
                                        <div class="col-lg-8">
                                            <p class="form-control-static"><?php echo $successs->Consignee ? $successs->Consignee : 'No Data' ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-lg-4 control-label visit_id_loading">Logistic Company</label>
                                        <div class="col-lg-8">
                                            <p class="form-control-static"><?php echo $successs->LogisticCompany ? $successs->LogisticCompany : 'No Data' ?></p>
                                        </div>
                                    </div>
                                    <br>
                                    <div class="col-lg-6">
                                        <fieldset class="delivery-request-border">
                                            <legend class="delivery-request-border">Vessel Information</legend>
                                            <div class="form-group">
                                                <label class="col-lg-4 control-label">Vessel Visit ID</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-static"><?php echo $successs->InfoVessel->VesselVisitID ? $successs->InfoVessel->VesselVisitID : 'No Data'; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-4 control-label">Vessel Name</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-static"><?php echo $successs->InfoVessel->VesselName ? $successs->InfoVessel->VesselName : 'No Data'; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-4 control-label">Voyage IN</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-static"><?php echo $successs->InfoVessel->VoyageIn ? $successs->InfoVessel->VoyageIn : 'No Data'; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-4 control-label">Voyage OUT</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-static"><?php echo $successs->InfoVessel->VoyageOut ? $successs->InfoVessel->VoyageOut : 'No Data'; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-4 control-label">Load Port</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-static"><?php echo $successs->InfoVessel->LoadPort ? $successs->InfoVessel->LoadPort : 'No Data'; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-4 control-label">Transit Port</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-static"><?php echo $successs->InfoVessel->TransitPort ? $successs->InfoVessel->TransitPort : 'No Data'; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-4 control-label">Discharge Port</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-static"><?php echo $successs->InfoVessel->DischargePort ? $successs->InfoVessel->DischargePort : 'No Data'; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-4 control-label">Next Port</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-static"><?php echo $successs->InfoVessel->NextPort ? $successs->InfoVessel->NextPort : 'No Data'; ?></p>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                    <div class="col-lg-6">
                                        <fieldset class="delivery-request-border">
                                            <legend class="delivery-request-border">Truck Information</legend>
                                            <div class="form-group">
                                                <label class="col-lg-4 control-label">Visit ID</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-static"><?php echo $successs->InfoTruck->VisitID ? $successs->InfoTruck->VisitID : 'No Data'; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-4 control-label">Truck Code</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-static"><?php echo $successs->InfoTruck->TruckCode ? $successs->InfoTruck->TruckCode : 'No Data'; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-4 control-label">License Plate</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-static"><?php echo $successs->InfoTruck->licenseplate? $successs->InfoTruck->licenseplate : 'No Data'; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-4 control-label">Truck Type</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-static"><?php echo $successs->InfoTruck->TruckType ? $successs->InfoTruck->TruckType : 'No Data'; ?></p>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <label class="col-lg-4 control-label">Driver</label>
                                                <div class="col-lg-8">
                                                    <p class="form-control-static"><?php echo $successs->InfoTruck->TruckDriver ? $successs->InfoTruck->TruckDriver : 'No Data'; ?></p>
                                                </div>
                                            </div>
                                        </fieldset>
                                    </div>
                                </fieldset>
                            <?php
                        }
                        ?>

                    </div>
                </div>
            <?php
        }else{
            ?>
            <div class="row">
                <form role="form" class="form-horizontal" action="" method="post">
                    <input type="hidden" name="length_vin" id="length_vin">
                    <div class="col-md-4 col-md-offset-4">
                        <div id="container-vin"></div>
                        <div class="extraVIN">
                            <div class="form-group text-center">
                                <label >VIN *</label>
                                <input type="text" class="form-control"
                                       name="no_vin" placeholder="Enter VIN"
                                />
                                <?php echo form_error('no_vin1', '<div class="error">', '</div><br/>'); ?>
                                <div class="error"></div>
                            </div>
                        </div>
                        <div class="form-group text-center">
                            <button type="submit" class="btn btn-primary">Next > </button>
                            <a id="addVin" class="btn btn-default">Add more VIN</a>
                        </div>
                    </div>

                </form>
            </div>
            <?php
        }

        ?>


    </div><!-- /.container -->
</div>

<?php $this->load->view('backend/elements/footer') ?>

<script type="text/javascript">
    $(document).ready(function () {
        $('<div/>', {
            'class' : 'extraPerson', html: GetHtml()
        }).appendTo('#container-vin');
        $('#length_vin').val(1);
        $('#addVin').click(function () {
            $('<div/>', {
                'class' : 'extraPerson', html: GetHtml()
            }).hide().appendTo('#container-vin').slideDown('slow');

        });
    });

    function GetHtml()
    {
        var len = $('.extraPerson').length+1;
        var $html = $('.extraVIN').clone();
        $html.find('[name=no_vin]')[0].name="no_vin" + len;
        $('#length_vin').val(len);
        return $html.html();
    }

</script>

</body>
</html>