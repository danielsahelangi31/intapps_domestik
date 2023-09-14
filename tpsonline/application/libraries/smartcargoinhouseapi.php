<?php
/** SmartCargo Inhouse API
  * Digunakan sebagai interface ke middleware
  *
  */

class SmartCargoInhouseAPI extends SoapClient{
	private $location;
	private $uri;
	
	public function __construct(){
		parent::__construct();	
	}
	
	public function checkContainerValidity($terminal_id, $containers){
		if(!is_array($containers)){
			$containers = array($containers);	
		}
		
		
	}
}