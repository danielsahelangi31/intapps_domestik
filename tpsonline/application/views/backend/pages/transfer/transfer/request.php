<html lang="en">
<head>	
<?php $this->load->view('backend/elements/basic_head') ?>
</head>
<!--<meta http-equiv="refresh" content="30" > -->
<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>

		<!--<div id="container">-->
		<div class="container">
			
			<div class="row">
            	<div class="col-md-8">
                	<h2>Form Transfer Data</h2>
                </div>
				<div class="col-md-4">
					<div class="pull-right back_list">
						
					</div>
				</div>
			</div>
			
			<?php
			if(isset($error_msg)){
			?>
			<div class="alert alert-danger fade in">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				<p><?php echo $error_msg ?></p>
			</div>
			<?php
			}
			?>
			
			<div class="row">
				<div class="col-lg-12">
					<fieldset class="delivery-request-border">
						<legend class="delivery-request-border">Download</legend>
							<?php echo form_open('transfer/cont_soap/request_data'); ?>
								<div class="col-lg-6">
									<div class="form-group">
										
										<label class="col-lg-4 control-label">Visit ID</label>
										<div class="col-lg-8">
											<input type="text" class="form-control" name="VISIT_ID" id="VISIT_ID">
											
										</div>
										
										
									</div>
										
								</div>
									<input type="submit" value="Download Data" class="btn btn-primary">
							<?php echo form_close(); ?>
										
							<?php 
							if(empty($datasource))
							{
								  echo "";
							}
							else
							{
								echo $datasource->MESSAGE;
							}
							?>
							
					</fieldset>
				</div>
			</div>		
			
		</div>
	</div>
	
	<?php $this->load->view('backend/elements/footer') ?>
	
</body>
</html>