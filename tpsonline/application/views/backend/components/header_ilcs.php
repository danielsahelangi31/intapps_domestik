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

            <a class="navbar-brand" href="<?php echo site_url('dashboard') ?>" style="padding:10px 20px 8px 10px"><img src="<?php echo base_url('assets/img/smartcargo/int_logo.png') ?>" alt="SmartCargo" /></a>
        </div>

        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                <li>
                    <a href="<?php echo site_url('dashboard') ?>">Beranda</a>
                </li>
                
				
				<?php
				if($auth->administrator_id){				
					$roles = explode('|', $auth->roles);					
					if(in_array('ALL', $roles)){
				?>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Administrasi<b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo site_url('member/activationlist')?>">Aktivasi Member</a></li>
                        <li role="presentation" class="divider"></li>
                        <li><a href="<?php echo site_url('member/listview')?>">Data Member</a></li>
                    	<li><a href="<?php echo site_url('users/listview')?>">Data Pengguna</a></li>
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
					}else if(in_array('CARTOS', $roles)){
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
                        <li><a href="<?php echo site_url('tps_online/kunjungan_kapal/listview') ?>">Kunjungan Kapal</a></li>
                        <li><a href="<?php echo site_url('tps_online/kargo/listview') ?>">Data Kargo</a></li>
						<li><a href="<?php echo site_url('tps_online/consignment/assign_bl') ?>">Update BL Kargo</a></li>
						<li><a href="<?php echo site_url('tps_online/sppb') ?>">Lihat SPPB Online</a></li>
						<li><a href="<?php echo site_url('tps_online/log/listview') ?>">Lihat Log Kargo</a></li>
                    </ul>
                </li>
				
				<li>
                    <a href="<?php echo site_url('tps_online/pelanggan/listview') ?>">Pelanggan</a>
                </li>
				
				<li>
                    <a href="<?php echo site_url('laporan') ?>">Pusat Laporan</a>
                </li>
				<?php
					}
				}
				
				if($auth->freight_forwarder_id){
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
				
				if($auth->trucking_company_id){
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
				
				if($auth->shipping_line_id){
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
                <li>
                    <a href="#contact">Bantuan</a>
                </li>
            </ul>
            
            <!-- User Panel -->
            <ul class="nav navbar-nav navbar-right">
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">Bahasa Indonesia <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo site_url('#') ?>">Bahasa Indonesia</a></li>
                        <li><a href="<?php echo site_url('#') ?>">English</a></li>
                    </ul>
                </li>
				<li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo $auth->nama_lengkap ?> <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo site_url('dashboard/passwordUpdate') ?>">Ganti Password</a></li>
                        <li><a href="<?php echo site_url('front/logout') ?>">Keluar</a></li>
                    </ul>
                </li>
            </ul>
        </div><!--/.nav-collapse -->
    </div>
</div>