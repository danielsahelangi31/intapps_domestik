<!DOCTYPE html>
<html lang="id">
<?php
    $integrasi_cardom_dev = $this->load->database('integrasi_cardom_dev', TRUE);
    $auth = $this->userauth->getLoginData();
    $userMode = "";
    $shipping_name = "";
    if($auth->intapps_type == "ADMIN") {
        $shipping_name = $auth->full_name;
        $userMode = 'ADMIN';
    } else {
        $query = $integrasi_cardom_dev->query("select NAME from M_ORGANIZATION WHERE ID = '".$auth->intapps_type."' ");
            if ($query->num_rows() > 0)
            {
                $hasil = $query->row();
                $shipping_name = $hasil->NAME . " (". $auth->full_name  .")";
            }
        $userMode = 'SHIPPING';

    }
 ?>
<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
</head>

<body>
    <div id="wrap">
        <?php $this->load->view('domestik/backend/components/header_domestik') ?>

        <div class="container">

            <h2>Selfdrive</h2>
            <hr />
            <?php
            if ($responses) {
            ?>
                <div class="alert alert-<?php echo $responses->responcode == '200' ? 'warning' : 'danger'; ?> fade in">
                    <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                    <!-- <h4><?php echo 'Info : ' . $responses->responcode . ' - ' . $responses->responmsg ?></h4> -->
                    <h4><?php echo 'Info : ' . $responses->responmsg ?></h4>
                    <?php
                    if ($responses->responcode == '200') {
                    ?>
                        <h4><?php echo 'VISIT ID : ' . $responses->InfoTruck->VisitID; ?></h4>
                    <?php
                    }
                    ?>
                </div>
            <?php
            }
            ?>

            <div class="col-md-12 mx-auto">
                <form id="main_form" role="form" action="" method="post" enctype="multipart/form-data">


                    <div class="form-group row">
                        <div class="col-sm-12">
                            <label class="text-left">Truck Code </label>
                            <input type="text" class="form-control" id="truckCode" name="truckCode" value="SELFDRIVE" />
                        </div>
                    </div>

                    <div class="form-group row">

                        <div class="col-sm-6">
                            <label class="text-left">Direction *</label>
                            <select class="form-control" id="directionId" name="directionId">
                                <option value="">-- Select --</option>
                                <option value="D">INBOUND(DISCHARGE)</option>
                                <option value="L">OUTBOUND(LOADING)</option>
                            </select>
                            <?php echo form_error('directionId', '<div class="error">', '</div><br/>'); ?>
                            <div class="error_direction"></div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-left">Driver Name *</label>
                            <input type="text" class="form-control" id="driver_name" name="driver_name" placeholder="" />
                            <?php echo form_error('driver_name', '<div class="error">', '</div><br/>'); ?>
                            <div class="error_driver_name"></div>
                        </div>
                        <!-- <div class="col-sm-6">
                            <label class="text-left">Tipe Input *</label>
                            <select class="form-control" id="tipeInput">
                                <option value="S">-- select --</option>
                                <option value="N">Input baru</option>
                                <option value="A">Asosiasi</option>
                            </select>
                        </div> -->
                        <!-- Backup -->
                        <!-- <div class="col-sm-6">
                            <label class="text-left">Driver Identity *<small>pdf max 2MB</small></label>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <span class="btn btn-primary btn-file">
                                        Browse&hellip; <input type="file" name="upload_identify" id="upload_identify">
                                    </span>
                                </span>
                                <input type="text" class="form-control" readonly="readonly">
                            </div>
                            <?php //echo form_error('upload_identify', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div>
                        </div> -->
                    </div>
                    <div class="form-group row">
                        <div id="vin-inbound" class="col-sm-6">
                            <label class="text-left">VIN *</label>
                            <select class="form-control" id="vin_request" name="vin_request">
                                <option value="">-- Select --</option>
                                <?php foreach($vin as $v) { ?>
                                    <option value="<?php echo $v->VIN_CODE ?>"><?php echo $v->VIN_CODE ?></option>
                                <?php } ?>
                            </select>
                            <?php echo form_error('vin_request', '<div class="error">', '</div><br/>'); ?>
                            <div class="error_vin_inbound"></div>
                        </div>

                        <div id="vin-outbound" class="col-sm-6">
                            <label class="text-left">VIN *</label>
                            <input type="text" class="form-control" id="vinRequestOutbound" name="vinRequestOutbound" placeholder="" />
                            <?php echo form_error('vinRequestOutbound', '<div class="error">', '</div><br/>'); ?>
                            <div class="error_vin_outbound"></div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-left">Driver Phone Number *</label>
                            <input type="text" class="form-control" id="driverPhoneNumber" name="driverPhoneNumber" placeholder="" />
                            <?php echo form_error('driverPhoneNumber', '<div class="error">', '</div><br/>'); ?>
                            <div class="error_driver_phone_number"></div>
                        </div>

                    </div>

                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="text-left">Direction Type</label>
                                <input type="text" class="form-control"
                                        name="directionType" value="DOMESTIC" readonly
                                />
                        </div>
                        <div id="div-fuel-in">
                            <div class="col-sm-6">
                                <label class="text-left">Fuel</label>
                                <input type="text" class="form-control" id="fuelIn" name="fuel" placeholder="">
                            </div>
                        </div>
                        <div id="div-fuel-out">
                            <div class="col-sm-6">
                                <label class="text-left">Fuel</label>
                                <input type="text" class="form-control" id="fuelOut" placeholder="">
                                <?php echo form_error('fuelOut', '<div class="error">', '</div><br/>'); ?>
                                <div class="error_fuelOut"></div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group row dir-inbound">
                        <div class="col-sm-6">
                            <label class="text-left">Model *</label>
                            <input type="text" class="form-control" id="modelInbound" name="modelInbound" placeholder="" readonly/>
                            <?php echo form_error('modelInbound', '<div class="error">', '</div><br/>'); ?>
                            <div class="error_model_inbound"></div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-left">Destination *</label>
                            <input type="text" class="form-control" id="destInbound" name="destInbound" placeholder="" readonly/>
                            <?php echo form_error('destInbound', '<div class="error">', '</div><br/>'); ?>
                            <div class="error_destination_inbound"></div>
                        </div>
                    </div>

                    <div class="form-group row dir-outbound">
                        <div class="col-sm-6">
                            <label class="text-left">Model *</label>
                            <select class="form-control" id="modelOutbound" name="modelOutbound">
                                <option value="">-- Select --</option>
                            </select>
                            <?php echo form_error('modelOutbound', '<div class="error">', '</div><br/>'); ?>
                            <div class="error_model_outbound"></div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-left">Destination *</label>
                            <select class="form-control" id="destOutbound" name="destOutbound">
                                <option value="">-- Select --</option>
                            </select>
                            <?php echo form_error('destOutbound', '<div class="error">', '</div><br/>'); ?>
                            <div class="error_destination_outbound"></div>
                        </div>
                    </div>
                    <?php
                        // untuk mengenerate nomor document transfer id
                        date_default_timezone_set('Asia/Jakarta');
                        $tanggal = date('y-m-d h:i:s');
                        $tanggal = (string) $tanggal;
                        $tanggal = str_replace(' ', '', $tanggal);
                        $tanggal = str_replace('-', '', $tanggal);
                        $tanggal = str_replace(':', '', $tanggal);
                        $FiveDigitRandomNumber = rand(10000,99999);
                        $FiveDigitRandomNumber = (string) $FiveDigitRandomNumber;
                        $documentTransferId = $tanggal.'~'.$FiveDigitRandomNumber;
                    ?>
                    <?php
                    if($userMode == "ADMIN") {
                    ?>
                        <div class="form-group row dir-admin-outbound">
                            <div class="col-sm-6">
                                <label class="text-left">Shipping Line *</label>
                                <select class="form-control" id="shippingLineOutboundAdmin" name="shippingLineOutboundAdmin">
                                    <option value="">-- Select --</option>
                                </select>
                                <?php echo form_error('shippingLineOutboundAdmin', '<div class="error">', '</div><br/>'); ?>
                                <div class="error_shipping_line_outbound"></div>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-left">Document Transfer ID</label>
                                <input type="text" class="form-control" id="documentTransferId" value="<?php echo $documentTransferId; ?>" readonly/>
                            </div>
                        </div>
                        <div class="form-group row dir-admin-inbound">
                            <div class="col-sm-6">
                                <label class="text-left">Shipping Line *</label>
                                <input type="hidden" id="idShippingLine">
                                <input type="text" class="form-control" id="shippingLineInboundAdmin" name="shippingLineInboundAdmin" readonly/>
                            </div>
                            <div class="col-sm-6">
                                <label class="text-left">Document Transfer ID</label>
                                <input type="text" class="form-control" id="documentTransferId" value="<?php echo $documentTransferId; ?>" readonly/>
                            </div>
                        </div>
                    <?php
                    } else {
                    ?>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="text-left">Shipping Line *</label>
                            <input type="text" class="form-control" id="shippingLineInbound" name="shippingLineInbound" value="<?php echo $shipping_name ?>" readonly/>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-left">Document Transfer ID</label>
                            <input type="text" class="form-control" id="documentTransferId" value="<?php echo $documentTransferId; ?>" readonly/>
                        </div>
                    </div>
                    <?php } ?>
                    <div class="form-group row">
                        <div class="col-sm-6">
                        <label class="text-left">Vessel Name *</label>
                        <select class="form-control" id="vesselName" name="vesselName">
                            <option value="">-- Select --</option>
                        </select>
                        <?php echo form_error('vesselName', '<div class="error">', '</div><br/>'); ?>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="text-left"><small>* is Mandatory</small></label>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-lg-12">
                            <button type="submit" class="btn btn-primary btn-block">Submit</button>
                        </div>
                    </div>

                </form>
            </div>

        </div><!-- /.container -->
    </div>

    <?php $this->load->view('domestik/backend/elements/footer_domestik') ?>

    <script type="text/javascript">
        var userMode = <?php echo json_encode($userMode); ?>;
        $('#vin_request').on('change', function() {
            $.ajax({
                url: '<?php echo site_url('domestik/selfdrive_domestik/getDataSelf') ?>',
                type: "post",
                dataType: 'json',
                cache:false,
                data: {
                    cari:$(this).val()
                },
                delay: 500,
                success: function(data) {
                    // console.log(data);
                    var realData = data[0];
                    if(data.length != 0){
                        $('#fuel').val(realData["FUEL_TYPE"]);
                        $('#modelInbound').val(realData["NAME"]);
                        $('#destInbound').val(realData["PORT_NAME"]);
                        if(userMode == "ADMIN"){
                        $('#shippingLineInboundAdmin').val(realData["SHIPPING_LINE"]);
                        $('#idShippingLine').val(realData["ORG_ID"]);
                        }
                    }
                }
            });
        });

        $('#main_form').submit(function(e) {
            e.preventDefault();
            var isError = false;
            var dirVal = $('#directionId').val();
            var driverName = $("#driver_name").val();
            var driverPhoneNumber = $("#driverPhoneNumber").val();
            var shippingLine = "";
            var vin = "";
            var model = "";
            var destination = "";
            var dirType = 'DOMESTIC';
            var listVal = [];


            $('.error').remove();
            if ($.trim($("#driver_name").val()) === "") {
                listVal.push("Driver Name harus diisi");
                isError = true;
            }
            if($.trim($("#driverPhoneNumber").val()) === ""){
                listVal.push("Driver Phone Number harus diisi");
                isError = true;
            }
            
            if(dirVal == 'D'){

                if ($.trim($("#vin_request").val()) === "") {
                    listVal.push("Vin Number harus diisi");
                    isError = true;
                } else {
                    vin = $("#vin_request").val();
                }

                if ($.trim($("#modelInbound").val()) === "") {
                    listVal.push("Model harus diisi");
                    isError = true;
                } else {
                    model = $("#modelInbound").val();
                }

                if ($.trim($("#destInbound").val()) === "") {
                    listVal.push("Destination harus diisi");
                    isError = true;
                } else {
                    destination = $("#destInbound").val();
                }

                if(userMode == "ADMIN") {
                    if ($.trim($("#shippingLineInboundAdmin").val()) === "") {
                        listVal.push("Shipping Line harus diisi");
                        isError = true;
                    } else {
                        shippingLine = $("#idShippingLine").val();
                    }
                } else {
                    shippingLine = <?php echo $auth->intapps_type?>;
                }

                if ($.trim($("#vesselName").val()) === "") {
                    listVal.push("Vessel Name harus diisi");
                    isError = true;
                } else {
                    vesselCode = $("#vesselName").val();
                }
                
            } else if (dirVal == 'L'){

                if ($.trim($("#vinRequestOutbound").val()) === "") {
                    listVal.push("Vin Number harus diisi");
                    isError = true;
                } else {
                    vin = $("#vinRequestOutbound").val();
                }

                if ($.trim($("#modelOutbound").val()) === "") {
                    listVal.push("Model harus diisi");
                    isError = true;
                } else {
                    model = $("#modelOutbound option:selected").attr("value");
                }

                if ($.trim($("#destOutbound").val()) === "") {
                    listVal.push("Destination harus diisi");
                    isError = true;
                } else {
                    destination = $("#destOutbound option:selected").attr('value');
                }

                if(userMode == "ADMIN"){
                    if ($.trim($("#shippingLineOutboundAdmin").val()) === "") {
                        listVal.push("Shipping Line harus diisi");
                        isError = true;
                    } else {
                        shippingLine = $("#shippingLineOutboundAdmin").val();
                    }
                } else {
                    shippingLine = <?php echo $auth->intapps_type; ?>;
                }

                if ($.trim($("#vesselName").val()) === "") {
                    listVal.push("Vessel Name harus diisi");
                    isError = true;
                } else {
                    vesselCode = $("#vesselName").val();
                }

            } else {
                isError = true;
                listVal.push("Driver Name harus diisi");
                listVal.push("Driver Phone Number harus diisi");
                listVal.push("Vin Number harus diisi");
                listVal.push("Model harus diisi");
                listVal.push("Destination harus diisi");
                listVal.push("Shipping Line harus diisi");
                listVal.push("Vessel Name harus diisi");
            }

            if(isError == false){
                var dform = {
                    "direction" : dirVal,
                    "driverName" : driverName,
                    "vin" : vin,
                    "driverPhoneNumber" : driverPhoneNumber,
                    "directionType" : dirType,
                    "model": model,
                    "dest" : destination,
                    "sl" : shippingLine,
                    "vesselCode": vesselCode
                };

                $.ajax({
                    url: "<?php echo site_url('domestik/selfdrive_domestik/insert_self_drive'); ?>",
                    data: {
                       vin: dform.vin,
                       direction: dform.direction,
                       directionType: dform.directionType,
                       model: dform.model,
                       dest: dform.dest,
                       sl: dform.sl,
                       driverPhoneNumber: dform.driverPhoneNumber,
                       driverName: dform.driverName,
                       truckCode: $('#truckCode').attr('value'), 
                       portCode: $('#destOutbound option:selected').attr('value'),
                       docTransferId: $('#documentTransferId').attr('value'),
                       tipeInput: $("#tipeInput").val(),
                       vesselCode: dform.vesselCode,
                       fuelIn: $('fuelIn').val(),
                       fuelOut: $('fuelOut').val()
                    },
                    type: "post",
                    dataType: "text",
                    cache: false,
                    success: function(data) {
                       //console.log(data);
                        alert(data);
                        location.reload(); 
                    }, 
                    error: function (error) {
                        console.log(error);
                    }
                });
            } else {
                alert(listVal.map(item => { return item + "\n"; }).join(''));
            }
           
            


            // var noDokVal = $('#noDok').val();
            // var tglDokVal = $('#tglDok').val();
            // var noDokRes = noDokVal.substring(noDokVal.length - 4, noDokVal.length);
            // var tglDokRes = tglDokVal.substring(0, 4);
            // if (noDokVal.length >= 0 && tglDokVal.length >= 0) {
            //     if (noDokRes == tglDokRes) {
            //         // alert("Tahun sama");
            //         return true;
            //     } else {
            //         alert("Tahun di Nomor Dokumen dan Tanggal Dokumen Berbeda");
            //         return false;
            //     }
            // }
        });

        $(document).ready(function() {

            // $('.vin_detail').hide();
            $('#typeIKT').select2();
            $('#directionId').select2({
                minimumResultsForSearch: -1
            });
            $('#truckCode').prop('disabled', true);
            $('#vin_request').prop('disabled', true);
            $('#tipeInput').prop('disabled', true);
            $('#driver_name').prop('disabled', true);
            $('#driverPhoneNumber').prop('disabled', true);
            // $(".conDesOut").hide();
            // $('.conDesIn').show();
            $('#destInbound').prop("diasbled", true);
            $("#vin-outbound").hide();
            $(".dir-outbound").hide();
            $(".dir-admin-outbound").hide();
            $(".dir-admin-inbound").show();
            $("#fuelIn").prop("disabled", true);
            $("#div-fuel-out").hide();
            $("#vesselName").prop('disabled', true);
            $('#modelOutbound').select2({
                ajax: {
                    url: '<?php echo site_url('domestik/selfdrive_domestik/getSelfdriveModel'); ?>',
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },
                    processResults: function(response) {
                    console.log(response);
                        return {
                            results: $.map(response, function(obj) {
                                return {
                                    id: obj.id,
                                    text: obj.text,
                                    value: obj.id
                                };
                            })
                        };

                    },
                    cache: true
                }
            });
            $('#destOutbound').select2({
                ajax: {
                    url: '<?php echo site_url('domestik/selfdrive_domestik/getDestination'); ?>',
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },
                    processResults: function(response) {
                    console.log(response);
                        return {
                            results: $.map(response, function(obj) {
                                return {
                                    id: obj.id,
                                    text: obj.text,
                                    value: obj.id
                                };
                            })
                        };

                    },
                    cache: true
                }
            });
            $('#shippingLineOutboundAdmin').select2({
                ajax: {
                    url: '<?php echo site_url('domestik/selfdrive_domestik/getShippingLine'); ?>',
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term
                        };
                    },
                    processResults: function(response) {
                    console.log(response);
                        return {
                            results: $.map(response, function(obj) {
                                return {
                                    id: obj.id,
                                    text: obj.text,
                                    value: obj.id
                                };
                            })
                        };

                    },
                    cache: true
                }
            }).prop('disabled', true);

            $('#vesselName').select2({
                ajax: {
                    url: '<?php echo site_url('domestik/announce_truck_domestik/getVesselName'); ?>',
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term // search term
                        };
                    },
                    processResults: function(response) {
                        console.log(response);
                        return {
                            results: $.map(response, function(obj) {
                                return {
                                    id: obj.id_vvd,
                                    text: obj.vessel_name,
                                    value: obj.id_vvd
                                };
                            })
                        };
                    },
                    cache: true
                },
            });
            
            
            $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
                var input = $(this).parents('.input-group').find(':text'),
                    log = numFiles > 1 ? numFiles + ' files selected' : label;

                if (input.length) {
                    input.val(log);
                } else {
                    if (log) alert(log);
                }

            });

            $(document).on('keyup', '#vinRequestOutbound', function() {
                this.value = this.value.replace(/\s/g,'');
                this.value = this.value.replace(/[`~!@#$%^&*()|+\-=?;:'",.<>\{\}\[\]\\\/]/g,'');
                $.ajax({
                    url:"<?php echo site_url('domestik/selfdrive_domestik/vinModel') ?>",
                    type:"post",
                    dataType:"json",
                    cache:true,
                    delay: 500,
                    data:{vin:$(this).val()},
                    success: function(data) {
                        if(data.length != 0){
                            var o = $("<option/>", {id: data[0]["ID_CATEGORY"], text: data[0]["NAME"], value: data[0]["ID_CATEGORY"]});
                            
                            $("#modelOutbound").append(o);
                            $("#modelOutbound").val(data[0]["ID_CATEGORY"]).trigger('change');
                        }
                    }
                })
            });



            $('#directionId').on('change', function() {
                // var valTrip = $('#trip_id').val();
                // if (valTrip === "IMPORT") {
                //     alert("Nomor Dokumen dan Tanggal Dokumen Wajib Diisi")
                // } else {
                //     alert("Nomor Dokumen dan Tanggal Dokumen Tidak Wajib Diisi")
                // }
                var dirVal = $('#directionId').val();
                $('#fuel').val('');
                $('#vin_request').val('').trigger('change');
                $('#vinRequestOutbound').val('');
                $('#modelInbound').val('');
                $('#modelOutbound').val(null).trigger('change');
                $('#destInbound').val('');
                $('#destOutbound').val(null).trigger('change');
                $('#shippingLineOutboundAdmin').val(null).trigger('change');
                $('#shippingLineInboundAdmin').val("");
                $('.error').remove();

                
                if(dirVal == "D"){
                    // $('#vin_request').prop('disabled', true);
                    $('#vin-inbound').show();
                    $('#vin-outbound').hide();
                    $('.dir-inbound').show();
                    $('.dir-admin-inbound').show();
                    $('.dir-outbound').hide();
                    $('.dir-admin-outbound').hide();
                    $('#vin_request').prop('disabled', false);
                    $('#driver_name').prop('disabled', false);
                    $('#driverPhoneNumber').prop('disabled', false);
                    $('#tipeInput').prop('disabled', true);
                    $('#tipeInput').val("S");
                    $('#fuelIn').prop('disabled', false);
                    $('#shippingLineOutboundAdmin').prop('disabled', false);
                    $(".conDesOut").hide();
                    $(".conDesIn").show();
                    $("#destInbound").prop("disabled", false);
                    $("#vesselName").prop('disabled', false); 
                } else if (dirVal == "L"){
                    $('#vin-outbound').show();
                    $('#vin-inbound').hide();
                    $('.dir-inbound').hide();
                    $('.dir-outbound').show();
                    $('#shippingLineOutboundAdmin').prop('disabled', false);
                    $('.dir-admin-inbound').hide();
                    $('.dir-admin-outbound').show();
                    $('#vin_request').prop('disabled', false);
                    $('#tipeInput').prop('disabled', false);
                    $('#vinRequestOutbound').prop('disabled', false);
                    $('#driverPhoneNumber').prop('disabled', false);
                    $('#driver_name').prop('disabled', false);
                    $("#div-fuel-in").hide();
                    $("#div-fuel-out").show();
                    $('#modelOutbound').prop('disabled', false);
                    $('#destOutbound').prop('disabled', false);
                    $('#shippingLineOutboundAdmin').prop('disabled', false);
                    $("#vesselName").prop('disabled', false);
                } else {
                    $('#vin_request').prop('disabled', true);
                    $('#driverPhoneNumber').prop('disabled', true);
                    $('#driver_name').prop('disabled', true);
                    $('#shippingLineOutboundAdmin').prop('disabled', true);
                    $('#vinRequestOutbound').prop('disabled', true);
                    $('#tipeInput').prop('disabled', true);
                    $("#fuelOut").prop("disabled", true);
                    $(".dir-inbound").show();
                    $(".dir-outbound").hide();
                    $('#modelOutbound').prop('disabled', true);
                    $(".conDesOut").hide();
                    $(".conDesIn").show();
                    $('#destInbound').prop('disabled', true);
                    $('#shippingLineOutboundAdmin').prop('disabled', true);
                    $('#documentTransferId').prop('readonly', true);
                    $(".dir-admin-outbound").hide();
                    $(".dir-admin-inbound").show();
                    $("#vesselName").prop('disabled', true);
                }
                // $('.vin-detail').show();
                // $('.dateVin').datepicker({
                //     format: 'dd-mm-yyyy'
                // });
            });

            $('#vin_request').select2({
                ajax: {
                    url: '<?php echo site_url('domestik/selfdrive_domestik/getSearchVin'); ?>',
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term, // search term
                            dir: $("#directionId").val()
                        };
                    },
                    processResults: function(response) {
                        return {
                            results: $.map(response, function(obj) {
                                return {
                                    id: obj.id,
                                    text: obj.text,
                                    value: obj.text
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength : 3
            });
            
            function hapusRow(trx) {
            $.ajax({
                url: '<?php echo site_url('domestik/eticket_list_domestik/')?>',
                type: 'post',
                dataType : 'text',
                data: {
                    trx: trx
                }, 
                success: function(data) {
                    console.log(data);
                }
            });
        }

        

        });

        $(document)
            .on('change', '.btn-file :file', function() {
                var input = $(this),
                    numFiles = input.get(0).files ? input.get(0).files.length : 1,
                    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                input.trigger('fileselect', [numFiles, label]);
            });

        // function addSelfdrive() {
        //     var form = $("#selfdriveFormModal");
        //     form.trigger("reset");
        //     form.find('.modal-title').text('Add Selfdrive');
        //     $('#truckCode').select2(
        //         {
        //             ajax: {
        //                 url: '<?php //echo site_url('eticket/return_cargo/getTruckInfo'); 
        //                         ?>',
        //                 type: "post",
        //                 dataType: 'json',
        //                 delay: 250,
        //                 data: function (params) {
        //                     return {
        //                         searchTerm: params.term  // search term
        //                     };
        //                 },
        //                 processResults: function (response) {
        //                     return {
        //                         results: $.map(response, function(obj) {
        //                             return {
        //                                 id: obj.id,
        //                                 text: obj.text
        //                             };
        //                         })
        //                     };
        //                 },
        //                 cache: true
        //             },
        //             minimumInputLength : 3
        //         }
        //     );
        
        //     $('#vin_request').select2(
        //         {
        //             ajax: {
        //                 url: '<?php //echo site_url('eticket/selfdrive/getVin'); 
        //                         ?>',
        //                 type: "post",
        //                 dataType: 'json',
        //                 delay: 250,
        //                 data: function (params) {
        //                     return {
        //                         searchTerm: params.term  // search term
        //                     };
        //                 },
        //                 processResults: function (response) {
        //                     return {
        //                         results: $.map(response, function(obj) {
        //                             return {
        //                                 id: obj.id,
        //                                 text: obj.text
        //                             };
        //                         })
        //                     };
        //                 },
        //                 cache: true
        //             },
        //             minimumInputLength : 3
        //         }
        //     );

            
        
        //     $('#selfdriveModal').modal({backdrop: 'static', keyboard: false});
        // }

        $("#selfdriveFormModal").submit(function(e) {
            e.preventDefault();
            var form = $(this);
        
            swal({
                title: 'Warning !',
                text: "Are you sure to submit?",
                type: 'warning',
                buttons: true,
                buttons: ["Cancel", "Sure!"],
                closeModal: false
            }).then((result) => {
                if(result){
                    $.ajax(
                        {
                            type:"post",
                            enctype: 'multipart/form-data',
                            url: "<?php echo base_url(); 
                                    ?>eticket/selfdrive/submit_item",
                            data:new FormData(this),
                            processData:false,
                            contentType:false,
                            cache:false,
                            success:function(response)
                            {
                                var obj = jQuery.parseJSON( response );
                                console.log(obj);
                                if(response){
                                    swal({
                                        title: 'Success!',
                                        text: obj.response.responmsg,
                                        type: 'success',
                                    });
                                }
        
                                 if(obj.response.StatusCode == 200){
                                     swal({
                                         title: 'Success!',
                                         text: 'The request is submitted successfully with request number '+obj.response.RCNumberReq,
                                         type: 'success',
                                     });
                                     $('#t_return_cargo').DataTable().ajax.reload();
                                     $('#printModal').modal('hide');
                                 }else{
                                     swal({
                                         title: 'Failed!',
                                         text: obj.response.RCStatus,
                                         type: 'success',
                                     });
                                     $('#t_return_cargo').DataTable().ajax.reload();
                                 }
        
                            },
                            error:function(response){
                                swal({
                                    title: 'Failed!',
                                    text: obj.response.RCStatus,
                                    type: 'error',
                                });
                                $('#t_return_cargo').DataTable().ajax.reload();
                            }
                        }
                    );
        
                }
            })
        
        });
    </script>

</body>

</html>
