<?php echo form_open($this->router->fetch_class().'/'.$this->router->fetch_method(), array('class' => 'row form-inline', 'role' => 'form')) ?>
    <div class="form-group col-lg-4">
        <input type="text" name="start_date" class="form-control date" placeholder="Ketikkan Tanggal Mulai" id="start_date" value="<?php echo post('start_date') ?>" />
    </div>
    <div class="form-group col-lg-4">
        <input type="text" name="keyword" class="form-control date" placeholder="Ketikkan Frasa Pencarian" id="end_date" value="<?php echo post('keyword') ?>" />
    </div>
    <div class="form-group col-lg-4">
        <button type="submit" class="btn btn-default">Cari</button>
        <?php
		if(post('start_date')){
		?>
        <a href="<?php echo site_url($this->router->fetch_class().'/'.$this->router->fetch_method()) ?>" class="btn btn-warning">Reset</a>
        <?php
		}
		?>
    </div>
<?php echo form_close() ?>