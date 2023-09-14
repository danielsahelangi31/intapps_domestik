<?php
$target_url = $this->router->fetch_class().'/'.$this->router->fetch_method();
if($this->router->fetch_directory()){
	$target_url = $this->router->fetch_directory().'/'.$target_url;
}
?>
<?php echo form_open($target_url, array('class' => 'row form-inline', 'role' => 'form')) ?>
    <div class="form-group col-lg-4">
        <select class="form-control" name="searchfield">
        <?php
		if(isset($searchable)){
			foreach($searchable as $name => $label){
		?>
         	<option value="<?php echo strtoupper($name) ?>" <?php if(strtoupper($name) == post('searchfield')) echo 'selected="selected"' ?>><?php echo $label ?></option>
        <?php
			}
		}else{
		?>
        	<option>ERROR! Please Define Searchable On Models &amp; Controllers</option>
        <?php
		}
		?>
        </select>
    </div>
    <div class="form-group col-lg-4">
        <input type="text" name="keyword" class="form-control" style="text-transform: uppercase" placeholder="Pencarian"  value="<?php echo  strtoupper(post('keyword')) ?>" />
    </div>
    <div class="form-group col-lg-4">
        <button type="submit" class="btn btn-default">Cari</button>
        <?php
		if(strtoupper(post('keyword'))){
		?>
        <a href="<?php echo site_url($target_url) ?>" class="btn btn-warning">Reset</a>
        <?php
		}
		?>
    </div>
<?php echo form_close() ?>