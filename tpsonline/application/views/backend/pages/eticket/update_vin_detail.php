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

    <div class="container">

        <h2>Update VIN</h2>
        <?php
        if($this->session->flashdata('responses')) {
            if(isset($this->session->flashdata('responses')->Asosiated)){
                foreach ($this->session->flashdata('responses')->Asosiated as $successs){
                    ?>
                        <div class="alert <?php echo $successs->status->StatusCode == 200 ? 'alert-success' : 'alert-danger'?> fade in">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                            <h4><?php echo 'Status '.$successs->Typed. ' : '.$successs->Value.' '.$successs->status->StatusDescription ?></h4>
                        </div>
                    <?php
                }
            }
            if(isset($this->session->flashdata('responses')->Remove)){
                foreach ($this->session->flashdata('responses')->Remove as $failed){
                    ?>
                    <div class="alert <?php echo $failed->Code == 200 ? 'alert-success' : 'alert-danger'?> fade in">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                        <h4><?php echo 'Status '.$failed->Typed.' : '.$failed->Value.' '.$failed->Status ?></h4>
                    </div>
                    <?php
                }
            }
        }
        ?>
        <div class="modal fade" id="import_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Add BL Import</h3>
                    </div>
                    <div class="modal-body">
                        <form id="form_import">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    
                                <label for="recipient-name" class="col-form-label">Choose BL:</label>
                                <select class="form-control js-example-basic-single" id="import_bl_val" name="NUM">
                                    <option value="">-- Select --</option>
                                </select>
                                <label for="recipient-name" class="col-form-label">No Dokumen:</label>
                                <input type="text" class="form-control dok_import"  name="NO_DOK" placeholder="No Dokumen" />
                                <label for="recipient-name" class="col-form-label">Tanggal Dokumen:</label>
                                <input class="form-control dateBl dok_import"  type="date" name="TGL_DOK" placeholder="dd-mm-yyyy" />
                                <label for="recipient-name" class="col-form-label">NPWP Impor:</label>
                                <input type="text" class="form-control dok_import"  name="NPWP" placeholder="NPWP" />
                                <label for="recipient-name" class="col-form-label">Kode Dokumen:</label>
                                <select class="form-control dok_import"  name="KD_DOK">
                                    <option value="">-- Select --</option>
                                    <?php foreach ($dokumen_import as $dok) { ?>
                                        <option value="<?php echo $dok->ID; ?>"><?php echo $dok->ID . '-' . $dok->DOC_TYPE; ?> </option>
                                    <?php } ?>
                                </select>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <a id="addImport" class="btn btn-primary">Add</a>
                    </div>
                </div>
            </div>
        </div>
        <hr />
        <div class="modal fade" id="export_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3 class="modal-title" id="exampleModalLabel">Add VIN Export</h3>
                    </div>
                    <div class="modal-body">
                        <form id="form_export">
                            <div class="form-group">
                                <div class="col-lg-12">
                                    
                                <label for="recipient-name" class="col-form-label">Choose VIN:</label>
                                <select class="form-control js-example-basic-single" id="export_vin_val" name="NUM">
                                    <option value="">-- Select --</option>
                                </select>
                                <label for="recipient-name" class="col-form-label">No Dokumen:</label>
                                <input type="text" class="form-control" name="NO_DOK" placeholder="No Dokumen" />
                                <label for="recipient-name" class="col-form-label">Tanggal Dokumen:</label>
                                <input type="date" class="form-control dateVin" id="tglNpe" name="TGL_DOK" placeholder="dd-mm-yyyy" />
                                <label for="recipient-name" class="col-form-label">NPWP Expor:</label>
                                <input type="text" class="form-control" value="<?php print_r($npwp[0]->NPWP) ?>" name="NPWP" placeholder="NPWP" />
                                <label for="recipient-name" class="col-form-label">Kode Dokumen:</label>
                                <select class="form-control" name="KD_DOK">
                                    <option value="">-- Select --</option>
                                    <?php foreach ($dokumen_export as $dok) { ?>
                                        <option value="<?php echo $dok->ID; ?>"><?php echo $dok->ID . '-' . $dok->DOC_TYPE; ?> </option>
                                    <?php } ?>
                                </select>

                                </div>


                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        <a id="addExport" class="btn btn-primary">Add</a>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <form role="form" class="form-horizontal" id="form-vin" action="" method="post">
                <div class="col-lg-12">
                    <div class="form-group">
                        <label class="text-left">Truck VISIT ID</label>
                        <input type="hidden" name="visit_id" value="<?php echo $visitID; ?>">
                        <input readonly type="text" class="form-control"
                               name="visit_readonly" placeholder=""
                               value="<?php echo $visitID; ?>" />
                    </div>
                </div>
                <div class="col-lg-12">

                    <div class="col-lg-6">
                        <div class="form-group">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#export_modal">Add Export VIN</button>
                            <table class="table table-striped table-condensed" id="export_table">
                                <thead>
                                <tr>
                                    <th>Status</th>
                                    <th>VIN</th>
                                    <?php if ($exports) { ?>
                                        <th>Jenis Dokumen</th>
                                    <?php } ?>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if($exports){
                                    foreach ($exports as $index=> $item) {
                                        ?>
                                        <tr>
                                            <td><input type="checkbox" value="<? echo $item->VIN; ?>" checked name="export_checkbox[]" /></td>
                                            <td><? echo $item->VIN; ?></td>
                                            <td><? if ($item->MAKE === 'HONDA' || $item->MAKE == 'TOYOC' || $item->MAKE == 'TOYOTA' || $item->MAKE == 'TOYOT' || $item->MAKE == 'TOYOH' || $item->MAKE == 'TOYOSP' || $item->MAKE == 'OTHER' || $item->MAKE == 'HONDC' || $item->MAKE == 'WULING') {
                                                echo 'NPE';
                                            } else {
                                                echo 'Auto NPE';
                                            } ?></td>
                                        </tr>
                                        <?php }
                                }else{
                                    ?>
                                    <tr id="export-no-data">
                                        <td><?  ?></td>
                                        <td><? echo 'Tidak ada VIN'; ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group" align="right">
                            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#import_modal" data-whatever="@fat">Add BL Import</button>
                            <table class="table table-striped table-condensed" id="import_table">
                                <thead>
                                <tr>
                                    <th class="text-right">BL Number</th>
                                    <?php if($imports) {?>
                                        <th class="text-right">Jenis Dokumen</th>
                                    <?php }?>
                                    <th class="text-right">Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                if($imports){
                                    foreach ($imports as $index=>$item){
                                        ?>
                                        <tr class="text-right">
                                            <td><? echo $item->BL_NUMBER; ?></td>
                                            <td><?php echo $item->DOC_TYPE?></td>
                                            <td><input type="checkbox" value="<? echo $item->BL_NUMBER; ?>" checked name="import_checkbox[]" /></td>
                                        </tr>
                                        <?php
                                    }
                                }else{
                                    ?>
                                    <tr id="import-no-data">
                                        <td><?  ?></td>
                                        <td><? echo 'Tidak ada BL'; ?></td>
                                    </tr>
                                    <?php
                                }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                        <div class="form-group">
                            <button type="submit" id="submit" class="btn btn-primary btn-block">Save</button>
                        </div>

                </div>

            </form>
        </div>


    </div><!-- /.container -->
</div>
<?php $this->load->view('backend/elements/footer') ?>

<script type="text/javascript">

    $(document).ready(function() {
        // $('.dateVin').datepicker({
        //     format: 'dd-mm-yyyy'
        // });
        var today = new Date();
        today.setDate(today.getDate() - 30);
        var min = today.toISOString().slice(0, 10);
        var max = new Date().toISOString().slice(0, 10);
        document.getElementById("tglNpe").min = min;
        document.getElementById("tglNpe").max = max;
        $('#export_vin_val').select2(
            {
                ajax: {
                    url: '<?php echo site_url('eticket/update_vin/getListVIN/EXPORT'); ?>',
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params.term, // search term
                            page_limit: 10,
                            page:params.page || 1
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
                minimumInputLength : 3,
                dropdownParent: $('#export_modal')
            }
        );
        $('#import_bl_val').select2(
            {
                ajax: {
                    url: '<?php echo site_url('eticket/announce_truck/getListBL'); ?>',
                    type: "post",
                    dataType: 'json',
                    delay: 250,
                    data: function (params) {
                        return {
                            searchTerm: params.term, // search term
                            page_limit: 10,
                            page:params.page || 1
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
                minimumInputLength : 3,
                dropdownParent: $('#import_modal')
            }
        );
        $('#import_bl_val').on('select2:select', function(e) {
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
                    if (obj[0].REMAINING_CARGO == 0) {
                        alert("Sisa Cargo 0, Proses tidak bisa dilanjutkan");
                        $(".dok_import").attr("disabled", "disabled"); 
                        $("#addImport").hide();
                    } else {
                        // alert("Sisa Cargo "+obj[0].REMAINING_CARGO+".");
                        $(".dok_import").removeAttr("disabled"); 
                        $("#addImport").show();
                    }
                }
            });
        });
    });

    $(document).ready(function () {

        $('#addImport').click(function () {
            $.ajax({
                url: "<?php echo site_url('eticket/update_vin/cek_dokumen/IMPORT'); ?>",
                type: 'post',
                data: $("#form_import").serialize(),
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    // console.log(data);
                    if(data == 'OK')
                    {
                        $('#import_table tr:last').after('<tr class="text-right">' +
                        '<td>'+ $('#import_bl_val').val() +'</td>' +'<td><input type="checkbox" value="'+ $('#import_bl_val').val() +'" checked name="import_checkbox[]"></td>'+
                        '</tr>');

                    // $('#import_vin_val').val($("#import_vin_val option:first").val());

                        $('#import_bl_val').val($('#mylist option:first-child').val()).trigger('change');


                        $('#import-no-data').remove();

                        $('#import_modal').modal('hide');
                    }else{
                        alert(data);
                    }
                },
                error: function(data){
                    alert("Server Error");
                }
            });

        });


        $('#addExport').click(function () {
            $.ajax({
                url: "<?php echo site_url('eticket/update_vin/cek_dokumen/EXPORT'); ?>",
                type: 'post',
                data: $("#form_export").serialize(),
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                success: function(data) {
                    // console.log(data);
                    if(data == 'OK')
                    {
                        $('#export_table tr:last').after('<tr class="text-left">' +
                            '<td><input type="checkbox" value="'+ $('#export_vin_val').val() +'" checked name="export_checkbox[]"></td>'+'<td>'+ $('#export_vin_val').val() +'</td>'+
                            '</tr>');

                        // $('#export_vin_val').val($("#export_vin_val option:first").val());
                        $('#export_vin_val').val($('#mylist option:first-child').val()).trigger('change');

                        $('#export-no-data').remove();

                        $('#export_modal').modal('hide');
                    }else{
                        alert(data);
                    }
                },
                error: function(data){
                    alert("Server Error");
                }
            });
            

        });

    });

</script>

</body>
</html>