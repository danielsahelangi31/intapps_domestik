<!DOCTYPE html>
<html lang="id">
<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
    <style>
        .extraVIN {
            display:none;
        }
        .select2 {
            width:100%!important;
        }

    </style>
</head>

<body>
<div id="wrap">
    <?php $this->load->view('backend/components/header') ?>

    <div class="container">

        <h2>Create Announcement VIN</h2>
        <?php
        if($docTransferID) {
            ?>
            <div class="alert alert-warning fade in">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                <h4><?php echo 'Document transfer ID : '.$docTransferID ?></h4>
            </div>
            <?php
            foreach ($vinResponseInfo as $index => $vin){
                ?>
                <div class="alert <?php echo $vin->status->StatusCode == 200 ? 'alert-success': 'alert-danger'; ?>">
                    <?php
                    foreach ($vin->vinDetailResponse->VinNumber as $in => $data){
                        ?>
                        <h4><?php echo 'VIN : '.$data ?></h4>
                        <?php
                    }
                    ?>
                    <!-- <h4><?php echo 'Status '.$vin->status->StatusName.': '.$vin->status->StatusCode.'-'.$vin->status->StatusDescription ?></h4> -->
                    <h4><?php echo 'Status '.$vin->status->StatusName.': '.$vin->status->StatusDescription ?></h4>
                </div>
                <?php
                ?>
                <?php
            }
        }
        ?>
        <hr />

        <div class="row">
            <form role="form" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="col-lg-6">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="pull-left">
                                <a href="<?php echo site_url('assets/csv/format_announcement_vin.xlsx') ?>" target="_blank" class="btn btn-success">Download Template Announcement VIN</a>
                            </div>
                        </div>
                    </div>
                    <?php

                    if($this->userauth->getLoginData()->sender == 'IKT'){
                        ?>
                        <div class="col-lg-12">
                            <div class="form-group">
                                <label class="text-left">MAKER</label>
                                <select class="form-control" id="typeIKT" name="typeIKT">
                                    <option value="">-- Select --</option>
                                    <?php
                                    foreach ($makers as $make){
                                        ?>
                                        <option value="<?php echo $make->MAKE.'_'.$make->SENDER; ?>_IKT_ADMINISTRATOR" ><?php echo $make->MAKE.'-'.$make->SENDER; ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                                <?php echo form_error('directionType', '<div class="error">', '</div><br/>'); ?>
                                <div class="error"></div>
                            </div>
                        </div>
                        <?php
                    }

                    ?>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <input type="hidden" name="length_vin" id="length_vin">
                            <label class="text-left">Document Transfer ID *</label>
                            <input  type="text" class="form-control" id="DocumentTransferId"
                                    name="DocumentTransferId" placeholder=""
                            />
                            <?php echo form_error('DocumentTransferId', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="input-group">
									<span class="input-group-btn">
										<span class="btn btn-primary btn-file">
											Upload Excel File&hellip; <input type="file" name="upload_vin_excel" id="upload_vin_excel">
										</span>
									</span>
                                <input type="text" class="form-control" readonly="readonly">
                            </div>
                            <?php echo form_error('upload_vin_excel', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="pull-left">
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                    <div class="pull-right">
                        <a id="addVin" class="btn btn-default">Add more vin</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="extraVIN">
                        <label class="text-left title-vin">VIN Info</label>
                        <div class="col-lg-12">
                            <div class="col-lg-6">
                                <label class="text-left">VIN Number *</label>
                                <input  type="text" class="form-control"
                                        name="VinNumber" placeholder=""
                                />
                                <?php echo form_error('VinNumber', '<div class="error">', '</div><br/>'); ?>
                                <div class="error"></div>
                            </div>
                            <div class="col-lg-6">
                                <label class="text-left">Direction *</label>
                                <select class="form-control" name="direction">
                                    <option value="">-- Select --</option>
                                    <option value="EXPORT" >EXPORT</option>
                                    <option value="IMPORT" >IMPORT</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="col-lg-6">
                                <label class="text-left">Direction Type *</label>
                                <select class="form-control" name="directionType">
                                    <option value="">-- Select --</option>
                                    <option value="INTERNATIONAL" >INTERNATIONAL</option>
                                    <option value="DOMESTIC" >DOMESTIC</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label class="text-left">Fuel</label>
                                <input  type="text" class="form-control"
                                        name="fuel" placeholder=""
                                />
                                <?php echo form_error('fuel', '<div class="error">', '</div><br/>'); ?>
                                <div class="error"></div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="col-lg-6">
                                <label class="text-left">Model *</label>
                                <select class="form-control models-get" name="models" >
                                    <option value="">-- Select --</option>
                                    <?php
                                    foreach ($models as $model){
                                        ?>
                                        <option value="<? echo $model->CODE ?>" ><? echo $model->CODE.' - '.$model->DESCRIPTION ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label class="text-left">Destination *</label>
                                <select class="form-control destinate-get" name="destinate" >
                                    <option value="">-- Select --</option>
                                    <?php
                                    foreach ($destinates as $destinate){
                                        ?>
                                        <option value="<? echo $destinate->CODE ?>" ><? echo $destinate->NAME ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="col-lg-6">
                                <label class="text-left">Controlling Org *</label>
                                <select class="form-control controll-get" name="controlling_org" >
                                    <option value="">-- Select --</option>
                                    <?php
                                    foreach ($controllings as $controll){
                                        ?>
                                        <option value="<? echo $controll->CODE ?>" ><? echo $controll->NAME ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label class="text-left">Consignee *</label>
                                <select class="form-control consignee-get" name="consignee" >
                                    <option value="">-- Select --</option>
                                    <?php
                                    foreach ($consignees as $consignee){
                                        ?>
                                        <option value="<? echo $consignee->CODE ?>" ><? echo $consignee->NAME ?></option>
                                        <?php
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12" id="container-box"></div>
                </div>

            </form>
        </div>


    </div><!-- /.container -->
</div>

<?php $this->load->view('backend/elements/footer') ?>
<script type="text/javascript">

    $(document)
        .on('change', '.btn-file :file', function() {
            var input = $(this),
                numFiles = input.get(0).files ? input.get(0).files.length : 1,
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [numFiles, label]);
        });


    $(document).ready(function () {

        $('.controll-get').select2();

        $('.destinate-get').select2();

        $('.models-get').select2();

        $('.consignee-get').select2();

        $('#typeIKT').select2();


        $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
            var input = $(this).parents('.input-group').find(':text'),
                log = numFiles > 1 ? numFiles + ' files selected' : label;

            if( input.length ) {
                input.val(log);
            } else {
                if( log ) alert(log);
            }

        });

        $('#length_vin').val(0);
        $('#addVin').click(function () {
            $('.models-get').select2('destroy');
            $('.controll-get').select2('destroy');
            $('.destinate-get').select2('destroy');
            $('.consignee-get').select2('destroy');
            $('<div/>', {
                'class' : 'extraPerson', html: GetHtml()
            }).hide().appendTo('#container-box').slideDown('slow');
            $('.consignee-get').select2(
                {
                    ajax: {
                        url: '<?php echo site_url('eticket/announce_truck/getControlling'); ?>',
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                searchTerm: params.term // search term
                            };
                        },
                        processResults: function (response) {
                            return {
                                results: $.map(response, function(obj) {
                                    return {
                                        id: obj.id,
                                        text: obj.text
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength : 3
                }
            );
            $('.controll-get').select2(
                {
                    ajax: {
                        url: '<?php echo site_url('eticket/announce_truck/getControlling'); ?>',
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                searchTerm: params.term // search term
                            };
                        },
                        processResults: function (response) {
                            return {
                                results: $.map(response, function(obj) {
                                    return {
                                        id: obj.id,
                                        text: obj.text
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength : 3
                }
            );
            $('.destinate-get').select2(
                {
                    ajax: {
                        url: '<?php echo site_url('eticket/announce_truck/getDestination'); ?>',
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                searchTerm: params.term // search term
                            };
                        },
                        processResults: function (response) {
                            return {
                                results: $.map(response, function(obj) {
                                    return {
                                        id: obj.id,
                                        text: obj.text
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength : 3
                }
            );
            $('.models-get').select2(
                {
                    ajax: {
                        url: '<?php echo site_url('eticket/announce_truck/getModel'); ?>',
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function (params) {
                            return {
                                searchTerm: params.term // search term
                            };
                        },
                        processResults: function (response) {
                            return {
                                results: $.map(response, function(obj) {
                                    return {
                                        id: obj.id,
                                        text: obj.text
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength : 3
                }
            );
        });

    });

    function GetHtml()
    {
        var len = $('.extraPerson').length+1;
        var $html = $('.extraVIN').clone();
        $html.find('[name=VinNumber]')[0].name="VinNumber" + len;
        $html.find('[name=direction]')[0].name="direction" + len;
        $html.find('[name=directionType]')[0].name="directionType" + len;
        $html.find('[name=fuel]')[0].name="fuel" + len;
        $html.find('[name=models]')[0].name="models" + len;
        $html.find('[name=destinate]')[0].name="destinate" + len;
        $html.find('[name=controlling_org]')[0].name="controlling_org" + len;
        $html.find('[name=consignee]')[0].name="consignee" + len;
        $html.find('label.title-vin').text("VIN Info "+len);
        $('#length_vin').val(len);
        return $html.html();
    }

</script>
</body>
</html>