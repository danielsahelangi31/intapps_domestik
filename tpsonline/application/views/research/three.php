<?php
foreach($obj as $key => $val){
	if(is_object($val)){
		$view = array(
			'obj' => $val
		);
		$this->load->view('research/'.$key, $view);
	}else{
		echo '<h1>'.$key.' => '.$val.'</h1>';
	}
}

var_dump($obj);
echo 'THREE';
echo '<hr/>';