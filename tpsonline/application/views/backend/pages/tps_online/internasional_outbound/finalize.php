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
                        <h2>Finalize Data Kunjungan Kapal Export</h2>
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
                                    <p class="form-control-static" id="voyage"><?php echo $kunjungan->VOYAGE_IN ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Voyage Out</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="voyage"><?php echo $kunjungan->VOYAGE_OUT ?></p>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Nomor BC 1.1</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="bc_number"><?php echo $kunjungan->BC_NUMBER ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Tanggal BC 1.1</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="bc_date_number"><?php echo $kunjungan->BC_DATE_NUMBER ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Load Port</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="load_port"><?php echo $kunjungan->LOAD_PORT ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Transit Port</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="transit_port"><?php echo $kunjungan->TRANSIT_PORT ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Discharge Port</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="discharger_port"><?php echo $kunjungan->DISCHARGER_PORT ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Next Port</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static" id="next_port"><?php echo $kunjungan->NEXT_PORT ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </fieldset>

                <p><sup>1</sup> Waktu yang ditampilkan adalah waktu setempat</p>

                <fieldset class="delivery-request-border">
                    <legend class="delivery-request-border">Cargo Belum Terkirim</legend>
                    <div class="row">
                        <div class="col-lg-12">
                            <table class="table table-bordered table-hover table-striped">
                                <thead>
                                    <tr>
                                        <th>VIN</th>
                                        <th>BL Number</th>
                                        <th>BL Date</th>
                                        <th>Consignee</th>
                                        <th>Owner</th>
                                        <th>On Terminal</th>
                                        <th>Loaded</th>
                                        <th>Left</th>
                                        <th>Tindakan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    foreach ($unsent as $row) {
                                        ?>
                                        <tr>
                                            <td><?php echo $row->VIN ?></td>
                                            <td><?php echo $row->BL_NUMBER ?></td>
                                            <td><?php echo $row->BL_NUMBER_DATE ?></td>
                                            <td><?php echo $row->CONSIGNEE_NAME ?></td>
                                            <td><?php echo $row->OWNER_NAME ?></td>
                                            <td><?php echo $row->DTS_ONTERMINAL ? date('d-M-Y', strtotime($row->DTS_ONTERMINAL)) : '-' ?></td>
                                            <td><?php echo $row->DTS_LOADED ? date('d-M-Y', strtotime($row->DTS_LOADED)) : '-' ?></td>
                                            <td><?php echo $row->DTS_LEFT ? date('d-M-Y', strtotime($row->DTS_LEFT)) : '-' ?></td>
                                            <td>
                                                <a href="<?php echo site_url('tps_online/kargo_internasional_outbound/view/' . $row->VIN) ?>" class="edit_link">Lihat</a>
                                            </td>
                                        </tr>
                                        <?php
                                    }
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </fieldset>

                <div class="row">
                    <div class="col-lg-6">
                        <a href="<?php echo site_url('tps_online/internasional_outbound/view/' . $kunjungan->VISIT_ID) ?>" class="btn btn-success"><span class="glyphicon glyphicon-folder-open"></span> Lihat Data Kunjungan</a>
                        <a href="<?php echo site_url('tps_online/consignment/assign_bl/' . $kunjungan->VISIT_ID) ?>" class="btn btn-danger"><span class="glyphicon glyphicon-random"></span> Map Bill of Lading</a>
                    </div>
                    <div class="col-lg-6">
                        <div class="pull-right">
                            <button type="submit" class="btn btn-primary">Finalize</button>
                            <a href="<?php echo site_url('tps_online/internasional_outbound/listview') ?>" class="btn btn-default">Kembali</a>
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