<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<?php $this->load->view('frontend/components/basic_head'); ?>
</head>
<body>
<header class="main-header">
	<?php $this->load->view('frontend/components/header'); ?>
</header>
<div class="container main">
	<div class="row-fluid content">
		<div class="span7 welcome-container">
			<h1>Selamat Datang</h1>
			<!--<h2>Use at your own risk!</h2>-->
			<h4>IT Integrasi Logistik Cipta Solusi</h4>
		</div>
		<!-- end of span 7 -->
		<aside class="span5">
			<div class="login">
				<form id="login_form" action="" method="post">
					<div class="form-inline">
						<span class="fl loginlogin">Login</span> <span class="fr "><span class="or">or</span> <a href="<?php echo base_url(); ?>register"><span class="register">Register now</span></a></span>
					</div><br />
					<hr />
					<?php
					if(isset($error_msg)){
					?>
					<div class="alert alert-error" id="information_bar" style=""><?php echo $error_msg ?></div>
					<?php
					}
					?>
					<div class="row-fluid">
						<div class="span12">
							<fieldset class="username">
								<input type="text" id="username" name="username" placeholder="Username" value="<?php secho(post('username')) ?>">
							</fieldset>						
						</div>
					</div>
					<!-- end of row fluid -->

					<div class="row-fluid">
						<div class="span12">
							<fieldset class="password">
								<input type="password" id="password" name="password" placeholder="Password">
							</fieldset>
							
						</div>
					</div>

					<div class="row-fluid">
						<div class="span12">
							<input type="checkbox" id="c1" name="cc" /></br>
							<!--<label for="c1"><span></span>Remember Password</label>-->

							<p><strong>Write the following word:</strong></p>
							<img src="<?php echo site_url('front/show_captcha/'.microtime(true))?>" id="captcha" /><br/>

							<!-- CHANGE TEXT LINK -->
							<a href="#" onClick="
							    document.getElementById('captcha').src='<?php echo site_url('front/show_captcha') ?>?'+Math.random();
							    document.getElementById('captcha-form').focus();
								return false;
							">Not readable? Change text.</a>

							<br/><br/>
							<input type="text" name="captcha" id="captcha-form" autocomplete="off" /><br/>
						</div>
					</div>
					<div class="row-fluid">
						<div class="pull-left">
							<a href="<?php echo site_url('front/fpass') ?>">Forgot Password?</a>
						</div>
						<div class="pull-right">
							<button id="login_button" class="btn btn-primary fr" type="submit">SIGN IN</button>
						</div>
					</div>
					<!-- end of row fluid -->
					<div class="row-fluid">
						<div class="form-inline">
							<!--sealverisign-->
							<!--
							<table width="135" border="0" cellpadding="2" cellspacing="0" title="Click to Verify - This site chose Symantec SSL for secure e-commerce and confidential communications.">
								<tr>
									<td width="135" align="center" valign="top">
										<script type="text/javascript" src="https://seal.verisign.com/getseal?host_name=www.inaportnet.com&amp;size=S&amp;use_flash=YES&amp;use_transparent=YES&amp;lang=en"></script><br />
										<a href="http://www.symantec.com/ssl-certificates" target="_blank"  style="color:#000000; text-decoration:none; font:bold 7px verdana,sans-serif; letter-spacing:.5px; text-align:center; margin:0px; padding:0px;">ABOUT SSL CERTIFICATES</a>
									</td>
								</tr>
							</table>
							-->
						</div>
					</div>
				</form>
			</div>
		</aside>
	</div>
	<!-- end row 2 --> 
</div>
<!-- end of container  --> 
<footer>
	<?php $this->load->view('frontend/components/footer'); ?>
</footer>

<!-- Menu dropown
	Back to top
	Search javascript --> 

<script src="<?php echo base_url(); ?>assets/js/jquery-2.0.3.min.js"></script> 
<script src="<?php echo base_url(); ?>assets/landing/js/bootstrap.js"></script> 
<script src="<?php echo base_url(); ?>assets/landing/js/bootstrap.min.js"></script> 


<!-- Le javascript
    ================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 
<!--
<script src="https://twitter.github.com/bootstrap/assets/js/bootstrap-transition.js"></script> 
<script src="https://twitter.github.com/bootstrap/assets/js/bootstrap-alert.js"></script> 
<script src="https://twitter.github.com/bootstrap/assets/js/bootstrap-modal.js"></script> 
<script src="https://twitter.github.com/bootstrap/assets/js/bootstrap-dropdown.js"></script> 
<script src="https://twitter.github.com/bootstrap/assets/js/bootstrap-scrollspy.js"></script> 
<script src="https://twitter.github.com/bootstrap/assets/js/bootstrap-tab.js"></script> 
<script src="https://twitter.github.com/bootstrap/assets/js/bootstrap-tooltip.js"></script> 
<script src="https://twitter.github.com/bootstrap/assets/js/bootstrap-popover.js"></script> 
<script src="https://twitter.github.com/bootstrap/assets/js/bootstrap-button.js"></script> 
<script src="https://twitter.github.com/bootstrap/assets/js/bootstrap-collapse.js"></script> 
<script src="https://twitter.github.com/bootstrap/assets/js/bootstrap-carousel.js"></script> 
<script src="https://twitter.github.com/bootstrap/assets/js/bootstrap-typeahead.js"></script> 
-->
<script type="text/javascript">
	$(function(){
	    $('[rel=popover]').popover({ 
	    html : true, 
	    content: function() {
	      return $('#popover_content_wrapper').html();
	    }

    });
});
</script>
<!-- add the backstretch plugin --> 
<script src="<?php echo base_url(); ?>assets/landing/js/backstretch.js"></script> 
<script type='text/javascript'>
	var bs = {
		baseURL : '<?php echo base_url(); ?>',
		siteURL : '<?php echo base_url(); ?>'	
	}

    $(document).ready(function() {
		var d = new Date();
		var hour = d.getHours();
		
		var waktu = '';
		
		if(hour < 5){
			waktu = 'night';
		}else if(hour < 9){
			waktu = 'morning';
		}else if(hour < 15){
			waktu = 'afternoon';
		}else if(hour < 19){
			waktu = 'evening';
		}else{
			waktu = 'night';
		}
		
        $.backstretch(bs.baseURL + "assets/landing/img/bg/port_" + waktu + ".jpg");
    });
</script>
</body>
</html>