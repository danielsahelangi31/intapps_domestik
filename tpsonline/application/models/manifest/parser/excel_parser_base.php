<?php
class Excel_Parser_Base extends CI_Model{
	// Helper untuk dapatkan isi dari array takutnya index yg dicari gak ada jd raising error
	public function get(&$array, $idx, $type = 'string'){
		if(isset($array[$idx])){ 
			switch($type){
				case 'time':
					return $this->convertTime($array[$idx]);
					break;
				case 'date':
					return $this->convertDate($array[$idx]);
					break;
				case 'datetime':
					return $this->convertDateTime($array[$idx]);
					break;
				case 'numeric':
					return $this->convertNumeric($array[$idx]);
			}
			
			// Default is string
			return trim($array[$idx]);
			
		}else return NULL;	
	}
	
	public function empty_row($row){
		foreach($row as $field){
			if($field){
				return false;
			}
		}
		return true;
	}
	
	public function convertNumeric($val){
		if(is_numeric($val)){
			return $val;
		}else{
			return NULL;
		}
	}
	
	public function convertTime($val){
		if(is_float($val)){
			$sehari = 86400;
			$detik = $val * $sehari;
			
			$jam = floor($detik / 3600);
			$menit = floor(($detik - ($jam * 3600)) / 60);
			
			return $jam.':'.$menit;
		}else{
			return $val;
		}
	}
	
	public function convertDate($val){
		if(is_int($val)){
			// EPOCH Excel Time lebih awal 2209161600 detik
			$phpTime = ($val * 86400) - 2209161600;
			return gmdate('Y-m-d', $phpTime);
		}else{
			return gmdate('Y-m-d', strtotime($val));
		}
	}
	
	public function convertDateTime($val){
		if(is_numeric($val)){
			// EPOCH Excel Time lebih awal 2209161600 detik
			$phpTime = ($val * 86400) - 2209161600;
			return gmdate('Y-m-d H:i:s', $phpTime);
		}else{
			return gmdate('Y-m-d H:i:s', strtotime($val));
		}
	}
	
}