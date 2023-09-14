<!DOCTYPE html>
<html lang="id">
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

        <!-- Request Modal -->
        
        <div id="printModal" class="modal fade" role="dialog">
            <div class="modal-dialog">
                <!-- Modal content-->
                <div class="modal-content">
                
                <?php echo form_open_multipart(null, array('id' => 'printFormModal'));?>
                    <!-- <form id="printFormModal" method="post" enctype="multipart/form-data"> -->
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="print-out-rd"></h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-lg-12 truckCode">
                                    <div class="form-group">
                                        <input type="hidden" name="vin_request" id="vin_request" value="">
                                        <label class="text-left">Truck Code * </label>
                                        <select class="form-control" name="truckCode" id="truckCode">
                                            <option value="">-- Insert Truck Code (without space) --</option>
                                        </select>
                                        <div class="error_truck_code"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12 truckType">
                                    <div class="form-group">
                                        <label class="text-left">Truck Type * </label>
                                        <input type="text" class="form-control" id="truckType" name="truckType" readonly/>
                                        <div class="error_truck_type"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12 truckCompany">
                                    <div class="form-group">
                                        <label class="text-left">Truck Company * </label>
                                        <input type="text" class="form-control" id="truckCompany" name="truckCompany" readonly/>
                                        <div class="error_truck_company"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">Driver Name * </label>
                                        <input type="text" class="form-control" id="driverName" name="driverName" placeholder="Insert truck driver name" />
                                        <?php echo form_error('driverName', '<div class="error">', '</div><br/>'); ?>
                                        <div class="error_driver_name"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12 driverPhone">
                                    <div class="form-group">
                                        <label class="text-left">Driver Phone * </label>
                                        <input type="text" class="form-control" id="driverPhone" name="driverPhone" placeholder="Insert truck driver phone" />
                                        <?php echo form_error('driverPhone', '<div class="error">', '</div><br/>'); ?>
                                        <div class="error_driver_phone"></div>
                                    </div>
                                </div>
                                <?php
                                // if ($this->userauth->getLoginData()->sender == 'IKT') {
                                ?>
                                    <div class="col-lg-12">
                                        <div class="form-group">
                                            <label class="text-left">KTP / SIM *<small>pdf max 2MB</small></label>
                                            <div class="input-group">
                                                <span class="input-group-btn">
                                                    <span class="btn btn-primary btn-file">
                                                        Browse&hellip; <input type="file"
                                                        accept="application/pdf" id="browse_ktp_sim" > 
                                                        <!-- required -->
                                                    </span>
                                                </span>
                                                <input type="text" class="form-control" id="upload-ktp-sim"
                                                readonly>
                                            </div>
                                            <?php echo form_error('browse_ktp_sim', '<div class="error">', '</div><br/>'); ?>
                                            <div class="error_ktp_sim"></div>
                                        </div>
                                    </div>
                                <?php
                                // }
                                ?>
                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">Surat Jalan * <small>jpg/jpeg/png/pdf</small></label>
                                        <div class="input-group">
                                            <span class="input-group-btn">
                                                <span class="btn btn-primary btn-file">
                                                    Browse&hellip; <input type="file" 
                                                    accept="image/jpg, image/png, image/jpeg, application/pdf"
                                                    name="browse_surat_jalan" id="browse_surat_jalan" >
                                                    <!-- required -->
                                                </span>
                                            </span>
                                            <input type="text" class="form-control" readonly="readonly">
                                        </div>
                                        <?php echo form_error('browse_surat_jalan', '<div class="error">', '</div><br/>'); ?>
                                        <div class="error_surat_jalan"></div>
                                    </div>
                                </div>
                                <div class="col-lg-12">
                                    <label class="text-left">Listing Checklist VIN</label>
                                    <h5 id="list-checked-vin"></h5>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    <!-- </form> -->
                <?php
                    echo form_close();
                ?> 
                </div>

            </div>
        </div>

        <div class="container">
            <h2>Request Return Cargo</h2>
            <hr />
            <div class="row" style="margin-left: 0.2%;">
                <label class="padding-right: 1%;">Type Batal Muat</label>
                <input type="radio" id="truckType" name="rd_typeBM" value="Truck" checked/> Truck
                <input type="radio" id="selfdriveType" name="rd_typeBM" value="Selfdrive" /> Selfdrive
            </div>
            <table class="table" id="t_return_cargo">
                <thead>
                    <tr>
                        <th>
                            <input type="checkbox" id="checkedAll"/>
                        </th>
                        <th>VIN</th>
                        <th>MODEL</th>
                        <th>SHIPPING NAME</th>
                        <th>VESSEL NAME</th>
                        <th>STATUS</th>
                        <!-- <th>ACTION</th> -->
                    </tr>
                </thead>
                <thead>
                    <tr>
                        <th>
                        </th>
                        <th>VIN</th>
                        <th>MODEL</th>
                        <th>SHIPPING NAME</th>
                        <th>VESSEL NAME</th>
                        <th>STATUS</th>
                        <!-- <th></th> -->
                    </tr>
                </thead>
                <tbody> 
                    <!-- <tr>
                        <td>
                            <input type="checkbox" id="checkbox0" class="checkSingle" value="123123123123"/>
                        </td>
                        <td>123123123123</td>
                        <td>Dummy A</td>
                        <td>Dummy A</td>
                        <td>Dummy A</td>
                        <td>Dummy A</td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" id="098098098098" class="checkSingle" value="098098098098"/>
                        </td>
                        <td>098098098098</td>
                        <td>Dummy B </td>
                        <td>Dummy B</td>
                        <td>Dummy B</td>
                        <td>Dummy B</td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" id="321321321312" class="checkSingle" value="321321321312"/>
                        </td>
                        <td>321321321312</td>
                        <td>Dummy C </td>
                        <td>Dummy C</td>
                        <td>Dummy C</td>
                        <td>Dummy C</td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" id="55555555555" class="checkSingle" value="55555555555"/>
                        </td>
                        <td>55555555555</td>
                        <td>Dummy AB </td>
                        <td>Dummy AB</td>
                        <td>Dummy AB</td>
                        <td>Dummy AB</td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" id="6666666666" class="checkSingle" value="6666666666"/>
                        </td>
                        <td>6666666666</td>
                        <td>Dummy AC </td>
                        <td>Dummy AC</td>
                        <td>Dummy AC</td>
                        <td>Dummy AC</td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" id="83938O1123" class="checkSingle" value="83938O1123"/>
                        </td>
                        <td>83938O1123</td>
                        <td>Dummy BC </td>
                        <td>Dummy BC</td>
                        <td>Dummy BC</td>
                        <td>Dummy BC</td>
                    </tr> -->
                </tbody>
                <tfoot>
                </tfoot>
            </table>
        </div><!-- /.container -->
       
    </div>

    <?php $this->load->view('domestik/backend/elements/footer_domestik') ?>
    <script type="text/javascript">
        var idDocument = <?php echo json_encode($idDocument); ?>;
        var table;
        var checkedArray = [];

        $("#browse_ktp_sim").click(function() {
            $('#upload-ktp-sim').val('');
            $("#browse_ktp_sim").val("");
        });

        $(document).ready(function() {

            $(document)
            .on('change', '.btn-file :file', function() {
                var input = $(this),
                    numFiles = input.get(0).files ? input.get(0).files.length : 1,
                    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                input.trigger('fileselect', [numFiles, label]);
            });

            $('#t_return_cargo thead tr:eq(1) th').each( function (n) {
                var title = $(this).text();
                if( n > 0 ) {
                    $(this).html( '<input type="text" placeholder="Search '+title+'" class="column_search"  />' );
                }
            });

            $( '#t_return_cargo thead'  ).on( 'keyup', ".column_search",function () { 
                var radioVal = $('input[type=radio][name=rd_typeBM]:checked').val();

                table
                .column( $(this).parent().index() )
                .search( this.value )
                .draw();
                
                  if(radioVal == "Selfdrive"){
                        $(".checkSingle:checked").each(function(){
                            checkedArray.forEach(element => {
                                if(element == $(this).val()){
                                    $('.checkSingle').not(this).prop('checked', false);
                                }
                            });
                        }); 
                    }
            });


            $("#checkedAll").change(function(){
                if(this.checked){
                    $(".checkSingle").each(function(){
                        this.checked=true;
                        if(!(checkedArray.includes(this.value))){
                            checkedArray.push(this.value)
                        }
                    });
                    console.log(checkedArray)
                }else{
                    $(".checkSingle").each(function(){
                        this.checked=false;
                        var index = checkedArray.indexOf(this.value);
                        if (index !== -1) {
                            checkedArray.splice(index, 1);
                        }
                    });          
                    console.log(checkedArray)
                }
            });

            // $(".checkSingle").click(function () {
            //     var radioVal = $('input[type=radio][name=rd_typeBM]:checked').val();
                
            //     if ($(this).is(":checked")){
            //         //Backup before filter
            //         var isThereUnchecked = 0;
            //         if(radioVal == "Selfdrive"){
            //             checkedArray.length = 0;
            //         }
            //         checkedArray.push(this.value)      
            //         $(".checkSingle").each(function(){
            //             if(!this.checked)
            //             isThereUnchecked = 1;
            //         });
                    
            //         if(isThereUnchecked == 0){ 
            //             $("#checkedAll").prop("checked", true);     
            //         }
                   
            //         console.log(checkedArray)
            //     } else {
            //         $("#checkedAll").prop("checked", false);
                    
            //         var filteredArray = checkedArray.filter(e => e !== this.value)
            //         checkedArray = filteredArray;
            //         console.log(checkedArray)
            //     }
            // });

            $('input[type=radio][name=rd_typeBM]').change(function() {
                checkedArray.length = 0;
                
                $(".checkSingle").each(function(){
                        this.checked=false;
                });
                $("#checkedAll").prop("checked", false);

                if (this.value == 'Truck') {
                    $("#checkedAll").removeAttr("disabled");
                    $("#truckCode").removeAttr("disabled");
                   
                }
                else if (this.value == 'Selfdrive') {
                    $("#checkedAll").attr("disabled", true);
                    $("#truckCode").attr("disabled", true);
                    
                }
            });

            table = $('#t_return_cargo').DataTable({
                "processing": true,
                "serverSide": false,
                "deferRender": true,
                "dom": 'Bfrtip',
                "buttons": [
                    {
                        text: "Request",
                        action: function (e, node, config){
                            var radioVal = $('input[type=radio][name=rd_typeBM]:checked').val();
                            if(checkedArray.length == 0){
                                alert("Pilih salah satu checkbox")
                            }
                            else if (confirm(`Apakah anda yakin memilih ${radioVal} ?`)) {
                                requestInputData();
                            } 
                        }
                    },
                    'colvis',
                    'pageLength',
                ],
                "ajax": {
                    "url": '<?php echo site_url('domestik/return_cargo_domestik/getTableItems'); ?>',
                    "type": "POST",
                    
                },
                "drawCallback" : function (settings){
                        console.log(settings.json)
                        $('.checkSingle').off('click');

                        $(".checkSingle").click(function () {
                            var radioVal = $('input[type=radio][name=rd_typeBM]:checked').val();
                            
                            if ($(this).is(":checked")){
                                //Backup before filter
                                var isThereUnchecked = 0;
                                if(radioVal == "Selfdrive"){
                                    checkedArray.length = 0;
                                }
                                checkedArray.push(this.value)      
                                $(".checkSingle").each(function(){
                                    if(!this.checked)
                                    isThereUnchecked = 1;
                                });
                                
                                if(isThereUnchecked == 0){ 
                                    $("#checkedAll").prop("checked", true);     
                                }
                            
                                console.log(checkedArray)
                            } else {
                                $("#checkedAll").prop("checked", false);
                                
                                var filteredArray = checkedArray.filter(e => e !== this.value)
                                checkedArray = filteredArray;
                                console.log(checkedArray)
                            }
                        });
                    },
                'columnDefs': [
                    {
                        'targets': 0,
                        "orderable": false,
                        "searchable": false,
                    },
                    {
                        'targets': [1, 2, 3, 5],
                        'visible': true,
                        'searchable': true
                    },
                ],
                // 'order': [[1, 'desc']]
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

            // $('#t_return_cargo tbody').on('click', 'tr', function () {
            //     if ($(this).hasClass('selected')) {
            //         $(this).removeClass('selected');
            //     }
            //     else {
            //         table.$('tr.selected').removeClass('selected');
            //         $(this).addClass('selected');
            //     }
            // });

            //OVERRIDE MAIN SEARCH FUNCTION
            $('.dataTables_filter input').unbind()
            .bind("input", function (e) {
                var radioVal = $('input[type=radio][name=rd_typeBM]:checked').val();
               
                  table.search($(this).val()).draw();
                  if(radioVal == "Selfdrive"){
                        $(".checkSingle:checked").each(function(){
                            checkedArray.forEach(element => {
                                if(element == $(this).val()){
                                    $('.checkSingle').not(this).prop('checked', false);
                                }
                            });
                        }); 
                    }
            });
        });


        function requestInputData() {

            var form = $("#printFormModal");
            $('.error').remove();
           
            $("#list-checked-vin").empty();
            $.each(checkedArray, function(index, value){
                $("#list-checked-vin").append('* ' + value + '<br>');
            });

            

            form.trigger("reset");
            var radioVal = $('input[type=radio][name=rd_typeBM]:checked').val();
            form.find('.print-out-rd').text("Tipe Batal Muat : " + radioVal);
            // form.find('#vin_request').val(vin);
            if ($('#truckCode').hasClass("select2-hidden-accessible")){
                $("#truckCode").select2('destroy');
            }
            
            
            if(radioVal == "Truck"){
                
                    $('#truckCode').show();
                    $('.truckCode').show();
                    $('.truckType').show();
                    $('#truckType').show();
                    $('.truckCompany').show();
                    $('#truckCompany').show();
                    // $('#driverPhone').hide();
                    // $('.driverPhone').hide();
                    $('#truckCode')
                    .replaceWith(`<select class="form-control" name="truckCode" id="truckCode">
                                            <option value="">-- Insert Truck Code (without space) --</option>
                                        </select>`);
                   $('#truckCode').select2({
                        ajax: {
                        url: '<?php echo site_url('domestik/return_cargo_domestik/getTruckCodeList'); ?>',
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
                                        id: obj.truck_code,
                                        text: obj.truck_code,
                                        value: obj.truck_code
                                    };
                                })
                            };

                        },
                        cache: true
                    }
                });

                $('#truckCode').on('select2:select', function(e) {
                    $.ajax({
                        url: '<?php echo site_url('domestik/return_cargo_domestik/getTruckCodeData') ?>',
                        type: "post",
                        dataType: 'json',
                        cache:false,
                        data: {
                            searchTerm:$(this).val()
                        },
                        delay: 500,
                        success: function(data) {
                            console.log(data);
                            var realData = data[0];
                            if(data.length != 0){
                                $('#truckType').val(realData["truck_type"]);
                                $('#truckCompany').val(realData["truck_company_name"]);
                            }
                        }
                    });
                });
            }else{
                $('#truckCode').hide();
                $('.truckCode').hide();
                $('.truckCompany').hide();
                $('#truckCompany').hide();
                $('.truckType').hide();
                $('#truckType').hide();
                // $('#driverPhone').show();
                // $('.driverPhone').show();
            }
         

            
            $('#printModal').modal({
                backdrop: 'static',
                keyboard: false
            });
        }

        // Selfdrive Only Func
        $(document).on('click', 'input[type="checkbox"]', function() {      
            var radioVal = $('input[type=radio][name=rd_typeBM]:checked').val();
            if(radioVal == "Selfdrive"){
                $('input[type="checkbox"]').not(this).prop('checked', false);      
            }
        });

        $("#printFormModal").submit(function(e) {
            e.preventDefault();
            var isError = false;
            var form = $(this);
            var radioVal = $('input[type=radio][name=rd_typeBM]:checked').val();
            var errorList = [];


            $('.error').remove();
         
           
            if(radioVal == "Truck"){
                if ($.trim($("#truckCode").val()) === "") {
                    // $(".error_truck_code").after("<span class='error'>Required field.</span>");
                    errorList.push('Truck Code kosong');
                    isError = true;
                }
                if ($.trim($("#truckType").val()) === "") {
                    // $(".error_truck_type").after("<span class='error'>Required field.</span>");
                    errorList.push('Truck Type harus terisi');
                    isError = true;
                }
                if ($.trim($("#truckCompany").val()) === "") {
                    // $(".error_truck_company").after("<span class='error'>Required field.</span>");
                    errorList.push('Truck Company harus terisi');
                    isError = true;
                }
            } 
            // else {
            //     if ($.trim($("#driverPhone").val()) === "") {
            //         // $(".error_truck_type").after("<span class='error'>Required field.</span>");
            //         errorList.push('Driver Phone harus terisi');
            //         isError = true;
            //     }
            // }

            if ($.trim($("#driverPhone").val()) === "") {
                    // $(".error_truck_type").after("<span class='error'>Required field.</span>");
                    errorList.push('Driver Phone harus terisi');
                    isError = true;
                }
            
           
            

            if ($.trim($("#driverName").val()) === "") {
                // $(".error_driver_name").after("<span class='error'>Required field.</span>");
                errorList.push('Nama Driver harus terisi');
                isError = true;
            }
            if($('#browse_ktp_sim').get(0).files.length === 0){
                // $(".error_ktp_sim").after("<span class='error'>Required field.</span>");
                errorList.push('KTP tidak boleh Kosong');
                isError = true;
            }
            if($('#browse_surat_jalan').get(0).files.length === 0){
                // $(".error_surat_jalan").after("<span class='error'>Required field.</span>");
                errorList.push('Surat Jalan tidak boleh Kosong');
                isError = true;
            }

            if(!isError){

                var fd = new FormData();    
                fd.append('return_type', radioVal);
                fd.append('browse_ktp_sim', $('#browse_ktp_sim').get(0).files[0]);
                fd.append('browse_surat_jalan', $('#browse_surat_jalan').get(0).files[0]);
                fd.append('truckCode', $('#truckCode').val());
                fd.append('truckType', $('#truckType').val());
                if(radioVal == "Truck"){
                    fd.append('truckCode', $('#truckCode').val());
                    fd.append('truckCompanyCode', $('#truckCompany').val());
                }else{
                    fd.append('truckCode', 'SELFDRIVE');
                    // fd.append('driverPhone',  $('#driverPhone').val());
                }
                fd.append('docTfId', idDocument);
                fd.append('driverName',  $('#driverName').val());
                fd.append('driverPhone',  $('#driverPhone').val());
                fd.append('eticketType', 'R');

                checkedArray.forEach(item => {
                    fd.append('listedVin[]', item);
                });

                // console.log(...fd);

                swal({
                    title: 'Warning !',
                    text: "Are you sure to request Returning Cargo?",
                    type: 'warning',
                    buttons: true,
                    buttons: ["Cancel", "Sure!"],
                    closeModal: false
                }).then((result) => {
                    if (result) {
                        $.ajax({
                            type: "post",
                            url: "<?php echo base_url("domestik/return_cargo_domestik/submitItems"); ?>",
                            data: fd,
                            enctype: 'multipart/form-data',
                            processData: false,
                            contentType: false,
                            cache: false,
                            dataType: "json",
                            success: function(response) {
                                console.log(response);
                                if(Array.isArray(response)){
                                    var errorMsg= "";
                                    response.forEach(element => {
                                        errorMsg += `${element["message"].replace('<p>', '').replace('</p>', '')}\n`;
                                    });
                                    alert(errorMsg);
                                } else if(response["isError"] == true){
                                                alert(response["message"]);
                                                $('#t_return_cargo').DataTable().ajax.reload();
                                } else {
                                    alert('Sukses menyimpan data' + '\ndengan vin sebanyak ' + checkedArray.length);
                                    checkedArray.length = 0;
                                    $('#t_return_cargo').DataTable().ajax.reload();
                                    $('#printModal').modal('hide');

                                    var date = new Date();
                                    var year = (date.getFullYear()+'').slice(-2);
                                    var month = String(date.getMonth()+1).padStart(2, "0");
                                    var day = String(date.getDate()).padStart(2, "0");
                                    var rand = Math.floor(Math.random() * (99999 - 10000 + 1)) + 10000
                                    var hhmmss = date.toTimeString().split(" ")[0].split(":").join("");
                                    idDocument = year+month+day+hhmmss+'~'+rand;
                                }
                            },
                            error: function(xhr, error) {
                                alert("Error ketika submit data");
                            }
                        });
                    }
                })
            } else {
                alert(errorList.map(item => {
                    return item + "\n";
                }).join(''));
            }
        });
        
    </script>

</body>

</html>