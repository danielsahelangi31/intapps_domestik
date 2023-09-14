<?php
$auth = $this->userauth->getLoginData();
?>
	<meta charset="utf-8">
	<meta content="width=device-width, initial-scale=1.0" name="viewport">
	<meta content="" name="description">
	<meta content="" name="author">
	
	<link rel="shortcut icon" href="<?php echo base_url('favicon.ico') ?>" type="image/x-icon">
	<link rel="icon" href="<?php echo base_url('favicon.ico') ?>" type="image/x-icon">
	
	<title>INTAPPS</title>
	<!--<?php echo isset($page_title) ? $page_title . ' | ' : '' ?><?php echo $this->config->item('website_name') ?></title>-->
    <!-- Bootstrap core CSS -->
	<link href="<?php echo base_url('assets/css/bootstrap.css') ?>" rel="stylesheet">
	<link href="<?php echo base_url('assets/css/datepicker.css') ?>" rel="stylesheet">
	<link href="<?php echo base_url('assets/css/smartcargo.css') ?>" rel="stylesheet">
	<link href="<?php echo base_url('assets/css/jquery-ui.css') ?>" rel="stylesheet">
	<link href="<?php echo base_url('assets/css/dataTables.bootstrap.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" type="text/css" media="all" href="<?php echo base_url('assets/css/daterangepicker-bs3.css') ?>"/>
	<!--<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
	<!-- Custom styles for this template -->
	<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	  <script src="<?php echo base_url('assets/js/html5shiv.js') ?>"></script>
	  <script src="<?php echo base_url('assets/js/respond.min.js') ?>"></script>
	<![endif]-->
    <script type="text/javascript" src="<?= base_url(); ?>assets/hchart/highcharts.js" ></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/hchart/highcharts-more.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/hchart/xrange.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/hchart/series-label.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/hchart/exporting.js"></script>
    <script type="text/javascript" src="<?= base_url(); ?>assets/hchart/solid-gauge.js"></script>
    <!-- Select2 -->
  	<link rel="stylesheet" href="<?= base_url(); ?>assets/select2/dist/css/select2.min.css">
  	<link rel="stylesheet" href="<?= base_url(); ?>assets/colvis/buttons.dataTables.min.css">

    <script type="text/javascript">
	var bs = {
		token : '<?php echo $auth->token ?>',
		siteURL : '<?php echo site_url() ?>',
		baseURL : '<?php echo base_url() ?>'
	};
	
	</script>