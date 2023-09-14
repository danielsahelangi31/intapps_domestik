<?php
class MY_Form_validation extends CI_Form_validation {
	function is_exist($str, $value){
		list($table, $field)=explode('.', $field);
		$query = $this->CI->db->limit(1)->get_where($table, array($field => $str));
		
		return $query->num_rows() !== 0;
	}
}