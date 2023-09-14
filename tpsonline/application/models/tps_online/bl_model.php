<?php
require_once('./application/models/base/modelbase.php');

class Bl_Model extends ModelBase{
	// Datagrid Sortable Fields
	
	public function __construct(){
		parent::__construct();
	}
		
	public function set_db($db){
		$this->db = $db;
	}
	
	public function update($id){        
        $data = array(
                            'BL_NUMBER' => post('bl_baru'),  
                            'FLAG_SEND_CODECO' => 0,
                            'FLAG_SEND_COARRI' => 0,
                            'DATE_SEND_CODECO' => '',
                            'DATE_SEND_COARRI' => '',                        
                    );
                    
        $this->db->where('BL_NUMBER', $id);
        $this->db->update('CARTOS_CARGO', $data);         
                    
        $data1 = array(
                        'BL_NUMBER' => post('bl_baru'),
                        'JUMLAH' => post('jml_vin'),                            
                );
                
        $this->db->where('BL_NUMBER', $id);
        $this->db->update('BL_CARGO_TYPE_MAPPING', $data1);      
                     

	}
	
}