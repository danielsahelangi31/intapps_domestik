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
                        <h2>Edit Data Kunjungan Kapal Export</h2>
                    </div>
                    <div class="col-md-4">
                        <div class="pull-right back_list">

                        </div>
                    </div>
                </div>

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
                    <legend class="delivery-request-border">Data Kunjungan</legend>			
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Visit ID</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static"><?php echo $kunjungan->VISIT_ID ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Nama Kapal</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static"><?php echo $kunjungan->VISIT_NAME ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Tiba<sup>1</sup> / Berangkat<sup>1</sup></label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="pol_pod"><?php echo date('d M Y H:i', strtotime($kunjungan->ETA)) . ' / ' . date('d M Y H:i', strtotime($kunjungan->ETD)) ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Voyage In</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="voyage_in"><?php echo $kunjungan->VOYAGE_IN ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Voyage Out</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="voyage_out"><?php echo $kunjungan->VOYAGE_OUT ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Visit Direction</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="visit_direction"><?php
                                        if ($kunjungan->VISIT_DIRECTION == 1) {
                                            echo "INTERNATIONAL";
                                        } else if ($kunjungan->VISIT_DIRECTION == 2) {
                                            echo "DOMESTIC";
                                        } else {
                                            echo "";
                                        }
                                        ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Visit Status</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="vessel_status"><?php
                                        if ($kunjungan->VESSEL_STATUS == 0) {
                                            echo "ANNOUNCED";
                                        } else if ($kunjungan->VESSEL_STATUS == 2) {
                                            echo "ARRIVED";
                                        } else if ($kunjungan->VESSEL_STATUS == 3) {
                                            echo "OPERATIONAL";
                                        } else if ($kunjungan->VESSEL_STATUS == 4) {
                                            echo "COMPLETED";
                                        } else if ($kunjungan->VESSEL_STATUS == 5) {
                                            echo "LEFT";
                                        } else {
                                            echo "DELETED";
                                        }
                                        ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <!--                            <div class="form-group">
                                                            <label class="col-lg-4 control-label">Nomor Inward BC 1.1</label>
                                                            <div class="col-lg-8">
                                                                <input type="text" class="form-control" name="INWARD_BC11" value="<?php echo $kunjungan->INWARD_BC11 ?>" />
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="col-lg-4 control-label">Tanggal Inward BC 1.1</label>
                                                            <div class="col-lg-8">
                                                                <input type="text" class="form-control date" name="INWARD_BC11_DATE" value="<?php echo $kunjungan->INWARD_BC11_DATE ?>" />
                                                            </div>
                                                        </div>-->
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Nomor Outward BC 1.1</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="OUTWARD_BC11" value="<?php echo $kunjungan->OUTWARD_BC11 ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Tanggal Outward BC 1.1</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control date" name="OUTWARD_BC11_DATE" value="<?php echo $kunjungan->OUTWARD_BC11_DATE ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Load Port</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="LOAD_PORT" value="<?php echo $kunjungan->LOAD_PORT ?>" maxlength="5" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Transit Port</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="TRANSIT_PORT" value="<?php echo $kunjungan->TRANSIT_PORT ?>" maxlength="5" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Discharge Port</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" name="DISCHARGER_PORT" value="<?php echo $kunjungan->DISCHARGER_PORT ?>" maxlength="5" />
                                </div>
                            </div>
                            <!--<div class="form-group">
                                    <label class="col-lg-4 control-label">Next Port</label>
                                    <div class="col-lg-8">
                                            <input type="text" class="form-control" name="NEXT_PORT" value="<?php echo $kunjungan->NEXT_PORT ?>" maxlength="5" />
                                    </div>
                            </div>-->
                        </div>
                    </div>
                </fieldset>

                <p><sup>1</sup> Waktu yang ditampilkan adalah waktu setempat</p>

                <div class="row">
                    <div class="col-lg-6">


        <!--<a href="<?php echo site_url('tps_online/internasional_outbound/finalize/' . $kunjungan->VISIT_ID) ?>" class="btn btn-success"><span class="glyphicon glyphicon-check"></span> Finalize Visit</a>-->
                        <a href="<?php echo site_url('tps_online/kargo_internasional_outbound/listview/sf:VISIT_ID/kw:' . $kunjungan->VISIT_ID) ?>" class="btn btn-success"><span class="glyphicon glyphicon-list-alt"></span> Lihat Daftar Kargo</a>
                        <a href="<?php echo site_url('tps_online/consignment/assign_bl/' . $kunjungan->VISIT_ID) ?>" class="btn btn-danger"><span class="glyphicon glyphicon-random"></span> Map Bill of Lading</a>
                    </div>
                    <div class="col-lg-6">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-primary">Simpan</button>
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