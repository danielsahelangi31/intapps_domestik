YTH,
<?php echo $freight_forwarder->nama_perusahaan ?> 
<?php echo $freight_forwarder->alamat ?> 

Kami telah mendelegasikan supir kami berikut:
Nama: <?php echo $assignment->nama_supir ?> 
TruckID : <?php echo $assignment->truck_id ?> 

Untuk mengambil container:
<?php echo $container->container_number ?> 

Di Teriminal <?php echo $container->nama_terminal_petikemas ?> 
Pada Tanggal <?php echo date('d-M-Y', strtotime($container->rencana_ambil)) ?>

Terima Kasih,
Hormat Kami,


Trucking Company:
<?php echo $trucking_company->nama_perusahaan ?>,
<?php echo $trucking_company->alamat ?>,
<?php echo $trucking_company->telepon ?>,
<?php echo $trucking_company->email ?>



SMARTCARGO: