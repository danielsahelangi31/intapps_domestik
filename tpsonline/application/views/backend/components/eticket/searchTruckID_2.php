<?php
$target_url = $this->router->fetch_class().'/'.$this->router->fetch_method();
if($this->router->fetch_directory()){
    $target_url = $this->router->fetch_directory().'/'.$target_url;
}
?>
<?php echo form_open($target_url, array('class' => 'row form-inline', 'role' => 'form')) ?>
    <div class="form-inline">
        <div class="col-sm-4">
            <p class="lead">
                <small>Search Truck ID</small>
            </p>
        </div>
        <div class="col-sm-4">
            <input type="text" name="keyword" class="form-control" placeholder="Frasa Pencarian"  value="<?php echo post('keyword') ?>" />
        </div>
        <button type="submit" class="btn btn-default">Cari</button>
    </div>

<?php echo form_close() ?>