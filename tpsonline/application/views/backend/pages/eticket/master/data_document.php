<!DOCTYPE html>
<html lang="id">
<head>
    <?php $this->load->view('backend/elements/basic_head') ?>
    <style>
        .select2 {
            width: 100% !important;
        }
        .datepicker{
            z-index:1151 !important;
        }
        .swal-title {
            margin: 0px;
            font-size: 18px;
            box-shadow: 0px 1px 1px rgba(0, 0, 0, 0.21);
            margin-bottom: 28px;
        }
    </style>
</head>

<body>
    <div id="wrap">
        <?php $this->load->view('backend/components/header') ?>

        <!-- Modal -->
        <div class="modal fade" id="printModal" tabindex="-1" role="dialog" aria-labelledby="printModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title"></h4>
                    </div>
                    <div class="modal-body">
                        <form id="formMasterDocument" method="post" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">No Dokumen <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" id="noDoc" name="noDoc" autocomplete="off" placeholder="Please insert" />
                                        <?php echo form_error('noDoc', '<div class="error">', '</div><br/>'); ?>
                                        <div class="error"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">Tanggal Dokumen <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" id="tglDoc" name="tglDoc" autocomplete="off" placeholder="Please select" />
                                        <?php echo form_error('tglDoc', '<div class="error">', '</div><br/>'); ?>
                                        <div class="error"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">Kode Dokumen <span style="color:red;">*</span></label>
                                        <input type="hidden" class="form-control" id="kdDocV" name="kdDocV" />
                                        <select class="form-control" id="kdDoc" name="kdDoc">
                                            <option value="">-- Select Kode Dokumen --</option>
                                            <?php foreach ($dokumen_import as $dok) { ?>
                                                <option value="<?= $dok->ID; ?>">
                                                    <?= $dok->ID . ' - ' . $dok->DOC_TYPE; ?>
                                                </option>
                                            <?php } ?>
                                        </select>
                                        <?php echo form_error('kdDoc', '<div class="error">', '</div><br/>'); ?>
                                        <div class="error"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">NPWP <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" id="NPWP" name="NPWP" autocomplete="off" placeholder="Please insert" />
                                        <?php echo form_error('NPWP', '<div class="error">', '</div><br/>'); ?>
                                        <div class="error"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">Jenis Kemasan <span style="color:red;">*</span></label>
                                        <input type="text" class="form-control" id="jenisKemasan" value="Non Peti Kemas" name="jenisKemasan" autocomplete="off" disabled />
                                        <?php echo form_error('jenisKemasan', '<div class="error">', '</div><br/>'); ?>
                                        <div class="error"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">Merk Kemasan</label>
                                        <input type="text" class="form-control" id="merkKemasan" name="merkKemasan" autocomplete="off" placeholder="Please insert" />
                                        <?php echo form_error('merkKemasan', '<div class="error">', '</div><br/>'); ?>
                                        <div class="error"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">Total Cargo <span style="color:red;">*</span> </label>
                                        <input type="text" class="form-control" id="totalCargo" name="totalCargo" autocomplete="off" placeholder="Please insert" />
                                        <?php echo form_error('totalCargo', '<div class="error">', '</div><br/>'); ?>
                                        <div class="error"></div>
                                    </div>
                                </div>
                                <?php if($this->userauth->getLoginData()->sender == 'IKT') { ?>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">Dokumen Pendukung <span style="color:red;">* <small>(Support document: pdf, jpg, png.)</small></span> </label>
                                        </br>
                                        <!-- <button class="btn btn-primary btn-file"> -->
                                            <!-- Upload File&hellip;<input type="text" class="form-control" id="docFile" name="docFile" /> -->
                                            <input type="file" class="form-control" id="docFile" name="docFile" />
                                        <!-- </button> -->
                                        <?php echo form_error('docFile', '<div class="error">', '</div><br/>'); ?>
                                        <div class="error"></div>
                                    </div>
                                </div>
                                <?php } ?>
                                
                                <input type="hidden" class="form-control" id="noDocU" name="noDocU" />
                                <input type="hidden" class="form-control" id="tglDocU" name="tglDocU" />
                                <input type="hidden" class="form-control" id="kdDocU" name="kdDocU" />
                                <input type="hidden" class="form-control" id="NPWPU" name="NPWPU" />
                                <input type="hidden" class="form-control" id="flag" name="flag" />
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                <button id="submit_item" type="submit" class="btn btn-primary">Submit</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal End -->

        <div class="container">
            <h2>Master Document</h2>
            
            <?php if($this->userauth->getLoginData()->sender != 'IKT') { ?>
                <!-- Maker View -->
                <hr />
                <a onclick="requestInputData()" type="button" class="btn btn-success"><b>+ Create</b></a>
            <?php } else { ?>
                <!-- Admin View -->
                <!-- <hr />
                <a href="<?= site_url('assets/csv/template_master_document.xlsx') ?>" target="_blank" type="button" class="btn btn-success"><b>Download Template</b></a>
                <a class="btn btn-primary" data-toggle="collapse" href="#editBulk" role="button" aria-expanded="false" aria-controls="collapseExample">
                    <b>Upload Document</b>
                </a>
                <div class="collapse" id="editBulk">
                    <form id="uploadDocument" method="post" action="<?php echo site_url('eticket/master_document/upload_doc');?>" enctype="multipart/form-data">
                        </br>
                        <div class="form-group">
                            <label class="text-left">Edit Bulk</label>
                            <div class="input-group">
                                <span class="input-group-btn">
                                    <span class="btn btn-warning btn-file">
                                        Upload Excel File&hellip; <input type="file" name="uploadBulk" id="uploadBulk">
                                    </span>
                                </span>
                                <input type="text" class="form-control" id="excel-upload">
                            </div>
                            <?php echo form_error('uploadBulk', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div>
                        </div>
                        <span class="input-group-btn">
                            <div class="pull-left">
                                <button id="uploadBtn" type="submit" class="btn btn-primary btn-block submitBtn">&nbsp;&nbsp;<b>Submit</b>&nbsp;&nbsp;</button>
                            </div>
                        </span>
                        </br>
                    </form>
                </div> -->
            <?php } ?>
            <hr />
            <?php //if($this->userauth->getLoginData()->sender == 'IKT') { ?>
            <table class="table table-striped table-condensed" id="m_document">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>No Dokumen</th>
                        <th>Tanggal</th>
                        <th>NPWP</th>
                        <th>Kode</th>
                        <th>Merk Kemasan</th>
                        <th>Total Cargo</th>
                        <?= $this->userauth->getLoginData()->sender == 'IKT' ? '<th>Action</th>' : '' ?>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <?php //} ?>
        </div><!-- /.container -->
        
    </div>

    <?php $this->load->view('backend/elements/footer') ?>

    <!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.4/js/bootstrap-datepicker.js" ></script> -->
    <script src="<?=base_url('assets/js/bootstrap-datepicker.js');?>" ></script>
    <script type="text/javascript">
        $("#tglDoc").datepicker(
            {
                format: 'yyyy-mm-dd',
                autoclose: true
            }
        );
        
        var table;
        $(document).ready(function() {
            $('select').select2({
                dropdownParent: $("#printModal"),
            });

            //datatables
            var table = $('#m_document').DataTable({
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
                    "url": '<?= site_url('eticket/master_document/list_document/'.$this->userauth->getLoginData()->sender) ?>',
                    "type": "POST"
                },
                "columnDefs": [{
                        "targets": [0, <?= $this->userauth->getLoginData()->sender == 'IKT' ? 7 : 6 ?>],
                        "orderable": false,
                    },
                    {
                        "targets": [1, 2, 3, 4, 5, 6],
                        "visible": true,
                        "searchable": true
                    },
                ],
                //Set column definition initialisation properties.
            });
        });

        function requestInputData() {
            var form = $("#formMasterDocument");
            $('.modal-title').text("Create Document");

            // remove disable modal form insert
            $("#noDoc").removeAttr("disabled");
            $("#tglDoc").removeAttr("disabled");
            $("#kdDoc").removeAttr("disabled");
            $("#NPWP").removeAttr("disabled");

            // select2 view
            $('#kdDoc').next(".select2-container").show();
            $("#kdDocV").attr("type",'hidden');

            $('#printModal').modal({
                // backdrop: 'static',
                keyboard: false
            });
        }

        function requestUpdateData(doc) {
            var list = doc.split('B4tA5');
            var form = $("#formMasterDocument");
            
            form.trigger("reset");
            $('.modal-title').text("Update Document: "+ list[0]);

            // fill input value
            form.find('#noDoc').val(list[0]);
            form.find('#tglDoc').val(list[1]);
            form.find('#NPWP').val(list[2]);
            form.find('#kdDocV').val(list[4]);
            form.find('#merkKemasan').val(list[5]);
            form.find('#totalCargo').val(list[6]);

            //select2 view
            $("#kdDocV").attr("type",'text');
            $('#kdDoc').next(".select2-container").hide();

            // hidden form input
            form.find('#noDocU').val(list[0]);
            form.find('#tglDocU').val(list[1]);
            form.find('#NPWPU').val(list[2]);
            form.find('#kdDocU').val(list[3]);
            form.find('#flag').val('update');
            // form.find('#docFile').val('ini file uploadan');


            // tidak boleh di-edit
            $("#noDoc").attr("disabled", true);
            $("#tglDoc").attr("disabled", true);
            $("#kdDocV").attr("disabled",true);
            $("#NPWP").attr("disabled", true);
            
            $('#printModal').modal({
                // backdrop: 'static',
                keyboard: false
            });
        }

        // $(document).on('change', '.btn-file :file', function() {
        //     var input = $(this),
        //         numFiles = input.get(0).files ? input.get(0).files.length : 1,
        //         label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        //     input.trigger('fileselect', [numFiles, label]);
        // });
        
        // $("#uploadDocument").submit(function(e) {
        //     e.preventDefault();
        //     var form = $(this);
        //     swal({
        //         title: 'Konfirmasi Submit:',
        //         text: "Apakah anda yakin upload dokumen ini?",
        //         type: 'warning',
        //         buttons: true,
        //         buttons: ["Cancel", "Save"],
        //         closeModal: false
        //     }).then((result) => {
        //         if (result) {
        //             $('#uploadBtn').attr('disabled',true);
        //             $.ajax({
        //                 type: "post",
        //                 enctype: 'multipart/form-data',
        //                 url: "<?= base_url(); ?>eticket/master_document/upload_doc",
        //                 data: new FormData(this),
        //                 processData: false,
        //                 contentType: false,
        //                 cache: false,
        //                 success: function(response) {
        //                     // console.log(`response:::`, response);
        //                     var obj = jQuery.parseJSON(response);
        //                     // console.log(obj);
        //                     if (obj.response.Code == 200) {
        //                         swal({
        //                             title: 'Success!',
        //                             text: 'Document updated successfully',
        //                             type: 'success',
        //                         });
        //                         $('#m_document').DataTable().ajax.reload();
        //                         // $('#printModal').modal('hide');
        //                         $('#uploadBtn').removeAttr('disabled');
        //                         form.trigger("reset");
        //                     } else {
        //                         swal({
        //                             title: 'Failed!',
        //                             text: obj.response.Msg,
        //                             type: 'error',
        //                         });
        //                         $('#m_document').DataTable().ajax.reload();
        //                         // $('#printModal').modal('hide');
        //                         $('#uploadBtn').removeAttr('disabled');
        //                     }
        //                 },
        //                 error: function(response) {
        //                     swal({
        //                         title: 'Failed!',
        //                         text: obj.response.Msg,
        //                         type: 'error',
        //                     });
        //                     $('#m_document').DataTable().ajax.reload();
        //                     // $('#printModal').modal('hide');
        //                     $('#uploadBtn').removeAttr('disabled');
        //                 }
        //             });
        //         }
        //     })
        // });

        $("#formMasterDocument").submit(function(e) {
            e.preventDefault();
            var form = $(this);
            // console.log('xxx :>> ', form.find('#totalCargo').val());
            swal({
                title: 'Konfirmasi Submit:',
                text: "Apakah anda yakin submit dokumen " + form.find('#noDoc').val() + '?',
                type: 'warning',
                buttons: true,
                buttons: ["Cancel", "Save"],
                closeModal: false
            }).then((result) => {
                if (result) {
                    $('#submit_item').attr('disabled',true);
                    $.ajax({
                        type: "post",
                        enctype: 'multipart/form-data',
                        url: "<?= base_url(); ?>eticket/master_document/submit",
                        data: new FormData(this),
                        processData: false,
                        contentType: false,
                        cache: false,
                        async:false,
                        success: function(response) {
                            // console.log(`response:::`, response);
                            var obj = jQuery.parseJSON(response);
                            // console.log(obj);
                            if (obj.response.Code == 200) {
                                swal({
                                    title: 'Success!',
                                    text: form.find('#flag').val() == 'update' ? 'Document updated successfully' : 'Document submitted successfully',
                                    type: 'success',
                                });
                                $('#m_document').DataTable().ajax.reload();
                                $('#printModal').modal('hide');
                                $('#submit_item').removeAttr('disabled');
                                form.trigger("reset");
                            } else {
                                swal({
                                    title: 'Failed!',
                                    text: obj.response.Msg,
                                    type: 'error',
                                });
                                // $('#m_document').DataTable().ajax.reload();
                                $('#printModal').modal('show');
                                $('#submit_item').removeAttr('disabled');
                            }
                        },
                        error: function(response) {
                            swal({
                                title: 'Failed!',
                                text: obj.response.Msg,
                                type: 'error',
                            });
                            $('#m_document').DataTable().ajax.reload();
                            $('#printModal').modal('hide');
                            $('#submit_item').removeAttr('disabled');
                        }
                    });
                }
            })
        });
    </script>

</body>

</html>
