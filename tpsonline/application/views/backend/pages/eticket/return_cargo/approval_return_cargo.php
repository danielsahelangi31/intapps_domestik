<!DOCTYPE html>
<html lang="id">
<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
    <style>
        .select2 {
            width:100%!important;
        }
    </style>
</head>

<body>
<div id="wrap">
    <?php $this->load->view('backend/components/header') ?>

    <!-- Modal -->
    <div id="docStatModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form id="docStatFormModal" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Input data print out</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="no_rc_doc" id="no_rc_doc"  value="">
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="text-left">Document *<small>pdf max 2MB</small></label>
                                    <div class="input-group">
									<span class="input-group-btn">
										<span class="btn btn-primary btn-file">
											Browse&hellip; <input type="file" name="docs_file" id="docs_file" required>
										</span>
									</span>
                                        <input type="text" class="form-control" readonly="readonly">
                                    </div>
                                    <?php echo form_error('docs_file', '<div class="error">', '</div><br/>'); ?>
                                    <div class="error"></div>
                                </div>
                            </div>
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

    <!-- Modal -->
    <div id="fileStatModal" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <form id="fileStatFormModal" method="post">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">Input data print out</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <input type="hidden" name="no_rc" id="no_rc"  value="">
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

        <h2>Approval Return Cargo</h2>

        <hr/>

        <div class="table-responsive">
            <table class="table table-striped table-condensed" id="t_return_cargo_approval">
                <thead>
                <tr>
                    <th>No</th>
                    <th>REQUEST NUMBER</th>
                    <th>SUBMISSION DATE</th>
                    <th>VIN</th>
                    <th>MAKER</th>
                    <th>MODEL</th>
                    <th>TRUCK</th>
                    <th>DRIVER</th>
                    <th>KTP/SIM</th>
                    <th>DAMAGE STATUS</th>
                    <th>DOCUMENT</th>
                    <th>ACTION</th>
                </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>


    </div><!-- /.container -->
</div>

<?php $this->load->view('backend/elements/footer') ?>

<script type="text/javascript">
    var table;
    $(document)
        .on('change', '.btn-file :file', function() {
            var input = $(this),
                numFiles = input.get(0).files ? input.get(0).files.length : 1,
                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
            input.trigger('fileselect', [numFiles, label]);
        });

    $(document).ready(function () {

        $('.btn-file :file').on('fileselect', function(event, numFiles, label) {
            var input = $(this).parents('.input-group').find(':text'),
                log = numFiles > 1 ? numFiles + ' files selected' : label;

            if( input.length ) {
                input.val(log);
            } else {
                if( log ) alert(log);
            }

        });

        //datatables
        var table = $('#t_return_cargo_approval').DataTable({
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
                "url": '<?php echo site_url('eticket/return_cargo/get_approval_items'); ?>',
                "type": "POST"
            },
            "columnDefs": [
                {
                    "targets": [0],
                    "orderable": false,
                },
                {
                    "targets": [1,3],
                    "visible": true,
                    "searchable": true
                },
                {
                    "targets": [4,5],
                    "orderable": false,
                    "visible": false,
                },
                {
                    "targets": [2,7,8,10],
                    "orderable": true,
                    "visible": false,
                },
            ],
            //Set column definition initialisation properties.

        })



    });

    function approveReturn(rc_no_req,vins){
        swal({
            title: 'Warning '+rc_no_req+'!',
            text: "Are you sure to approve this request?",
            type: 'warning',
            buttons: true,
            buttons: ["Cancel", "Sure!"],
            closeModal: false
        }).then((result) => {
            if(result){
                $.ajax(
                    {
                        type:"post",
                        url: "<?php echo base_url(); ?>eticket/return_cargo/approve_request",
                        data:{
                            rc_no_req:rc_no_req,
                            vins : vins
                        },
                        success:function(response)
                        {
                            if(response){
                                swal({
                                    title: 'Success!',
                                    text: 'The request '+rc_no_req+' has approved!',
                                    type: 'success',
                                });
                                $('#t_return_cargo_approval').DataTable().ajax.reload();
                            }else{
                                swal({
                                    title: 'Failed!',
                                    text: 'The request '+rc_no_req+' failed to approve!',
                                    type: 'error',
                                });
                                $('#t_return_cargo_approval').DataTable().ajax.reload();
                            }
                        }
                    }
                );

            }
        })
    }

    function rejectReturn(rc_no_req,vins){
        swal({
            title: 'Warning '+rc_no_req+'!',
            text: "Are you sure to reject this request?",
            type: 'warning',
            buttons: true,
            dangerMode: true,
            buttons: ["Cancel", "Reject!"],
            closeModal: false
        }).then((result) => {
            if(result){
                $.ajax(
                    {
                        type:"post",
                        url: "<?php echo base_url(); ?>eticket/return_cargo/reject_request",
                        data:{
                            rc_no_req:rc_no_req,
                            vins:vins
                        },
                        success:function(response)
                        {
                            if(response){
                                swal({
                                    title: 'Success!',
                                    text: 'The request '+rc_no_req+' has rejected!',
                                    type: 'success',
                                });
                                $('#t_return_cargo_approval').DataTable().ajax.reload();
                            }else{
                                swal({
                                    title: 'Failed!',
                                    text: 'The request '+rc_no_req+' failed to rejected!',
                                    type: 'error',
                                });
                                $('#t_return_cargo_approval').DataTable().ajax.reload();
                            }
                        }
                    }
                );

            }
        })
    }

    function requestPrint(rc_no_req) {
        if(rc_no_req){
            var win = window.open("<?php echo base_url(); ?>eticket/return_cargo/print_rc/"+rc_no_req, '_blank');
            if (win) {
                win.focus();
                $('#printModal').modal('hide');
            } else {
                alert('Please allow popups for this website');
            }
        }else{
            alert('Print Failed');
        }
    }

    $("#fileStatFormModal").submit(function(e) {
        e.preventDefault();
        var form = $(this);

        swal({
            title: 'Warning '+form.find('#no_rc').val()+'!',
            text: "Are you sure to upload file?",
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
                        url: "<?php echo base_url(); ?>eticket/return_cargo/upload_file_stats",
                        data:new FormData(this),
                        processData:false,
                        contentType:false,
                        cache:false,
                        success:function(response)
                        {
                            var obj = jQuery.parseJSON( response );
                            $('#t_return_cargo_approval').DataTable().ajax.reload();

                            $('#fileStatModal').modal('hide');
                            if(obj.statusCode){
                                swal({
                                    title: 'Success!',
                                    text: 'File uploaded!',
                                    type: 'success',
                                });
                            }else{
                                swal({
                                    title: 'Failed!',
                                    text: obj.message,
                                    type: 'success',
                                });

                            }

                        },
                        error:function(response){
                            swal({
                                title: 'Failed!',
                                text: response,
                                type: 'error',
                            });
                            $('#t_return_cargo_approval').DataTable().ajax.reload();
                        }
                    }
                );
            }
        })
    });

    function file_stats(no_rc) {
        var form = $("#fileStatFormModal");
        form.trigger("reset");
        form.find('.modal-title').text('Upload KTP / SIM : '+no_rc);
        form.find('#no_rc').val(no_rc);
        $('#fileStatModal').modal({backdrop: 'static', keyboard: false});
    }

    function doc_stats(no_rc) {
        var form = $("#docStatFormModal");
        form.trigger("reset");
        form.find('.modal-title').text('Document of '+no_rc);
        form.find('#no_rc_doc').val(no_rc);
        $('#docStatModal').modal({backdrop: 'static', keyboard: false});
    }

    $("#docStatFormModal").submit(function(e) {
        e.preventDefault();
        var form = $(this);
        swal({
            title: 'Warning '+form.find('#no_rc_doc').val()+'!',
            text: "Are you sure to upload file?",
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
                        url: "<?php echo base_url(); ?>eticket/return_cargo/upload_doc_stats",
                        data:new FormData(this),
                        processData:false,
                        contentType:false,
                        cache:false,
                        success:function(response)
                        {
                            var obj = jQuery.parseJSON( response );
                            $('#t_return_cargo_approval').DataTable().ajax.reload();

                            $('#docStatModal').modal('hide');
                            if(obj.statusCode){
                                swal({
                                    title: 'Success!',
                                    text: 'File uploaded!',
                                    type: 'success',
                                });
                            }else{
                                swal({
                                    title: 'Failed!',
                                    text: obj.message,
                                    type: 'success',
                                });

                            }

                        },
                        error:function(response){
                            swal({
                                title: 'Failed!',
                                text: response,
                                type: 'error',
                            });
                            $('#t_return_cargo_approval').DataTable().ajax.reload();
                        }
                    }
                );
            }
        })
    });


</script>

</body>
</html>