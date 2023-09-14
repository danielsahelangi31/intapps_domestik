<!DOCTYPE html>
<html lang="id">
<?php
    $auth = $this->userauth->getLoginData();
    $userMode = "";
    if($auth->intapps_type == "ADMIN") {
        $shipping_name = $auth->full_name;
        $userMode = 'ADMIN';
    } else {
        $userMode = 'SHIPPING';
    }

    date_default_timezone_set('Asia/Jakarta');
    $tanggal = date('y-m-d h:i:s');
    $tanggal = (string) $tanggal;
    $tanggal = str_replace(' ', '', $tanggal);
    $tanggal = str_replace('-', '', $tanggal);
    $tanggal = str_replace(':', '', $tanggal);
    $FiveDigitRandomNumber = rand(10000,99999);
    $FiveDigitRandomNumber = (string) $FiveDigitRandomNumber;
    $idDocument = $tanggal.'~'.$FiveDigitRandomNumber;
 ?>
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
    <?php $this->load->view('domestik/backend/components/header_domestik') ?>
    <div class="container">
    <?php
        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('y-m-d h:i:s');
        $tanggal = (string) $tanggal;
        $tanggal = str_replace(' ', '', $tanggal);
        $tanggal = str_replace('-', '', $tanggal);
        $tanggal = str_replace(':', '', $tanggal);
        $FiveDigitRandomNumber = rand(10000,99999);
        $FiveDigitRandomNumber = (string) $FiveDigitRandomNumber;
        $idDocument = $tanggal.'~'.$FiveDigitRandomNumber;
    ?>

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
            <form id="main-form" role="form" class="form-horizontal" action="" method="post" enctype="multipart/form-data">
                <div class="col-lg-6">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="pull-left">
                                <!-- <a href="<?php echo site_url('assets/csv_domestik/format_announcement_vin_domestic.xlsx') ?>" target="_blank" class="btn btn-success">Download Template Announcement VIN</a>
                                 -->
                                 <a href="<?php echo site_url('assets/csv_domestik/Contoh_Template_Vin.pdf') ?>" target="_blank" class="btn btn-danger"> <i class="glyphicon glyphicon-download"></i> Contoh Template Announcement VIN</a>
                                 <a href=""  class="btn btn-success btn-sm" id="simpan">  <i class="glyphicon glyphicon-download"></i> Download Template Announcement VIN</a> 
                                  
                            </div>
                        </div>
                    </div>
                    <?php

                    //if($this->userauth->getLoginData()->sender == 'IKT'){
                        ?>
                        <!-- <div class="col-lg-12">
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
                        </div> -->
                        <?php
                   // }

                    ?>

                    <div class="col-lg-12">
                        <div class="form-group">
                            <input type="hidden" name="length_vin" id="length_vin">
                            <label class="text-left">Document Transfer ID *</label>
                            <div class="input-group">
									<span class="input-group-btn">
										<span id="copy-id" class="btn btn-success btn-file">
											Copy
										</span>
									</span>
                                    <input  type="text" class="form-control" id="DocumentTransferId"
                                            name="DocumentTransferId" value="<?php echo $idDocument; ?>" placeholder="<?php echo $idDocument; ?>" readonly
                                    />
                            </div>
                            <!-- <?php// echo form_error('DocumentTransferId', '<div class="error">', '</div><br/>'); ?>
                            <div class="error"></div> -->
                        </div>
                    </div>

                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="input-group">
									<span class="input-group-btn">
										<span class="btn btn-primary btn-file">
											Upload Excel File&hellip; <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" name="upload_vin_excel" id="upload_vin_excel">
										</span>
									</span>
                                <input type="text" class="form-control" readonly="readonly">
                            </div>
                            <?php echo form_error('upload_vin_excel', '<div class="error"> <script> alert("', '") </script> </div><br/>'); ?>
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
                        <label id="title-vin" class="text-left title-vin">VIN Info</label>
                        <div class="col-lg-12">
                            <div class="col-lg-6">
                                <label class="text-left">VIN Number *</label>
                                <input  type="text" class="form-control vin-get" id="vinNumber0"
                                        name="VinNumber" placeholder=""
                                />
                                <?php $errorVin = form_error('VinNumber', '<div class="error">', '</div><br/>'); ?>
                                <div class="error"></div>
                                <div class="error_vin_number"></div>
                            </div>
                            <div class="col-lg-6">
                                <label class="text-left">Direction *</label>
                                <select class="form-control" id="IdDirection0" name="direction">
                                    <option value="">-- Select --</option>
                                    <option value="D" >Inbound(Discharge)</option>
                                    <option value="L" >Outbound(Loading)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="col-lg-6">
                                <label class="text-left">Direction Type *</label>
                                <input  type="text" class="form-control" id="IdDirType0"
                                        name="directionType" placeholder="DOMESTIC" readonly
                                />
                                <!-- <select class="form-control" name="directionType">
                                    <option value="">-- Select --</option>
                                    <option value="INTERNATIONAL" >INTERNATIONAL</option>
                                    <option value="DOMESTIC" >DOMESTIC</option>
                                </select> -->
                            </div>
                            <div class="col-lg-6">
                                <label class="text-left">Fuel</label>
                                <input  type="text" class="form-control"
                                        name="fuel" placeholder="" id="IdFuel0"
                                />
                                <?php echo form_error('fuel', '<div class="error">', '</div><br/>'); ?>
                                <div class="error"></div>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="col-lg-6">
                                <label class="text-left">Model *</label>
                                <select class="form-control models-get" id="IdModel0" name="models" >
                                    <option value="">-- Select --</option>
                                </select>
                            </div>
                            <div class="col-lg-6">
                                <label class="text-left">Destination *</label>
                                <select class="form-control destinate-get" id="IdDestination0" name="destinate" >
                                    <option value="">-- Select --</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <?php
                                $auth = $this->userauth->getLoginData();
                                if($auth->intapps_type == "ADMIN") {
                            ?>
                            <div class="col-lg-6">
                                <label class="text-left">Shipping Line *</label>
                                <select class="form-control shipping-get" id="IdShipping0" name="controlling_org">
                                    <option value="">-- Select --</option>
                                </select>
                            </div>
                            <?php } else { ?>
                            <div class="col-lg-6">
                                <label class="text-left">Shipping Line *</label>
                                <select class="form-control shipping-get" id="IdShipping0" name="controlling_org" disabled>
                                    <?php foreach($ship as $s) { ?>
                                        <option value="<?= $s->ID ?>"><?= $s->NAME ?></option>
                                        <!-- <input type="hidden" id="IdHiddenShip0" name="controlling_org" value="">
                                        <input type="text" class="form-control" id="IdShipping0" placeholder="" readonly> -->
                                        <?php } ?>
                                    </select>
                                </div>
                                <?php } ?>
                            <div class="col-lg-6">
                                <div></div><br/>
                                <button id="deleteVin" onclick="return false;" class="btn deleteVin btn-danger">Delete Info 1</button>
                                <!-- <label class="text-left">Consignee *</label>
                                <select class="form-control consignee-get" name="consignee" >
                                    <option value="">-- Select --</option>
                                    <?php
                                    foreach ($consignees as $consignee){
                                        ?>
                                        <option value="<? echo $consignee->CODE ?>" ><? echo $consignee->NAME ?></option>
                                        <?php
                                    }
                                    ?>
                                </select> -->
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="col-lg-6">
                                <input type="text" id="tambahana0" style="visibility:hidden;">
                            </div>
                            <div class="col-lg-6">
                                <input type="text" id="tambahanb0" style="visibility:hidden;">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12" id="container-box"></div>
                </div>

            </form>
        </div>


    </div><!-- /.container -->
</div>

<?php $this->load->view('domestik/backend/elements/footer_domestik') ?>
<script type="text/javascript">
    //TEST

    var userMode = <?php echo json_encode($userMode); ?>;

    $(document)
    .on('change', '.btn-file :file', function() {
        var input = $(this),
            numFiles = input.get(0).files ? input.get(0).files.length : 1,
            label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
        input.trigger('fileselect', [numFiles, label]);
    });
    
    $("#upload_vin_excel").click(function() {
            // $('#excel-upload').val('');
            // $("#upload_vin_excel").val("");
        });

        $(document).on('click', '.deleteVin', function(event) {
                var currentId = parseInt(event.target.id.split('deleteVin')[1]);
                $(`#extraPerson${currentId}`).remove();
                var extraVinCounter = $('.extraPerson').length;
                if(extraVinCounter == 0){
                    $("#upload_vin_excel").attr('disabled', false);
                }
                $(".extraPerson").each(function(index) {
                    if(currentId <= index+1){
                        $(`#IdModel${currentId+index}`).select2('destroy');
                        $(`#IdDestination${currentId+index}`).select2('destroy');
                        // if(userMode == "ADMIN"){  
                            // if ($('.shipping-get').hasClass("select2-hidden-accessible")) {
                                $(`#IdShipping${currentId+index}`).select2('destroy');
                            // }
                            $(`#IdShipping${currentId+index}`).off('select2:select');
                        // }
                        $(`#IdModel${currentId+index}`).off('select2:select');
                        $(`#IdDestination${currentId+index}`).off('select2:select');

                        var $html = $('.extraPerson');

                        if(currentId == 1){
                            $(`#extraPerson${currentId+index+1}`).attr("id","extraPerson"+(currentId+index));
                            $(`#title-vin${currentId+index+1}`).text("VIN Info " +  (currentId+index));
                            $html.find(`[id=title-vin${currentId+index+1}]`)[0].id = "title-vin" + (currentId+index);
                            $html.find(`[id=vinNumber${currentId+index+1}]`)[0].id = "vinNumber" + (currentId+index);
                            $html.find(`[id=IdDirection${currentId+index+1}]`)[0].id="IdDirection" +  (currentId+index);
                            $html.find(`[id=IdDirType${currentId+index+1}]`)[0].id="IdDirType" +  (currentId+index);
                            $html.find(`[id=IdFuel${currentId+index+1}]`)[0].id = "IdFuel" +  (currentId+index);
                            $html.find(`[id=IdModel${currentId+index+1}]`)[0].id = "IdModel" +  (currentId+index);
                            $html.find(`[id=IdDestination${currentId+index+1}]`)[0].id = "IdDestination" +  (currentId+index);
                            $html.find(`[id=IdShipping${currentId+index+1}]`)[0].id = "IdShipping" + (currentId+index);

                            $html.find(`button#deleteVin${currentId+index+1}`).text("Delete Info " +  (currentId+index));

                            $html.find(`[id=deleteVin${currentId+index+1}]`)[0].id = "deleteVin" + (currentId+index);

                        } else {
                            console.log("index yang keganti : "  + (index+2) + "diganti dengan : " + (index+1));
                            $(`#extraPerson${index+2}`).attr("id","extraPerson"+(index+1));
                            $(`#title-vin${index+2}`).text("VIN Info " +  (index+1));
                            $html.find(`[id=title-vin${index+2}]`)[0].id = "title-vin" + (index+1);
                            $html.find(`[id=vinNumber${index+2}]`)[0].id = "vinNumber" + (index+1);
                            $html.find(`[id=IdDirection${index+2}]`)[0].id="IdDirection" +  (index+1);
                            $html.find(`[id=IdDirType${index+2}]`)[0].id="IdDirType" +  (index+1);
                            $html.find(`[id=IdFuel${index+2}]`)[0].id = "IdFuel" +  (index+1);
                            $html.find(`[id=IdModel${index+2}]`)[0].id = "IdModel" +  (index+1);
                            $html.find(`[id=IdDestination${index+2}]`)[0].id = "IdDestination" +  (index+1);
                            $html.find(`[id=IdShipping${index+2}]`)[0].id = "IdShipping" + (index+1);

                            $html.find(`button#deleteVin${index+2}`).text("Delete Info " +  (index+1));

                            $html.find(`[id=deleteVin${index+2}]`)[0].id = "deleteVin" + (index+1);
                 


                        }
                        $('#length_vin').val(index+1);
                    }
                });

                $(".extraPerson").each(function(index){
                    if(currentId <= index+1){
                        $(document).on('keyup', `#vinNumber${index+1}`, function() {
                            this.value = this.value.replace(/\s/g,'');
                            this.value = this.value.replace(/[`~!@#$%^&*()|+\-=?;:'",.<>\{\}\[\]\\\/]/g,'');
                            $.ajax({
                                url:"<?php echo site_url('domestik/announce_vin_domestik/vinModel') ?>",
                                type:"post",
                                dataType:"json",
                                cache:true,
                                delay: 500,
                                data:{
                                    vin: $(this).val()
                                },
                                success: function(data) {
                                    if(data.length != 0){
                                        // alert(data)
                                        
                                        //$(`.models-get`).val(data[0]["NAME"]).trigger("change");
                                        
                                        var o = $("<option/>", {id: data[0]["ID_CATEGORY"], text: data[0]["NAME"], value: data[0]["ID_CATEGORY"]});
                                        
                                        $(`#IdModel${index+1}`).append(o);
                                        $(`#IdModel${index+1}`).val(data[0]["ID_CATEGORY"]).trigger('change');
                                    }
                                }
                            })
                        })
                    }
                    
                });

                $('.destinate-get').select2(
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
            $('.shipping-get').select2(
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
                
            });
        
    $(document).ready(function () {    
        $('#simpan').click(function(e){
        console.log(bs.baseURL);
        e.preventDefault(); 
        window.location.href = bs.baseURL + 'domestik/template_excel/excel_download';      
        });
            
        var DocumentTransferId = $('#DocumentTransferId').val();   
        
        $("#upload_vin_excel").change(function() {
                if ($("#upload_vin_excel").val() !== "") {              
                    $("#addVin").attr('disabled', true);
                    $("#extraVIN").attr('disabled', true);
;
                }
            })
         $("#addVin").click(function() {          
                $("#upload_vin_excel").attr('disabled', true);            
         })
        
        $('.controll-get').select2();

        $('.destinate-get').select2();

        $('.models-get').select2();

        $('.consignee-get').select2();

        $('.shipping-get').select2();

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
        $('#copy-id').click(function () {
            copyToClipboard("<?php echo $idDocument; ?>");
            alert("Document Transfer ID Copied");
        });

        // var firstName = document.getElementById('VinNumber');
        // function makeFakeEmail(){
            //   alert(HAHA);
            // }
            // firstName.addEventListener('keyup', makeFakeEmail);

        $('#main-form').submit(function(e) {
            if($('#upload_vin_excel').val() == ''){
                e.preventDefault();
                var lenEx = $(".extraPerson").length;
                var dataListPush = [];
                var isError = false;
                var listVal = [];
                
                for(let i = 1; i <= lenEx; i++) 
                {                
                    var vinNum = $(`#vinNumber${i}`).val();
                    var direction = $(`#IdDirection${i}`).val();
                    var model = $(`#IdModel${i} option:selected`).text();
                    var shippingLine = <?php if($auth->intapps_type == "ADMIN") { ?> $(`#IdShipping${i}`).val(); <?php } else { ?> $(`#IdShipping${i}`).attr('placeholder'); <?php } ?>
                    var idShippingLine = $(`#IdShipping${i} option:selected`).attr('value');
                    var destination = $(`#IdDestination${i}`).val();
                    // console.log(model);
                    
                    if (vinNum === "") {
                        listVal.push(`Vin Number ke ${i} harus diisi`);
                        isError = true;
                    }

                    if (direction === "") {
                        listVal.push(`Direction ke ${i} harus diisi`);
                        isError = true;
                    }

                    if ($(`#IdModel${i} option:selected`).val() === "") {
                        listVal.push(`Model ke ${i} harus diisi`);
                        isError = true;
                    }

                    if (shippingLine === "") {
                        listVal.push(`Shipping Line ke ${i} harus diisi`);
                        isError = true;
                    }

                    if (destination === "") {
                        listVal.push(`Destination ke ${i} harus diisi`);
                        isError = true;
                    }

                    // alert(listVal.map(item => { return item + "\n"; }).join(''));

                    var dataList = {
                        "documentTransferId" : $('#DocumentTransferId').attr('placeholder'),
                        "vinNum" : vinNum,
                        "direction" : direction,
                        "model" : model,
                        "shippingLine" : shippingLine,
                        "destination": destination,
                        "fuel": $(`#IdFuel${i}`).val(),
                        "directionType": $(`#IdDirType${i}`).attr('placeholder'),
                        "portCode": $(`#IdDestination${i} option:selected`).attr('value'),
                        "idCategory": $(`#IdModel${i} option:selected`).attr('value'),
                        "idShippingLine": idShippingLine,
                        "id": i
                    }
                    dataListPush.push(dataList);
                }
                // console.log(dataListPush[0].vinNum);
   
                if(isError == false){
                        $.ajax({
                        url: "<?php echo site_url('domestik/announce_vin_domestik/insert_create_announce_vin') ?>",
                        type: "post",
                        dataType: "json",
                        data: {
                            data: dataListPush
                        },
                        success: function(data) {
                            // untuk munculin pesan
                            if(data.length != 0) {
                                if(data["isError"] != 1)
                                {
                                    alert(`Sukses menyimpan document transfer id : ${data['doc']} dengan vin sebanyak ${data['jumlah_vin']}`);
                                    window.location = "<?php echo site_url('domestik/announce_vin_domestik/'); ?>";
                                } else {
                                    alert(data["message"]);
                                    window.location = "<?php echo site_url('domestik/announce_vin_domestik/'); ?>";
                                }
                            } else {
                                alert('Upload file excel kosong');
                            }
                        },
                        error: function(xhr, error) {
                            console.log(xhr);
                            console.log(error);
                        }, 
                        cache:false
                    });
                    
                } else 
                {
                    alert(listVal.map(item => { return item + "\n"; }).join(''));
                }
            } 
       
        });
            
        $('#addVin').click(function () {
                //alert("Testing");
            var currentId = $(".extraPerson").length + 1;
            
            $('.models-get').select2('destroy');
            $('.controll-get').select2('destroy');
            $('.destinate-get').select2('destroy');
            $('.consignee-get').select2('destroy');
            $('.shipping-get').select2('destroy');
            $('<div/>', {
                'class' : 'extraPerson', 
                'id': 'extraPerson' + currentId,
                html: GetHtml()
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
            $('.shipping-get').select2(
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

            $(document).on('keyup', `#vinNumber${currentId}`, function() {
                this.value = this.value.replace(/\s/g,'');
                this.value = this.value.replace(/[`~!@#$%^&*()|+\-=?;:'",.<>\{\}\[\]\\\/]/g,'');
                $.ajax({
                    url:"<?php echo site_url('domestik/announce_vin_domestik/vinModel') ?>",
                    type:"post",
                    dataType:"json",
                    cache:true,
                    delay: 500,
                    data:{
                        vin: $(this).val()
                    },
                    success: function(data) {
                        if(data.length != 0){
                            // alert(data)
                            
                            //$(`.models-get`).val(data[0]["NAME"]).trigger("change");
                            
                            var o = $("<option/>", {id: data[0]["ID_CATEGORY"], text: data[0]["NAME"], value: data[0]["ID_CATEGORY"]});
                            
                            $(`#IdModel${currentId}`).append(o);
                            $(`#IdModel${currentId}`).val(data[0]["ID_CATEGORY"]).trigger('change');
                        }
                    }
                })
            })

            
            

        });
    
    });

    

    

    function GetHtml()
    {
        var len = $('.extraPerson').length+1;
        var $html = $('.extraVIN').clone();
        $html.find('[id=title-vin]')[0].id = "title-vin" + len;
        $html.find('[id=vinNumber0]')[0].id="vinNumber" + len;
        $html.find('[id=IdDirection0]')[0].id="IdDirection" + len;
        $html.find('[id=IdDirType0]')[0].id="IdDirType" + len;
        $html.find('[id=IdFuel0]')[0].id="IdFuel" + len;
        $html.find('[id=IdModel0]')[0].id="IdModel" + len;
        $html.find('[id=IdDestination0]')[0].id="IdDestination" + len;
        $html.find('[id=IdShipping0]')[0].id="IdShipping" + len;
        $html.find('[id=deleteVin]')[0].id="deleteVin" + len;
        $html.find('[id=tambahana0]')[0].id="tambahana" + len;
        $html.find('[id=tambahanb0]')[0].id="tambahanb" + len;
        $html.find('label.title-vin').text("VIN Info "+len);
        $html.find('button.deleteVin').text("Delete Info " + len);
        $('#length_vin').val(len);
        return $html.html();
    }

    function copyToClipboard(text) {
        var $temp = $("<input>");
        $("body").append($temp);
        $temp.val(text).select();
        document.execCommand("copy");
        $temp.remove();
    }




</script>
</body>
</html>
