YTH,
<?php echo $trucking_company->nama_perusahaan ?> 
<?php echo $trucking_company->alamat ?> 

Kami telah mendelegasikan pengambilan container berikut:
<?php echo $container->container_number ?>

Untuk diambil di Teriminal <?php echo $container->nama_terminal_petikemas ?> 
Pada Tanggal <?php echo date('d-M-Y', strtotime($container->rencana_ambil)) ?>

Terima Kasih,
Hormat Kami,


Freight Forwarder:
<?php echo $freight_forwarder->nama_perusahaan ?>,
<?php echo $freight_forwarder->alamat ?>,
<?php echo $freight_forwarder->telepon ?>,
<?php echo $freight_forwarder->email ?>



SMARTCARGO: