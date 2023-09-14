<!DOCTYPE html>
<html lang="id">
    <head>
        <?php $this->load->view('backend/elements/basic_head') ?>
        <link href="<?php echo base_url('assets/datatables/datatables.min.css') ?>" rel="stylesheet">
        <style>
        .modal-lg {
            width: 900px;
        }
            </style>
    </head>

    <body>
        <div id="wrap">
            <?php $this->load->view('backend/components/header') ?>

            <div class="container">

                <h2>Detail Notifikasi</h2>
                <p class="lead">
                    <small>Visit ID: <?php echo $data->VISIT_ID ?></small>
                </p>
                <div class="row">
                    <div class="col-md-6 table-scrollable table-responsive">
                        <table class="table table-bordered table-condensed table-advance">
                            <tbody>
                                <tr>
                                    <td class="text-left" width="30%" style="background-color: #f5f5f5;">Nama Kapal</td>
                                    <td class="text-left"><?php echo $data->VISIT_NAME ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left" width="30%" style="background-color: #f5f5f5;">Voyage IN</td>
                                    <td class="text-left"><?php echo $data->VOYAGE_IN ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left" width="30%" style="background-color: #f5f5f5;">Voyage OUT</td>
                                    <td class="text-left"><?php echo $data->VOYAGE_OUT ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6 table-scrollable table-responsive">
                        <table class="table table-bordered table-condensed table-advance">
                            <tbody>
                                <tr>
                                    <td class="text-left" width="30%" style="background-color: #f5f5f5;">Tiba <sup>1</sup></td>
                                    <td class="text-left"><?php echo $data->ETA ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left" width="30%" style="background-color: #f5f5f5;">Berangkat <sup>1</sup></td>
                                    <td class="text-left"><?php echo $data->ETD ?></td>
                                </tr>
                                <tr>
                                    <td class="text-left" width="30%" style="background-color: #f5f5f5;">Visit Status</td>
                                    <td class="text-left">
                                        <?php
                                            if ($data->VESSEL_STATUS == 0) {
                                                echo "ANNOUNCED";
                                            } else if ($data->VESSEL_STATUS == 2) {
                                                echo "ARRIVED";
                                            } else if ($data->VESSEL_STATUS == 3) {
                                                echo "OPERATIONAL";
                                            } else if ($data->VESSEL_STATUS == 4) {
                                                echo "COMPLETED";
                                            } else if ($data->VESSEL_STATUS == 5) {
                                                echo "LEFT";
                                            } else {
                                                echo "DELETED";
                                            }
                                        ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="table-responsive">
                            <table id="summaryBL" class="table table-striped table-bordered table-condensed">
                                <thead>
                                    <tr>
                                        <th>No</th>
                                        <th>Nomor BL</th>
                                        <th>Tanggal BL</th>
                                        <th>Customs No.</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php
                                    if(count($cargo) > 0) {
                                        $no = 1;
                                        foreach($cargo as $bl => $rows) {

                                ?>
                                <tr>
                                    <td><?php echo $no ?></td>
                                    <td>
                                        <a href="#" data-toggle="modal" data-target="#row<?php echo $no ?>"><?php echo (!empty($bl)) ? $rows['databl']['BL_NUMBER'] : 'Kosong' ?></a>
                                        <div class="modal fade" id="row<?php echo $no ?>" tabindex="-1" role="dialog" aria-hidden="true">
                                            <div class="modal-dialog modal-lg">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                                                        <h4 class="modal-title">Data Kargo Nomor BL: <?php echo $rows['databl']['BL_NUMBER'] ?></h4>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="table-responsive">
                                                            <table class="table table-striped table-condensed detailkargo">
                                                                <thead>
                                                                    <tr>
                                                                        <th>No</th>
                                                                        <th>VIN</th>
                                                                        <th>Model</th>
                                                                        <th>Direction</th>
                                                                        <th>No. PEB</th>
                                                                        <th>Tanggal PEB</th>
                                                                    </tr>
                                                                </thead>
                                                                <tbody>
                                                                    <?php
                                                                        if(count($rows['datakargo']) > 0) {
                                                                            $i = 1;
                                                                            foreach($rows['datakargo'] as $obj) {


                                                                    ?>
                                                                    <tr>
                                                                        <td><?php echo $i ?></td>
                                                                        <td><?php echo $obj->VIN ?></td>
                                                                        <td><?php echo $obj->MODEL_NAME ?></td>
                                                                        <td><?php echo $obj->DIRECTION ?></td>
                                                                        <td><?php echo $obj->CUSTOMS_NUMBER ?></td>
                                                                        <td><?php echo $obj->CUSTOMS_DATE ?></td>
                                                                    </tr>
                                                                    <?php
                                                                                $i++;

                                                                            }
                                                                        }
                                                                    ?>
                                                                </tbody>
                                                            </table>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div
                                    </td>
                                    <td><?php echo (!empty($rows['databl']['BL_NUMBER_DATE'])) ? date('d-M-Y', strtotime($rows['databl']['BL_NUMBER_DATE'])) : '' ?></td>
                                    <td><?php echo $rows['databl']['CUSTOMS_NUMBER'] ?></td>
                                </tr>
                                <?php
                                            $no++;
                                        }
                                    }
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>


                <div class="row">
                    <div class="col-md-12">
                        <br>
                        <p><sup>1</sup> Waktu yang ditampilkan adalah waktu setempat</p>

                        <div class="pull-right">
                            <a href="<?php echo site_url($grid_state) ?>" class="btn btn-default">Kembali</a>
                        </div>
                    </div>
                </div>
            </div><!-- /.container -->
        </div>

        <?php $this->load->view('backend/elements/footer') ?>
        <script src="<?php echo base_url('assets/datatables/datatables.min.js') ?>"></script>
        <script>
            $(function() {
                $('.detailkargo').DataTable();
                $('#summaryBL').DataTable();
            })
        </script>
    </body>
</html>
