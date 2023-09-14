<!DOCTYPE html>
<html lang="id">
    <head>
        <?php $this->load->view('backend/elements/basic_head') ?>
    </head>

    <body>
        <div id="wrap">
            <?php $this->load->view('backend/components/header') ?>

            <div class="container">

                <h1>Data Log Pengiriman</h1>
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
                                <!--<th><?php echo gridHeader('VISIT_NAME', 'Nama Kapal', $cfg) ?></th>-->
                                <!--<th><?php echo gridHeader('VOYAGE_IN', 'Voyage In', $cfg) ?></th>-->
                                <!--<th><?php echo gridHeader('VOYAGE_OUT', 'Voyage Out', $cfg) ?></th>-->
                                <!--<th><?php echo gridHeader('ETA', 'ETA', $cfg) ?></th>-->
                                <!--<th><?php echo gridHeader('ETD', 'ETD', $cfg) ?></th>-->
                                <th><?php echo gridHeader('VIN', 'VIN', $cfg) ?></th>
                                <th><?php echo gridHeader('BL_NUMBER', 'BL Number', $cfg) ?></th>
                                <th><?php echo gridHeader('TYPE_CARGO', 'Type', $cfg) ?></th>
                                <th><?php echo gridHeader('DTS_ONTERMINAL', 'On Terminal', $cfg) ?></th>
                                <th><?php echo gridHeader('DTS_LEFT', 'Left', $cfg) ?></th>
                                <!--<th><?php echo gridHeader('FLAG_SEND_COARRI', 'Left', $cfg) ?></th>-->
                                <th><?php echo gridHeader('RESPONSE_COARRI', 'Response On Terminal', $cfg) ?></th>
                                <!--<th><?php echo gridHeader('FLAG_SEND_CODECO', 'Left', $cfg) ?></th>-->
                                <th><?php echo gridHeader('RESPONSE_CODECO', 'Response Left', $cfg) ?></th>
                                <!--<th>Tindakan</th>-->
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grid_state = $cfg->pagingURL . '/p:' . $cfg->currPage;
                            
                            if ($datasource) {
                                foreach ($datasource as $row) {
                                    ?>
                                    <tr>
                                        <!--<td><?php echo $row->VISIT_NAME ?></td>-->
                                        <!--<td><?php echo $row->VOYAGE_IN ?></td>-->
                                        <!--<td><?php echo $row->VOYAGE_OUT ?></td>-->
                                        <!--<td><?php echo date('d-M-Y H:i:s', strtotime($row->ETA)) ?></td>-->
                                        <!--<td><?php echo date('d-M-Y H:i:s', strtotime($row->ETD)) ?></td>-->
                                        <td><?php echo $row->VIN ?></td>
                                        <td><?php echo $row->BL_NUMBER ?></td>
                                        <td><?php echo $row->TYPE_CARGO ?></td>
                                        <td><?php echo $row->DTS_ONTERMINAL != "" ? date('d-M-Y H:i:s', strtotime($row->DTS_ONTERMINAL)) : "" ?></td>
                                        <td><?php echo $row->DTS_LEFT != "" ? date('d-M-Y H:i:s', strtotime($row->DTS_LEFT)) : "" ?></td>
                                        <td>
                                            <?php echo $row->RESPONSE_COARRI ?>
                                            <?php
                                            if ($row->FLAG_SEND_COARRI != '0') {
                                                echo '<br>';
                                                echo '<a href="">Kirim Ulang</a>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php echo $row->RESPONSE_CODECO ?>
                                            <?php
                                            if ($row->FLAG_SEND_CODECO != '0') {
                                                echo '<br>';
                                                echo '<a href="">Kirim Ulang</a>';
                                            }
                                            ?>
                                        </td>
        <!--                                        <td>
                                            <a href="<?php echo site_url('tps_online/kunjungan_kapal/view/' . $row->VISIT_ID . '/' . $grid_state) ?>" class="edit_link">Edit</a>
                                        </td>-->
                                    </tr>
                                    <?php
                                }
                            } else {
                                ?>
                                <tr><td colspan="7"><em>Tidak ada data</em></td></tr>
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