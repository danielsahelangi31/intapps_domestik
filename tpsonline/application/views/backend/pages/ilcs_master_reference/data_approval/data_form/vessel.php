<div class="col-lg-12 alpha beta data-form">
	<fieldset class="fieldset-bordered fieldset-nested" id="vessel">
		<legend class="fieldset-bordered">
			<ol class="breadcrumb">
				<li><a name="<?php echo get_class($obj) ?>" class="collapse_fieldset" href="#"><span class="caret"></span></a></li>
				<?php
				foreach($trace as $class_name => $humanize_name){
				?>
				<li><a href="#<?php echo $class_name ?>" class="goto_form"><?php echo $humanize_name ?></a></li>
				<?php
				}
				?>
				<li class="active"><?php echo humanize(get_class($obj)) ?></li>
				<?php
				$trace[get_class($obj)] = humanize(get_class($obj));
				$field_name .= '['.get_class($obj).']';
				?>
			</ol>
		</legend>
		<?php
		$data = $obj->data;
		?>
		<div class="col-lg-6">
			<div class="form-group">
				<label class="col-lg-4 control-label">IMO Number</label>
				<div class="col-lg-8">
					<p class="form-control-static"><?php echo $data->imo_number ?></p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-4 control-label">MMSI</label>
				<div class="col-lg-8">
					<p class="form-control-static"><?php echo $data->mmsi ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-4 control-label">Call Sign</label>
				<div class="col-lg-8">
					<p class="form-control-static"><?php echo $data->call_sign ?></p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-4 control-label">Nama Kapal</label>
				<div class="col-lg-8">
					<p class="form-control-static"><?php echo $data->vessel_name ?></p>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			<div class="form-group">
				<label class="col-lg-4 control-label">Panjang Kapal</label>
				<div class="col-lg-8">
					<p class="form-control-static"><?php echo $data->length_overall ?></p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-4 control-label">Lebar Kapal</label>
				<div class="col-lg-8">
					<p class="form-control-static"><?php echo $data->breadth ?> m</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-4 control-label">Dead Weight Tonnage</label>
				<div class="col-lg-8">
					<p class="form-control-static"><?php echo $data->dead_weight_tonnage ?> Ton</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-4 control-label">Bendera</label>
				<div class="col-lg-8">
					<p class="form-control-static"><?php echo $data->flag ?></p>
				</div>
			</div>
		</div>
		
		<div class="col-lg-6 detail_data" style="display:none;">
			<div class="form-group">
				<label class="col-lg-4 control-label">Kap. Penumpang</label>
				<div class="col-lg-8">
					<p class="form-control-static"><?php echo $data->passanger_capacity ?></p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-4 control-label">Kap. Cairan</label>
				<div class="col-lg-8">
					<p class="form-control-static"><?php echo $data->liquid_capacity ?></p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-4 control-label">Kap. Petikemas</label>
				<div class="col-lg-8">
					<p class="form-control-static"><?php echo $data->teus_capacity ?></p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-4 control-label">Kap. Petikemas Pendingin</label>
				<div class="col-lg-8">
					<p class="form-control-static"><?php echo $data->reefer_capacity ?></p>
				</div>
			</div>
		</div>
		<div class="col-lg-6 detail_data" style="display:none;">
			<div class="form-group">
				<label class="col-lg-4 control-label">Draft Maks</label>
				<div class="col-lg-8">
					<p class="form-control-static"><?php echo $data->max_draft ?> m</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-4 control-label">Draft Depan</label>
				<div class="col-lg-8">
					<p class="form-control-static"><?php echo $data->max_draft ?> m</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-4 control-label">Draft Belakang</label>
				<div class="col-lg-8">
					<p class="form-control-static"><?php echo $data->max_draft ?> m</p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-4 control-label">Tahun Pembuatan</label>
				<div class="col-lg-8">
					<p class="form-control-static"><?php echo $data->year_of_construction ?></p>
				</div>
			</div>
			<div class="form-group">
				<label class="col-lg-4 control-label">Gross Tonnage</label>
				<div class="col-lg-8">
					<p class="form-control-static"><?php echo $data->gross_tonnage ?> Ton</p>
				</div>
			</div>
			
		</div>
		
		<div class="clearfix"></div>
		
		<a href="#" class="detail_toggle">Lihat Detail</a>
		<div class="clearfix"></div>
		<hr>
		
		<p><strong>Saran Penggabungan Data</strong></p>
		
		<button class="btn btn-primary fr new_data" type="button">Tetapkan Sebagai Data Baru</button>
		<button class="btn btn-primary fr show_lookup" type="button" name="vessel">Cari Data Lain</button>
		
		<br><br>
		
		<?php 
		$class_name = get_class($obj);
		
		$grid_var = array(
			'class_name' => $class_name,
			'field_name' => $field_name,
			'suggestion_fields' => $class_name::$lookup_fields,
			'suggestions' => $obj->suggestions,
			'trace' => $trace
		);
		
		$this->load->view('backend/pages/ilcs_master_reference/data_approval/form_component/table_grid', $grid_var); 
		
		if($obj->parents){
		?>
		<div class="parents-ct">
		<?php
			foreach($obj->parents as $class_name => $val){
				if(is_object($val)){
					$view = array(
						'obj' => $val,
						'field_name' => $field_name,
						'trace' => $trace
					);
					$this->load->view('backend/pages/ilcs_master_reference/data_approval/data_form/'.$class_name, $view);
				}
			}
		?>
		</div>
		<?php
		}
		?>
		<!-- Parents Container -->
	</fieldset>
</div>