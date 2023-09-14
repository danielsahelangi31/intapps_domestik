<?php

class Model_template_excel extends CI_Model
{
    public function __construct()
    {
        parent::__construct();
    }


    public function getModel()
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);

        $queryModel = 
        "SELECT DISTINCT NAME FROM M_CATEGORY
        ORDER BY NAME ASC";
        $dataModel = $db_car->query($queryModel)->result_array();

        
        return $dataModel;

      
    }

    public function getFinal()
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);

        $queryFinal = 
        "SELECT DISTINCT PORT_NAME FROM M_PORT
        ORDER BY PORT_NAME ASC
        ";
        $dataFinal = $db_car->query($queryFinal)->result_array();

        
        return $dataFinal;

      
    }

    public function getConsignee()
    {
        $db_car = $this->load->database('integrasi_cardom_dev', TRUE);

        $queryConsignee = 
        "SELECT DISTINCT NAME,ID FROM M_ORGANIZATION WHERE TYPE = 'SHIPPING_LINE'
        ORDER BY NAME  ASC
        ";
        $dataConsignee = $db_car->query($queryConsignee)->result_array();

        
        return $dataConsignee;

      
    }

    public function getTruck()
    {
    $db = $this->load->database("ilcs_tps_online", TRUE);

    $queryTruck = "SELECT * FROM STID_MST_TRUCK";
    $dataTruck = $db->query($queryTruck)->result_array();

    var_dump($dataTruck);die;
    return $dataTruck;

    }
}
?>