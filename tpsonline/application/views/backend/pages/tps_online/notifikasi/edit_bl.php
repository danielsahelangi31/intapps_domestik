<!DOCTYPE html>
<html lang="id">
    <head>
        <?php $this->load->view('backend/elements/basic_head') ?>
    </head>

    <body>
        <div id="wrap">
            <?php $this->load->view('backend/components/header') ?>

            <div class="container">

                <div class="row">
                    <div class="col-md-8">
                        <h2>Lihat Data Kargo</h2>
                    </div>
                    <div class="col-md-4">
                        <div class="pull-right back_list">

                        </div>
                    </div>
                </div>

                <?php echo form_open('', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>


                <?php
                if (isset($error_msg)) {
                    ?>
                    <div class="alert alert-danger fade in">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><?php echo isset($error_header) ? $error_header : 'Maaf Tidak Bisa Memproses Lebih Lanjut!' ?></h4>
                        <p><?php echo $error_msg ?></p>
                    </div>
                    <?php
                }
                ?>

                <?php
                if (isset($info_msg)) {
                    ?>
                    <div class="alert alert-success fade in">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><?php echo $info_msg ?></h4>
                    </div>
                    <?php
                }
                ?>

                <?php echo form_open(NULL, array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>


                <fieldset class="delivery-request-border">
                    <legend class="delivery-request-border">Data Kargo</legend>
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Visit ID</label>
                                <div class="col-lg-8">
                                    <div class="col-md-10">
                                        <p class="form-control-static"><?php echo $kargo->VISIT_ID ?></p>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">VIN</label>
                                <div class="col-lg-8">
                                    <div class="col-md-10">
                                        <p class="form-control-static"><?php echo $kargo->VIN ?></p>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">BL Number </label>
                                <div class="col-lg-8">
                                    <div class="col-md-10">
                                        <p class="form-control-static"><?php echo $kargo->BL_NUMBER ?></p>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">BL Number Date</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static"><?php echo $kargo->BL_NUMBER_DATE ?></p>    
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Customs Number</label>
                                <div class="col-lg-8">
                                    <div class="col-md-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->CUSTOMS_NUMBER ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->CUSTOMS_NUMBER ?>" style="display: none">
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Customs Date</label>
                                <div class="col-lg-8">
                                    <div class="col-md-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->CUSTOMS_DATE ?></p>
                                        <input class="form-control input-sm date" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->CUSTOMS_DATE ?>" style="display: none">
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Type Cargo</label>
                                <div class="col-lg-8">
                                    <div class="col-md-10">
                                        <p class="form-control-static" id="TYPE_CARGO">
                                    <select class="form-control" id="TYPE_CARGO" name="TYPE_CARGO">
                                    <?php
                                    foreach($TYPE_CARGO_DS as $row){
                                    ?>
                                    <option value="<?php echo $row->CUSTOMS_CODE ?>" <?php if($row->CUSTOMS_CODE==$kargo->TYPE_CARGO){ echo 'selected';}?>>
                                    <?php echo $row->CUSTOMS_CODE.' '.$row->DESCRIPTION ?></option>
                                    <?php
                                    }
                                    ?>
                                </select>
                                    </p>
                                    </div>
                                    
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-lg-4 control-label">Weight</label>
                                <div class="col-lg-8">
                                    <div class="col-md-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->WEIGHT ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->WEIGHT ?>" style="display: none">
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">DTS On Terminal</label>
                                <div class="col-lg-8">
                                    <div class="col-md-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->DTS_ONTERMINAL ?></p>
                                        <input class="form-control input-sm date" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->DTS_ONTERMINAL ?>" style="display: none">
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label class="col-lg-4 control-label">DTS Left</label>
                                <div class="col-lg-8">
                                    <div class="col-md-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->DTS_LEFT ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->DTS_LEFT ?>" style="display: none">
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group ">
                                <label class="col-lg-4 control-label">Police Number</label>
                                <div class="col-lg-8">
                                    <div class="col-md-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->NUMBER_POLICE ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->NUMBER_POLICE ?>" style="display: none">    
                                    </div>
                                    <div class="col-md-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                    
                                    
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Flag Send Codeco</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->FLAG_SEND_CODECO ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->FLAG_SEND_CODECO ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>

                                </div>

                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Flag Send Coarri</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->FLAG_SEND_COARRI ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->FLAG_SEND_COARRI ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">DTS Announced</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->DTS_ANNOUNCED ?></p>
                                        <input class="form-control date input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->DTS_ANNOUNCED ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Direction</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->DIRECTION ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->DIRECTION ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Direction Type</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->DIRECTION_TYPE ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->DIRECTION_TYPE ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">No SPPB</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->NO_SPPB ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->NO_SPPB ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Jenis Kemasan</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->JNS_KMS ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->JNS_KMS ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Jumlah</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->JUMLAH ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->JUMLAH ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Inward BC11</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->INWARD_BC11 ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->DIRECTION_TYPE ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Inward BC11 Date</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->INWARD_BC11_DATE ?></p>
                                        <input class="form-control input-sm date" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->INWARD_BC11_DATE ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Outward BC11</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->OUTWARD_BC11 ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->OUTWARD_BC11 ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Outward BC11 Date</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->OUTWARD_BC11_DATE ?></p>
                                        <input class="form-control input-sm date" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->OUTWARD_BC11_DATE ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Remark</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->REMARK ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->REMARK ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">No NPE</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->NO_NPE ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->NO_NPE ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Npe Date</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->NPE_DAT ?></p>
                                        <input class="form-control input-sm date" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->NPE_DAT ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">CONSIGNEE ID</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->CONSIGNEE_ID ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->CONSIGNEE_ID ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">CONSIGNEE NAME</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->CONSIGNEE_NAME ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->CONSIGNEE_NAME ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Merk</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->MERK ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->MERK ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Bruto</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->BRUTO ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->BRUTO ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">In Out Doc</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->IN_OUT_DOC ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->IN_OUT_DOC ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">In Out Doc Date</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->IN_OUT_DOC_DATE ?></p>
                                        <input class="form-control input-sm date" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->IN_OUT_DOC_DATE ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Kd Dok</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo @$kargo->KD_DOK ?></p>
                                        <input class="form-control input-sm" type="text" name="NUMBER_POLICE" value="<?php echo @$kargo->KD_DOK ?>" style="display: none">    
                                    </div>
                                    <div class="col-lg-1">
                                        <button class="btn btn-xs btn-primary editRow" data-editmode="false" style="display: none">
                                            <span class=""><i class="glyphicon glyphicon-pencil"></i></span>    
                                        </button>
                                        
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Discharge Port</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="NUMBER_POLICE"><?php echo $kargo->DISCHARGER_PORT ?></p>    
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Next Port</label>
                                <div class="col-lg-8">
                                    <div class="col-lg-10">
                                        <p class="form-control-static" id="NUMBER_POLICE"><?php echo $kargo->NEXT_PORT ?></p>
                                    </div>
                                    
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>


                <div class="row">
                    <div class="col-lg-6">

                    </div>
                    <div class="col-lg-6">
                        <div class="pull-right">
                            <a href="<?php echo site_url($grid_state) ?>" class="btn btn-default">Kembali</a>
                            <button type="submit" class="btn btn-primary">Simpan</button>
                        </div>
                    </div>
                </div>
                <?php echo form_close() ?>

            </div><!-- /.container -->
        </div>

        <?php $this->load->view('backend/elements/footer') ?>
        <script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
        <script type="text/javascript">

            $(document).ready(function () {

                function updateNow(name,val){
                    $.ajax({
                        url:bs.siteURL + 'tps_online/notifikasi/updateNow',
                        data: {name:name, val:val},
                        dataType: 'json',
                        type:'post',
                        success: function(response) {
                            if (response.status==true) {

                            } else {

                            }
                            // alert('asdsadsads');
                        },
                        failure: function(err) {
                            alert(err);
                        } 
                    });
                }
                
                $('.form-group').mouseover(function(){
                    $(this).find('button').show();

                })
                .mouseleave(function(){
                    $(this).find('button').hide();
                });

                $(document).on('click','.editRow',function(e){
                    e.preventDefault();
                    if ($(this).attr('data-editmode')==='false') {
                            let inpt = $(this).parent().siblings().find('input');
                            let teks = $(this).parent().siblings().find('p');
                            teks.hide();
                            inpt.show();
                            $(this).attr('data-editmode','true');
                    }
                    else if($(this).attr('data-editmode')==='true'){
                            let inpt = $(this).parent().siblings().find('input');
                            let teks = $(this).parent().siblings().find('p');
                            res = updateNow(inpt.attr('name'),inpt.val());
                            console.log(res);
                            inpt.hide();
                            teks.show();
                            $(this).attr('data-editmode','false');
                    }
                });
                



            });
        </script>
    </body>
</html>
