<!DOCTYPE HTML>
<html lang="en-US">
<head>
	<?php $this->load->view('frontend/components/basic_head'); ?>
</head>
<body>
<header class="main-header">
	<?php $this->load->view('frontend/components/header'); ?>
</header>

<div class="container">
	<div class="row-fluid content">

		<?php if(validation_errors()) { ?>
			<div class="alert alert-danger alert-block" style="margin-bottom:50px">
				<button type="button" class="close" data-dismiss="alert">×</button>
				<h4>Harap perbaiki isian yang salah</h4><br/><br/>
			</div>
		<?php } ?>

		<div class="span7 form-container">
			<h1 class="ribbon">
				<div class="ribbon-content">Registration</div>
			</h1>
			<form class="setengah" action="" method="post">
				<div class="row-fluid">
					<div class="span6">
						<fieldset>
							<label>Username *</label>
							<input type="text" name="username" placeholder="" value="<?php echo set_value('username'); ?>"><?php echo form_error('username', '<div class="error">', '</div><br/>'); ?>
						</fieldset>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span6">
						<fieldset>
							<label>Password *</label>
							<input type="password" name="password" placeholder="" value=""><?php echo form_error('password', '<div class="error">', '</div><br/>'); ?>
						</fieldset>
					</div>
					<div class="span6">
						<fieldset>
							<label>Confirm Password *</label>
							<input type="password" name="passconf" placeholder="" value=""><?php echo form_error('passconf', '<div class="error">', '</div><br/>'); ?>
						</fieldset>
					</div>
				</div>
				<hr class="fancy" />
				<div class="row-fluid">
					<fieldset>
						<label>Full Name *</label>
						<input type="text" class="long_form_control" name="nama_lengkap" placeholder="" value="<?php echo set_value('nama_lengkap'); ?>"><?php echo form_error('nama_lengkap', '<div class="error">', '</div><br/>'); ?>
					</fieldset>
				</div>
				<div class="row-fluid">
					<div class="span6">
						<fieldset>
							<label>Mobile phone *</label>
							<input type="text" name="cp_handphone" placeholder="+62XXXXXXXXXX" value="<?php echo set_value('cp_handphone'); ?>"><?php echo form_error('cp_handphone', '<div class="error">', '</div><br/>'); ?>
						</fieldset>
					</div>
					<div class="span6">
						<fieldset>
							<label>Phone</label>
							<input type="text" name="cp_telepon" placeholder="" value="<?php echo set_value('cp_telepon'); ?>"><?php echo form_error('cp_telepon', '<div class="error">', '</div><br/>'); ?>
						</fieldset>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span6">
						<fieldset>
							<label>Email *</label>
							<input type="text" name="cp_email" placeholder="" value="<?php echo set_value('cp_email'); ?>"><?php echo form_error('cp_email', '<div class="error">', '</div><br/>'); ?>
						</fieldset>
					</div>
				</div>
				<hr class="fancy"/>
				<div class="row-fluid">
					<div class="span6">
						<fieldset>
							<label>Company Name *</label>
							<input type="text" name="nama_perusahaan" placeholder="" value="<?php echo set_value('nama_perusahaan'); ?>"><?php echo form_error('nama_perusahaan', '<div class="error">', '</div><br/>'); ?>
						</fieldset>
					</div>
					<div class="span6">
						<fieldset>
							<label>Tax Reference (NPWP) *</label>
							<input type="text" name="npwp" placeholder="99.999.999.9-999.999" maxlength="20" value="<?php echo set_value('npwp'); ?>"><?php echo form_error('npwp', '<div class="error">', '</div><br/>'); ?>
						</fieldset>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span6">
						<fieldset>
							<label>Phone *</label>
							<input type="text" name="com_telepon" placeholder="" value="<?php echo set_value('com_telepon'); ?>"><?php echo form_error('com_telepon', '<div class="error">', '</div><br/>'); ?>
						</fieldset>
					</div>
					<div class="span6">
						<fieldset>
							<label>Fax</label>
							<input type="text" name="com_fax" placeholder="" value="<?php echo set_value('com_fax'); ?>">
						</fieldset>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span6">
						<fieldset>
							<label>Business Type *</label>
							<select name="membership_type">
								<option value="">-- Select --</option>
								<option value="FREIGHT_FORWARDER">Freight Forwarder</option>
								<option value="TRUCKING_COMPANY">Trucking Company</option>
								<option value="SHIPPING_LINE">Shipping Line</option>
								<option value="SHIPPING_AGENT">Shipping Agent</option>
							</select>
						</fieldset>
					</div>
					<div class="span6">
						<fieldset>
							<label>Company ID *</label>
							<input type="text" name="ipc_id" placeholder="" value="<?php echo set_value('ipc_id'); ?>"><?php echo form_error('ipc_id', '<div class="error">', '</div><br/>'); ?>
						</fieldset>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span12">
						<label>Address</label>
						<textarea class="long_form_control" name="com_alamat" rows="4"><?php echo set_value('com_alamat'); ?></textarea>
					</div>
				</div>
				<div class="row-fluid">
					<div class="span12">
						<p><strong>Write the following word:</strong></p>
							<img src="<?php echo site_url('front/show_captcha/'.microtime(true))?>" id="captcha" /><br/>

							<!-- CHANGE TEXT LINK -->
							<a href="#" onClick="
							    document.getElementById('captcha').src='<?php echo site_url('front/show_captcha') ?>?'+Math.random();
							    document.getElementById('captcha-form').focus(); 
								return false;"
							>Not readable? Change text.</a>
							<br/><br/>
							<input type="text" name="captcha" id="captcha-form" autocomplete="off" /><br/>
					</div>
				</div>
				<div class="row-fluid">
					<input type="checkbox" id="c1" name="cc" /><?php echo form_error('cc', '<div class="error">', '</div><br/>'); ?>
					<label for="c1"><span></span> I Declare The Data is True and Agree to Terms of Use</label>
				</div>
				<div class="row-fluid">
					<button class="btn btn-primary fr" type="submit">REGISTER</button>
				</div>

				<p>* Mandatory fields</p>
				<!--sealverisign-->
				<table width="135" border="0" cellpadding="2" cellspacing="0" title="Click to Verify - This site chose Symantec SSL for secure e-commerce and confidential communications.">
				<tr>
					<td width="135" align="center" valign="top">
						<script type="text/javascript" src="https://seal.verisign.com/getseal?host_name=www.inaportnet.com&amp;size=S&amp;use_flash=YES&amp;use_transparent=YES&amp;lang=en"></script><br />
						<a href="http://www.symantec.com/ssl-certificates" target="_blank"  style="color:#000000; text-decoration:none; font:bold 7px verdana,sans-serif; letter-spacing:.5px; text-align:center; margin:0px; padding:0px;">ABOUT SSL CERTIFICATES</a>
					</td>
				</tr>
				</table>
				<!--sealverisign-->
			</form>

		</div>
		
		<!-- end of span 7 -->
		
		<aside class="span4">
			<div class="sidebar">
				<h1 class="sidebar row-fluid">Smart Cargo Services <img src="img/logo-min.png" alt="" /></h1>
				<div class="item no1">
					<h4 style="height:40px">Port Document Clearance Services</h4>
				</div>
				<!-- end item -->
				
				<div class="item no2">
					<h4 style="height:40px">Information Services</h4>
				</div>
				<!-- end item -->
				
				<div class="item no3">
					<h4 style="height:40px">Alert and Notification Services</h4>
				</div>
				<!-- end item -->
				
				<div class="item no4">
					<h4 style="height:40px">Track and Tracing Services</h4>
				</div>
				<!-- end item -->
				
				<div class="item no5">
					<h4 style="height:40px">Payment Services</h4>
				</div>
				<!-- end item -->
			</div>
		</aside>
	</div>
	<!-- end row 2 --> 
</div>
<!-- end of container  --> 

<br />
<br />
<footer> <br />
	<br />
	<br />
	<?php $this->load->view('frontend/components/footer'); ?>
</footer>

<!-- Menu dropown
	Back to top
	Search javascript --> 

<script src="<?php echo base_url('assets/js/jquery-2.0.3.min.js') ?>"></script> 
<script src="<?php echo base_url('assets/landing/js/bootstrap.js') ?>"></script> 
<script src="<?php echo base_url('assets/landing/js/bootstrap.min.js') ?>"></script> 
<script >
	$(document).ready(function() {
		$(window).scroll(function() {
			if($(this).scrollTop() > 200) {
				$('.go-top').fadeIn(400);
			} else {
				$('.go-top').fadeOut(400);
			}
		});
		
		$('.go-top').click(function(event){
			event.preventDefault();
			$('html, body').animate({scrollTop: 0}, 300);
		})
		
		
	});	
</script> 

<!-- Le javascript
    ================================================== --> 
<!-- Placed at the end of the document so the pages load faster --> 
<script type="text/javascript">
$(function(){
	
});
</script> 
</body>
</html>