<!DOCTYPE html>
<html lang="id">
    <head>
        <?php $this->load->view('backend/elements/basic_head') ?>
    </head>

    <body>
        <div id="wrap">
            <?php $this->load->view('backend/components/header') ?>

            <div class="container">

                <h1>Data Kunjungan Kapal Export</h1>
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
                                <th><?php echo gridHeader('VISIT_ID', 'VISIT_ID', $cfg) ?></th>
                                <th><?php echo gridHeader('VISIT_NAME', 'Nama Kapal', $cfg) ?></th>
                                <!--<th><?php echo gridHeader('VOYAGE_IN', 'Voyage In', $cfg) ?></th>-->
                                <th><?php echo gridHeader('VOYAGE_OUT', 'Voyage Out', $cfg) ?></th>
                                <!--<th><?php echo gridHeader('ETA', 'ETA', $cfg) ?></th>-->
                                <th><?php echo gridHeader('ETD', 'ETD', $cfg) ?></th>
                                <!--<th><?php echo gridHeader('VISIT_DIRECTION', 'Visit Direction', $cfg) ?></th>-->
                                <th><?php echo gridHeader('VESSEL_STATUS', 'Visit Status', $cfg) ?></th>
                                <th>Tindakan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grid_state = $cfg->pagingURL . '/p:' . $cfg->currPage;

                            if ($datasource) {
                                foreach ($datasource as $row) {
                                    ?>
                                    <tr>
                                        <td><?php echo $row->VISIT_ID ?></td>
                                        <td><?php echo $row->VISIT_NAME ?></td>
                                        <!--<td><?php echo $row->VOYAGE_IN ?></td>-->
                                        <td><?php echo $row->VOYAGE_OUT ?></td>
                                        <!--<td><?php echo date('d-M-Y H:i:s', strtotime($row->ETA)) ?></td>-->
                                        <td><?php echo date('d-M-Y H:i:s', strtotime($row->ETD)) ?></td>
        <!--                                        <td><?php
                                        if ($row->VISIT_DIRECTION == 1) {
                                            echo "INTERNATIONAL";
                                        } else if ($row->VISIT_DIRECTION == 2) {
                                            echo "DOMESTIC";
                                        } else {
                                            echo "";
                                        }
                                        ?>
                                        </td>-->
                                        <td><?php
                                            if ($row->VESSEL_STATUS == 0) {
                                                echo "ANNOUNCED";
                                            } else if ($row->VESSEL_STATUS == 2) {
                                                echo "ARRIVED";
                                            } else if ($row->VESSEL_STATUS == 3) {
                                                echo "OPERATIONAL";
                                            } else if ($row->VESSEL_STATUS == 4) {
                                                echo "COMPLETED";
                                            } else if ($row->VESSEL_STATUS == 5) {
                                                echo "LEFT";
                                            } else {
                                                echo "DELETED";
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="<?php echo site_url('tps_online/internasional_outbound/view/' . $row->VISIT_ID . '/' . $grid_state) ?>" class="edit_link">Edit</a>
                                        </td>
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