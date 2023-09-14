<?php
$CI =& get_instance();
$CI->load->helper('url');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<title>404 Page Not Found Boss</title>
<style type="text/css">

::selection{ background-color: #E13300; color: white; }
::moz-selection{ background-color: #E13300; color: white; }
::webkit-selection{ background-color: #E13300; color: white; }

body {
	background-color: #fff;
	margin: 40px;
	font: 13px/20px normal Helvetica, Arial, sans-serif;
	color: #4F5155;
	background:url(<?php echo base_url('assets/goodies/empty640.jpg') ?>) no-repeat 20px 20px;
	padding-left:500px;
	padding-top:100px;
}

a {
	color: #003399;
	background-color: transparent;
	font-weight: normal;
}

h1 {
	color: #444;
	background-color: transparent;
	border-bottom: 1px solid #D0D0D0;
	font-size: 19px;
	font-weight: normal;
	margin: 0 0 14px 0;
	padding: 14px 15px 10px 15px;
}

code {
	font-family: Consolas, Monaco, Courier New, Courier, monospace;
	font-size: 12px;
	background-color: #f9f9f9;
	border: 1px solid #D0D0D0;
	color: #002166;
	display: block;
	margin: 14px 0 14px 0;
	padding: 12px 10px 12px 10px;
}

#container {
	margin: 10px;
	border: 1px solid #D0D0D0;
	-webkit-box-shadow: 0 0 8px #D0D0D0;
	
}

p {
	margin: 12px 15px 12px 15px;
}
</style>
</head>
<body>
	<div id="container">
		<h1><?php echo $heading; ?></h1>
		<h1>Sori om/tante, halamannya gak ada. Coba cek URLnya</h1>
		<p>Mending cari makan dulu barangkali lagi laper. Atau balik aja ke <a href="<?php echo site_url('/') ?>">rumah.</a></p>
	</div>
	<p>Image from: http://thenewsrecord.blogspot.com/</p>
</body>
</html>