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

            <a class="navbar-brand" href="<?php echo site_url('domestik/dashboard_domestik') ?>" style="padding:10px 20px 8px 10px"><img src="<?php echo base_url('assets/img/smartcargo/int_logo.png') ?>" alt="SmartCargo" /></a>
        </div>

        <div class="collapse navbar-collapse">
            <ul class="nav navbar-nav">
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">Create Announce<b class="caret"></b></a>
                            <ul class="dropdown-menu">
                                <li><a href="<?php echo site_url('domestik/announce_vin_domestik') ?>">VIN</a></li>
                                <li><a href="<?php echo site_url('domestik/announce_truck_domestik') ?>">Truck</a></li>
                                <li><a href="<?php echo site_url('domestik/selfdrive_domestik') ?>">Selfdrive</a></li>
                            </ul>
                        </li>
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown">E-Ticket<b class="caret"></b></a>
                            <ul class="dropdown-menu">
<!--                                <li><a href="--><?php //echo site_url('eticket/truck_id_list') ?><!--">Truck ID List</a></li>-->
<!--                                <li><a href="--><?php //echo site_url('eticket/asosiasi_truck') ?><!--">Asosiasi Truck</a></li>-->
                                <li><a href="<?php echo site_url('domestik/eticket_list_domestik') ?>">E-Ticket List</a></li>
                                <li><a href="<?php echo site_url('domestik/return_cargo_domestik') ?>">Return Cargo</a></li>
                            </ul>
                        </li>


            </ul>

            <!-- User Panel -->
            <ul class="nav navbar-nav navbar-right">

                <li class="dropdown">
                    <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                    <?php
                      //echo $auth->full_name; print_r($auth);

                      $integrasi_cardom_dev = $this->load->database('integrasi_cardom_dev', TRUE);

                      if($auth->intapps_type == "ADMIN") {
                        echo $auth->full_name;
                      } else {
                        $query = $integrasi_cardom_dev->query("select NAME from M_ORGANIZATION WHERE ID = '".$auth->intapps_type."' ");
                        if ($query->num_rows() > 0)
                        {
                          $hasil = $query->row();
                          echo  $hasil->NAME . " (". $auth->full_name  .")" ;
                        }
                      }


                    ?>
                    <b class="caret"></b></a>
                    <ul class="dropdown-menu">
                        <li><a href="<?php echo site_url('domestik/dashboard_domestik/passwordUpdateDomestik') ?>">Ganti Password</a></li>
                        <li><a href="<?php echo site_url('front/logout') ?>">Keluar</a></li>
                    </ul>
                </li>
            </ul>
        </div>
        <!--/.nav-collapse -->
    </div>
</div>
