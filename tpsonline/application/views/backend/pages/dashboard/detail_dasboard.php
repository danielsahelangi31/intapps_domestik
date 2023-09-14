<!DOCTYPE html>
<html lang="id">
<head>

	<?php $this->load->view('backend/elements/basic_head') ?>
	<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
	<style>
		.border-left-warning {
			border-left: .25rem solid #f6c23e !important;
		}

		.mb-4, .my-4 {
			margin-bottom: 1.5rem !important;
		}

		.card {
			position: relative;
			display: -webkit-box;
			display: -ms-flexbox;
			display: flex;
			-webkit-box-orient: vertical;
			-webkit-box-direction: normal;
			-ms-flex-direction: column;
			flex-direction: column;
			min-width: 0;
			word-wrap: break-word;
			background-color: #fff;
			background-clip: border-box;
			border: 1px solid #e3e6f0;
			border-left-color: rgb(227, 230, 240);
			border-left-style: solid;
			border-left-width: 1px;
			border-radius: .35rem;
		}

		*, ::after, ::before {
			-webkit-box-sizing: border-box;
			box-sizing: border-box;
		}

		.card-header:first-child {
			border-radius: calc(.35rem - 1px) calc(.35rem - 1px) 0 0;
		}

		.pb-3, .py-3 {
			padding-bottom: 1rem !important;
		}

		.pt-3, .py-3 {
			padding-top: 1rem !important;
		}

		.align-items-center {
			-webkit-box-align: center !important;
			-ms-flex-align: center !important;
			align-items: center !important;
		}

		.card-header {
			padding: .75rem 1.25rem;
			padding-top: 0.75rem;
			padding-bottom: 0.75rem;
			margin-bottom: 0;
			color: inherit;
			background-color: #f8f9fc;
			border-bottom: 1px solid #e3e6f0;
		}

		.card-body {
			-webkit-box-flex: 1;
			-ms-flex: 1 1 auto;
			flex: 1 1 auto;
			padding: 1.25rem;
		}

		.text-white {
			color: #fff !important;
		}

		.text-warning {
			color: #f6c23e !important;
		}

		.text-success {
			color: #1cc88a !important;
		}

		.text-danger {
			color: #e74a3b !important;
		}

		.text-primary {
			color: #4e73df !important;
		}

		.shadow {
			-webkit-box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15) !important;
			box-shadow: 0 .15rem 1.75rem 0 rgba(58,59,69,.15) !important;
		}

		.bg-primary {
			background-color: #4e73df !important;
		}

		.bg-success {
			background-color: #1cc88a !important;
		}

		.bg-warning {
			background-color: #f6c23e !important;
		}

		.bg-danger {
			background-color: #e74a3b !important;
		}

		.bg-purple {
			background-color: #ff4a6b !important;
		}

		.bg-blue {
			background-color: #96b3d9 !important;
		}

		.bg-dark-blue {
			background-color: #96b3d9 !important;
		}

		.bg-gray {
			background-color: #6d8ba6 !important;
		}

		.space{
			margin-top: 10px;
			margin-bottom: 5px;
		}

		.label-right{
			text-align: right;
		}
	</style>
</head>

<body>
	<div id="wrap">
		<?php $this->load->view('backend/components/header') ?>
		<!-- echo -->

		<br><br><br>
		<div class="col-md-12">
			<div class="col-md-12 card border-left-warning">
				<div class="card-header text-primary"><i class='material-icons'>arrow_forward</i><strong>On Terminal Export</strong></div>

				<div class="panel panel-default">
					<div class="panel-body">

						<div class="row">
							<div class="col-md-3">
								<div class="card">
									<div class="card-header bg-warning text-white">Truck In</div>
									<div class="card-body" style="
									padding-bottom: 1px;
									height: 140px;
									">
									<div class="row">
										<div class="col-md-6">
											<div class="panel panel-default">
												
												<div class="panel-heading">
													<strong >Visit Announced</strong>
												</div>
												<a href="<?php echo base_url('DashboardReal/visit_truk_in');?>">
												<div class="panel-body">
													<?php echo $data[0]->JML_VISIT?> <!--TRUCK IN-->
												</div>
												</a>
											</div>
										</div>
										<div class="col-md-6">
											<div class="panel panel-default">
												<div class="panel-heading">
													<strong>VIN Announced</strong>
												</div>
												<a href="<?php echo base_url('DashboardReal/vin_truk_in');?>">
												<div class="panel-body">
													<?php echo $data[0]->JML_VIN?> <!--TRUCK IN-->
												</div> </a>
											</div>
										</div>
									</div>
								</div>

							</div>
						</div>
						<div class="col-md-6">
							<div class="card">
								<div class="card-header bg-primary text-white">Terminal In</div>
								<div class="card-body" style="
								padding-bottom: 1px;
								height: 140px;
								">
								<div class="row">
									<div class="col-md-4">
										<!-- <img class="img-responsive" src="https://img.freepik.com/free-vector/pricing-table_23-2148153440.jpg?size=338&ext=jpg" alt="Chania"> -->
										<div class="panel panel-default">
											<div class="panel-heading">
												<strong>Jumlah NON NPE</strong>
											</div>
											<a href="<?php echo base_url('DashboardReal/non_npe_detail');?>">
												<div class="panel-body">
													<!-- <a href="<?php echo base_url('DashboardReal/npe_in');?>"> -->
														<!-- <?php echo $data[6]->JML_VIN?>  -->
														<?php echo $data[8]->JML_VIN?>
												</div> </a>
										</div>

									</div>
									<div class="col-md-4">
										<!-- <img class="img-responsive" src="https://img.freepik.com/free-vector/pricing-table_23-2148153440.jpg?size=338&ext=jpg" alt="Chania"> -->
										<div class="panel panel-default">
											<div class="panel-heading">
												<strong>Jumlah NPE</strong>
											</div>
											<a href="#">
												<div class="panel-body">
													<a href="<?php echo base_url('DashboardReal/npe_detail');?>">
														<?php echo $data[6]->JML_VIN?><!--NPE IN-->					
												</div> </a>
										</div>
									</div>
									<div class="col-md-4">
										<div class="panel panel-default">
											<div class="panel-heading">
												<strong>Jumlah Vin</strong>
											</div>
											<a href="<?php echo base_url('DashboardReal/jumlah_vin_terminal_in');?>">
												<div class="panel-body">
													<?php echo $data[4]->JML_VIN?> <!--TERMINAL IN-->					
												</div> </a>
										</div>
									</div>
									
								</div>
								<br>

							</div>  
						</div>
					</div>

					<div class="col-md-3">
						<div class="card">
							<div class="card-header bg-success text-white">Vessel Export</div>
							<div class="card-body" style="
							padding-bottom: 1px;
							height: 140px;
							">
							<div class="row">
								<div class="col-md-6">
									<div class="panel panel-default">
										<div class="panel-heading">
											<strong>Visit Left</strong>
										</div>
										<a href="<?php echo base_url('DashboardReal/visit_vessel_export');?>">
												<div class="panel-body">
													<?php echo $data[2]->JML_VISIT?> <!--VESEL IN-->
																		
												</div> </a>
									</div>
								</div>
								<div class="col-md-6">
									<div class="panel panel-default">
										<div class="panel-heading">
											<strong>VIN Left</strong>
										</div>
										<a href="<?php echo base_url('DashboardReal/vin_vessel_export');?>">
												<div class="panel-body">
													<?php echo $data[2]->JML_VIN?> <!--VESEL IN-->			
												</div> </a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>




	</div>
</div><!-- /.container -->
<div class="col-md-12">
	<div class="col-md-12 card border-left-warning">
		<div class="card-header text-primary"><i class='material-icons'>arrow_back</i><strong>On Terminal Import</strong></div>

				<div class="panel panel-default">
					<div class="panel-body">

						<div class="row">
							<div class="col-md-3">
								<div class="card">
									<div class="card-header bg-warning text-white">Truck Out</div>
									<div class="card-body" style="
									padding-bottom: 1px;
									height: 140px;
									">
									<div class="row">
										<div class="col-md-6">
											<div class="panel panel-default">
												
												<div class="panel-heading">
													<strong >Visit Left</strong>
												</div>
												<a href="<?php echo base_url('DashboardReal/visit_truk_out');?>">
												<div class="panel-body">
													<?php echo $data[1]->JML_VISIT?> <!--TRUCK OUT-->
												</div>
												</a>
											</div>
										</div>
										<div class="col-md-6">
											<div class="panel panel-default">
												<div class="panel-heading">
													<strong>VIN Left</strong>
												</div>
												<a href="<?php echo base_url('DashboardReal/vin_truk_out');?>">
												<div class="panel-body">
													<?php echo $data[1]->JML_VIN?> <!--TRUCK OUT-->					
												</div> </a>
											</div>
										</div>
									</div>
								</div>

							</div>
						</div>
						<div class="col-md-6">
							<div class="card">
								<div class="card-header bg-primary text-white">Terminal Out</div>
								<div class="card-body" style="
								padding-bottom: 1px;
								height: 140px;
								">
								<div class="row">
									<div class="col-md-4">
										<!-- <img class="img-responsive" src="https://img.freepik.com/free-vector/pricing-table_23-2148153440.jpg?size=338&ext=jpg" alt="Chania"> -->

										<div class="panel panel-default">
											<div class="panel-heading">
												<strong>Jumlah NON SPPB</strong>
											</div>
											<a href="<?php echo base_url('DashboardReal/non_sppb_detail');?>">
												<div class="panel-body">
													<!-- <a href="<?php echo base_url('DashboardReal/npe_in');?>"> -->
														<!-- <?php echo $data[6]->JML_VIN?>  -->
														<?php echo $data[9]->JML_VIN?>
												</div> </a>
										</div>

									</div>
									<div class="col-md-4">
										<!-- <img class="img-responsive" src="https://img.freepik.com/free-vector/pricing-table_23-2148153440.jpg?size=338&ext=jpg" alt="Chania"> -->
										<div class="panel panel-default">
											<div class="panel-heading">
												<strong>Jumlah SPPB</strong>
											</div>
											<a href="#">
												<div class="panel-body">
													<a href="<?php echo base_url('DashboardReal/sppb_detail');?>">
													<?php echo $data[7]->JML_VIN?> <!--NPE OUT-->					
												</div> </a>
										</div>

									</div>
									<div class="col-md-4">
										<div class="panel panel-default">
											<div class="panel-heading">
												<strong>Jumlah VIN</strong>
											</div>
											<a href="#">
												<div class="panel-body">
													<a href="<?php echo base_url('DashboardReal/jumlah_vin_terminal_out');?>">
													<?php echo $data[5]->JML_VIN?> <!--TERMINAL OUT-->					
												</div> </a>
										</div>
									</div>
									
								</div>
								<br>

							</div>  
						</div>
					</div>
					<div class="col-md-3">
						<div class="card">
							<div class="card-header bg-success text-white">Vessel Import</div>
							<div class="card-body" style="
							padding-bottom: 1px;
							height: 140px;
							">
							<div class="row">
								<div class="col-md-6">
									<div class="panel panel-default">
										<div class="panel-heading">
											<strong>Visit Announced</strong>
										</div>
										<a href="<?php echo base_url('DashboardReal/visit_vessel_import');?>">
												<div class="panel-body">
													<?php echo $data[3]->JML_VISIT?> <!--VESSEL OUT-->				
												</div> </a>
									</div>
								</div>
								<div class="col-md-6">
									<div class="panel panel-default">
										<div class="panel-heading">
											<strong>VIN Announced</strong>
										</div>
										<a href="<?php echo base_url('DashboardReal/vin_vessel_import');?>">
												<div class="panel-body">
													<?php echo $data[3]->JML_VIN?> <!--VESSEL OUT-->
												</div> </a>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>

			</div>
		</div>
	</div>




	</div>
</div>
</div>




</div>
</div><!-- /.container -->



<?php $this->load->view('backend/elements/footer') ?>

</body>
</html>