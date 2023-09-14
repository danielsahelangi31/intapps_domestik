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
    <?php $this->load->view('domestik/backend/components/header_domestik') ?>

    <div class="container">

        <h2>Update VIN Domestik</h2>
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
                        <input type="hidden" name="visit_id">
                        <input readonly type="text" id="truckVisit" class="form-control"
                               name="visit_readonly" placeholder=""
                               value="<?= $truck; ?>" />
                    </div>
                </div>
                <div class="col-lg-12">

                    <div class="col-lg-6">
                        <div class="form-group">
                            <a class="btn btn-warning" href="<?php echo site_url('domestik/eticket_list_domestik'); ?>" style="color:#fff;">Kembali</a>
                            <?php 
                                if($res["tr"]->TRUCK_CODE != "SELFDRIVE") 
                            { ?>
                            <button type="button" class="btn btn-primary" id="btn-vin-modal" data-toggle="modal" data-target="#vin_modal">Add Export VIN</button>
                            <?php } ?>
                        </div>
                    </div>
                    <table class="table table-striped table-condensed" id="export_table">
                        <thead>
                            <tr>
                                <th>VIN</th>
                                <th>Kapal</th>
                                <th>Destination</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php for($i = 0; $i < count($res["vin"]); $i++) {?>
                            <tr>
                                <td><?= $res["vin"][$i]->VIN; ?></td>
                                <td><?php if($res["vessel"] == '') {echo '';} else {echo $res["vessel"];} ?></td>
                                <td><?= $res["port"][$i]->PORT_NAME; ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>

                </div>

            </form>
        </div>

    </div><!-- /.container -->
</div>
<div class="modal fade" id="vin_modal" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Announce Vin</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
    </div>
    <div class="modal-body">
        <form id="form-vin-modal">
            <input type="hidden" id="truckCode" value="<?= $res["tr"]->TRUCK_CODE; ?>"/>
            <div class="form-group">
                <label for="vinnumber">Vin Number *</label>
                <input type="text" class="form-control" id="vinnumber">
            </div>
            <div class="form-group">
                <label for="direction">Direction *</label>
                <input type="text" class="form-control" id="direction" placeholder="<?= $res["truck"]->DIRECTION; ?>" readonly>
            </div>
            <div class="form-group">
                <label for="directiontype">Direction Type *</label>
                <input type="text" class="form-control" id="directiontype" placeholder="DOMESTIC" readonly>
            </div>
            <div class="form-group">
                <label for="fuel">Fuel</label>
                <input type="text" class="form-control" id="fuel" value="">
            </div>
            <div class="form-group">
                <label for="model">Model *</label>
                <select class="form-control models-get" id="model">
                    <option value="">pilih</option>
                </select>
            </div>
            <div class="form-group">
                <label for="destination">Destination *</label>
                <select class="form-control" id="destination">
                    <option value="">pilih</option>
                </select>
            </div>
            <div class="form-group">
                <label for="shipping">Shipping Line *</label>
                <?php if($this->userauth->getLoginData()->intapps_type == "ADMIN") {?>
                <select class="form-control" id="shipping1">
                    <option value="">pilih</option>
                </select>
                <?php } else { ?>
                <input type="hidden" id="shipping2" readonly>
                <input type="text" id="shipping2a" class="form-control" readonly>
                <?php } ?>
            </div>
      </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal" aria-label="Close">
            Cancel
        </button>
        <button type="button" id="btn-form-modal" class="btn btn-primary">Submit</button>
      </div>
    </div>
  </div>
</div>
</div>
<?php $this->load->view('domestik/backend/elements/footer_domestik') ?>

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
        //$('.models-get').select2('destroy');
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

        $('#destination').select2(
            {
                ajax: {
                    url: '<?php echo site_url('domestik/announce_vin_domestik/getPort'); ?>',
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
                    url: '<?php echo site_url('domestik/announce_vin_domestik/getCategory'); ?>',
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
                                    text: obj.text,
                                    value: obj.text
                                };
                            })
                        };
                    },
                    cache: true
                },
                minimumInputLength : 3
            }
        );
        $('#shipping1').select2(
            {
                ajax: {
                    url: '<?php echo site_url('domestik/announce_vin_domestik/getSearchShippingLine'); ?>',
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

        $(document).on('keyup', '#vinnumber', function(){
            this.value = this.value.replace(/\s/g,'');
            this.value = this.value.replace(/[`~!@#$%^&*()|+\-=?;:'",.<>\{\}\[\]\\\/]/g,'');
        })

        // add Export Vin 
        $('#btn-vin-modal').click(function() {
            $.ajax({
                url: "<?php echo site_url('domestik/update_vin_domestik/dataShipping'); ?>", 
                type: "post",
                dataType: "json", 
                data: {

                }, 
                success: function(data) {
                        // console.log(data[0]);
                    
                        var oship = $("<option/>", {id: data[0]["ID"], text: data[0]["NAME"], value: data[0]["ID"]});
                        $("#shipping1").append(oship);
                        $("#shipping1").val(data[0]["ID"]).trigger('change');
                        $('#shipping2').val(data[0]["ID"]);
                        $('#shipping2a').val(data[0]["NAME"]);
                }
            }); 
        });

        $(document).on('keyup', "#vinnumber", function() {
            // console.log(index)
            
            $.ajax({
                url:"<?php echo site_url('domestik/announce_vin_domestik/vinModel'); ?>",
                type:"post",
                dataType:"json",
                cache:true,
                delay: 500,
                data:{
                    vin: $(this).val()
                },
                success: function(data) {
                    if(data.length != 0){
                        var o = $("<option/>", {id: data[0]["ID_CATEGORY"], text: data[0]["NAME"], value: data[0]["ID_CATEGORY"]});
                        
                        $("#model").append(o);
                        $("#model").val(data[0]["ID_CATEGORY"]).trigger('change');
                    }
                }
            })
        }); 
        $('#btn-form-modal').click(function(e) {
            e.preventDefault();
            let vinNum = $("#vinnumber").val();
            var direction = $("#direction").attr('placeholder');
            var idShippingLine = <?php if($this->userauth->getLoginData()->intapps_type == "ADMIN") { ?> $("#shipping1 option:selected").attr('value'); <?php } else { ?> $("#shipping2").attr('value'); <?php } ?>
            var destination = $("#destination").val();
            var isError = false;
            var listVal = [];
            
            if (vinNum === "") {
                listVal.push(`Vin Number harus diisi`);
                isError = true;
            }

            if (direction === "") {
                listVal.push(`Direction harus diisi`);
                isError = true;
            }

            if ($("#model option:selected").val() === "") {
                listVal.push(`Model harus diisi`);
                isError = true;
            }

            if (destination === "") {
                listVal.push(`Destination harus diisi`);
                isError = true;
            }
            
            <?php if($this->userauth->getLoginData()->intapps_type == "ADMIN") { ?>
            if ($("#shipping1 option:selected").val() === "") {
                listVal.push(`Shipping Line harus diisi`);
                isError = true;
            }
            <?php } ?>


            if(isError == false) {
                $.ajax({
                    url: "<?php echo site_url('domestik/update_vin_domestik/insertVinEticket'); ?>",
                    type: "POST",
                    dataType: "json",
                    data: {
                        dataVinNum: vinNum,
                        dataDirection: direction,
                        dataDirectionType: $("#directiontype").attr('placeholder'),
                        dataFuel: $("#fuel").val(),
                        dataPortCode:  $("#destination option:selected").attr('value'),
                        dataIdShippingLine: idShippingLine,
                        dataIdCategory: $("#model option:selected").attr('value'),
                        dataTruckVisit: $('#truckVisit').attr('value'),
                        dataTruckCode: $('#truckCode').val()
                    },
                    success: function(data) {
                        // untuk munculin pesan 
                        if(data["exis"] == 1) {
                            alert(data["pesan"]);
                        } else {
                            alert(data["message"]);
                            location.reload();
                        }
                    },
                    error: function(xhr, error) {
                        console.log(xhr);
                        console.log(error);
                    },
                    cache:false
                });
            } else {
                alert(listVal.map(item => { return item + "\n"; }).join(''));
            }
            
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


        // $('#addExport').click(function () {
        //     $.ajax({
        //         url: "<?php // echo site_url('eticket/update_vin/cek_dokumen/EXPORT'); ?>",
        //         type: 'post',
        //         data: $("#form_export").serialize(),
        //         headers: {
        //           'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        //         },
        //         success: function(data) {
        //             // console.log(data);
        //             if(data == 'OK')
        //             {
        //                 $('#export_table tr:last').after('<tr class="text-left">' +
        //                     '<td><input type="checkbox" value="'+ $('#export_vin_val').val() +'" checked name="export_checkbox[]"></td>'+'<td>'+ $('#export_vin_val').val() +'</td>'+
        //                     '</tr>');

        //                 // $('#export_vin_val').val($("#export_vin_val option:first").val());
        //                 $('#export_vin_val').val($('#mylist option:first-child').val()).trigger('change');

        //                 $('#export-no-data').remove();

        //                 $('#export_modal').modal('hide');
        //             }else{
        //                 alert(data);
        //             }
        //         },
        //         error: function(data){
        //             alert("Server Error");
        //         }
        //     });
            

        // });

    });

</script>

</body>
</html>