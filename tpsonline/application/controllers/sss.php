<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Eticket extends CI_Controller {

    var $dummy;

    public function __construct(){
        parent::__construct();
        $this->load->library(array('form_validation'));
        $this->load->helper(array('form'));
        // Dapatkan data login
        if(!$this->auth = $this->userauth->getLoginData()){
            redirect(LOGIN_PAGE);
        }

        $this->dummy = array(
            (object) array('code'=>'B9294HH','license'=>'B 9294 HH','carrier'=>'PT. KMDI','driver'=>'Andri','company' => 'PT.Yusen Logistic','truck_type' => 'Towing','plan_date'=>date("Y-m-d", strtotime('2020-11-10')),'eticket'=>'ETKT-SZKI-3452-444441','vin'=>array((object)array('status'=>'R','vin_number'=>'123123123123123123'))),
            (object) array('code'=>'B9543BOH','license'=>'B 9543 BOH','carrier'=>'PT. Indah Cargo','driver'=>'Soegiyanto','company' => 'PT.Indah Cargo Logistic','truck_type' => 'Towing','plan_date'=>date("Y-m-d", strtotime('2020-08-15')),'eticket'=>'ETKT-HNDA-0820-000001','vin'=>array((object)array('status'=>'R','vin_number'=>'12301-24xcvd123-123sg36'),(object)array('status'=>'R','vin_number'=>'34523-2352saxcv-21369dsc'),(object)array('status'=>'L','vin_number'=>'56123-234509sdv-2359639'))),
            (object) array('code'=>'B9879UPK','license'=>'B 9879 UPK','carrier'=>'PT. Lorena Express','driver'=>'Jecky','company' => 'PT.Tanto','truck_type' => 'Towing','plan_date'=>date("Y-m-d", strtotime('2020-11-25')),'eticket'=>'ETKT-WLNG-4123-546321','vin'=>array((object)array('status'=>'L','vin_number'=>'78967967967967867'))),
        );

    }



    public function truck_id_list(){
        $data = array(
            'datas' => $this->dummy
        );
        $this->load->view('backend/pages/eticket/truck_id_list',$data);
    }

    public function truck_id_detail($license){

        $datas = null;

        foreach ( $this->dummy as $element ) {
            if ( $license == $element->code ) {
                $datas = $element;
            }
        }

        $data = array(
            'datas' =>$datas
        );

        $this->load->view('backend/pages/eticket/truck_id_detail',$data);
    }

    public function create_associate(){
        $this->load->view('backend/pages/eticket/create_associate');
    }

    public function detail_create_associate(){
        $datas = null;

        foreach ( $this->dummy as $element ) {
            if ( $this->input->post('truck_id') == $element->code ) {
                $datas = $element;
            }
        }

        $data = array(
            'datas' =>$datas
        );

        $this->load->view('backend/pages/eticket/create_associate_detail',$data);
    }

    public function submit_create_associate(){

        foreach ( $this->dummy as $key => &$element ) {
            if ( $this->input->post('truck_id') == $element->code ) {
                $element->plan_date = date("Y-m-d", strtotime($this->input->post('plan_date')));
            }
        }
        echo '<pre>';
        var_dump($this->dummy );die();
        echo '</pre>';
    }

    public function eticket_list(){
        $data = array(
            'datas' => $this->dummy
        );
        $this->load->view('backend/pages/eticket/eticket_list',$data);
    }

    public function e_ticket_detail($eticket){
        $datas = null;

        foreach ( $this->dummy as $element ) {
            if ( $eticket == $element->eticket ) {
                $datas = $element;
            }
        }

        $data = array(
            'datas' =>$datas
        );

        $this->load->view('backend/pages/eticket/e_ticket_detail',$data);
    }

    public function update_vin(){
        $this->load->view('backend/pages/eticket/update_vin');
    }

    public function update_vin_detail(){


        $mod = model('etickets');
        $res = $mod->getAsosiasiByTruckVisitID($this->userauth->getLoginData()->sender,$this->input->post('truck_id'));

        $data = array(
            'datas' =>$res->response->itrAsosiasi
        );

        $this->load->view('backend/pages/eticket/update_vin_detail',$data);
    }

    public function update_vin_submit(){
        foreach ( $this->dummy as $key => &$element ) {
            if ( $this->input->post('truck_id') == $element->code ) {
                $element->plan_date = date("Y-m-d", strtotime($this->input->post('plan_date')));
            }
        }
        echo '<pre>';
        var_dump($this->dummy );die();
        echo '</pre>';
    }

    public function create_announce_truck(){
        $mod = model('etickets');
        $res = $mod->get_model($this->userauth->getLoginData()->sender);
        $destinate = $mod->getDestination();
        $controllings = $mod->getControlling();
        $consignees = $mod->getControlling();

        $data = array(
            'models' => $res,
            'destinates' => $destinate,
            'controllings' => $controllings,
            'consignees' => $consignees,
        );


        $this->load->view('backend/pages/eticket/announce/create_announce_truck',$data);
    }

    public function create_announce_truck_submit(){

        $vin = [];
        $mod = model('etickets');
        $truckInfo = $mod->getTruckInfo($this->input->post('truckCode'));

        for ($i = 1 ; $i <=intval($this->input->post('length_vin')) ; $i++){
            $vin['vinDetail'][] = array(
                'VinNumber' => $this->input->post('VinNumber'.$i),
                'Direction' => $this->input->post('direction'.$i),
                'Directiontype' => $this->input->post('directionType'.$i),
                'Fuel' => $this->input->post('fuel'.$i),
                'Model' => $this->input->post('models'.$i),
                'Destination' => $this->input->post('destinate'.$i),
                'Controlling_Organization' => $this->input->post('controlling_org'.$i),
                'Consignee' => $this->input->post('consignee'.$i)
            );
        }

        $payload = array(
            'ADCMessageHeader' => array(
                'DocumentTransferId' => $this->input->post('DocumentTransferId'),
                'MessageType' => 'ANNOUNCE_TRUCK',
                'Sender' => $this->userauth->getLoginData()->sender,
                'Receiver' => 'CARTOS',
                'SentTime' => date("Ymdhis")
            ),
            'ADCMessageBody' => array(
                'AnnounceTruckReqest' => array(
                    'announceTruckInfo' => array(
                        'truckInfo' => array(
                            'visitID' => $this->input->post('truck_visit_id') ? $this->input->post('truck_visit_id') : null,
                            'Truck_Licenseplate' => $truckInfo[0]->TRUCK_CODE,
                            'Truck_Drivername' => $truckInfo[0]->DRIVER_NAME ? $truckInfo[0]->DRIVER_NAME : null,
                            'Truck_Driverphonenumber' => $this->input->post('driverPhoneNumber'),
                            'Truck_Carrier' => $truckInfo[0]->CARRIER_CODE,
                        ),
                        'VinInfo' => $vin
                    )
                )
            )
        );

        $res = $mod->OpAnnounceTruck($payload);

        $this->session->set_flashdata('visitID', $res->response->ADCAcknowledgeBody->AnnounceTruckResponse->announceTruckResponseInfo->visitId);
        redirect(base_url()."eticket/create_announce_truck");

    }

    public function create_announce_vin(){

        $mod = model('etickets');
        $res = $mod->get_model($this->userauth->getLoginData()->sender);
        $destinate = $mod->getDestination();
        $controllings = $mod->getControlling();
        $consignees = $mod->getControlling();

        $data = array(
            'models' => $res,
            'destinates' => $destinate,
            'controllings' => $controllings,
            'consignees' => $consignees
        );


        $this->load->view('backend/pages/eticket/announce/create_announce_vin',$data);
    }

    public function create_announce_vin_submit(){
        $vin = [];
        $mod = model('etickets');

        for ($i = 1 ; $i <=intval($this->input->post('length_vin')) ; $i++){
            $vin['vinDetail'][] = array(
                'VinNumber' => $this->input->post('VinNumber'.$i),
                'Direction' => $this->input->post('direction'.$i),
                'Directiontype' => $this->input->post('directionType'.$i),
                'Fuel' => $this->input->post('fuel'.$i),
                'Model' => $this->input->post('models'.$i),
                'Destination' => $this->input->post('destinate'.$i),
                'Controlling_Organization' => $this->input->post('controlling_org'.$i),
                'Consignee' => $this->input->post('consignee'.$i)
            );
        }

        $payload = array(
            'ADCMessageHeader' => array(
                'DocumentTransferId' => $this->input->post('DocumentTransferId'),
                'MessageType' => 'ANNOUNCE_VIN',
                'Sender' => $this->userauth->getLoginData()->sender,
                'Receiver' => 'CARTOS',
                'SentTime' => date("Ymdhis")
            ),
            'ADCMessageBody' => array(
                'AnnounceVinReqest' => array(
                    'VinInfo' => $vin
                )
            )
        );

        $res = $mod->OpAnnounceVin($payload);

        echo '<pre>';
        var_dump($res);die();
        echo '</pre>';
    }

    public function master_data_truck(){
        $this->load->view('backend/pages/eticket/master/data_truck');
    }

    public function master_data_truck_submit(){
        $mod = model('etickets');
        $datas = $mod->getMasterDataTruck($this->userauth->getLoginData()->sender,$this->input->post('truck_code'));

        echo '<pre>';
        var_dump($datas);die();
        echo '</pre>';

    }


}

/* End of file welcome.php */
/* Location: ./application/controllers/welcome.php */