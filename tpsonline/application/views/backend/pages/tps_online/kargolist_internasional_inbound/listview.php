<!DOCTYPE html>
<html lang="id">
    <head>
        <?php $this->load->view('backend/elements/basic_head') ?>
        <style>
        @media only screen and (min-width: 1200px) {
            .container {
                max-width: 2000px;
            }
        }
    </style>
    </head>

    <body>
        <div id="wrap">
            <?php $this->load->view('backend/components/header') ?>

            <div class="container">

                <h1>Data Kargo Import</h1>
                <p class="lead">
                    <small></small>
                </p>

                <div class="row ct-listview-toolbar">
                    <div class="col-md-6">
                        <?php $this->load->view('backend/components/searchform') ?>
                    </div>
                    <div class="col-md-6">
                        <div class="pull-right">

                        </div>
                    </div>
                </div>

                <hr />

                <div class="table-responsive">
                    <table class="table table-striped table-condensed">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th><?php echo gridHeader('VIN', 'VIN', $cfg) ?></th>
                                <th><?php echo gridHeader('VISIT_ID', 'Visit ID', $cfg) ?></th>
                                <th><?php echo gridHeader('BL_NUMBER', 'No BL', $cfg) ?></th>
                                <th><?php echo gridHeader('BL_NUMBER_DATE', 'Tanggal BL', $cfg) ?></th>
                                <th><?php echo gridHeader('CONSIGNEE_NAME', 'Penerima', $cfg) ?></th>
                                <th><?php echo gridHeader('LOGISTIC_COMPANY', 'Logistik', $cfg) ?></th>
                                <th><?php echo gridHeader('DTS_ONTERMINAL', 'On Terminal', $cfg) ?></th>
                                <th><?php echo gridHeader('DTS_LOADED', 'Loaded', $cfg) ?></th>
                                <th><?php echo gridHeader('DTS_LEFT', 'Left', $cfg) ?></th>
                                <th>Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grid_state = $cfg->pagingURL . '/p:' . $cfg->currPage;
                            //echo '<pre>';
                            //print_r($datasource);
                            //echo '</pre>';
                            //die;
                            $number = 0;
                            if ($cfg->currPage == 1) {
                                $number = 0;
                            } else {
                                $number = ($cfg->currPage * 10) - 10;
                            }
                            if ($datasource) {
                                foreach ($datasource as $row) {
                                    $number = $number + 1;
                                    ?>
                                    <tr>
                                        <td><?php echo $number ?></td>
                                        <td><?php echo $row->VIN ?></td>
                                        <td><?php echo $row->VISIT_ID ?></td>
                                        <td><?php echo $row->BL_NUMBER ?></td>
                                        <td><?php echo $row->BL_NUMBER_DATE ?></td>
                                        <td><?php echo $row->CONSIGNEE_NAME ?></td>
                                        <td><?php echo $row->LOGISTIC_COMPANY ?></td>
                                        <td><?php echo $row->DTS_ONTERMINAL ? date('d-M-Y', strtotime($row->DTS_ONTERMINAL)) : '-' ?></td>
                                        <td><?php echo $row->DTS_LOADED ? date('d-M-Y', strtotime($row->DTS_LOADED)) : '-' ?></td>
                                        <td><?php echo $row->DTS_LEFT ? date('d-M-Y', strtotime($row->DTS_LEFT)) : '-' ?></td>
                                        <td>
                                            <a href="<?php echo site_url('tps_online/kargolist_internasional_inbound/view/' . $row->VIN . '/' . $grid_state) ?>" class="edit_link">Lihat</a>
                                            <!-- <a href="<?php // echo site_url('tps_online/kargolist_internasional_inbound/edit/' . $row->VIN . '/' . $grid_state) ?>" class="edit_link">Edit</a> -->
                                        </td>
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr><td colspan="9"><em>Tidak ada data</em></td></tr>
                                <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>

                <?php $this->load->view('backend/components/paging') ?>
            </div><!-- /.container -->
        </div>

        <?php $this->load->view('backend/elements/footer') ?>
    </body>
</html>