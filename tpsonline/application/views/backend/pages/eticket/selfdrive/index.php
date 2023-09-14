<!DOCTYPE html>
<html lang="id">

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
        <?php $this->load->view('backend/components/header') ?>

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

                        <?php
                        if ($this->userauth->getLoginData()->sender == 'IKT') {
                        ?>
                            <div class="col-sm-6">
                                <label class="text-left">Sender *</label>
                                <select class="form-control" id="typeIKT" name="typeIKT">
                                    <option value="">-- Select --</option>
                                    <?php
                                    foreach ($makers as $make) {
                                    ?>
                                        <option value="<?php echo $make->MAKE . '_' . $make->SENDER; ?>_IKT_ADMINISTRATOR"><?php echo $make->MAKE . '-' . $make->SENDER; ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                <?php echo form_error('typeIKT', '<div class="error">', '</div><br/>'); ?>
                                <div class="error"></div>
                            </div>
                        <?php
                        }
                        ?>

                        <div class="col-sm-<?php echo $this->userauth->getLoginData()->sender == 'IKT' ? '6' : '12' ?>">
                            <label class="text-left">Truck Code *</label>
                            <select class="form-control" id="truckCode" name="truckCode">
                                <option value="">Select Truck</option>
                                <?php
                                foreach ($trucks as $truck) {
                                ?>
                                    <option value="<?php echo $truck->CODE; ?>"><?php echo $truck->CODE . " - " . $truck->NAME; ?></option>
                                <?php
                                }
                                ?>
                            </select>
                            <?php echo form_error('truckCode', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div>
                        </div>
                    </div>

                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="text-left">Driver Name *</label>
                            <input type="text" class="form-control" id="driver_name" name="driver_name" placeholder="" />
                            <?php echo form_error('driver_name', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-left">Driver Identity *<small>pdf max 2MB</small></label>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <span class="btn btn-primary btn-file">
                                        Browse&hellip; <input type="file" name="upload_identify" id="upload_identify">
                                    </span>
                                </span>
                                <input type="text" class="form-control" readonly="readonly">
                            </div>
                            <?php echo form_error('upload_identify', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="text-left">Trip *</label>
                            <select class="form-control" id="trip_id" name="trip_id">
                                <option value="">Select Trip</option>
                                <option value="EXPORT">EXPORT</option>
                                <option value="IMPORT">IMPORT</option>
                            </select>
                            <?php echo form_error('trip_id', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div>
                        </div>
                        <div class="col-sm-6">
                            <label class="text-left">VIN <small>* (depends on sender and trip)</small></label>
                            <select class="form-control" id="vin_request" name="vin_request">
                                <option value="">Select VIN</option>
                            </select>
                            <?php echo form_error('vin_request', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div>
                        </div>
                    </div>

                    <div class="form-group row vin-detail" style="display: none;">
                        <div class="col-sm-3">
                            <label class="text-left">No Dokumen *</label>
                            <input type="text" class="form-control" id="noDok" name="noDok" placeholder="" />
                            <?php echo form_error('noDok', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div>
                        </div>

                        <div class="col-sm-3">
                            <label class="text-left">Tanggal Dokumen *</label>
                            <input type="date" class="form-control" id="tglDok" name="tglDok" placeholder="dd-mm-yyyy" />
                            <?php echo form_error('tglDok', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div>
                        </div>
                        <div class="col-sm-3">
                            <label class="text-left">Kode Dokumen *</label>
                            <select class="form-control" name="kdDok">
                                <option value="">-- Select --</option>
                                <?php foreach ($dokumen as $dok) { ?>
                                    <option value="<?php echo $dok->ID; ?>"><?php echo $dok->ID . '-' . $dok->DOC_TYPE; ?> </option>
                                <?php } ?>
                            </select>
                            <?php echo form_error('kdDok', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div>
                        </div>

                        <div class="col-sm-3">
                            <label class="text-left">NPWP *</label>
                            <input type="text" class="form-control" id="npwp" name="npwp" placeholder="" />
                            <?php echo form_error('npwp', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div>
                        </div>
                    </div>

                    <div class="clearfix"></div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="text-left"><small>* is required</small></label>
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

    <?php $this->load->view('backend/elements/footer') ?>

    <script type="text/javascript">
        $('#main_form').submit(function() {
            var noDokVal = $('#noDok').val();
            var tglDokVal = $('#tglDok').val();
            var noDokRes = noDokVal.substring(noDokVal.length - 4, noDokVal.length);
            var tglDokRes = tglDokVal.substring(0, 4);
            if (noDokVal.length >= 0 && tglDokVal.length >= 0) {
                if (noDokRes == tglDokRes) {
                    // alert("Tahun sama");
                    return true;
                } else {
                    alert("Tahun di Nomor Dokumen dan Tanggal Dokumen Berbeda");
                    return false;
                }
            }
        });

        $(document).ready(function() {

            // $('.vin_detail').hide();
            $('#typeIKT').select2();

            $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
                var input = $(this).parents('.input-group').find(':text'),
                    log = numFiles > 1 ? numFiles + ' files selected' : label;

                if (input.length) {
                    input.val(log);
                } else {
                    if (log) alert(log);
                }

            });
            $('#trip_id').on('change', function() {
                // var valTrip = $('#trip_id').val();
                // if (valTrip === "IMPORT") {
                //     alert("Nomor Dokumen dan Tanggal Dokumen Wajib Diisi")
                // } else {
                //     alert("Nomor Dokumen dan Tanggal Dokumen Tidak Wajib Diisi")
                // }
                $('.vin-detail').show();
                $('.dateVin').datepicker({
                    format: 'dd-mm-yyyy'
                });
            });

            $('#vin_request').on('select2:open', function(e) {
                if ($('#trip_id').val() == "") {
                    alert("TRIP Wajib Diisi Sebelum Mencari VIN")
                }
            });

            $('#vin_request').select2({
                ajax: {
                    url: '<?php echo site_url('eticket/selfdrive/getVINSeldrive'); ?>',
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function(params) {
                        return {
                            searchTerm: params.term, // search term
                            page_limit: 10,
                            page: params.page || 1,
                            maker: $('#typeIKT').val(),
                            trip: $('#trip_id').val()
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

        });

        $(document)
            .on('change', '.btn-file :file', function() {
                var input = $(this),
                    numFiles = input.get(0).files ? input.get(0).files.length : 1,
                    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                input.trigger('fileselect', [numFiles, label]);
            });

        //function addSelfdrive() {
        //    var form = $("#selfdriveFormModal");
        //    form.trigger("reset");
        //    form.find('.modal-title').text('Add Selfdrive');
        //    $('#truckCode').select2(
        //        {
        //            ajax: {
        //                url: '<?php //echo site_url('eticket/return_cargo/getTruckInfo'); 
                                ?>//',
        //                type: "post",
        //                dataType: 'json',
        //                delay: 250,
        //                data: function (params) {
        //                    return {
        //                        searchTerm: params.term // search term
        //                    };
        //                },
        //                processResults: function (response) {
        //                    return {
        //                        results: $.map(response, function(obj) {
        //                            return {
        //                                id: obj.id,
        //                                text: obj.text
        //                            };
        //                        })
        //                    };
        //                },
        //                cache: true
        //            },
        //            minimumInputLength : 3
        //        }
        //    );
        //
        //    $('#vin_request').select2(
        //        {
        //            ajax: {
        //                url: '<?php //echo site_url('eticket/selfdrive/getVin'); 
                                ?>//',
        //                type: "post",
        //                dataType: 'json',
        //                delay: 250,
        //                data: function (params) {
        //                    return {
        //                        searchTerm: params.term // search term
        //                    };
        //                },
        //                processResults: function (response) {
        //                    return {
        //                        results: $.map(response, function(obj) {
        //                            return {
        //                                id: obj.id,
        //                                text: obj.text
        //                            };
        //                        })
        //                    };
        //                },
        //                cache: true
        //            },
        //            minimumInputLength : 3
        //        }
        //    );
        //
        //    $('#selfdriveModal').modal({backdrop: 'static', keyboard: false});
        //}

        //$("#selfdriveFormModal").submit(function(e) {
        //    e.preventDefault();
        //    var form = $(this);
        //
        //    swal({
        //        title: 'Warning !',
        //        text: "Are you sure to submit?",
        //        type: 'warning',
        //        buttons: true,
        //        buttons: ["Cancel", "Sure!"],
        //        closeModal: false
        //    }).then((result) => {
        //        if(result){
        //            $.ajax(
        //                {
        //                    type:"post",
        //                    enctype: 'multipart/form-data',
        //                    url: "<?php //echo base_url(); 
                                    ?>//eticket/selfdrive/submit_item",
        //                    data:new FormData(this),
        //                    processData:false,
        //                    contentType:false,
        //                    cache:false,
        //                    success:function(response)
        //                    {
        //                        var obj = jQuery.parseJSON( response );
        //                        console.log(obj);
        //                        if(response){
        //                            swal({
        //                                title: 'Success!',
        //                                text: obj.response.responmsg,
        //                                type: 'success',
        //                            });
        //                        }
        //
        //                        // if(obj.response.StatusCode == 200){
        //                        //     swal({
        //                        //         title: 'Success!',
        //                        //         text: 'The request is submitted successfully with request number '+obj.response.RCNumberReq,
        //                        //         type: 'success',
        //                        //     });
        //                        //     $('#t_return_cargo').DataTable().ajax.reload();
        //                        //     $('#printModal').modal('hide');
        //                        // }else{
        //                        //     swal({
        //                        //         title: 'Failed!',
        //                        //         text: obj.response.RCStatus,
        //                        //         type: 'success',
        //                        //     });
        //                        //     $('#t_return_cargo').DataTable().ajax.reload();
        //                        // }
        //
        //                    },
        //                    error:function(response){
        //                        swal({
        //                            title: 'Failed!',
        //                            text: obj.response.RCStatus,
        //                            type: 'error',
        //                        });
        //                        $('#t_return_cargo').DataTable().ajax.reload();
        //                    }
        //                }
        //            );
        //
        //        }
        //    })
        //
        //});
    </script>

</body>

</html>