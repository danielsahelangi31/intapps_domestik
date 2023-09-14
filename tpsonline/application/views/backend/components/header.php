<?php
$auth = $this->userauth->getLoginData();
?>
<div class="navbar navbar-inverse navbar-fixed-top">
    <div class="container">
        <div class="navbar-header">
            <button class="navbar-toggle" data-target=".navbar-collapse" data-toggle="collapse" type="button">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>

            <a class="navbar-brand" href="<?php echo in_array('ETICKET', explode('|', $auth->roles)) ? site_url('dashboard_eticket') : site_url('dashboard') ?>" style="padding:10px 20px 8px 10px"><img src="<?php echo base_url('assets/img/smartcargo/int_logo.png') ?>" alt="SmartCargo" /></a>
        </div>

        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <?php
                if ($auth->administrator_id) {
                    $roles = explode('|', $auth->roles);
                    if (in_array('BEACUKAI', $roles)) {
                        if (site_url('dashboard') == current_url()) {
                            header("location: " . site_url('dashboard/cargo'));
                        }
                    } elseif (in_array('ETICKET', $roles)) {
                        ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">E-Ticket<b class="caret"></b></a>
                            <ul class="dropdown-menu">
<!--                                <li><a href="--><?php //echo site_url('eticket/truck_id_list') ?><!--">Truck ID List</a></li>-->
<!--                                <li><a href="--><?php //echo site_url('eticket/asosiasi_truck') ?><!--">Asosiasi Truck</a></li>-->
                                <li><a href="<?php echo site_url('eticket/eticket_list') ?>">E-Ticket List</a></li>
                                <li><a href="<?php echo site_url('eticket/update_vin') ?>">Update VIN</a></li>
                                <li><a href="<?php echo site_url('eticket/return_cargo/request_return') ?>">Return Cargo</a></li>
                                <?php
                                if($auth->sender == 'IKT'){
                                    ?>
                                    <li><a href="<?php echo site_url('eticket/return_cargo/approval_return_cargo') ?>">Approval Return Cargo</a></li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Create Announce<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo site_url('eticket/announce_truck') ?>">Truck</a></li>
                                <li><a href="<?php echo site_url('eticket/announce_vin') ?>">VIN</a></li>
                                <li><a href="<?php echo site_url('eticket/selfdrive') ?>">Selfdrive</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Master<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo site_url('eticket/master_data_truck') ?>">Data Truck</a></li>
                                <li><a href="<?php echo site_url('eticket/master_document') ?>">Data Dokumen</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Cek Asosiasi<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo site_url('eticket/asosiasi_by_truck_code') ?>">By Truck Code</a></li>
                                <li><a href="<?php echo site_url('eticket/asosiasi_by_vin') ?>">By VIN</a></li>
                            </ul>
                        </li>
                        <?php
                         }elseif (in_array('DASHBOARD-IKT', $roles)) {
                            ?>     
                            <!-- <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dashboard<b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo site_url('DashboardReal') ?>">Dashboard</a></li>
                                    <li><a href="<?php echo site_url('DashboardReal/index_uc') ?>">Dashboard UC Browser</a></li>
                                    <li><a href="<?php echo site_url('DashboardReal/detail_dasboard') ?>">Dashboard Invenory Summary</a></li>
                                </ul>
                            </li> -->
    
                            ?>     
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dashboard Manual<b class="caret"></b></a>
                                <ul class="dropdown-menu">                                
                                    <li><a href="<?php echo site_url('tps_online/form_dom/listview') ?>">Monitoring BM Domestik</a></li>
                                    <li><a href="<?php echo site_url('tps_online/form_intr/listview') ?>">Monitoring BM Internasional</a></li>
                                    <li><a href="<?php echo site_url('tps_online/zero_defect/listview') ?>">Zero Defect (Quality)</a></li>
                                    <li><a href="<?php echo site_url('tps_online/zero_safety/listview') ?>">Safety</a></li>
                                    <!-- <li><a href="<?php echo site_url('tps_online/berthing_time/listview') ?>">Berthing Plan</a></li> -->
                                    <li><a href="<?php echo site_url('tps_online/rkap_trafik_kapal/listview') ?>">RKAP Trafik Kapal</a></li>
                                    <li><a href="<?php echo site_url('tps_online/rkap_arus_barang/listview') ?>">RKAP Arus Barang</a></li>
                                    <li><a href="<?php echo site_url('tps_online/rkap_pendapatan/listview') ?>">RKAP Pendapatan</a></li>
                                    <li><a href="<?php echo site_url('tps_online/kpi_barang/listview') ?>">KPI</a></li>
                                    <li><a href="<?php echo site_url('tps_online/tarif_tw/listview') ?>">TARIF TW</a></li>
                                </ul>
                            </li>
    
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown">Laporan<b class="caret"></b></a>
                                <ul class="dropdown-menu">
                                    <li><a href="<?php echo site_url('tps_online/lap_trafik_kapal/listview') ?>">Trafik/Arus Kedatangan Kapal</a></li>
                                    <li><a href="<?php echo site_url('tps_online/lap_arus_barang/listview') ?>">Trafik/Arus Barang</a></li>
                                    <li><a href="<?php echo site_url('tps_online/lap_pendapatan/listview') ?>">Pendapatan per pusat layanan</a></li>
                                    <li><a href="<?php echo site_url('tps_online/laporan_yor/listview') ?>">YOR</a></li>
                           
                                </ul>
                            </li>
    
                            
                            <!-- <li>
                                <a href="<?php echo site_url('dashboard') ?>">Beranda</a>
                            </li> -->
                    <?php
                         
                    }else {
                        ?>     
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dashboard<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo site_url('DashboardReal') ?>">Dashboard</a></li>
                                <li><a href="<?php echo site_url('DashboardReal/index_uc') ?>">Dashboard UC Browser</a></li>
                                <li><a href="<?php echo site_url('DashboardReal/detail_dasboard') ?>">Dashboard Invenory Summary</a></li>
                            </ul>
                        </li>

                        ?>     
                       
                        
                        <!-- <li>
                            <a href="<?php echo site_url('dashboard') ?>">Beranda</a>
                        </li> -->
                <?php
                    }
                }
                ?>

                <?php
                if ($auth->administrator_id) {
                    $roles = explode('|', $auth->roles);
                    if (in_array('ALL', $roles)) {
                        ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Administrasi<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo site_url('member/activationlist') ?>">Aktivasi Member</a></li>
                                <li role="presentation" class="divider"></li>
                                <li><a href="<?php echo site_url('member/listview') ?>">Data Member</a></li>
                                <li><a href="<?php echo site_url('users/listview') ?>">Data Pengguna</a></li>
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">EDI<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo site_url('edi_interface/listview_kapal') ?>">EDI Interface</a></li>
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Transfer<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo site_url('transfer/request') ?>">Transfer Data</a></li>
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">ILCS Mapper<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo site_url('ilcs_mapper/summary_mapping/listview') ?>">Pencocokan Data</a></li>
                                <li><a href="<?php echo site_url('ilcs_mapper/summary_enhancer/listview') ?>">Lengkapi Data</a></li>
                                <li><a href="<?php echo site_url('ilcs_mapper/summary_approval/listview') ?>">Persetujuan Data</a></li>
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">TPS Online<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo site_url('tps_online/kunjungan_kapal/listview') ?>">Kunjungan Kapal</a></li>
                                <li><a href="<?php echo site_url('tps_online/kargo/listview') ?>">Data Kargo</a></li>
                                <li><a href="<?php echo site_url('tps_online/consignment/assign_bl') ?>">Update BL Kargo</a></li>
                            </ul>
                        </li>

                    <?php
                        } else if (in_array('CARTOS', $roles)) {
                            ?>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Billing<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo site_url('cartos/discharge_tally/verify') ?>">Transfer Realisasi Bongkar / Muat</a></li>
                            </ul>
                        </li>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">TPS Online<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <!--<li><a href="<?php echo site_url('tps_online/kunjungan_kapal/listview') ?>">Kunjungan Kapal</a></li>-->
                                <li><a href="<?php echo site_url('tps_online/internasional_inbound/listview') ?>">Import</a></li>
                                <li><a href="<?php echo site_url('tps_online/internasional_outbound/listview') ?>">Export</a></li>

                                <!--<li><a href="<?php echo site_url('tps_online/domestik_inbound/listview') ?>">Domestik Inbound</a></li>-->
                                <!--<li><a href="<?php echo site_url('tps_online/domestik_outbound/listview') ?>">Domestik Outbound</a></li>-->
                                <!--<li><a href="<?php echo site_url('tps_online/kargo/listview') ?>">Data Kargo</a></li>-->
                                <li><a href="<?php echo site_url('tps_online/kargo_internasional_inbound/listview') ?>">Kargo Import</a></li>
                                <li><a href="<?php echo site_url('tps_online/kargo_internasional_outbound/listview') ?>">Kargo Export</a></li>
                                <!--<li><a href="<?php echo site_url('tps_online/kargo_domestik_inbound/listview') ?>">Data Kargo Domestik Inbound</a></li>-->
                                <!--<li><a href="<?php echo site_url('tps_online/kargo_domestik_outbound/listview') ?>">Data Kargo Domestik Outbound</a></li>-->
                                <li><a href="<?php echo site_url('tps_online/log_pengiriman/listview') ?>">Log Pengiriman</a></li>
                                <li><a href="<?php echo site_url('tps_online/consignment/assign_bl') ?>">Update BL Kargo</a></li>
                                <li><a href="<?php echo site_url('tps_online/sppb') ?>">Lihat SPPB Online</a></li>
                                <!-- <li><a href="<?php //echo site_url('tps_online/log/listview') 
                                                            ?>">Lihat Log Kargo</a></li> -->
                                <li><a href="<?php echo site_url('tps_online/notifikasi/listview') ?>">Lihat Notifikasi Import</a></li>
                                <li><a href="<?php echo site_url('tps_online/notifikasi/listview_export') ?>">Lihat Notifikasi Export</a></li>
                                <!-- <li><a href="<?php echo site_url('tps_online/notifikasi/listview_export') ?>">Lihat Notifikasi Export</a></li> -->
                                

                                <li><a href="<?php echo site_url('tps_online/notifikasi/input_bl') ?>">Input BL</a></li>
                                <li><a href="<?php echo site_url('tps_online/edit_bl') ?>">Edit BL</a></li>
                                <li><a href="<?php echo site_url('tps_online/notifikasi/kirim_tpsonline') ?>">Kirim TPS ONLINE</a></li>

                                <!-- yang ini guys -->
                                
                                <!-- <li><a href="<?php echo site_url('tps_online/manual_sppb/') ?>">Input SPPB Manual</a></li> -->


                                <!-- <li><a href="<?php #echo site_url('tps_online/manual_sppb/') ?>">Input SPPB Manual</a></li> -->
                                <li><a href="<?php echo site_url('tps_online/import_sppb/') ?>">Input Import SPPB</a></li>
                                <li><a href="<?php echo site_url('tps_online/Tam/') ?>">TAM</a></li>
                                <li><a href="<?php echo site_url('tps_online/Upload_manifest/') ?>">Upload Manifest</a></li>
                                <li><a href="<?php echo site_url('doc_inquiry/inquiry/') ?>">Document Inquiry</a></li>
                            </ul>
                        </li>

                        <li>
                            <a href="<?php echo site_url('tps_online/pelanggan/listview') ?>">Pelanggan</a>
                        </li>

                        <li>
                            <a href="<?php echo site_url('laporan') ?>">Pusat Laporan</a>
                        </li>
                      
                        

                    <?php
                        } else if (in_array('BEACUKAI', $roles)) {
                            ?>

                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Dashboard <b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo site_url('dashboard/cargo') ?>">Data Cargo</a></li>
                                <!--<li><a href="<?php echo site_url('dashboard/rekap_data') ?>">Rekapitulasi Data</a></li>-->
                                <li><a href="<?php echo site_url('dashboard/truck') ?>">Data Truk</a></li>
                                <li><a href="<?php echo site_url('dashboard/log_autogate') ?>">Log Autogate</a></li>
                            </ul>
                        </li>

                    <?php
                        }
                    }

                    if ($auth->freight_forwarder_id) {
                        ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Request <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo site_url('delivery_request_og/listview') ?>">Ocean Going Delivery Request</a></li>
                            <li class="disabled"><a href="#"><span class="label label-info">Coming Soon!</span> Ocean Going Receiving Request</a></li>
                            <li role="presentation" class="divider"></li>
                            <li class="disabled"><a href="#"><span class="label label-info">Coming Soon!</span> Domestic Delivery Request</a></li>
                            <li class="disabled"><a href="#"><span class="label label-info">Coming Soon!</span> Domestic Receiving Request</a></li>
                        </ul>
                    </li>

                <?php
                }

                if ($auth->trucking_company_id) {
                    ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Trucking <span class="badge" style="display:none">42</span> <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo site_url('trucking/listview') ?>">Trucking Request </a></li>
                            <li role="presentation" class="divider"></li>
                            <li><a href="<?php echo site_url('driver/listview') ?>">Daftarkan Supir Truck</a></li>
                        </ul>
                    </li>
                <?php
                }

                if ($auth->shipping_line_id) {
                    ?>
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Pelayaran <span class="badge" style="display:none">42</span> <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo site_url('kapal/listview') ?>">Daftarkan Kapal </a></li>
                            <li><a href="<?php echo site_url('kapal_agen/listview') ?>">Daftarkan Agen</a></li>
                        </ul>
                    </li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Manifest<b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="<?php echo site_url('manifest/manifest_upload') ?>">Manifest Interface</a></li>
                        </ul>
                    </li>

                <?php
                }
                ?>
                <!--
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Laporan <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo site_url('laporan/ocean_going/riwayat_delivery') ?>">Riwayat Ocean Going Delivery Request</a></li>
						<li class="disabled"><a href="#"><span class="label label-info">Coming Soon!</span> Riwayat Ocean Going Receiving Request</a></li>
						<li role="presentation" class="divider"></li>
						<li class="disabled"><a href="#"><span class="label label-info">Coming Soon!</span> Riwayat Domestic Delivery Request</a></li>
						<li class="disabled"><a href="#"><span class="label label-info">Coming Soon!</span> Riwayat Domestic Receiving Request</a></li>
						<li role="presentation" class="divider"></li>
                        <li><a href="<?php echo site_url('laporan/ocean_going/riwayat_delivery') ?>">Riwayat Transaksi</a></li>
						<li role="presentation" class="divider"></li>
						<li><a href="<?php echo site_url('laporan/ocean_going/riwayat_delivery') ?>">Permintaan Trucking</a></li>
                    </ul>
                </li>
				-->
                <!-- <li>
                    <a href="#contact">Bantuan</a>
                </li> -->
            </ul>

            <!-- User Panel -->
            <ul class="nav navbar-nav navbar-right">
                <!-- <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Bahasa Indonesia <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo site_url('#') ?>">Bahasa Indonesia</a></li>
                        <li><a href="<?php echo site_url('#') ?>">English</a></li>
                    </ul>
                </li> -->
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $auth->nama_lengkap ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo in_array('ETICKET', $roles) ? site_url('dashboard_eticket/passwordUpdate') : site_url('dashboard/passwordUpdate') ?>">Ganti Password</a></li>
                        <li><a href="<?php echo site_url('front/logout') ?>">Keluar</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</div>
