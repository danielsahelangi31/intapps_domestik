<!DOCTYPE html>
<html lang="id">
    <head>
        <?php $this->load->view('backend/elements/basic_head') ?>
        <style type="text/css">
            .error_row{
                cursor:pointer;
            }
        </style>
    </head>

    <body>
        <div id="wrap">
            <?php $this->load->view('backend/components/header') ?>

            <div class="container">

                <div class="row">
                    <div class="col-md-8">
                        <h2>Unggah Manifest Baru</h2>
                    </div>
                    <div class="col-md-4">
                        <div class="pull-right back_list">

                        </div>
                    </div>
                </div>

                <?php
                if (isset($parser_error)) {
                    ?>
                    <div class="alert alert-danger fade in">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4><?php echo isset($error_header) ? $error_header : 'Hmmm.. Sepertinya Berkas yang Anda Kirim Salah' ?></h4>
                        <p><?php echo $parser_error ?></p>

                        <p>
                            <a href="<?php echo base_url('assets/documents/Format Manifest Upload ILCS - V5.xlsx') ?>" class="btn btn-danger">Unduh Contoh Berkas</a>
                            <a href="<?php echo site_url('tps_online/internasional_inbound/add') ?>" class="btn btn-default">Mulai Lagi Dari Awal</a>
                        </p>
                    </div>
                    <?php
                }
                ?>

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
                if (isset($success_msg)) {
                    ?>
                    <div class="alert alert-success fade in">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                        <h4>Terima Kasih!</h4>
                        <p><?php echo $success_msg ?></p>
                    </div>
                    <?php
                }
                ?>

                <?php echo form_open_multipart(null, array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
                <div class="row">
                    <div class="col-lg-12">
                        <fieldset class="delivery-request-border">
                            <legend class="fieldset-bordered">Isian Manifest</legend>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Berkas Manifest *</label>
                                <div class="col-lg-8">
                                    <div class="input-group">
                                        <span class="input-group-btn">
                                            <span class="btn btn-primary btn-file">
                                                Unggah Berkas&hellip; <input type="file" name="manifest_file">
                                            </span>
                                        </span>
                                        <input type="text" class="form-control" readonly="readonly">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Koresponden Shipping Agent di Pelabuhan Tujuan</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="username_correspondent" name="username_correspondent" placeholder="Isikan username koresponden" value="" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label"></label>
                                <div class="col-lg-8">
                                    <p class="form-control-static"><a href="<?php echo base_url('assets/documents/Format Manifest Upload ILCS - V5.xlsx') ?>">Unduh Contoh</a></p>
                                </div>
                            </div>
                        </fieldset>
                    </div>
                </div>

                <?php
                if (isset($result) && $result->status == false) {
                    ?>
                    <div class="row">
                        <div class="col-lg-12">
                            <fieldset class="delivery-request-border">
                                <legend class="delivery-request-border">Rincian</legend>

                                <p><em>Silakan periksa kembali hal-hal yang masih belum memenuhi standar dalam dokumen manifest.</em></p>

                                <!-- Nav tabs -->
                                <ul class="nav nav-tabs">
                                    <li class="active"><a href="#informasi_umum" data-toggle="tab">Informasi Umum <span class="badge"><?php echo isset($result->informasi_kapal) ? count($result->informasi_kapal->errors) : 'T/A' ?></span></a></li>
                                    <li><a href="#containers" data-toggle="tab">Container <span class="badge"><?php echo isset($result->containers) ? count($result->containers->errors) : 'T/A' ?></span></a></li>
                                    <li><a href="#consignments" data-toggle="tab">Consignment <span class="badge"><?php echo isset($result->consignments) ? count($result->consignments->errors) : 'T/A' ?></span></a></li>
                                    <li><a href="#packages" data-toggle="tab">Packages <span class="badge"><?php echo isset($result->packages) ? count($result->packages->errors) : 'T/A' ?></span></a></li>
                                </ul>

                                <!-- Tab panes -->
                                <div class="tab-content">
                                    <div class="tab-pane fade in active" id="informasi_umum">
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:50px;">Diperiksa</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="informasi_kapal_landing">
                                                        <?php
                                                        if (isset($result->informasi_kapal)) {
                                                            if ($result->informasi_kapal->errors) {
                                                                foreach ($result->informasi_kapal->errors as $err_msg) {
                                                                    ?>
                                                                    <tr class="error_row">
                                                                        <td><input type="checkbox" class="checker"></td>
                                                                        <td><?php echo $err_msg ?></td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            } else {
                                                                ?>
                                                                <tr><td colspan="2">Tidak ditemukan adanya error dalam bagian ini</td></tr>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <tr><td colspan="2">Error tidak dicek karena sheet tidak ditemukan</td></tr>
                                                            <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="containers">
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:50px;">Diperiksa</th>
                                                            <th>Baris</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="containers_landing">
                                                        <?php
                                                        if (isset($result->containers)) {
                                                            if ($result->containers->errors) {
                                                                foreach ($result->containers->errors as $rownum => $errors) {
                                                                    ?>
                                                                    <tr class="error_row">
                                                                        <td><input type="checkbox" class="checker"></td>
                                                                        <td><?php echo $rownum ?></td>
                                                                        <td>
                                                                            <ul>
                                                                                <?php
                                                                                foreach ($errors as $err_msg) {
                                                                                    ?>
                                                                                    <li><?php echo $err_msg ?></li>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </ul>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            } else {
                                                                ?>
                                                                <tr><td colspan="3">Tidak ditemukan adanya error dalam bagian ini</td></tr>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <tr><td colspan="3">Error tidak dicek karena sheet tidak ditemukan</td></tr>
                                                            <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="consignments">
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:50px;">Diperiksa</th>
                                                            <th>Baris</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="consignment_landing">
                                                        <?php
                                                        if (isset($result->consignments)) {
                                                            if ($result->consignments->errors) {
                                                                foreach ($result->consignments->errors as $rownum => $errors) {
                                                                    ?>
                                                                    <tr class="error_row">
                                                                        <td><input type="checkbox" class="checker"></td>
                                                                        <td><?php echo $rownum ?></td>
                                                                        <td>
                                                                            <ul>
                                                                                <?php
                                                                                foreach ($errors as $err_msg) {
                                                                                    ?>
                                                                                    <li><?php echo $err_msg ?></li>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </ul>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            } else {
                                                                ?>
                                                                <tr><td colspan="3">Tidak ditemukan adanya error dalam bagian ini</td></tr>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <tr><td colspan="3">Error tidak dicek karena sheet tidak ditemukan</td></tr>
                                                            <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-pane fade" id="packages">
                                        <div class="col-lg-12">
                                            <div class="row">
                                                <table class="table table-bordered table-hover">
                                                    <thead>
                                                        <tr>
                                                            <th style="width:50px;">Diperiksa</th>
                                                            <th>Baris</th>
                                                            <th>Keterangan</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="packages_landing">
                                                        <?php
                                                        if (isset($result->packages)) {
                                                            if ($result->packages->errors) {
                                                                foreach ($result->packages->errors as $rownum => $errors) {
                                                                    ?>
                                                                    <tr class="error_row">
                                                                        <td><input type="checkbox" class="checker"></td>
                                                                        <td><?php echo $rownum ?></td>
                                                                        <td>
                                                                            <ul>
                                                                                <?php
                                                                                foreach ($errors as $err_msg) {
                                                                                    ?>
                                                                                    <li><?php echo $err_msg ?></li>
                                                                                    <?php
                                                                                }
                                                                                ?>
                                                                            </ul>
                                                                        </td>
                                                                    </tr>
                                                                    <?php
                                                                }
                                                            } else {
                                                                ?>
                                                                <tr><td colspan="3">Tidak ditemukan adanya error dalam bagian ini</td></tr>
                                                                <?php
                                                            }
                                                        } else {
                                                            ?>
                                                            <tr><td colspan="3">Error tidak dicek karena sheet tidak ditemukan</td></tr>
                                                            <?php
                                                        }
                                                        ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </fieldset>					
                        </div>
                    </div>
                    <?php
                }
                ?>

                <div class="row">
                    <div class="col-lg-12">
                        <div class="pull-right">
                            <button class="btn btn-primary fr" type="submit" name="simpan" id="simpan" value="1"><span class="glyphicon glyphicon-floppy-disk"></span> Simpan</button>
                            <a class="btn btn-default fr" href="<?php echo site_url('tps_online/kunjungan_kapal') ?>">Kembali</a>
                        </div>
                    </div>
                </div>
                <?php echo form_close() ?>

            </div><!-- /.containers -->
        </div>

        <?php $this->load->view('backend/elements/footer') ?>

        <script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>


        <script type="text/javascript">
            $(document)
                    .on('change', '.btn-file :file', function () {
                        var input = $(this),
                                numFiles = input.get(0).files ? input.get(0).files.length : 1,
                                label = input.val().replace(/\\/g, '/').replace(/.*\//, '');
                        input.trigger('fileselect', [numFiles, label]);
                    });

            $(document).ready(function () {
                $('.btn-file :file').on('fileselect', function (event, numFiles, label) {
                    var input = $(this).parents('.input-group').find(':text'),
                            log = numFiles > 1 ? numFiles + ' files selected' : label;

                    if (input.length) {
                        input.val(log);
                    } else {
                        if (log)
                            alert(log);
                    }

                });

                $('.error_row').click(function () {
                    var target = $(this).find('.checker');

                    if ($(target).is(':checked')) {
                        $(this).removeClass('success');
                        $(target).prop('checked', false);
                    } else {
                        $(this).addClass('success');
                        $(target).prop('checked', true);
                    }
                });

                $('.checker').change(function (e) {
                    if ($(this).is(':checked')) {
                        $(this).parent().parent().addClass('success');
                    } else {
                        $(this).parent().parent().removeClass('success');
                    }

                    e.stopPropagation();
                });

                initialize();
            });


        </script>
    </body>
</html>