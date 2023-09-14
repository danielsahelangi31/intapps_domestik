<?php
require_once('./application/models/base/modelbase.php');

class Entry_Ticket_Model extends ModelBase{
    // Datagrid Sortable Fields

    public function __construct(){
        parent::__construct();
    }

    public function set_db($db){
        $this->db = $db;
    }



}