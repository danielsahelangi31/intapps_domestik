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
                        <h2>Edit Data Kargo Import</h2>
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
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
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
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">�</button>
                        <h4><?php echo $info_msg ?></h4>
                    </div>
                    <?php
                }
                ?>

                <?php echo form_open(NULL, array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>

                <fieldset class="delivery-request-border">

                    <?php echo form_open('#', array('id' => 'main_form', 'role' => 'form', 'class' => 'form-horizontal')) ?>
                    <legend class="delivery-request-border">Data Kargo</legend>			
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="form-group">
                                <label class="col-lg-4 control-label">VIN</label>
                                <div class="col-lg-8">
                                    <p class="form-control-static"><?php echo $kargo->VIN ?></p>
                                    <input type="hidden" class="form-control" id="VIN" name="VIN" value="<?php echo $kargo->VIN ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Visit ID</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="VISIT_ID" name="VISIT_ID" value="<?php echo $kargo->VISIT_ID ?>" />

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">No. PIB</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="CUSTOMS_NUMBER" name="CUSTOMS_NUMBER" value="<?php echo $kargo->CUSTOMS_NUMBER ?>" />

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Tanggal PIB</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control date" id="CUSTOMS_DATE" name="CUSTOMS_DATE" value="<?php echo date("d-m-Y", strtotime($kargo->CUSTOMS_DATE)); ?>" />

                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Consignee</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="CONSIGNEE_NAME" name="CONSIGNEE_NAME" value="<?php echo $kargo->CONSIGNEE_NAME ?>" />

                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6">

                            <div class="form-group">
                                <label class="col-lg-4 control-label">Type Cargo</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="TYPE_CARGO" name="TYPE_CARGO" value="<?php echo $kargo->TYPE_CARGO ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Model</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="MODEL_NAME" name="MODEL_NAME" value="<?php echo $kargo->MODEL_NAME ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Maker</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="MAKE_NAME" name="MAKE_NAME" value="<?php echo $kargo->MAKE_NAME ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">BL Number</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control" id="BL_NUMBER" name="BL_NUMBER" value="<?php echo $kargo->BL_NUMBER ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">BL Date</label>
                                <div class="col-lg-8">
                                    <input type="text" class="form-control date" id="BL_NUMBER_DATE" name="BL_NUMBER_DATE" value="<?php echo $kargo->BL_NUMBER_DATE ?>" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-4 control-label">Keterangan</label>
                                <div class="col-lg-8">
                                    <textarea type="text" class="form-control" id="KETERANGAN" name="KETERANGAN" value="" ></textarea>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="pull-right">
                        <div class="btn ajax-load" id="simpan_load" style="display:none"></div>
                        <a href="#" class="btn btn-primary" id="simpan">Simpan</a>
                    </div>
                    <?php echo form_close() ?>
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
        <script src="<?php echo base_url('assets/js/typeahead.modified.js') ?>"></script>
        <script type="text/javascript" src="<?php echo base_url('assets/js/smartcargoux.js') ?>"></script>
        <script>
            $(document).ready(function () {
                /**$( "#ORGANIZATION" ).keypress(function() {
                 $('#ORGANIZATION').val (function () {
                 return this.value.toUpperCase();
                 });
                 });*/

                function unbubble_event() {
                    $(this).unbind('click');
                    return false;
                }

                function auto_remove_popover_on_change() {
                    $(this).popover('destroy');
                    $(this).parent().removeClass('has-error');

                    $(this).unbind('change', auto_remove_popover_on_change);
                }

                function add_validation_popover(selector, msg, position) {
                    if (typeof (position) === 'undefined') {
                        position = 'right';
                    }

                    $(selector).popover('destroy');

                    $(selector).popover({
                        'content': msg,
                        'placement': 'auto ' + position,
                        'trigger': 'focus'
                    });

                    $(selector).popover('show');
                    $(selector).change(auto_remove_popover_on_change);
                }

                function destroy_all_validation_popovers() {
                    $('.has-error').find('input, select').popover('destroy');
                    $('.has-error').removeClass('has-error');
                }




                $('#simpan').click(function () {

                    var is_error = false;

                    var param = {
                        'VIN': $('#VIN').val(),
                        'VISIT_ID': $('#VISIT_ID').val(),
                        'CUSTOMS_NUMBER': $('#CUSTOMS_NUMBER').val(),
                        'CUSTOMS_DATE': $('#CUSTOMS_DATE').val(),
                        'BL_NUMBER': $('#BL_NUMBER').val(),
                        'BL_NUMBER_DATE': $('#BL_NUMBER_DATE').val(),
                        'KETERANGAN': $('#KETERANGAN').val(),
                    }




                    console.log(param);

                    if (!param.VISIT_ID || param.VISIT_ID == "") {
                        $('#VISIT_ID').parent().addClass('has-error');
                        add_validation_popover('#VISIT_ID', 'VISIT_ID Harus diisi');

                        is_error = true;
                    }

                    if (!param.CUSTOMS_NUMBER || param.CUSTOMS_NUMBER == "") {
                        $('#CUSTOMS_NUMBER').parent().addClass('has-error');
                        add_validation_popover('#CUSTOMS_NUMBER', 'CUSTOMS_NUMBER Harus diisi');

                        is_error = true;
                    }

                    if (!param.CUSTOMS_DATE || param.CUSTOMS_DATE == "") {
                        $('#CUSTOMS_DATE').parent().addClass('has-error');
                        add_validation_popover('#CUSTOMS_DATE', 'CUSTOMS_DATE Harus diisi');

                        is_error = true;
                    }
                    if (!param.BL_NUMBER || param.BL_NUMBER == "") {
                        $('#BL_NUMBER').parent().addClass('has-error');
                        add_validation_popover('#BL_NUMBER', 'BL_NUMBER Harus diisi');

                        is_error = true;
                    }
                    if (!param.BL_NUMBER_DATE || param.BL_NUMBER_DATE == "") {
                        $('#BL_NUMBER_DATE').parent().addClass('has-error');
                        add_validation_popover('#BL_NUMBER_DATE', 'BL_NUMBER_DATE Harus diisi');

                        is_error = true;
                    }

                    if (!param.KETERANGAN || param.KETERANGAN == "") {
                        $('#KETERANGAN').parent().addClass('has-error');
                        add_validation_popover('#KETERANGAN', 'KETERANGAN Harus diisi');

                        is_error = true;
                    }

                    if (is_error) {
                        sc_alert('Validation Error', 'Harap perbaiki field yang ditandai');
                    } else {
                        $('#simpan_load').show();

                        var url = bs.siteURL + 'tps_online/kargolist_internasional_inbound/update/' + bs.token;

                        $.post(url, param, function (data) {
                            $('#simpan_load').hide();

                            if (data.success) {
                                sc_alert('Sukses', data.msg);
                                reset_form();
                            } else {
                                sc_alert('ERROR', data.msg);
                            }
                        }, 'json');
                    }

                    return false;
                });
                function reset_form() {

                }

                initialize();

            });
        </script>
    </body>
</html>