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

                <?php echo form_open('#', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>


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
                                <label class="col-lg-4 control-label">VIN</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static"><?php echo $kargo->VIN ?></p>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-lg-4 control-label">NO. RANGKA</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static"><?php echo $kargo->VIN ?></p>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-lg-4 control-label">BRUTO (KG)</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static"><?php echo $kargo->WEIGHT ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Visit ID</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static"><?php echo $kargo->VISIT_ID ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Nama Kapal</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static"><?php echo $kargo->VISIT_NAME ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Voyage In</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="voyage"><?php echo $kargo->VOYAGE_IN ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Voyage Out</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="voyage"><?php echo $kargo->VOYAGE_OUT ?></p>
                                </div>
                            </div>
                            
							<div class="form-group">
                                <label class="col-lg-4 control-label">KODE DOK INOUT</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="KD_DOK_INOUT"><?php echo $kargo->KD_DOK ?></p>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-lg-4 control-label">NO. DOK INOUT</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="NO_DOK_INOUT"><?php echo $kargo->ID_TRX ?></p>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-lg-4 control-label">KODE SARANA ANGKUT INOUT</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo $kargo->KD_DOK_INOUT ?></p>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-lg-4 control-label">TGL DOK INOUT</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="TGL_DOK_INOUT"><?php echo $kargo->DT ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Load Port</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="LOAD_PORT"><?php echo $kargo->LOAD_PORT ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Transit Port</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="TRANSIT_PORT"><?php echo $kargo->TRANSIT_PORT ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Discharge Port</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="DISCHARGER_PORT"><?php echo $kargo->DISCHARGER_PORT ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Next Port</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="NEXT_PORT"><?php echo $kargo->NEXT_PORT ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
							<div class="form-group">
                                <label class="col-lg-4 control-label">Inward BC 11 Number</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="INWARD_BC11"><?php echo $kargo->INWARD_BC11 ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Inward BC 11 Date</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="INWARD_BC11_DATE"><?php echo $kargo->INWARD_BC11_DATE ?></p>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-lg-4 control-label">Outward BC 11 Number</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="OUTWARD_BC11"><?php echo $kargo->OUTWARD_BC11 ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Outward BC 11 Date</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="OUTWARD_BC11_DATE"><?php echo $kargo->OUTWARD_BC11_DATE ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">POS BC 11 Number</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id=""></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">NPWP</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="CONSIGNEE_TAX_REF"><?php echo $kargo->CONSIGNEE_TAX_REF ?></p>
                                </div>
                            </div>
							<div class="form-group">
                                <label class="col-lg-4 control-label">ID Consignee</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="CONSIGNEE_ID"><?php echo $kargo->CONSIGNEE_ID ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Consignee</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="CONSIGNEE_NAME"><?php echo $kargo->CONSIGNEE_NAME ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Type Cargo</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="TYPE_CARGO"><?php echo $kargo->TYPE_CARGO ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Model</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="MODEL_NAME"><?php echo $kargo->MODEL_NAME ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Maker</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="MAKE_NAME"><?php echo $kargo->MAKE_NAME ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">BL Number</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="BL_NUMBER"><?php echo $kargo->BL_NUMBER ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">BL Date</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="BL_NUMBER_DATE"><?php echo $kargo->BL_NUMBER_DATE ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Customs Number</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="CUSTOMS_NUMBER"><?php echo $kargo->CUSTOMS_NUMBER ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Customs Date</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="CUSTOMS_NO"><?php echo $kargo->CUSTOMS_DATE ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">On Terminal Date</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="DTS_ONTERMINAL"><?php echo $kargo->DTS_ONTERMINAL ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Left Date</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="DTS_LEFT"><?php echo $kargo->DTS_LEFT ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Police Number</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="NUMBER_POLICE"><?php echo $kargo->NUMBER_POLICE ?></p>
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

            });
        </script>
    </body>
</html>