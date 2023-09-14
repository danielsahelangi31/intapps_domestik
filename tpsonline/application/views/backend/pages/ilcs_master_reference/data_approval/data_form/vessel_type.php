<div class="col-lg-12 alpha beta data-form">
	<fieldset class="fieldset-bordered fieldset-nested" id="vessel_type">
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
				<label class="col-lg-4 control-label">Jenis Kapal</label>
				<div class="col-lg-8">
					<p class="form-control-static" id="kode_shipping_line">Container Ship</p>
				</div>
			</div>
		</div>
		<div class="col-lg-6">
			
		
		</div>
		
		<div class="clearfix"></div>
		<hr>
		
		<p><strong>Saran Penggabungan Data</strong></p>
		
		<button class="btn btn-primary fr new_data" type="button">Tetapkan Sebagai Data Baru</button>
		<button class="btn btn-primary fr show_lookup" type="button" name="vessel_type">Cari Data Lain</button>
		
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
		<!-- Parents Container -->		
	</fieldset>
</div>