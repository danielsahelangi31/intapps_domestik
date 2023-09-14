<div class="well well-sm">
	<p style="margin-top:0px"><strong>Report ID: Cartos/CartosBMKapal</strong></p>
	<p><em>Menampilkan data statistik bongkar muat yang tercatat di sistem CarTOS. Laporan dibuat per periode dengan filter tambahan per Visit ID</em></p>
	
	<div class="form-group">
		<label class="col-lg-4 control-label">Visit ID</label>
		<div class="col-lg-8">
			<input type="text" class="form-control" name="visit_id" value="" />
		</div>
	</div>
	<div class="form-group">
		<label class="col-lg-4 control-label">Format Keluaran</label>
		<div class="col-lg-8">
			<select class="form-control" name="output_format">
				<option value="HTML">HTML</option>
				<option value="EXCEL2007">Excel 2007 (.xlsx)</option>
			</select>
		</div>
	</div>
	
	<div class="form-group">
		<div class="col-lg-12">
			<div class="pull-right">
				<button type="submit" class="btn btn-primary"><span class="glyphicon glyphicon-new-window"></span> Buat Laporan</button>
			</div>
		</div>
	</div>
</div>