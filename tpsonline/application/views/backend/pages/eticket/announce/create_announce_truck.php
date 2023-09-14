<!DOCTYPE html>
<html lang="id">

<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
</head>

<body>
    <div id="wrap">
        <?php $this->load->view('backend/components/header') ?>

        <div class="container">
<?php
// echo "<pre>";
// print_r($list_no_doc);
// exit;
?>
            <h2>Create Announcement Truck</h2>
            <div id="error_info" style="display: none;"></div>
            <div id="info_status1" style="display: none;"></div>
            <div id="info_status2" style="display: none;"></div>
            <hr />

            <form id="main_form" role="form" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="col-lg-12">
                            <div class="form-group">
                                <div class="pull-left">
                                    <a href="<?php echo site_url('assets/csv/format_announce_truck.xlsx') ?>" target="_blank" class="btn btn-success">Download Template Announcement Truck</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <?php
                            if ($this->userauth->getLoginData()->sender == 'IKT') {
                            ?>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">MAKER</label>
                                        <select class="form-control" id="typeIKT" name="typeIKT">
                                            <option value="">-- Select --</option>
                                            <?php
                                            foreach ($makers as $make) {
                                            ?>
                                                <option value="<?php echo $make->MAKE . '_' . $make->SENDER; ?>_IKT_<?php echo strtoupper($this->userauth->getLoginData()->username); ?>"><?php echo $make->MAKE . '-' . $make->SENDER; ?></option>
                                            <?php
                                            }
                                            ?>
                                        </select>
                                        <?php echo form_error('typeIKT', '<div class="error">', '</div><br/>'); ?>
                                        <div class="error"></div>
                                    </div>
                                </div>
                            <?php
                            }
                            ?>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <input type="hidden" name="length_vin" id="length_vin">
                                    <input type="hidden" name="length_bl" id="length_bl">
                                    <input type="hidden" name="senderNi" id="senderNi" value="<?php echo $this->userauth->getLoginData()->sender ?>">
                                    <label class="text-left">Truck Code * </label>
                                    <input type="text" class="form-control" id="truckCode" name="truckCode" placeholder="Required" />
                                    <div class="error"></div>
                                </div>
                            </div>

                            <input type="hidden" name="directionType" id="directionType" value="INTERNATIONAL">
                        </div>

                        <div class="col-lg-6">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="text-left">Driver Phone Number</label>
                                    <input type="text" class="form-control" id="driverPhoneNumber" name="driverPhoneNumber" placeholder="Optional" />
                                    <?php echo form_error('driverPhoneNumber', '<div class="error">', '</div><br/>'); ?>
                                    <div class="error"></div>
                                </div>
                            </div>
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="text-left">Announce with excel</label>
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-primary btn-file">
                                                Upload Excel File&hellip; <input type="file" name="upload_vin_excel" id="upload_vin_excel">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control" id="excel-upload" readonly="readonly">
                                    </div>
                                    <?php echo form_error('upload_vin_excel', '<div class="error">', '</div><br/>'); ?>
                                    <div class="error"></div>
                                </div>
                            </div>
                        </div>

                        <div class="clearfix"></div>
                        <br>

                        <div class="col-lg-6">
                            <div class="extraVIN" style="display: none;">
                                <div class="col-lg-12">
                                    <label class="text-left title-vin">will replace with js</label>
                                </div>
                                <div class="col-lg-6">
                                    <label class="text-left">VIN Number *</label>
                                    <select class="form-control vin-get" id="VinNumber" name="VinNumber">
                                        <option value="">Enter Vin Number</option>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label class="text-left">Fuel</label>
                                    <input type="text" class="form-control" name="fuel" placeholder="" />
                                    <?php echo form_error('fuel', '<div class="error">', '</div><br/>'); ?>
                                    <div class="error"></div>
                                </div>

                                <div class="col-lg-6">
                                    <label class="text-left">Model *</label>
                                    <select class="form-control models-get" name="models">
                                        <option value="">-- Select --</option>

                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label class="text-left">Destination *</label>
                                    <select class="form-control destinate-get" name="destinate">
                                        <option value="">-- Select --</option>
                                    </select>
                                </div>

                                <div class="col-lg-6">
                                    <label class="text-left">Controlling Org *</label>
                                    <select class="form-control controll-get" name="controlling_org">
                                        <option value="">-- Select --</option>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label class="text-left">Consignee *</label>
                                    <select class="form-control consignee-get" name="consignee">
                                        <option value="">-- Select --</option>
                                    </select>
                                </div>

                                <div class="col-lg-6">
                                    <label class="text-left">No Dokumen</label>
                                    <select class="form-control noDok" name="noDok">
                                        <option value="">-- Select --</option>
                                        <?php foreach ($list_no_doc as $dok) { ?>
                                            <option value="<?= $dok->CUSTOMS_NUMBER; ?>">
                                                <?= $dok->CUSTOMS_NUMBER; ?>
                                            </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <!-- <input type="hidden" class="form-control" id="noNpe" name="noDok" placeholder="No Dokumen" /> -->

                                <div class="col-lg-6">
                                    <label class="text-left">Tanggal Dokumen</label>
                                    <input type="hidden" class="form-control dateVin" id="tglNpe" name="tglNpe" placeholder="dd-mm-yyyy" />
                                    <input type="date" class="form-control dateVinV" id="tglNpeV" name="tglNpeV" placeholder="dd-mm-yyyy" disabled='disabled' />
                                </div>

                                <div class="col-lg-6">
                                    <label class="text-left">NPWP Expor</label>
                                    <input type="hidden" class="form-control" id="npwpEksport" name="npwp" placeholder="NPWP" />
                                    <input type="text" class="form-control" id="npwpEksportV" name="npwpV" placeholder="NPWP" disabled='disabled' />
                                </div>
                                <div class="col-lg-6">
                                    <label class="text-left">Kode Dokumen *</label>
                                    <input type="hidden" class="form-control" id="kdDok_export" name="kdDok_export" placeholder="Kode Document" />
                                    <select class="form-control kdDok_exportV" name="kdDok_exportV" disabled='disabled'>
                                        <option value="">Kode Dokumen</option>
                                        <?php foreach ($dokumen_export as $dok) { ?>
                                            <option value="<?php echo $dok->ID; ?>"><?php echo $dok->ID . '-' . $dok->DOC_TYPE; ?> </option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label class="text-left">Total Cargo * </label>
                                    <input type="hidden" class="form-control" id="totalCargo" name="totalCargo" autocomplete="off" placeholder="Total Cargo" />
                                    <input type="text" class="form-control" id="totalCargoV" name="totalCargoV" autocomplete="off" placeholder="Total Cargo" disabled='disabled' />
                                </div>
                                <div class="col-lg-6">
                                    <label class="text-left">Sisa Cargo * </label>
                                    <input type="hidden" class="form-control" id="sisaCargo" name="sisaCargo" autocomplete="off" placeholder="Sisa Cargo" />
                                    <input type="text" class="form-control" id="sisaCargoV" name="sisaCargoV" autocomplete="off" placeholder="Sisa Cargo" disabled='disabled' />
                                    <input type="hidden" class="form-control" id="noDokCounter" name ="noDokCounter" />
                                </div>

                                <div class="clearfix"></div>
                                <br>
                            </div>
                            <div class="col-lg-12" id="container-box"></div>
                            <div class="clearfix"></div>
                            <br>
                            <div class="pull-left">
                                <a id="addVin" class="btn btn-primary">Add more vin</a>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="extraBL" style="display: none;">
                                <div class="col-lg-12">
                                    <label class="text-left title-bl">will replace with js</label>
                                </div>
                                <div class="col-lg-6">
                                    <label class="text-left">BL Number *</label>
                                    <input type="hidden" name="counter">
                                    <select class="form-control bl-gets" name="BLNumber">
                                        <option value="">-- Select --</option>
                                    </select>
                                    <?php echo form_error('BLNumber', '<div class="error">', '</div><br/>'); ?>
                                    <div class="error"></div>
                                </div>
                                <div class="col-lg-3">
                                    <label class="text-left">Total Cargo</label>
                                    <input readonly type="text" class="form-control" name="total_vin" placeholder="" />
                                    <?php echo form_error('total_vin', '<div class="error">', '</div><br/>'); ?>
                                    <div class="error"></div>
                                </div>

                                <div class="col-lg-3">
                                    <label class="text-left">Sisa Cargo</label>
                                    <input readonly type="text" class="form-control" name="remaining_cargo" placeholder="" />
                                    <?php echo form_error('remaining_cargo', '<div class="error">', '</div><br/>'); ?>
                                    <div class="error"></div>
                                </div>

                                <div class="col-lg-6">
                                    <label class="text-left">BL Date</label>
                                    <input readonly type="text" class="form-control" name="BLDate" placeholder="" />
                                </div>

                                <div class="col-lg-6">
                                    <label class="text-left">No Dokumen *</label>
                                    <input type="text" class="form-control" name="noDok" placeholder="No Dokumen" />
                                </div>
                                <div class="col-lg-6">
                                    <label class="text-left">Tanggal Dokumen *</label>
                                    <input class="form-control dateBl" type="date" name="tglDok" placeholder="dd-mm-yyyy" />
                                </div>

                                <div class="col-lg-6">
                                    <label class="text-left">Kode Dokumen *</label>
                                    <select class="form-control" name="kdDok">
                                        <option value="">-- Select --</option>
                                        <?php foreach ($dokumen_import as $dok) { ?>
                                            <option value="<?php echo $dok->ID; ?>"><?php echo $dok->ID . '-' . $dok->DOC_TYPE; ?> </option>
                                        <?php } ?>
                                    </select>
                                </div>

                                <div class="col-lg-6">
                                    <label class="text-left">NPWP Impor*</label>
                                    <input type="text" class="form-control" name="npwp" placeholder="NPWP" />
                                </div>

                                <div class="clearfix"></div>
                                <br>
                            </div>
                            <div class="col-lg-12" id="bl-container-box"></div>
                            <div class="clearfix"></div>
                            <br>
                            <div class="pull-right">
                                <a id="addImportBL" class="btn btn-primary">Add import BL</a>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="col-lg-12">
                        <button id="submitBtn" type="submit" class="btn btn-primary btn-block submitBtn">Submit</button>
                    </div>
                </div>
            </form>


        </div><!-- /.container -->
    </div>

    <?php $this->load->view('backend/elements/footer') ?>
    <script type="text/javascript">
        
        $('#main_form').submit(function(e) {
            e.preventDefault();
            $('.models-get').attr('disabled', false);
            $('.destinate-get').attr('disabled', false);
            $.ajax({
                url: "<?php echo site_url('eticket/announce_truck/saveData'); ?>",
                data: new FormData(this),
                type: "post",
                dataType: "json",
                processData: false,
                contentType: false,
                beforeSend: function() {
                    $('#error_info').hide();
                    $('#error_info').html('');
                    $('#info_status1').hide();
                    $('#info_status1').html('');
                    $('#info_status2').hide();
                    $('#info_status2').html('');
                    $('#info_status1').removeAttr('class');
                    $('#info_status2').removeAttr('class');
                    jQuery('button').prop('disabled', true);
                    jQuery('select').prop('disabled', true);
                    $('input:text').attr("disabled", 'disabled');
                },
                success: function(data) {
                    if (data.status == 'Failed') {
                        $('html, body').animate({
                            scrollTop: $('body').offset().top
                        });
                        $('#error_info').show();
                        $('#error_info').html(
                            '<div class="alert alert-warning fade in">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>' +
                            '<h4>Info: ' + data.message + '</h4>' +
                            '</div>'
                        );
                        $('input').removeAttr('disabled');
                        jQuery('button').prop('disabled', false);
                        jQuery('select').prop('disabled', false);

                        // set disabled input / select
                        $('input[name="npwpV' + counter + '"]').attr("disabled", true);
                        $('select[name="kdDok_exportV' + counter + '"]').attr("disabled", true);
                        $('input[name="tglNpeV' + counter + '"]').attr("disabled", true);
                        $('input[name="totalCargoV' + counter + '"]').attr("disabled", true);
                        $('input[name="sisaCargoV' + counter + '"]').attr("disabled", true);
                    } else if (data.status == 'Warning') {
                        alert('Sisa Cargo 0, Proses tidak bisa dilanjutkan');
                        $('input').removeAttr('disabled');
                        jQuery('button').prop('disabled', false);
                        jQuery('select').prop('disabled', false);

                        // set disabled input / select
                        $('input[name="npwpV' + counter + '"]').attr("disabled", true);
                        $('select[name="kdDok_exportV' + counter + '"]').attr("disabled", true);
                        $('input[name="tglNpeV' + counter + '"]').attr("disabled", true);
                        $('input[name="totalCargoV' + counter + '"]').attr("disabled", true);
                        $('input[name="sisaCargoV' + counter + '"]').attr("disabled", true);
                    } else if (data.status == 'Success') {
                        $('html, body').animate({
                            scrollTop: $('body').offset().top
                        });
                        $('#error_info').show();
                        $('#error_info').html(
                            '<div class="alert alert-warning fade in">' +
                            '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>' +
                            '<h4>Visit ID: ' + data.visitID + '</h4>' +
                            '</div>'
                        );
                        const blResponse = data.blResponseInfo[0];
                        const vinResponse = data.vinResponseInfo[0];
                        let statusVin = vinResponse.status.StatusCode;
                        let statusBill = blResponse.Status.StatusCode;

                        if (statusVin == 200 && statusBill == 200) {
                            statusAllSuccess(vinResponse, blResponse);
                        } else if (statusVin == 200) {
                            statusVinSuccess(vinResponse);
                        } else if ((statusVin != "" || statusVin != 200) && statusBill != 200) {
                            statusVinFailure(vinResponse);
                        } else if (statusBill == 200 && statusVin == "") {
                            statusBlSuccess(blResponse);
                        } else if (statusBill != "") {
                            statusBlFailure(blResponse);
                        }

                    }
                },
                error: function(xhr, error) {
                    console.log(xhr);
                    console.log(error);
                    alert('Data Tidak Ditemukan!');
                    $('input').removeAttr('disabled');
                    jQuery('button').prop('disabled', false);
                    jQuery('select').prop('disabled', false);
                }
            });
        });

        $("#upload_vin_excel").click(function() {
            $('#excel-upload').val('');
            $("#upload_vin_excel").val("");
        });

        $(document)
            .on('change', '.btn-file :file', function() {
                var input = $(this),
                    numFiles = input.get(0).files ? input.get(0).files.length : 1,
                    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                input.trigger('fileselect', [numFiles, label]);
            });

        $(document).ready(function() {
        
            $('.controll-get').select2();

            $('#typeIKT').select2();

            $('.destinate-get').select2();

            $('.vin-get').select2();

            $('.models-get').select2();

            $('.consignee-get').select2();

            $('.noDok').select2();
            
            $('.bl-gets').select2();

            $("#upload_vin_excel").change(function() {
                if ($("#upload_vin_excel").val() !== "") {
                    // $('#truckCode').removeAttr('value');
                    $('#directionType').prop('disabled', true);
                    // document.getElementById("truckCode").disabled = true;
                    // document.getElementById("driverPhoneNumber").disabled = true;
                    $("#addVin").attr('disabled', true);
                    $("#addImportBL").attr('disabled', true);
                    $('#length_vin').val(0);
                    $('#length_bl').val(0);
                    $(".extraPerson").remove();
                    $(".extraPersonBL").remove();
                }
            })

            $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
                var input = $(this).parents('.input-group').find(':text'),
                    log = numFiles > 1 ? numFiles + ' files selected' : label;

                if (input.length) {
                    input.val(log);
                } else {
                    if (log) alert(log);
                }
            });

            $('#length_vin').val(0);
            $('#addVin').click(function() {
                var counterG = $('.extraPerson').length;
                $('.dateVin').datepicker({
                    format: 'dd-mm-yyyy'
                });
                var today = new Date();
                today.setDate(today.getDate() - 30);
                var min = today.toISOString().slice(0, 10);
                var max = new Date().toISOString().slice(0, 10);
                document.getElementById("tglNpe").min = min;
                document.getElementById("tglNpe").max = max;
                $('#container-box').show();
                $('.vin-get').select2('destroy');
                $('.models-get').select2('destroy');
                $('.controll-get').select2('destroy');
                $('.destinate-get').select2('destroy');
                $('.consignee-get').select2('destroy');
                $('.noDok').select2('destroy');
                
                $('<div/>', {
                    'class': 'extraPerson',
                    'id': 'extraPerson' + counterG,
                    html: GetHtml()
                }).hide().appendTo('#container-box').slideDown('fast');

                if ($('#length_vin').val() == 1) {
                    // console.log(`object::`, $('#length_vin').val());
                    // alert("Don't forget to choose Direction and Direction Type. Thank you!");
                } 
                
                if($('.noDok*').val() == "") {
                    $("#addVin").attr('disabled', true);
                }
                
                var senderNi = $('#senderNi').val();
                // console.log(senderNi)
                $.ajax({
                    type: "POST",
                    url: '<?php echo site_url('eticket/announce_truck/getNpwp'); ?>',
                    data: {
                        sender: senderNi
                    },
                    success: function(msg) {
                        var obj = jQuery.parseJSON(msg);
                        $("[id='npwpEksport']").val(obj[0].NPWP);
                    }
                });
                $('.vin-get').select2({
                    tags: true,
                    ajax: {
                        url: '<?php echo site_url('eticket/announce_truck/getVin'); ?>',
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                searchTerm: params.term // search term
                            };
                        },
                        processResults: function(response) {
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
                    minimumInputLength: 3
                });
                $('.vin-get').on('select2:select', function(e) {
                    var counter = $('.extraPerson').length;
                    $.ajax({
                        type: "POST",
                        url: '<?php echo site_url('eticket/announce_truck/getModelByVin'); ?>',
                        data: {
                            searchTerm: e.params.data.id
                        },

                        success: function(msg) {
                            var obj = jQuery.parseJSON(msg);
                            var modelVal = obj[0].modelVal;
                            var destinateVal = obj[0].destinateVal;
                            var codeDestinate = destinateVal.split(' - ')[0];
                            var controllVal = obj[0].controllVal;
                            var consigneeVal = obj[0].consigneeVal;
                            // console.log('aaaaa::', obj[0].text);
                            // $('.models-get').html('<option value="'+obj[0].text+'">'+obj[0].text+'</option>');
                            $('[name="models' + counter + '"]').html('<option value="' + modelVal + '">' + modelVal + '</option>').prop("disabled", true);
                            $('[name="destinate' + counter + '"]').html('<option value="' + codeDestinate + '">' + destinateVal + '</option>').prop("disabled", true);
                            $('[name="controlling_org' + counter + '"]').html('<option value="' + controllVal + '">' + controllVal + '</option>');
                            $('[name="consignee' + counter + '"]').html('<option value="' + consigneeVal + '">' + consigneeVal + '</option>');
                        }
                    });
                });

                // $('.noDok' + counterG).on("select2:select", function(e) {
                $('.noDok').on("select2:select", function(e) {
                    if(e.params.data.id != "") {
                        $('#addVin').removeAttr('disabled');
                    }
                    // else {
                    //     // console.log('object kosong');
                    //     $("#extraPerson" + counter).remove();
                    //     // $("#addVin").attr('disabled', true);
                    // }
                    // console.log('??? :>> ', counterG);
                    var counter = $('.extraPerson').length;
                    var doc = e.params.data.id.split('/').join('B4tA5');
                    var sisa = "";
                    if(counter > 1) {
                        var last_id = counter -1;
                        var lastdoc = $('input[name="noDokCounter' + last_id + '"]').val();
                        sisa = lastdoc.split('/').join('B4tA5') + 'R4ha51A' + doc;
                    }
                    $.ajax({
                        type: "post",
                        enctype: 'multipart/form-data',
                        url: counter > 1 ? "<?= base_url(); ?>eticket/announce_truck/getJumlahCargo/" + doc + "/" + sisa : "<?= base_url(); ?>eticket/announce_truck/getJumlahCargo/" + doc,
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function(response) {
                            var obj = jQuery.parseJSON(response);
                            
                            // obj.SISA_CARGO < 0 ? $('input[name="sisaCargoV' + counter + '"]').val('-').attr("disabled", true) : $('input[name="sisaCargoV' + counter + '"]').val(obj.SISA_CARGO).attr("disabled", true);
                            if(counter > 1) {
                                $('input[name="noDokCounter' + counter + '"]').val(lastdoc + 'R4ha51A' + obj.CUSTOMS_NUMBER);
                            } else {
                                $('input[name="noDokCounter' + counter + '"]').val(obj.CUSTOMS_NUMBER);
                            }

                            if(obj.SISA_CARGO <= 0) {
                                swal({
                                    title: 'Warning!',
                                    text: 'Status Jumlah cargo yang di input melewati batas sisa cargo',
                                    type: 'success',
                                });
                                $('input[name="sisaCargoV' + counter + '"]').val('').attr("disabled", true);
                                $("#addVin").attr('disabled', true);
                                $(".submitBtn").attr('disabled', true);
                            } else {
                                // ========================================
                                $('input[name="npwp' + counter + '"]').val(obj.NPWP);
                                $('input[name="npwpV' + counter + '"]').val(obj.NPWP).attr("disabled", true);
                                $('input[name="kdDok_export' + counter + '"]').val(obj.KD_DOK);
                                $('select[name="kdDok_exportV' + counter + '"]').val(obj.KD_DOK).attr("disabled", true);
                                $('input[name="tglNpe' + counter + '"]').val(obj.CUSTOMS_DATE);
                                $('input[name="tglNpeV' + counter + '"]').val(obj.CUSTOMS_DATE).attr("disabled", true);
                                $('input[name="totalCargo' + counter + '"]').val(obj.JUMLAH_CARGO);
                                $('input[name="totalCargoV' + counter + '"]').val(obj.JUMLAH_CARGO).attr("disabled", true);
                                $('input[name="sisaCargo' + counter + '"]').val(obj.SISA_CARGO);
                                $('input[name="sisaCargoV' + counter + '"]').val(obj.SISA_CARGO).attr("disabled", true);
                                // ========================================
                                $('.submitBtn').removeAttr('disabled');
                            }
                        },
                    });
                });

                $('.consignee-get').select2({
                    ajax: {
                        url: '<?php echo site_url('eticket/announce_truck/getControlling'); ?>',
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                searchTerm: params.term, // search term
                                page_limit: 10,
                                page: params.page || 1
                            };
                        },
                        processResults: function(response) {
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
                    minimumInputLength: 3
                });
                $('.controll-get').select2({
                    ajax: {
                        url: '<?php echo site_url('eticket/announce_truck/getControlling'); ?>',
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                searchTerm: params.term // search term
                            };
                        },
                        processResults: function(response) {
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
                    minimumInputLength: 3
                });
                $('.destinate-get').select2({
                    ajax: {
                        url: '<?php echo site_url('eticket/announce_truck/getDestination'); ?>',
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                searchTerm: params.term // search term
                            };
                        },
                        processResults: function(response) {
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
                    minimumInputLength: 3
                });
                $('.models-get').select2({
                    ajax: {
                        url: '<?php echo site_url('eticket/announce_truck/getModel'); ?>',
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                searchTerm: params.term // search term
                            };
                        },
                        processResults: function(response) {
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
                    minimumInputLength: 3
                });

                $('.noDok').select2();

                $('#directionType').prop('disabled', false);
            });

            $('#length_bl').val(0);
            $('#addImportBL').click(function() {
                $('#bl-container-box').show();
                $('.dateBl').datepicker({
                    format: 'dd-mm-yyyy'
                });
                $('.bl-gets').select2('destroy');
                $('<div/>', {
                    'class': 'extraPersonBL',
                    html: BLGetHtml()
                }).hide().appendTo('#bl-container-box').slideDown('slow');

                $('.bl-gets').select2({
                    ajax: {
                        url: '<?php echo site_url('eticket/announce_truck/getListBL'); ?>',
                        type: "post",
                        dataType: 'json',
                        delay: 250,
                        data: function(params) {
                            return {
                                searchTerm: params.term // search term
                            };
                        },
                        processResults: function(response) {
                            return {
                                results: $.map(response, function(obj) {
                                    return {
                                        id: obj.id,
                                        text: obj.text,
                                        dates: obj.dates
                                    };
                                })
                            };
                        },
                        cache: true
                    },
                    minimumInputLength: 3
                });

                $('.bl-gets').on('select2:select', function(e) {
                    var counter = $(this).data('id');
                    $("[name='BLDate" + $(this).data('id') + "']").val(e.params.data.dates);
                    $.ajax({
                        type: "POST",
                        url: '<?php echo site_url('eticket/announce_truck/getInfoBL'); ?>',
                        data: {
                            truckCode: e.params.data.id
                        },
                        success: function(msg) {
                            var obj = jQuery.parseJSON(msg);
                            $("[name='total_vin" + counter + "']").val(obj[0].TOTAL_CARGO);
                            $("[name='remaining_cargo" + counter + "']").val(obj[0].REMAINING_CARGO);
                            if (obj[0].REMAINING_CARGO == 0) {
                                alert("Sisa Cargo 0, Proses tidak bisa dilanjutkan");
                                $("#submitBtn").hide();
                            } else {
                                $("#submitBtn").show();
                            }
                        }
                    });
                });
            });
        });

        const alertS = 'alert alert-success';
        const alertF = 'alert alert-danger';

        function GetHtml() {
            var len = $('.extraPerson').length + 1;
            var $html = $('.extraVIN').clone();
            $html.find('[name=VinNumber]')[0].name = "VinNumber" + len;
            $html.find('[name=fuel]')[0].name = "fuel" + len;
            $html.find('[name=models]')[0].name = "models" + len;
            $html.find('[name=destinate]')[0].name = "destinate" + len;
            $html.find('[name=controlling_org]')[0].name = "controlling_org" + len;
            $html.find('[name=consignee]')[0].name = "consignee" + len;
            $html.find('[name=noDok]')[0].name = "noDok" + len;
            $html.find('[name=kdDok_export]')[0].name = "kdDok_export" + len;
            $html.find('[name=kdDok_exportV]')[0].name = "kdDok_exportV" + len;
            $html.find('[name=npwp]')[0].name = "npwp" + len;
            $html.find('[name=npwpV]')[0].name = "npwpV" + len;
            $html.find('[name=tglNpe]')[0].name = "tglNpe" + len;
            $html.find('[name=tglNpeV]')[0].name = "tglNpeV" + len;
            $html.find('[name=totalCargo]')[0].name = "totalCargo" + len;
            $html.find('[name=totalCargoV]')[0].name = "totalCargoV" + len;
            $html.find('[name=sisaCargo]')[0].name = "sisaCargo" + len;
            $html.find('[name=sisaCargoV]')[0].name = "sisaCargoV" + len;
            $html.find('[name=noDokCounter]')[0].name = "noDokCounter" + len;
            
            $html.find('label.title-vin').text("VIN Info " + len);
            $('#length_vin').val(len);
            return $html.html();
        }

        function BLGetHtml() {
            var len = $('.extraPersonBL').length + 1;
            var $html = $('.extraBL').clone();
            $html.find('[name=BLNumber]')[0].name = "BLNumber" + len;
            $html.find('[name=total_vin]')[0].name = "total_vin" + len;
            $html.find('[name=remaining_cargo]')[0].name = "remaining_cargo" + len;
            // $html.find('[name=counter]')[0].name="counter" + len;
            $html.find('[name=BLNumber' + len + ']').attr("data-id", len);
            // $html.find('[name=counter'+len+']').val(len);
            $html.find('[name=BLDate]')[0].name = "BLDate" + len;
            $html.find('[name=noDok]')[0].name = "noDok" + len;
            $html.find('[name=tglDok]')[0].name = "tglDok" + len;
            $html.find('[name=kdDok]')[0].name = "kdDok" + len;
            $html.find('[name=npwp]')[0].name = "npwp" + len;
            $html.find('label.title-bl').text("BL Info " + len);
            $('#length_bl').val(len);
            return $html.html();
        }

        function statusVinFailure(vinResponse) {
            const vinNumber = vinResponse.vinDetailResponse.VinNumber;
            $('#info_status1').show();
            $('#info_status1').addClass(alertF);
            setTimeout(function() {
                for (let j = 0; j < vinNumber.length; j++) {
                    const vin = vinNumber[j];
                    $('#info_status1').append('<h4>VIN : ' + vin + '</h4>');
                }
            }, 250);
            setTimeout(function() {
                $('#info_status1').append('<h4>Status ' + vinResponse.status.StatusName + ' : ' + vinResponse.status.StatusDescription + '</h4></div>');
            }, 350);
            $('a').removeAttr("disabled");
            $('input:text').removeAttr("disabled");
            jQuery('select').prop('disabled', false);
            jQuery('button').prop('disabled', false);
        }

        function statusBlFailure(blResponse) {
            const blNumber = blResponse.blDetailResponse.BLNumber;
            $('#info_status2').addClass(alertF);
            $('#info_status2').show();
            setTimeout(function() {
                for (let j = 0; j < blNumber.length; j++) {
                    const bl = blNumber[j];
                    $('#info_status2').append('<h4>BL : ' + bl + '</h4>');
                }
                $('#info_status2').append('</div>');
            }, 250);
            $('a').removeAttr("disabled");
            $('input:text').removeAttr("disabled");
            jQuery('select').prop('disabled', false);
            jQuery('button').prop('disabled', false);
        }

        function statusBlSuccess(blResponse) {
            const blNumber = blResponse.blDetailResponse.BLNumber;
            $('#info_status2').addClass(alertS);
            $('#info_status2').show();
            setTimeout(function() {
                for (let j = 0; j < blNumber.length; j++) {
                    const bl = blNumber[j];
                    $('#info_status2').append('<h4>BL : ' + bl + '</h4>');
                }
                $('#info_status2').append('</div>');
            }, 250);
            $('#main_form').trigger('reset');
            $('#length_bl').val(0);
            $('#bl-container-box').hide();
            $(".extraPerson").remove();
            $(".extraPersonBL").remove();
            $('a').removeAttr("disabled");
            $('input:text').removeAttr("disabled");
            $('#upload_vin_excel').trigger('reset');
            jQuery('select').prop('disabled', false);
            jQuery('button').prop('disabled', false);
        }

        function statusVinSuccess(vinResponse) {
            const vinNumber = vinResponse.vinDetailResponse.VinNumber;
            $('#info_status1').show();
            $('#info_status1').addClass(alertS);
            setTimeout(function() {
                for (let j = 0; j < vinNumber.length; j++) {
                    const vin = vinNumber[j];
                    $('#info_status1').append('<h4>VIN : ' + vin + '</h4>');
                }
            }, 250);
            setTimeout(function() {
                $('#info_status1').append('<h4>Status ' + vinResponse.status.StatusName + ' : ' + vinResponse.status.StatusDescription + '</h4></div>');
            }, 350);
            $('#main_form').trigger('reset');
            $('#length_vin').val(0);
            $('#container-box').hide();
            $(".extraPerson").remove();
            $(".extraPersonBL").remove();
            $('a').removeAttr("disabled");
            $('input:text').removeAttr("disabled");
            $('#upload_vin_excel').trigger('reset');
            jQuery('select').prop('disabled', false);
            jQuery('button').prop('disabled', false);
        }

        function statusAllSuccess(vinResponse, blResponse) {
            const vinNumber = vinResponse.vinDetailResponse.VinNumber;
            const blNumber = blResponse.blDetailResponse.BLNumber;
            $('#info_status1').show();
            $('#info_status2').show();
            $('#info_status1').addClass(alertS);
            $('#info_status2').addClass(alertS);
            setTimeout(function() {
                for (let j = 0; j < vinNumber.length; j++) {
                    const vin = vinNumber[j];
                    $('#info_status1').append('<h4>VIN : ' + vin + '</h4>');
                }
            }, 250);
            setTimeout(function() {
                $('#info_status1').append('<h4>Status ' + vinResponse.status.StatusName + ' : ' + vinResponse.status.StatusDescription + '</h4></div>');
            }, 300);
            setTimeout(function() {
                for (let j = 0; j < blNumber.length; j++) {
                    const bl = blNumber[j];
                    $('#info_status2').append('<h4>BL : ' + bl + '</h4>');
                }
                $('#info_status2').append('</div>');
            }, 350);
            $('#main_form').trigger('reset');
            $('#length_vin').val(0);
            $('#length_bl').val(0);
            $('#container-box').hide();
            $('#bl-container-box').hide();
            $(".extraPerson").remove();
            $(".extraPersonBL").remove();
            $('input:text').removeAttr("disabled");
            $('a').removeAttr("disabled");
            $('#upload_vin_excel').trigger('reset');
            jQuery('select').prop('disabled', false);
            jQuery('button').prop('disabled', false);
        }
    </script>
</body>

</html>