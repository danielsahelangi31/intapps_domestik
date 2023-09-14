<?php

function secho($str){
	echo htmlspecialchars($str);	
}

function post($key= '', $clean = false){
	if($key){
		$CI =& get_instance();	
		return $CI->input->post($key);
	}else{
		return $_POST;
	}
}

function is_post_request(){
	if($_SERVER['REQUEST_METHOD'] === 'POST'){
		return true;
	}else{
		return false;
	}
}

function model($model = '', $alias = ''){
	$CI =& get_instance();

	if($alias){
		$CI->load->model($model, $alias);
		return $CI->$alias;
	}else{
		$CI->load->model($model);
		$modelName = explode('/', $model);
		$modelName = $modelName[count($modelName) - 1];
		return $CI->$modelName;
	}
}

function library($lib = ''){
	$libName = explode('/', $lib);
	
	$CI =& get_instance();
	$CI->load->library($lib);
	
	$libName = $libName[count($libName) - 1];
	return $CI->{$libName};
}



function gridHeader($field, $label, $cfg){
	$urlAdd = ($cfg->keyword ? "/kw:$cfg->keyword" : '').($cfg->searchField ? "/sf:$cfg->searchField" : '');
	
	if($cfg->sortField == $field){
		if($cfg->sortMethod == 'ASC'){
			$url = "$cfg->base/ob:$field@DESC".$urlAdd;
			
			$class = 'glyphicon glyphicon-arrow-up';
			$tooltip = 'Ascending';
		}else{
			$url = "$cfg->base/ob:$field@ASC".$urlAdd;
			$class = 'glyphicon glyphicon-arrow-down';
			$tooltip = 'Descending';
		}
	}else{
		$url = "$cfg->base/ob:$field@ASC".$urlAdd;
		$class = '';
		$tooltip = '';
	}
?>
<a href="<?php echo site_url($url) ?>" class=""><span class="<?php echo $class ?>" title="<?php echo $tooltip ?>"></span> <?php echo $label ?></a>
<?php
}

function  kekata($x) {
	$x = abs($x);
	$angka = array("", "satu", "dua", "tiga", "empat", "lima", "enam", "tujuh", "delapan", "sembilan", "sepuluh", "sebelas");
	$temp = "";
	if($x <12) {
		$temp = " ". $angka[$x];
	}else if($x <20) {
		$temp = kekata($x - 10). " belas";
	}else if($x <100) {
		$temp = kekata($x/10)." puluh". kekata($x % 10);
	}else if($x <200) {
		$temp = " seratus" . kekata($x - 100);
	}else if($x <1000) {
		$temp = kekata($x/100) . " ratus" . kekata($x % 100);
	}else if($x <2000) {
		$temp = " seribu" . kekata($x - 1000);
	}else if($x <1000000) {
		$temp = kekata($x/1000) . " ribu" . kekata($x % 1000);
	}else if($x <1000000000) {
		$temp = kekata($x/1000000) . " juta" . kekata($x % 1000000);
	}else if($x <1000000000000) {
		$temp = kekata($x/1000000000) . " milyar" . kekata(fmod($x,1000000000));
	}else if($x <1000000000000000) {
		$temp = kekata($x/1000000000000) . " trilyun" . kekata(fmod($x,1000000000000));
	}      
		return $temp;
}
function  getBilangan($x, $style=4) {
	if($x<0) {
		$hasil = "minus ". trim(kekata($x));
	} else {
		$hasil = trim(kekata($x)) ." rupiah";
	}
	 
	return $hasil;
}

function bln_rom($bln){
    switch ($bln){
        case 1:
            return "I";
            break;
        case 2:
            return "II";
            break;
        case 3:
            return "III";
            break;
        case 4:
            return "IV";
            break;
        case 5:
            return "V";
            break;
        case 6:
            return "VI";
            break;
        case 7:
            return "VII";
            break;
        case 8:
            return "VIII";
            break;
        case 9:
            return "IX";
            break;
        case 10:
            return "X";
            break;
        case 11:
            return "XI";
            break;
        case 12:
            return "XII";
            break;
    }
}