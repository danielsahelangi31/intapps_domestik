<!DOCTYPE html>
<html lang="id">
<?php
    $integrasi_cardom_dev = $this->load->database('integrasi_cardom_dev', TRUE);
    $auth = $this->userauth->getLoginData();
    $userMode = "";
    $shipping_name = "";
    $shipping_code = $auth->full_name;
    if($auth->intapps_type == "ADMIN") {
        $shipping_name = $auth->full_name;
        $userMode = 'ADMIN';
    } else {
        $query = $integrasi_cardom_dev->query("select NAME from M_ORGANIZATION WHERE ID = '".$auth->intapps_type."' ");
            if ($query->num_rows() > 0)
            {
                $hasil = $query->row();
                $shipping_name = $hasil->NAME;
            }
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
</head>

<body>
    <div id="wrap">
        <?php $this->load->view('domestik/backend/components/header_domestik') ?>

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
                                <a data-toggle="modal" href="#myContoh" class="btn btn-danger">Contoh Template Announcement Vin</a>
                                     <a data-toggle="modal" href="#myModal" class="btn btn-success"> <i class="glyphicon glyphicon-download"></i> Download Template Announcement Vin</a>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <?php
                            // if ($this->userauth->getLoginData()->sender == 'IKT') {
                            ?>
                                <!-- <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">MAKER *</label>
                                        <select class="form-control" id="typeIKT" name="typeIKT">
                                            <option value="">-- Select --</option>
                                            <?php
                                            //foreach ($makers as $make) {
                                            ?>
                                                <option value="<?php //echo $make->MAKE . '_' . $make->SENDER; ?>_IKT_<?php //echo strtoupper($this->userauth->getLoginData()->username); ?>"><?php //echo $make->MAKE . '-' . $make->SENDER; ?></option>
                                            <?php
                                            //}
                                            ?>
                                        </select>
                                        <?php //echo form_error('typeIKT', '<div class="error">', '</div><br/>'); ?>
                                        <div class="error"></div>
                                    </div>
                                </div> -->
                            <?php
                            // }
                            ?>

<!-- Backup -->
                        <!--
                            <div class="col-lg-12">
                                <div class="form-group">
                                    <input type="hidden" name="length_vin" id="length_vin">
                                    <input type="hidden" name="length_bl" id="length_bl">
                                    <input type="hidden" name="senderNi" id="senderNi" value="<?php //echo $this->userauth->getLoginData()->sender ?>">
                                    <label class="text-left">Truck Code * </label>
                                    <input type="text" class="form-control" id="truckCode" name="truckCode" placeholder="Required" />
                                    <div class="error"></div>
                                </div>
                            </div> -->
<!--  -->

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">Truck Code *</label>
                                        <select class="form-control" id="truckCode" name="truckCode">
                                            <option value="">-- Select --</option>

                                        </select>
                                        <?php echo form_error('truckCode', '<div class="error">', '</div><br/>'); ?>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">Truck Type * </label>
                                        <input type="text" class="form-control" id="truckType" name="truckType"/>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">Truck Company * </label>
                                        <input type="text" class="form-control" id="truckCompany" name="truckCompany"/>
                                    </div>
                                </div>
                        </div>

                        <div class="col-lg-6">

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
                                        <input  type="text" class="form-control" id="DocumentTransferId" value="<?php echo $idDocument; ?>"
                                        name="DocumentTransferId" placeholder="<?php echo $idDocument; ?>" readonly/>
                                    </div>
                                </div>
                            </div>

                            <div class="col-lg-12">
                                <div class="form-group">
                                    <label class="text-left">Announce With</label>
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-primary btn-file">
                                                Upload Excel File&hellip; <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" name="upload_vin_excel" id="upload_vin_excel">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control" id="excel-upload" readonly="readonly">
                                    </div>
                                    <?php echo form_error('upload_vin_excel', '<div class="error">', '</div><br/>'); ?>
                                    <div class="error"></div>
                                </div>
                            </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">Vessel Name *</label>
                                        <select class="form-control" id="vesselName" name="vesselName">
                                            <option value="">-- Select --</option>

                                        </select>
                                        <?php echo form_error('vesselName', '<div class="error">', '</div><br/>'); ?>
                                    </div>
                                </div>

                                <div class="col-lg-12">
                                    <div class="form-group">
                                        <label class="text-left">Driver Phone Number</label>
                                        <input type="text" class="form-control" id="driverPhoneNumber" value="" name="driverPhoneNumber" placeholder="Optional" />
                                    </div>
                                </div>


                        </div>


                        <div class="clearfix"></div>
                        <br>

                        <div class="col-lg-6">
                            <div class="extraVIN" style="display: none;">
                                <div class="col-lg-12">
                                    <label id="title-vin" class="text-left title-vin">will replace with js </label>
                                </div>
                                <div id="sc-vin0" class="col-lg-6">
                                    <label class="text-left">VIN Number *</label>
                                    <select class="form-control vin-get" id="VinNumber0">
                                    <option value="">-- Select --</option> 
                                    </select>
                                </div>
                                <div class="col-lg-6">
                                    <label class="text-left">Direction *</label>
                                    <input type="text" class="form-control direction"
                                            id="direction" readonly
                                            value="OUTBOUND(LOADING)"
                                    />
                                </div>

                                <div class="col-lg-6">
                                    <label class="text-left">Direction Type *</label>
                                    <input  type="text" class="form-control"
                                        id="directionType" value="DOMESTIC" readonly
                                    />
                                    <!-- <select class="form-control" name="directionType">
                                        <option value="">-- Select --</option>
                                        <option value="INTERNATIONAL" >INTERNATIONAL</option>
                                        <option value="DOMESTIC" >DOMESTIC</option>
                                    </select> -->
                                </div>
                                <div class="col-lg-6">
                                    <label class="text-left">Fuel</label>
                                    <input  type="text" class="form-control fuel"
                                        id="fuel" placeholder=""
                                    />
                                </div>

                                <div class="col-lg-6">
                                    <label class="text-left">Model *</label>
                                    <select class="form-control models-get" id="models0">
                                        <option value="">-- Select --</option>

                                    </select>
                                 
                                </div>
                                <div class="col-lg-6">
                                    <label class="text-left">Destination *</label>
                                    <select class="form-control destinate-get" id="destinate0">
                                        <option value="">-- Select --</option>
                                    </select>
                                   
                                </div>

                                <?php
                                if($userMode == "ADMIN") {
                                ?>
                                        <div class="col-lg-6" style="display: none;">
                                            <label class="text-left">Shipping Line ID</label>
                                            <input type="text" class="form-control" id="shippingLineId0" readonly/>
                                            
                                        </div>

                                        <div class="col-lg-6">
                                            <label class="text-left">Shipping Line *</label>
                                             <select class="form-control shippingLine-get" id="shippingLine0" >
                                                <option value="">-- Select --</option>
                                            </select>
                                            
                                        </div>
                                <?php
                                } else {
                                ?>
                                    <div class="col-lg-6">
                                        <label class="text-left">Shipping Line</label>

                                        <input type="text" class="form-control shippingLine-get" id="shippingLine0" value="<?php echo($shipping_name); echo " (". $auth->full_name  .")"  ?>" readonly/>
                                       
                                    </div>
                                <?php } ?>

                                <div class="col-lg-6">
                                    <br>
                                        <button id="deleteVin" onclick="return false;" class="btn deleteVin btn-danger">
                                        <span class="ui-button-text">Delete Info 1</span>
                                        </button>
                                </div>

                                <!-- TODO::UNUSED <div class="col-lg-6">
                                    <label class="text-left">Controlling Org *</label>
                                    <select class="form-control controll-get" name="controlling_org">
                                        <option value="">-- Select --</option>
                                    </select>
                                </div> -->

                                 <!-- <div class="col-lg-6">
                                    <label class="text-left">Consignee *</label>
                                    <select class="form-control consignee-get" name="consignee">
                                        <option value="">-- Select --</option>
                                    </select>
                                </div> -->

                                 <!-- <div class="col-lg-6">
                                    <label class="text-left">No Dokumen</label>
                                    <select class="form-control noDok" name="noDok">
                                        <option value="">-- Select --</option>
                                        <?php// foreach ($list_no_doc as $dok) { ?>
                                            <option value="<?= $dok->CUSTOMS_NUMBER; ?>">
                                                <?= $dok->CUSTOMS_NUMBER; ?>
                                            </option>
                                        <?php //} ?>
                                    </select>
                                </div> -->
                                <!-- <input type="hidden" class="form-control" id="noNpe" name="noDok" placeholder="No Dokumen" /> -->

                                <!-- <div class="col-lg-6">
                                    <label class="text-left">Tanggal Dokumen</label>
                                    <input type="hidden" class="form-control dateVin" id="tglNpe" name="tglNpe" placeholder="dd-mm-yyyy" />
                                    <input type="hidden" class="form-control dateVinV" id="tglNpeV" name="tglNpeV" placeholder="dd-mm-yyyy" disabled='disabled' />
                                </div> -->

                                <!-- <div class="col-lg-6">
                                    <label class="text-left">NPWP Expor</label>
                                    <input type="hidden" class="form-control" id="npwpEksport" name="npwp" placeholder="NPWP" />
                                    <input type="text" class="form-control" id="npwpEksportV" name="npwpV" placeholder="NPWP" disabled='disabled' />
                                </div> -->
                                <!-- <div class="col-lg-6">
                                    <label class="text-left">Kode Dokumen *</label>
                                    <input type="hidden" class="form-control" id="kdDok_export" name="kdDok_export" placeholder="Kode Document" />
                                    <select class="form-control kdDok_exportV" name="kdDok_exportV" disabled='disabled'>
                                        <option value="">Kode Dokumen</option>
                                        <?php //foreach ($dokumen_export as $dok) { ?>
                                            <option value="<?php// echo $dok->ID; ?>"><?php //echo $dok->ID . '-' . $dok->DOC_TYPE; ?> </option>
                                        <?php //} ?>
                                    </select>
                                </div> -->
                                <!-- <div class="col-lg-6">
                                    <label class="text-left">Total Cargo * </label>
                                    <input type="hidden" class="form-control" id="totalCargo" name="totalCargo" autocomplete="off" placeholder="Total Cargo" />
                                    <input type="text" class="form-control" id="totalCargoV" name="totalCargoV" autocomplete="off" placeholder="Total Cargo" disabled='disabled' />
                                </div> -->
                                <!-- <div class="col-lg-6">
                                    <label class="text-left">Sisa Cargo * </label>
                                    <input type="hidden" class="form-control" id="sisaCargo" name="sisaCargo" autocomplete="off" placeholder="Sisa Cargo" />
                                    <input type="text" class="form-control" id="sisaCargoV" name="sisaCargoV" autocomplete="off" placeholder="Sisa Cargo" disabled='disabled' />
                                    <input type="hidden" class="form-control" id="noDokCounter" name ="noDokCounter" />
                                </div> -->

                                <div class="clearfix"></div>
                                <br>
                            </div>
                            <div class="col-lg-12" id="container-box"></div>
                            <div class="clearfix"></div>
                            <br>
                            <div class="pull-left">
                                <button id="addVin" onclick="return false;" class="btn btn-success">Add more vin</button>
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
                                <!-- Backup Import BL -->
                                <!-- <a id="addImportBL" class="btn btn-primary">Add import BL</a> -->
                                <input type="checkbox" name="checkboxInbound" id="checkboxInbound">
                                <label for="checkbox_id">Inbound(Discharge)</label>
                            </div>

                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <br>
                    <div class="form-group row">
                        <div class="col-sm-6">
                            <label class="text-left"><small>* is Mandatory</small></label>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <button id="submitBtn" type="submit" class="btn btn-primary btn-block submitBtn">Submit</button>
                    </div>
                </div>
            </form>


        </div><!-- /.container -->
    </div>
            <div class="modal fade" id="myModal">
                <div class="modal-dialog">
	      			<div class="modal-content">
	      				<div class="modal-header">
	      					<b>Download Template Announcement Vin</b>
	      				</div>	
	        			<div class="modal-body">	        			
	        				<p>Silahkan, pilih list template announcement Vin</p>
	        			</div>
	        			<div class="modal-footer">
                            <a href=""  class="btn btn-success btn-sm" id="simpan"><i class="glyphicon glyphicon-download"></i> Outbound(Loading)</a>
                            <a href="<?php echo site_url('assets/csv_domestik/format_announcement_inbound_domestic.xlsx') ?>" target="_blank" class="btn btn-primary"><i class="glyphicon glyphicon-download"></i> Inbound(Discharge)</a>
                            <button type="button" class="btn btn-danger" id="btn-cancel" data-dismiss="modal">Cancel</button>
	        			</div>
	      			</div><!-- /.modal-content -->
	    		</div><!-- /.modal-dialog -->
	  		</div><!-- /.modal -->
              <div class="modal fade" id="myContoh">
                <div class="modal-dialog">
	      			<div class="modal-content">
	      				<div class="modal-header">
	      					<b>Contoh Template Announcement Vin</b>
	      				</div>	
	        			<div class="modal-body">	        			
	        				<p>Silahkan, Pilih contoh list template announcement Vin</p>
	        			</div>
	        			<div class="modal-footer">
                            <a href="<?php echo site_url('assets/csv_domestik/Contoh_Template_Vin_Outbound.pdf') ?>" target="_blank" class="btn btn-success"> <i class="glyphicon glyphicon-download"></i> Outbound(Loading)</a>
                            <a href="<?php echo site_url('assets/csv_domestik/Contoh_Template_Vin_Inbound.pdf') ?>" target="_blank" class="btn btn-primary"> <i class="glyphicon glyphicon-download"></i> Inbound(Discharge)</a>
                            <button type="button" class="btn btn-danger" id="btn-cancel" data-dismiss="modal">Cancel</button>
	        			</div>
	      			</div><!-- /.modal-content -->
	    		</div><!-- /.modal-dialog -->
	  		</div><!-- /.modal -->

    <?php $this->load->view('domestik/backend/elements/footer_domestik') ?>
    <script type="text/javascript">
        var idDocument = <?php echo json_encode($idDocument); ?>;
        var userMode = <?php echo json_encode($userMode); ?>;
      
       
        var shippingName = <?php echo json_encode($shipping_name); ?>;             
        var shippingCode = <?php echo json_encode($auth->full_name); ?>;
        // var listIsAssociation = [];

        $("#upload_vin_excel").click(function() {
            $('#excel-upload').val('');
            $("#upload_vin_excel").val("");
        });

        $(document).on('click', '.deleteVin', function(event){
            var currentId = parseInt(event.target.id.split('deleteVin')[1]);
            console.log("Id yang dipilih : " + currentId);
            if (confirm(`Apakah anda yakin delete VIN Info ${currentId} ?`)) {
                $(`#extraPerson${currentId}`).remove();
                var extraVinCounter = $('.extraPerson').length;
                if(extraVinCounter == 0){
                    $("#upload_vin_excel").attr('disabled', false);
                }
                $(".extraPerson").each(function(index){
                if(currentId <= index+1){ //currentId == 3 
                    $(`#VinNumber${currentId+index}`).select2('destroy');
                   
                    if(!$('#checkboxInbound').is(":checked")){
                        $(`#models${currentId+index}`).select2('destroy');
                        $(`#destinate${currentId+index}`).select2('destroy');
                    } else {
                        if(userMode == "ADMIN"){  
                            if ($('.shippingLine-get').hasClass("select2-hidden-accessible")) {
                                $(`#shippingLine${currentId+index}`).select2('destroy');
                            }
                            $(`#shippingLine${currentId+index}`).off('select2:select');
                        }
                    }
                    
                    $(`#VinNumber${currentId+index}`).off('select2:select');
                    $(`#models${currentId+index}`).off('select2:select');
                    $(`#destinate${currentId+index}`).off('select2:select');
                    var $html = $(".extraPerson");

                    if(currentId == 1){
                        $(`#extraPerson${currentId+index+1}`).attr("id","extraPerson"+(currentId+index));
                        $(`#title-vin${currentId+index+1}`).text("VIN Info " +  (currentId+index));
                        $html.find(`[id=title-vin${currentId+index+1}]`)[0].id = "title-vin" + (currentId+index);
                        $html.find(`[id=VinNumber${currentId+index+1}]`)[0].id = "VinNumber" + (currentId+index);
                        $html.find(`[id=direction${currentId+index+1}]`)[0].id="direction" +  (currentId+index);
                        $html.find(`[id=directionType${currentId+index+1}]`)[0].id="directionType" +  (currentId+index);
                        $html.find(`[id=fuel${currentId+index+1}]`)[0].id = "fuel" +  (currentId+index);
                        $html.find(`[id=models${currentId+index+1}]`)[0].id = "models" +  (currentId+index);
                        $html.find(`[id=destinate${currentId+index+1}]`)[0].id = "destinate" +  (currentId+index);
                        $html.find(`[id=shippingLine${currentId+index+1}]`)[0].id = "shippingLine" + (currentId+index);
                        $html.find(`[id=sc-vin${currentId+index+1}]`)[0].id = "sc-vin" + (currentId+index);

                        $html.find(`button#deleteVin${currentId+index+1}`).text("Delete Info " +  (currentId+index));

                        $html.find(`[id=deleteVin${currentId+index+1}]`)[0].id = "deleteVin" + (currentId+index);

                        if(userMode == "ADMIN"){
                            $html.find(`[id=shippingLineId${currentId+index+1}]`)[0].id = "shippingLineId" + (currentId+index);
                        }
                    } else {
                            console.log("index yang keganti : "  + (index+2) + "diganti dengan : " + (index+1));
                            $(`#extraPerson${index+2}`).attr("id","extraPerson"+(index+1));
                            $(`#title-vin${index+2}`).text("VIN Info " +  (index+1));
                            $html.find(`[id=title-vin${index+2}]`)[0].id = "title-vin" + (index+1);
                            $html.find(`[id=VinNumber${index+2}]`)[0].id = "VinNumber" + (index+1);
                            $html.find(`[id=direction${index+2}]`)[0].id="direction" +  (index+1);
                            $html.find(`[id=directionType${index+2}]`)[0].id="directionType" +  (index+1);
                            $html.find(`[id=fuel${index+2}]`)[0].id = "fuel" +  (index+1);
                            $html.find(`[id=models${index+2}]`)[0].id = "models" +  (index+1);
                            $html.find(`[id=destinate${index+2}]`)[0].id = "destinate" +  (index+1);
                            $html.find(`[id=shippingLine${index+2}]`)[0].id = "shippingLine" + (index+1);
                            $html.find(`[id=sc-vin${index+2}]`)[0].id = "sc-vin" + (index+1);

                            $html.find(`button#deleteVin${index+2}`).text("Delete Info " +  (index+1));

                            $html.find(`[id=deleteVin${index+2}]`)[0].id = "deleteVin" + (index+1);
                                        
                            if(userMode == "ADMIN"){
                                $html.find(`[id=shippingLineId${index+2}]`)[0].id = "shippingLineId" + (index+1);
                            }
                        }
                                                                      
                        if($('#checkboxInbound').is(":checked")){
                            $html.find('.direction').attr('value', 'INBOUND(DISCHARGE)')
                        } else {
                            $html.find('.direction').attr('value', 'OUTBOUND(LOADING)');
                        }
                                    
                        $('#length_vin').val(index+1);

                    }
                });
                           
                $('.vin-get').select2({
                    tags:true,
                    createTag: function (params) {
                        if($('#checkboxInbound').is(":checked")){
                            return null;
                        } 
                        return {
                            id: params.term,
                            text: params.term
                        }
                    },
                    ajax: {
                            url: '<?php echo site_url('domestik/announce_truck_domestik/getSearchVin'); ?>',
                            type: "post",
                            dataType: 'json',
                            delay: 250,
                            data: function(params) {
                                return {
                                    searchTerm: params.term,
                                    isInbound : $('#checkboxInbound').is(":checked")
                                };
                            },
                            processResults: function(response) {
                                console.log(response);
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

                                $(".extraPerson").each(function(index){
                                    if(currentId <= index+1){
                                        $(`#VinNumber${index+1}`).on('select2:select', function(e) {
                                            $.ajax({
                                                type: "POST",
                                                url: '<?php echo site_url('domestik/announce_truck_domestik/getVinData'); ?>',
                                                data: {
                                                    searchTerm: e.params.data.value
                                                },
                                                dataType: 'json',
                                                delay: 500,
                                                success: function(data) {
                                                    var realData = data[0];
                                                    if(data.length != 0){
                                                        // listIsAssociation[index] = true;

                                                        $(`#fuel${index+1}`).val(data[0]["FUEL_TYPE"]).prop('disabled', true);

                                                        var oModel = $("<option/>", {id: realData["NAME"], text: realData["NAME"], value: realData["NAME"]});
                                                        
                                                        $(`#models${index+1}`).append(oModel);
                                                        $(`#models${index+1}`).val(realData["NAME"]).trigger('change').prop('disabled', true);

                                                        var destCode = realData["PORT_NAME"];
                                                        var oDestinate = $("<option/>", {id: destCode, text: destCode, value: destCode});

                                                    
                                                        $(`#destinate${index+1}`).append(oDestinate).val(destCode).trigger('change').prop('disabled', true);

                                                        if(userMode == "ADMIN"){
                                                            var shipping_line = realData["SHIPPING_LINE"];
                                                            var cut = shipping_line.split('~');
                                                            shippingName = cut[0].split('(')[0].slice(0, -1);
                                                            $(`#shippingLineId${index+1}`).val(cut[1]);

                                                            if($('#checkboxInbound').is(":checked")){
                                                                $(`#shippingLine${currentId}`).val(cut[0]).prop('disabled', true);      
                                                            } else {
                                                                var oShipping = $("<option/>", {id: cut[0], text: cut[0], value: cut[0]});
                                                        
                                                                $(`#shippingLine${index+1}`).append(oShipping);
                                                                $(`#shippingLine${index+1}`).val(cut[0]).trigger('change');
                                                            }
                                                        }
                                                    } else {
                                                        // listIsAssociation[index] = false;
                                                        if(!$('#checkboxInbound').is(":checked")){
                                                            $(`#fuel${index+1}`).val('').prop('disabled', false);
                                                            $(`#models${index+1}`).val('').trigger('change').prop('disabled', false);
                                                            $(`#destinate${index+1}`).val('').trigger('change').prop('disabled', false);
                                                            if(userMode == "ADMIN"){
                                                                $(`#shippingLine${index+1}`).val('').prop('disabled', false);//.trigger('change');
                                                            }
                                                        }
                                                    }
                                                }
                                            });


                                        });
                                        if(!$('#checkboxInbound').is(":checked")){
                                            if(userMode == "ADMIN"){
                                                $('.shippingLine-get').select2({
                                                    ajax: {
                                                    url: '<?php echo site_url('domestik/announce_truck_domestik/getTruckShippingLine'); ?>',
                                                    type: "post",
                                                    dataType: 'json',
                                                    delay: 250,
                                                    data: function(params) {
                                                        return {
                                                            searchTerm: params.term
                                                        };
                                                    },
                                                    processResults: function(response) {
                                                        return {
                                                            results: $.map(response, function(obj) {
                                                                return {
                                                                    id: obj.full_name,
                                                                    text: obj.full_name,
                                                                    value: obj.value,
                                                                    shippingId: obj.id
                                                                };
                                                            })
                                                        };
                                                    },
                                                    cache: true
                                                },
                                                    minimumInputLength: 3
                                                }).on('select2:select', function(e) {
                                                    var data = e.params.data;
                                                    console.log(data);
                                                    $(`#shippingLineId${index+1}`).val(data.shippingId);
                                                });
                                            } 
                                        }
                                      
                                    }
                                    
                                });

                                if(!$('#checkboxInbound').is(":checked")){
                                    $('.models-get').select2({
                                    ajax: {
                                        url: '<?php echo site_url('domestik/announce_truck_domestik/getTruckOutboundModel'); ?>',
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

                                $('.destinate-get').select2({
                                    ajax: {
                                        url: '<?php echo site_url('domestik/announce_truck_domestik/getTruckOutboundDestination'); ?>',
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
                                });
                                
                            }}
        });

        $('#main_form').submit(function(e) {
            if($('#upload_vin_excel').get(0).files.length === 0){
                e.preventDefault();

                var isError = false;
                var extraVinCounter = $('.extraPerson').length;
                var errorList = [];
                var dataInsert = [];
                var dirVal = 'L';
                

                if($('#checkboxInbound').is(":checked")){
                    dirVal = 'D';
                } else {
                    dirVal = 'L';
                }

                if(extraVinCounter == 0){
                    errorList.push('VIN masih kosong');
                    isError = true;
                }
                if ($.trim($("#truckCode").val()) === "") {
                    errorList.push('Truck Code harus diisi');
                    isError = true;
                }
                if ($.trim($("#truckType").val()) === "") {
                    errorList.push('Truck Type harus terisi');
                    isError = true;
                }
                if ($.trim($("#truckCompany").val()) === "") {
                    errorList.push('Truck Company harus terisi');
                    isError = true;
                }
                if ($.trim($("#vesselName").val()) === "") {
                    errorList.push('Vessel Name harus diisi');
                    isError = true;
                }

                for (let index = 0; index < extraVinCounter; index++) {
                    if ($.trim($(`#VinNumber${index+1}`).val()) === "") {
                        errorList.push(`Vin Number ke ${index+1} harus diisi`);
                        isError = true;
                    }
                    if ($.trim($(`#models${index+1}`).val()) === "") {
                        if($.trim($(`#VinNumber${index+1}`).val()) === ""){
                            errorList.push(`Model ke ${index+1} harus diisi`);
                        }else{
                            errorList.push(`Model ke ${index+1} harus diisi (${$(`#VinNumber${index+1}`).val()})`);
                        }
                        isError = true;
                    }
                    if ($.trim($(`#destinate${index+1}`).val()) === "") {
                        if($.trim($(`#VinNumber${index+1}`).val()) === ""){
                            errorList.push(`Destination ke ${index+1} harus diisi`);
                        }else{
                            errorList.push(`Destination ke ${index+1} harus diisi (${$(`#VinNumber${index+1}`).val()})`);
                        }
                        isError = true;
                    }
                    if ($.trim($(`#shippingLine${index+1}`).val()) === "") {
                        if($.trim($(`#VinNumber${index+1}`).val()) === ""){
                            errorList.push(`Shipping Line ke ${index+1} harus diisi`);
                        }else{
                            errorList.push(`Shipping Line ke ${index+1} harus diisi (${$(`#VinNumber${index+1}`).val()})`);
                        }
                        isError = true;
                    }
                }

                if(!isError){

                    var truckData = {
                        "truckCode" : $('#truckCode').val(),
                        "truckType" : $('#truckType').val(),
                        "truckCompanyCode" : $('#truckCompany').val(),
                        "docTfId" : idDocument,
                        "vesselCode" : $('#vesselName').val(),
                        "driverPhoneNumber" :  $('#driverPhoneNumber').val(),
                        "eticketType" : dirVal,
                        "listedVin": [],
                        "organizationId" : []
                    };

                    for (let index = 0; index < extraVinCounter; index++) {
                        var model = "";
                        var destination = "";
                        if(dirVal == 'D'){
                            destination = $(`#destinate${index+1}`).val().split('~')[0];
                            // Ex: IDMDN~MEDAN
                        }else{
                            destination = $(`#destinate${index+1}`).val();
                            // if(userMode == "ADMIN"){
                            //     shippingLine = $(`#shippingLine${index+1}`).val(); 
                            // }else{
                            //     shippingLineId = $(`#shippingLine${index+1}`).val().split('~')[1];
                            // }
                        }
                            
                        model = $(`#models${index+1}`).val(); 
                        var shippingLineName = $(`#shippingLine${index+1}`).val();
                        var loopedData = {
                            "docTfId" : idDocument,
                            "vin" : $(`#VinNumber${index+1}`).val(),
                            "direction" : dirVal,
                            "directionType" : "DOMESTIC",
                            "fuel" : $(`#fuel${index+1}`).val(),
                            "model" : model,
                            "destination" : destination.split('~')[0],
                            "idVvd" : $('#vesselName').val()
                            // "isAssociation": listIsAssociation[index]
                        };
                        if(userMode == "ADMIN"){
                            loopedData['shippingLineId'] = $(`#shippingLineId${index+1}`).val();
                            loopedData['shippingLineName'] = shippingLineName.split('(')[0].slice(0, -1);
                        } else {
                            loopedData['shippingLineId'] = <?php echo json_encode($auth->intapps_type); ?>;
                            loopedData['shippingLineName'] = <?php echo json_encode($shipping_name); ?>;
                        }

                        dataInsert.push(loopedData);
                        truckData['listedVin'].push($(`#VinNumber${index+1}`).val());
                        truckData['organizationId'].push(loopedData['shippingLineId']);
                        
                    }

                    console.log(dataInsert);
                    console.log(truckData);

                        $.ajax({
                            url: "<?php echo site_url('domestik/announce_truck_domestik/checkTruckActivity'); ?>",
                            data: {
                                truckCode: truckData["truckCode"]
                            },
                            type: "post",
                            dataType: "json",
                            cache:false,
                            beforeSend: function() {
                                jQuery('button').prop('disabled', true);
                                jQuery('button').prop('disabled', true);
                                jQuery('select').prop('disabled', true);
                                $('input:text').attr("disabled", true);
                                $('input:checkbox').attr("disabled", true);
                            },
                            success: function(data) {
                                if(data["isError"] == true){
                                    alert(data["message"]);
                                    jQuery('button').prop('disabled', false);
                                    jQuery('button').prop('disabled', false);
                                    jQuery('select').prop('disabled', false);
                                    $('input:text').prop("disabled", false);
                                    $('input:checkbox').prop("disabled", false);
                                    $('#truckType').prop('disabled', true);
                                    $('#truckCompany').prop('disabled', true);
                                } else {
                                    if(dirVal == 'L'){
                                        $.ajax({
                                            url: "<?php echo site_url('domestik/announce_truck_domestik/insertTruckVINList'); ?>",
                                            data: {
                                                dataInsert: dataInsert
                                            },
                                            type: "post",
                                            dataType: "json", 
                                            cache:false,
                                            success: function(data) {
                                                if(data["isError"] != '1'){
                                                    $.ajax({
                                                        url: "<?php echo site_url('domestik/announce_truck_domestik/insertTruckData'); ?>",
                                                        data: {
                                                            truckData: truckData
                                                        },
                                                        type: "post",
                                                        dataType: "json",
                                                        cache:false,
                                                        success: function(data) {
                                                            console.log(data);
                                                            if(data["isError"] == true){
                                                                alert(data["message"]);
                                                                jQuery('button').prop('disabled', false);
                                                                jQuery('button').prop('disabled', false);
                                                                jQuery('select').prop('disabled', false);
                                                                $('input:text').prop("disabled", false);
                                                                $('input:checkbox').prop("disabled", false);
                                                                $('#truckType').prop('disabled', true);
                                                                $('#truckCompany').prop('disabled', true);
                                                            } else {
                                                                // alert('Sukses menyimpan dengan Data : \nDocument Transfer Id : ' 
                                                                // + truckData["docTfId"] 
                                                                // + '\nInserted VIN :\n'
                                                                // + dataInsert.map(item => {
                                                                //     if(item['isAssociation'] == false){
                                                                //         return '* ' + item['vin'] + "\n";
                                                                //     }
                                                                // }).join('')
                                                                // + 'Updated VIN : \n'
                                                                // + dataInsert.map(item => {
                                                                //     if(item['isAssociation'] == true){
                                                                //         return '* ' + item['vin'] + "\n";
                                                                //     }
                                                                // }).join(''));
                                                                alert('Sukses menyimpan document transfer id : ' + truckData["docTfId"] + '\ndengan vin sebanyak ' + truckData['listedVin'].length);
                                                                window.location.reload(true)
                                                            }
                                                        },
                                                        error: function(xhr, error) {
                                                            console.log("Error Di Truck Data")
                                                            console.log(xhr);
                                                            console.log(error);
                                                            alert("Error Pengisian Truck Data");
                                                            jQuery('button').prop('disabled', false);
                                                            jQuery('button').prop('disabled', false);
                                                            jQuery('select').prop('disabled', false);
                                                            $('input:text').prop("disabled", false);
                                                            $('input:checkbox').prop("disabled", false);
                                                            $('#truckType').prop('disabled', true);
                                                            $('#truckCompany').prop('disabled', true);
                                                        }
                                                    });
                                                } else {
                                                    alert(data["message"]);
                                                    jQuery('button').prop('disabled', false);
                                                    jQuery('button').prop('disabled', false);
                                                    jQuery('select').prop('disabled', false);
                                                    $('input:text').prop("disabled", false);
                                                    $('input:checkbox').prop("disabled", false);
                                                    $('#truckType').prop('disabled', true);
                                                    $('#truckCompany').prop('disabled', true);
                                                }
                                            },
                                            error: function(xhr, error) {
                                                console.log("Error Di VIN List")
                                                console.log(xhr);
                                                console.log(error);
                                                alert("Error Pengisian VIN List");
                                                jQuery('button').prop('disabled', false);
                                                jQuery('button').prop('disabled', false);
                                                jQuery('select').prop('disabled', false);
                                                $('input:text').prop("disabled", false);
                                                $('input:checkbox').prop("disabled", false);
                                                $('#truckType').prop('disabled', true);
                                                $('#truckCompany').prop('disabled', true);
                                            }
                                        });
                                    }else{
                                        $.ajax({
                                            url: "<?php echo site_url('domestik/announce_truck_domestik/insertTruckData'); ?>",
                                            data: {
                                                truckData: truckData
                                            },
                                            type: "post",
                                            dataType: "json",
                                            cache:false,
                                            beforeSend: function() {
                                                jQuery('button').prop('disabled', true);
                                                jQuery('button').prop('disabled', true);
                                                jQuery('select').prop('disabled', true);
                                                $('input:text').attr("disabled", true);
                                                $('input:checkbox').attr("disabled", true);
                                            },
                                            success: function(data) {
                                                if(data["isError"] == true){
                                                    jQuery('button').prop('disabled', false);
                                                    jQuery('button').prop('disabled', false);
                                                    jQuery('select').prop('disabled', false);
                                                    $('input:text').prop("disabled", false);
                                                    $('input:checkbox').prop("disabled", false);
                                                } else {
                                                    // alert('Sukses Memasukan dengan Data : \nDocument Transfer Id : ' 
                                                    //             + truckData["docTfId"] 
                                                    //             + 'Updated VIN : \n'
                                                    //             + dataInsert.map(item => {
                                                    //                 if(item['isAssociation'] == true){
                                                    //                     return '* ' + item['vin'] + "\n";
                                                    //                 }
                                                    //             }).join(''));
                                                    alert('Sukses menyimpan document transfer id : ' + truckData["docTfId"] + '\ndengan vin sebanyak ' + truckData['listedVin'].length);

                                                    window.location.reload(true)
                                                }
                                            },
                                            error: function(xhr, error) {
                                                console.log("Error Di Truck Data")
                                                console.log(xhr);
                                                console.log(error);
                                                alert("Error Pengisian Truck Data");
                                                jQuery('button').prop('disabled', false);
                                                jQuery('button').prop('disabled', false);
                                                jQuery('select').prop('disabled', false);
                                                $('input:text').prop("disabled", false);
                                                $('input:checkbox').prop("disabled", false);
                                            }
                                        });
                                    }
                                }
                            },
                            error: function(xhr, error) {
                                alert("Error Di Check Aktivitas Truck Data");
                                console.log("Error Di Check Aktivitas Truck Data")
                                console.log(xhr);
                                console.log(error);
                                
                                jQuery('button').prop('disabled', false);
                                jQuery('button').prop('disabled', false);
                                jQuery('select').prop('disabled', false);
                                $('input:text').prop("disabled", false);
                                $('input:checkbox').prop("disabled", false);
                                $('#truckType').prop('disabled', true);
                                $('#truckCompany').prop('disabled', true);
                            }
                        });
            } else {
                alert(errorList.map(item => {
                    return item + "\n";
                }).join(''));
            }
           

            // $('.models-get').attr('disabled', false);
            // $('.destinate-get').attr('disabled', false);
            }
        });


        $(document)
            .on('change', '.btn-file :file', function() {
                var input = $(this),
                    numFiles = input.get(0).files ? input.get(0).files.length : 1,
                    label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                input.trigger('fileselect', [numFiles, label]);
            });

                $(document).on('keyup', `.select2-search__field`, function() {
                            this.value = this.value.replace(/\s/g,'');
                            this.value = this.value.replace(/[^A-Za-z0-9_]/g,'');
                            var selectItem = $('.select2-container--open').prev();
                            var currentId = selectItem.attr('id').split('r')[1];
                            var currentName = selectItem.attr('id').split(/[0-9]/)[0]
                            if(this.value.length > 2 && currentName == "VinNumber"){
                                $.ajax({
                                    url:"<?php echo site_url('domestik/announce_truck_domestik/getVinOutboundData') ?>",
                                    type:"post",
                                    dataType:"json",
                                    cache:true,
                                    delay: 500,
                                    data:{
                                        searchTerm: $(this).val()
                                    },
                                    success: function(data) {
                                        if(data.length != 0){
                                            var o = $("<option/>", {id: data[0]["NAME"], text: data[0]["NAME"], value: data[0]["NAME"]});
                                            
                                            $(`#models${currentId}`).append(o);
                                            $(`#models${currentId}`).val(data[0]["NAME"]).trigger('change');
                                            
                                        }
                                    }
                                })
                            }
                          
                        });

        $(document).ready(function() {
            $('#simpan').click(function(e){
                console.log(bs.baseURL);
                e.preventDefault(); 
                window.location.href = bs.baseURL + 'domestik/template_excel_truck/excel_download';      
             });
             $('#contoh').click(function(e){
                console.log(bs.baseURL);
                e.preventDefault(); 
                window.location.href = bs.baseURL + 'domestik/template_excel_truck/contoh_template_vin';      
             });
            var DocumentTransferId = $('#DocumentTransferId').val();   
         
            var truckCompany = $('#truckCompany').val().split('~')[1];   
            var vesselName = $('#vesselName').val();
            var driverPhoneNumber = $('#driverPhoneNumber').val();            
            var checkboxInbound = $('#checkboxInbound').is(":checked");                        
    
            var truckCompany= $('#truckCompany').val();
            var truckType =$('#truckType').val();

            $('#copy-id').click(function () {
                copyToClipboard("<?php echo $idDocument; ?>");
                alert("Document Transfer ID Copied");
            });
            $('#truckCode').on('select2:select', function(e) {
                $.ajax({
                    url: '<?php echo site_url('domestik/announce_truck_domestik/getTruckCodeData') ?>',
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
         
  

            $("#checkboxInbound").change(function(){
                $('.fuel').val('');
                $('.vin-get').val('').trigger('change');
                if(this.checked){
                    $('.direction').val('INBOUND(DISCHARGE)');
                    $('.models-get').select2('destroy');
                    $('.destinate-get').select2('destroy');
                    if(userMode == "ADMIN"){
                        $('.shippingLine-get').select2('destroy');
                    }
                    $('.fuel').prop('readonly', true);

                    $(".vin-get").each(function(index){
                        // $(".vin-get").eq(index)
                        // .replaceWith(`<select class="form-control vin-get" id="VinNumber${index}">` +
                        //        ` <option value="">-- Select --</option>` +
                        //         `</select>`);
                        // $(`#VinNumber${index}`).on('select2:select', function(e) {
                        //     $.ajax({
                        //         type: "POST",
                        //         url: '<?php echo site_url('domestik/announce_truck_domestik/getVinData'); ?>',
                        //         data: {
                        //             searchTerm: e.params.data.value
                        //         },
                        //         dataType: 'json',
                        //         delay: 500,
                        //         success: function(data) {
                        //             console.log(data);
                        //             var realData = data[0];
                        //             if(data.length != 0){
                        //                 $(`#fuel${index}`).val(realData["FUEL_TYPE"]);
                        //                 $(`#models${index}`).val(realData["NAME"]);
                        //                 $(`#destinate${index}`).val(realData["PORT_NAME"]);
                        //                 if(userMode == "ADMIN"){
                                       
                        //                 var shipping_line = realData["SHIPPING_LINE"];
                        //                 var cut = shipping_line.split('~');
                        //                 shippingName = cut[0].split('(')[0].slice(0, -1);

                        //                 $(`#shippingLine${index}`).val(cut[0]);
                        //                 $(`#shippingLineId${index}`).val(cut[1]);

                        //                 }
                        //             } else {
                        //                 $(`#fuel${currentId}`).val('');
                        //                 $(`#models${currentId}`).val('');
                        //                 $(`#destinate${currentId}`).val('');
                        //             }
                        //         }
                        //     });
                        // });
                        $(".models-get").eq(index)
                        .replaceWith(`<input type="text" class="form-control models-get"` +
                                            `id="models${index}" placeholder="" readonly/>`);
                        $(".destinate-get").eq(index)
                        .replaceWith(`<input type="text" class="form-control destinate-get"` +
                                            `id="destinate${index}" placeholder="" readonly/>`);


                        // if(userMode == "ADMIN"){
                            $(".shippingLine-get").eq(index)
                            .replaceWith(`<input type="text" class="form-control shippingLine-get"` +
                                                `id="shippingLine${index}" value="${userMode != "ADMIN" ? shippingName : ""}" readonly/>`);

                            // $(`#shippingLine${index}`).on('select2:select', function(e) {
                            //     var data = e.params.data;
                            //     console.log(data);
                            //     $(`#shippingLineId${index}`).val(data.shippingId);
                            // });
                        // }
                        // if(userMode != "ADMIN"){
                            // $(".shippingLine-get").eq(index)
                            // .replaceWith(`<input type="text" class="form-control shippingLine-get"` +
                            //                     `id="shippingLine${index}" value="${userMode != "ADMIN" ? shippingName + ' (' + shippingCode + ')'  : ""}" readonly/>`);
                        // } else {
                        //     $(".shippingLine-get").eq(index)
                        //     .replaceWith(`<input type="text" class="form-control shippingLine-get"` +
                        //                         `id="shippingLine${index}" readonly/>`);
                        // }
                        
                    });    

                    // if(userMode == "ADMIN"){
                    //     $('.shippingLine-get').select2({
                    //         ajax: {
                    //             url: '<?php echo site_url('domestik/announce_truck_domestik/getTruckShippingLine'); ?>',
                    //             type: "post",
                    //             dataType: 'json',
                    //             delay: 250,
                    //             data: function(params) {
                    //                 return {
                    //                     searchTerm: params.term
                    //                 };
                    //             },
                    //             processResults: function(response) {
                    //                 return {
                    //                     results: $.map(response, function(obj) {
                    //                         return {
                    //                             id: obj.full_name,
                    //                             text: obj.full_name,
                    //                             value: obj.value,
                    //                             shippingId: obj.id
                    //                         };
                    //                     })
                    //                 };
                    //             },
                    //             cache: true
                    //         },
                    //             minimumInputLength: 3
                    //     });
                    // }

                    // $('.vin-get').select2({
                    //     ajax: {
                    //         url: '<?php echo site_url('domestik/announce_truck_domestik/getSearchVin'); ?>',
                    //         type: "post",
                    //         dataType: 'json',
                    //         delay: 250,
                    //         data: function(params) {
                    //             return {
                    //                 searchTerm: params.term
                    //             };
                    //         },
                    //         processResults: function(response) {
                    //             return {
                    //                 results: $.map(response, function(obj) {
                    //                     return {
                    //                         id: obj.id,
                    //                         text: obj.text,
                    //                         value: obj.text
                    //                     };
                    //                 })
                    //             };
                    //         },
                    //         cache: true
                    //     },
                    //     minimumInputLength : 3
                    // });
                }else{
                    // if(userMode == "ADMIN"){
                    //     $('.shippingLine-get').select2('destroy');
                    // }
                    $('.direction').val('OUTBOUND(LOADING)');
                    // $('.vin-get').select2('destroy');
                    $('.fuel').prop('readonly', false);

                    $(".vin-get").each(function(index){
                        // $(".vin-get").eq(index)
                        // .replaceWith(`<input type="text" class="form-control vin-get"` +
                        //                     `id="VinNumber${index}" placeholder=""/>`);
                        $(".models-get").eq(index)
                        .replaceWith(`<select class="form-control models-get" id="models${index}">` +
                               ` <option value="">-- Select --</option>` +
                                `</select>`);
                        // $(document).on('keyup', `#VinNumber${index}`, function() {
                        //     this.value = this.value.replace(/\s/g,'');
                        //     this.value = this.value.replace(/[^A-Za-z0-9.]/g,'');
                        //     console.log(this.value);
                        //     if(this.value.length > 3){
                        //         $.ajax({
                        //             url:"<?php echo site_url('domestik/announce_truck_domestik/getVinOutboundData') ?>",
                        //             type:"post",
                        //             dataType:"json",
                        //             cache:false,
                        //             delay: 1000,
                        //             data:{
                        //                 searchTerm: $(this).val()
                        //             },
                        //             success: function(data) {
                        //             console.log(data);
                        //                 if(data.length != 0){
                        //                     var o = $("<option/>", {id: data[0]["ID_CATEGORY"], text: data[0]["NAME"], value: data[0]["ID_CATEGORY"]});
                                            
                        //                     if(data[0]["NAME"] != $(`#models${index}`).val()) {
                        //                         $(`#models${index}`).append(o);
                        //                     $(`#models${index}`).val(data[0]["ID_CATEGORY"]).trigger('change');
                        //                     }
                                            
                                          
                        //                 }
                        //             }
                        //         })
                        //     } 
                        // });
                        $(".destinate-get").eq(index)
                        .replaceWith(`<select class="form-control destinate-get" id="destinate${index}">` +
                               ` <option value="">-- Select --</option>` +
                                `</select>`);
                        if(userMode == "ADMIN"){
                            $(".shippingLine-get").eq(index)
                            .replaceWith(`<select class="form-control shippingLine-get" id="shippingLine${index}">` +
                                ` <option value="">-- Select --</option>` +
                                    `</select>`);

                            $(`#shippingLine${index}`).on('select2:select', function(e) {
                                var data = e.params.data;
                                console.log(data);
                                $(`#shippingLineId${index}`).val(data.shippingId);
                            });
                        }
                    });  
                    
                    $('.models-get').select2({
                        ajax: {
                            url: '<?php echo site_url('domestik/announce_truck_domestik/getTruckOutboundModel'); ?>',
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

                    $('.destinate-get').select2({
                        ajax: {
                            url: '<?php echo site_url('domestik/announce_truck_domestik/getTruckOutboundDestination'); ?>',
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
                    });
                    if(userMode == "ADMIN"){
                        $('.shippingLine-get').select2({
                            ajax: {
                                url: '<?php echo site_url('domestik/announce_truck_domestik/getTruckShippingLine'); ?>',
                                type: "post",
                                dataType: 'json',
                                delay: 250,
                                data: function(params) {
                                    return {
                                        searchTerm: params.term
                                    };
                                },
                                processResults: function(response) {
                                    return {
                                        results: $.map(response, function(obj) {
                                            return {
                                                id: obj.full_name,
                                                text: obj.full_name,
                                                value: obj.value,
                                                shippingId: obj.id
                                            };
                                        })
                                    };
                                },
                                cache: true
                            },
                                minimumInputLength: 3
                        });
                    }
                }
            });

            // $('#typeIKT').select2();

            $('#truckCode').select2({
                ajax: {
                    url: '<?php echo site_url('domestik/announce_truck_domestik/getTruckCodeList'); ?>',
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
                },
                minimumInputLength : 3
            });



            $('#truckType').prop('disabled', true);
            $('#truckCompany').prop('disabled', true);


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

            $('.destinate-get').select2();
            $('.models-get').select2();

            if(userMode == "ADMIN"){
                $('.shippingLine-get').select2();
            }
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

            $("#addVin").click(function() {          
                $("#upload_vin_excel").attr('disabled', true);
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
                if (confirm(`Apakah anda yakin menambah VIN ?`)) {
                    $("#upload_vin_excel").attr('disabled', true);
                    var counterG = $('.extraPerson').length;
                    var currentId = $('.extraPerson').length + 1;
                    // $('.dateVin').datepicker({
                    //     format: 'dd-mm-yyyy'
                    // });
                    var today = new Date();
                    today.setDate(today.getDate() - 30);
                    var min = today.toISOString().slice(0, 10);
                    var max = new Date().toISOString().slice(0, 10);
                    $('#container-box').show();

                    if ($('.vin-get').hasClass("select2-hidden-accessible")) {
                        $('.vin-get').select2('destroy');
                    }
                    if(!$('#checkboxInbound').is(":checked")){
                        $('.models-get').select2('destroy');
                        $('.destinate-get').select2('destroy');
                        if(userMode == "ADMIN"){
                            if ($('.shippingLine-get').hasClass("select2-hidden-accessible")) {
                                $('.shippingLine-get').select2('destroy');
                            }
                        }
                    }
                    // if(userMode == "ADMIN"){
                    //     if ($('.shippingLine-get').hasClass("select2-hidden-accessible")) {
                    //         $('.shippingLine-get').select2('destroy');
                    //     }
                    // }
                
                    $('<div/>', {
                        'class': 'extraPerson',
                        'id': 'extraPerson' + currentId,
                        html: GetHtml()
                    }).hide().appendTo('#container-box').slideDown('fast');

                    $('.vin-get').select2({
                        tags:true,
                        createTag: function (params) {
                                // params.term = params.term.replace(/\s/g,'');
                                // params.term = params.term.replace(/[^A-Za-z0-9.]/g,'');
                        if($('#checkboxInbound').is(":checked")){
                            return null;
                        } 
                                return {
                                    id: params.term,
                                    text: params.term
                                }
                            },
                            ajax: {
                                url: '<?php echo site_url('domestik/announce_truck_domestik/getSearchVin'); ?>',
                                type: "post",
                                dataType: 'json',
                                delay: 250,
                                data: function(params) {
                                    return {
                                        searchTerm: params.term,
                                        isInbound : $('#checkboxInbound').is(":checked")
                                    };
                                },
                                processResults: function(response) {
                                    console.log(response);
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

                       

                        $(`#VinNumber${currentId}`).on('select2:select', function(e) {
                            $.ajax({
                                type: "POST",
                                url: '<?php echo site_url('domestik/announce_truck_domestik/getVinData'); ?>',
                                data: {
                                    searchTerm: e.params.data.value
                                },
                                dataType: 'json',
                                delay: 500,
                                success: function(data) {
                                    var realData = data[0];
                                    if(data.length != 0){
                                        // listIsAssociation[counterG] = true;

                                        $(`#fuel${currentId}`).val(data[0]["FUEL_TYPE"]).prop('disabled', true);

                                        var oModel = $("<option/>", {id: realData["NAME"], text: realData["NAME"], value: realData["NAME"]});
                                        
                                        $(`#models${currentId}`).append(oModel);
                                        $(`#models${currentId}`).val(realData["NAME"]).trigger('change').prop('disabled', true);

                                        var destCode = realData["PORT_NAME"];
                                        var oDestinate = $("<option/>", {id: destCode, text: destCode, value: destCode});

                                       
                                        $(`#destinate${currentId}`).append(oDestinate).val(destCode).trigger('change').prop('disabled', true);

                                        if(userMode == "ADMIN"){
                                            var shipping_line = realData["SHIPPING_LINE"];
                                            var cut = shipping_line.split('~');
                                            shippingName = cut[0].split('(')[0].slice(0, -1);
                                            $(`#shippingLineId${currentId}`).val(cut[1]);

                                            if($('#checkboxInbound').is(":checked")){
                                                
                                                $(`#shippingLine${currentId}`).val(cut[0]).prop('disabled', true);      
                                            }else{
                                                var oShipping = $("<option/>", {id: cut[0], text: cut[0], value: cut[0]});
                                        
                                                $(`#shippingLine${currentId}`).append(oShipping);
                                                $(`#shippingLine${currentId}`).val(cut[0]).trigger('change');
                                            }
                                        }
                                    } else {
                                        // listIsAssociation[counterG] = false;
                                        if(!$('#checkboxInbound').is(":checked")){
                                            $(`#fuel${currentId}`).val('').prop('disabled', false);
                                            $(`#models${currentId}`).val('').trigger('change').prop('disabled', false);
                                            $(`#destinate${currentId}`).val('').trigger('change').prop('disabled', false);
                                            if(userMode == "ADMIN"){
                                                $(`#shippingLine${currentId}`).val('').prop('disabled', false);     ;//.trigger('change');
                                            }
                                        }
                                    }
                                }
                            });
                        });
    
                        if(!$('#checkboxInbound').is(":checked")){
                            $('.models-get').select2({
                            ajax: {
                                url: '<?php echo site_url('domestik/announce_truck_domestik/getTruckOutboundModel'); ?>',
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

                            $('.destinate-get').select2({
                                ajax: {
                                    url: '<?php echo site_url('domestik/announce_truck_domestik/getTruckOutboundDestination'); ?>',
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
                            });

                            if(userMode == "ADMIN"){
                                $('.shippingLine-get').select2({
                                    ajax: {
                                    url: '<?php echo site_url('domestik/announce_truck_domestik/getTruckShippingLine'); ?>',
                                    type: "post",
                                    dataType: 'json',
                                    delay: 250,
                                    data: function(params) {
                                        return {
                                            searchTerm: params.term
                                        };
                                    },
                                    processResults: function(response) {
                                        return {
                                            results: $.map(response, function(obj) {
                                                return {
                                                    id: obj.full_name,
                                                    text: obj.full_name,
                                                    value: obj.value,
                                                    shippingId: obj.id
                                                };
                                            })
                                        };
                                    },
                                    cache: true
                                },
                                    minimumInputLength: 3
                                }).on('select2:select', function(e) {
                                    var data = e.params.data;
                                    console.log(data);
                                    $(`#shippingLineId${currentId}`).val(data.shippingId);
                                });
                            }
                        }
                       
                    $('#directionType').prop('disabled', false);
                }

            });

        });

       

        const alertS = 'alert alert-success';
        const alertF = 'alert alert-danger';

        function GetHtml() {
            var len = $('.extraPerson').length + 1;
            var $html = $('.extraVIN').clone();
            $html.find('[id=title-vin]')[0].id = "title-vin" + len;
            $html.find('[id=VinNumber0]')[0].id = "VinNumber" + len;
            $html.find('[id=direction]')[0].id="direction" + len;
            $html.find('[id=directionType]')[0].id="directionType" + len;
            $html.find('[id=fuel]')[0].id = "fuel" + len;
            $html.find('[id=models0]')[0].id = "models" + len;
            $html.find('[id=destinate0]')[0].id = "destinate" + len;
            $html.find('[id=shippingLine0]')[0].id = "shippingLine" + len;
            $html.find('[id=sc-vin0]')[0].id = "sc-vin" + len;
            $html.find('[id=deleteVin]')[0].id = "deleteVin" + len;

            if(userMode == "ADMIN"){
                $html.find('[id=shippingLineId0]')[0].id = "shippingLineId" + len;
            }
        
            if($('#checkboxInbound').is(":checked")){
                $html.find('.direction').attr('value', 'INBOUND(DISCHARGE)')
            } else {
                $html.find('.direction').attr('value', 'OUTBOUND(LOADING)');
            }
            $html.find('label.title-vin').text("VIN Info " + len);
            
            // $(`#deleteVin${len} span`).text("Delete Info " + len);
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
