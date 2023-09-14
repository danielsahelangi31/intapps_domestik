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

        <!-- Modal -->
        <div id="printModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                    <form id="printFormModal" method="post" enctype="multipart/form-data">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title">Input data print out</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <input type="hidden" name="vin_request" id="vin_request" value="">
                                        <label class="text-left">Truck Code * </label>
                                        <select class="form-control" name="truckCode" id="truckCode">
                                            <option value="">-- Insert Truck Code (without space) --</option>
                                        </select>
                                        <?php echo form_error('truckCode', '<div class="error">', '</div><br/>'); ?>
                                        <div class="error"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">Carrier * </label>
                                        <input readonly type="text" class="form-control" id="carrierID" name="carrierID" />
                                        <?php echo form_error('carrierID', '<div class="error">', '</div><br/>'); ?>
                                        <div class="error"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">Driver Name * </label>
                                        <input type="text" class="form-control" id="driverName" name="driverName" placeholder="Insert truck driver name" />
                                        <?php echo form_error('driverName', '<div class="error">', '</div><br/>'); ?>
                                        <div class="error"></div>
                                    </div>
                                </div>
                                <?php
                                if ($this->userauth->getLoginData()->sender == 'IKT') {
                                ?>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="text-left">KTP / SIM *<small>pdf max 2MB</small></label>
                                            <div class="input-group">
                                                <span class="input-group-btn">
                                                    <span class="btn btn-primary btn-file">
                                                        Browse&hellip; <input type="file" name="browse_ktp_sim" id="browse_ktp_sim" required>
                                                    </span>
                                                </span>
                                                <input type="text" class="form-control" readonly="readonly">
                                            </div>
                                            <?php echo form_error('browse_ktp_sim', '<div class="error">', '</div><br/>'); ?>
                                            <div class="error"></div>
                                        </div>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </div>

            </div>
        </div>

        <div class="container">

            <h2>Request Return Cargo</h2>

            <hr />

            <table class="table table-striped table-condensed" id="t_return_cargo">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>VIN</th>
                        <th>MAKER</th>
                        <th>MODEL</th>
                        <th>DAMAGE</th>
                        <th>DOCUMENT BC</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>


        </div><!-- /.container -->
    </div>

    <?php $this->load->view('backend/elements/footer') ?>

    <script type="text/javascript">
        var table;

        $(document).ready(function() {
            //datatables
            var table = $('#t_return_cargo').DataTable({
                "processing": true,
                "serverSide": true,
                "deferRender": true,
                "dom": 'Bfrtip',
                "buttons": [
                    'colvis',
                    'pageLength'
                ],
                "order": [],
                "ajax": {
                    "url": '<?php echo site_url('eticket/return_cargo/get_items'); ?>',
                    "type": "POST"
                },
                "columnDefs": [{
                        "targets": [0, 6],
                        "orderable": false,
                    },
                    {
                        "targets": [1, 2, 3, 5],
                        "visible": true,
                        "searchable": true
                    },
                ],
                //Set column definition initialisation properties.

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

        });

        function requestInputData(vin) {
            var form = $("#printFormModal");
            form.trigger("reset");
            form.find('.modal-title').text('Input data print : ' + vin);
            form.find('#vin_request').val(vin);
            $('#truckCode').select2({
                ajax: {
                    url: '<?php echo site_url('eticket/return_cargo/getTruckInfo'); ?>',
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

            $('#truckCode').on('select2:select', function(e) {
                $.ajax({
                    type: "POST",
                    url: '<?php echo site_url('eticket/return_cargo/getCarrierByTruck'); ?>',
                    data: {
                        truckCode: e.params.data.id
                    },
                    success: function(msg) {
                        var obj = jQuery.parseJSON(msg);
                        $('#carrierID').val(obj[0].NAME);
                    }
                });
            })
            $('#printModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        }

        $(document)
            .on('change', '.btn-file :file', function() {
                var input = $(this),
                    numFiles = input.get(0).files ? input.get(0).files.length : 1,
                    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                input.trigger('fileselect', [numFiles, label]);
            });

        $("#printFormModal").submit(function(e) {
            e.preventDefault();
            var form = $(this);

            swal({
                title: 'Warning ' + form.find('#vin_request').val() + '!',
                text: "Are you sure to request Returning Cargo?",
                type: 'warning',
                buttons: true,
                buttons: ["Cancel", "Sure!"],
                closeModal: false
            }).then((result) => {
                if (result) {
                    $.ajax({
                        type: "post",
                        enctype: 'multipart/form-data',
                        url: "<?php echo base_url(); ?>eticket/return_cargo/submit_item",
                        data: new FormData(this),
                        processData: false,
                        contentType: false,
                        cache: false,
                        success: function(response) {
                            var obj = jQuery.parseJSON(response);
                            console.log(obj);
                            if (obj.response.StatusCode == 200) {
                                swal({
                                    title: 'Success!',
                                    text: 'The request is submitted successfully with request number ' + obj.response.RCNumberReq,
                                    type: 'success',
                                });
                                $('#t_return_cargo').DataTable().ajax.reload();
                                $('#printModal').modal('hide');
                            } else {
                                const wrapper = document.createElement('div');
                                wrapper.innerHTML = obj.response.RCStatus;
                                swal({
                                    title: 'Failed!',
                                    content: wrapper,
                                    type: 'success',
                                });
                                $('#t_return_cargo').DataTable().ajax.reload();
                            }

                        },
                        error: function(response) {
                            swal({
                                title: 'Failed!',
                                text: obj.response.RCStatus,
                                type: 'error',
                            });
                            $('#t_return_cargo').DataTable().ajax.reload();
                        }
                    });

                }
            })

        });


        function rejectReturn(rc_no_req, vins) {
            swal({
                title: 'Warning ' + rc_no_req + '!',
                text: "Are you sure to cancel this request?",
                type: 'warning',
                buttons: true,
                dangerMode: true,
                buttons: ["Cancel", "Cancel!"],
                closeModal: false
            }).then((result) => {
                if (result) {
                    $.ajax({
                        type: "post",
                        url: "<?php echo base_url(); ?>eticket/return_cargo/reject_request",
                        data: {
                            rc_no_req: rc_no_req,
                            vins: vins
                        },
                        success: function(response) {
                            if (response) {
                                swal({
                                    title: 'Success!',
                                    text: 'The request ' + rc_no_req + ' has canceled!',
                                    type: 'success',
                                });
                                $('#t_return_cargo').DataTable().ajax.reload();
                            } else {
                                swal({
                                    title: 'Failed!',
                                    text: 'The request ' + rc_no_req + ' failed to cancel!',
                                    type: 'error',
                                });
                                $('#t_return_cargo').DataTable().ajax.reload();
                            }
                        }
                    });

                }
            })
        }

        function requestPrint(rc_no_req) {
            if (rc_no_req) {
                var win = window.open("<?php echo base_url(); ?>eticket/return_cargo/print_rc/" + rc_no_req, '_blank');
                if (win) {
                    win.focus();
                    // $('#printModal').modal('hide');
                } else {
                    alert('Please allow popups for this website');
                }
            } else {
                alert('Print Failed');
            }
        }
    </script>

</body>

</html>