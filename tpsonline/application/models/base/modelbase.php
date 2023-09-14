<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ModelBase extends CI_Model {
	protected $searchField;
	protected $keyword;
	protected $sort;
	protected $offset;
	protected $limit;
	
	public function setFilter($searchField, $keyword){
		$this->searchField = $searchField;
		$this->keyword = $keyword;	
		return $this;	
	}
	
	public function setSort($sort){
		$this->sort = $sort;
		return $this;
	}
	
	public function clearState(){
		$this->filter	= NULL;
		$this->sort		= NULL;	
	}
	
	public function terapkanConfig($cfg){
		if(isset($cfg->sortField) && isset($cfg->sortMethod)){
			$this->sort = new StdClass();
			$this->sort->field 	= $cfg->sortField;
			$this->sort->method = $cfg->sortMethod;
		}
		if(isset($cfg->searchField)) $this->searchField = $cfg->searchField;
		if(isset($cfg->keyword)) $this->keyword 		= $cfg->keyword;
		
		if(isset($cfg->currPage)){
			//echo ($cfg->currPage);
			//die;
			if($cfg->currPage == 1){
				$this->offset 	= ($cfg->currPage - 1) * $cfg->rowPerPage;
				$this->limit 	= $cfg->rowPerPage+1;
			}
			else{
				$this->offset 	= (($cfg->currPage - 1) * $cfg->rowPerPage) + 1;
				$this->limit 	= $cfg->rowPerPage;
			}
		}else{
			$this->offset 	= 0;
			$this->limit 	= $cfg->rowPerPage+1;
		}
	}

	public function siapkanDB($noLimit = false){
		if($this->keyword) 	$this->db->like($this->searchField, urldecode($this->keyword));
		if($this->sort) 	$this->db->order_by($this->sort->field, $this->sort->method);
		
	
		if(!$noLimit){
			$this->db->limit((int) $this->limit, (int) $this->offset);
		}
	}
	
	public function parseParameter($numArgs, $args){
		$searchMethod		= array('ASC', 'DESC');
		
		$cfg = new StdClass();
		
		$cfg->rowPerPage 	= 10;
		$cfg->sortField 	= NULL;
		$cfg->sortMethod 	= NULL;
		$cfg->currPage		= 1;
		$cfg->base			= $this->router->fetch_class().'/'.$this->router->fetch_method();
		
		if($this->router->fetch_directory()){
			$cfg->base = str_replace('/', '', $this->router->fetch_directory()).'/'.$cfg->base;
		}
		
		// Parameter parsing
		
		for($i = 0; $i < $numArgs; $i++){
			$param = $args[$i];
			$subParam = explode(':', $param, 2);			
			if(count($subParam) == 2){				
				switch($subParam[0]){
					case 'p':
						$cfg->currPage = (int) $subParam[1];
						if($cfg->currPage < 1) $cfg->currPage = 1;
						break;	
					case 'ob':
						$microParam = explode('@', $subParam[1], 2);
						if(count($microParam) == 1){
							$microParam[1] = 'ASC';	
						}
						if(isset($this->sortable[$microParam[0]]) && in_array($microParam[1], $searchMethod)){
							$cfg->sortField 	= $microParam[0];
							$cfg->sortMethod 	= $microParam[1];
						}
						break;
					case 'sf':					
						if(!$this->input->post('searchfield') && isset($this->searchable[$subParam[1]])){
							$_POST['searchfield'] = $subParam[1];							
						} else if ($subParam[1] != ""){
							$_POST['searchfield'] = $subParam[1];
						}
						break;
					case 'kw':
						if(!$this->input->post('keyword')){
							$_POST['keyword'] = $subParam[1];
						}
						break;
				}
			}
		}
		
		$cfg->searchField 	= $this->input->post('searchfield');
		$cfg->keyword 		= strtoupper($this->input->post('keyword'));
	
		if($cfg->searchField == 'VISIT_ID'){
			$cfg->searchField = 'A.VISIT_ID';
		}
		
		if($cfg->searchField == 'ETA'){
			$cfg->searchField = "TO_CHAR(ETA,'MM-YYYY')";	
		}
	
		$dates = "'MM-YYYY'";
		if($cfg->searchField == 'ETAA'){
			$cfg->searchField =  'TO_CHAR("ETA",'.$dates.')';			
			$eta = true;
		}	
		if($cfg->searchField == 'ATA'){
			$cfg->searchField =  'TO_CHAR("ATA",'.$dates.')';			
			$ata = true;
		}
		if($cfg->searchField == 'ATB'){
			$cfg->searchField =  'TO_CHAR("ATB",'.$dates.')';			
			$atb = true;
		}
		if($cfg->searchField == 'ATD'){
			$cfg->searchField =  'TO_CHAR("ATD",'.$dates.')';			
			$atd = true;
		}
		
		$dates = "'MM-YYYY'";
		if($cfg->searchField == 'ETDD'){
			$cfg->searchField = 'TO_CHAR("ETD",'.$dates.')';
			$etd = true;
		}
		$date = "'YYYY'";
		if($cfg->searchField == 'PERIODE'){
			$cfg->searchField = 'TO_CHAR("PERIODE",'.$date.')';
		}
		
		if($cfg->searchField == 'ARRIVAL'){
			$cfg->searchField = 'TO_CHAR("ARRIVAL",'.$dates.')';
			$arrival = true;
		}

		if($cfg->searchField == 'OPERATIONAL'){
			$cfg->searchField = 'TO_CHAR("OPERATIONAL",'.$dates.')';
			$operational = true;
		}

		if($cfg->searchField == 'DEPARTURE'){
			$cfg->searchField = 'TO_CHAR("DEPARTURE",'.$dates.')';
			$departure = true;
		}

		if($cfg->searchField == 'ETB'){
			$cfg->searchField = 'TO_CHAR("ETB",'.$dates.')';
			$etb = true;
		}

		if($cfg->searchField == 'ETD'){
			$cfg->searchField = "TO_CHAR(ETD,'MM-YYYY')";
		
		}
		if($cfg->searchField == 'DTS_ONTERMINAL'){
			$cfg->searchField = "TO_CHAR(DTS_ONTERMINAL,'DD-MM-YYYY')";
		}
		if($cfg->searchField == 'DTS_LEFT'){
			$cfg->searchField = "TO_CHAR(DTS_LEFT,'DD-MM-YYYY')";
		}
		if($cfg->searchField == 'VESSEL_STATUS'){
			$cfg->searchField = "DECODE(VESSEL_STATUS,0,'ANNOUNCED',2,'ARRIVED',3,'OPERATIONAL',4,'COMPLETED',5,'LEFT')";
		}
		
		// Paging URL
		$class 	= $this->router->fetch_class();
		$method = $this->router->fetch_method();

		if($eta == true){
		$etas = "ETAA";	
		$cfg->pagingURL		= $cfg->base .	($cfg->searchField ? "/sf:$etas" : '').
											($cfg->keyword ? "/kw:$cfg->keyword" : '').
											($cfg->sortField ? "/ob:$cfg->sortField@$cfg->sortMethod" : '');
		
											
		return $cfg;
		} else if ($etb == true){
		$etbs = "ETB";	
		$cfg->pagingURL		= $cfg->base .	($cfg->searchField ? "/sf:$etbs" : '').
											($cfg->keyword ? "/kw:$cfg->keyword" : '').
											($cfg->sortField ? "/ob:$cfg->sortField@$cfg->sortMethod" : '');
		
											
		return $cfg;
		
		} else if($etd == true){
			$etds = "ETDD";	
			$cfg->pagingURL		= $cfg->base .	($cfg->searchField ? "/sf:$etds" : '').
												($cfg->keyword ? "/kw:$cfg->keyword" : '').
												($cfg->sortField ? "/ob:$cfg->sortField@$cfg->sortMethod" : '');
			
												
			return $cfg;
		}else if($ata == true){
				$atas = "ATA";	
				$cfg->pagingURL		= $cfg->base .	($cfg->searchField ? "/sf:$atas" : '').
													($cfg->keyword ? "/kw:$cfg->keyword" : '').
													($cfg->sortField ? "/ob:$cfg->sortField@$cfg->sortMethod" : '');
				
													
				return $cfg;
		}else if($atb == true){
				$atbs = "ATB";	
				$cfg->pagingURL		= $cfg->base .	($cfg->searchField ? "/sf:$atbs" : '').
														($cfg->keyword ? "/kw:$cfg->keyword" : '').
														($cfg->sortField ? "/ob:$cfg->sortField@$cfg->sortMethod" : '');
					
														
				return $cfg;
		} else if($atd == true){
				$atds = "ATD";	
				$cfg->pagingURL		= $cfg->base .	($cfg->searchField ? "/sf:$atds" : '').
														($cfg->keyword ? "/kw:$cfg->keyword" : '').
														($cfg->sortField ? "/ob:$cfg->sortField@$cfg->sortMethod" : '');
					
														
				return $cfg;
		} else if($arrival == true){
					$arrivals = "ARRIVAL";	
					$cfg->pagingURL		= $cfg->base .	($cfg->searchField ? "/sf:$arrivals" : '').
															($cfg->keyword ? "/kw:$cfg->keyword" : '').
															($cfg->sortField ? "/ob:$cfg->sortField@$cfg->sortMethod" : '');
						
															
					return $cfg;

		} else if($operational == true){
					$operationals = "OPERATIONAL";	
					$cfg->pagingURL		= $cfg->base .	($cfg->searchField ? "/sf:$operationals" : '').
															($cfg->keyword ? "/kw:$cfg->keyword" : '').
															($cfg->sortField ? "/ob:$cfg->sortField@$cfg->sortMethod" : '');
						
															
					return $cfg;
		} else if($departure == true){
					$departures = "DEPARTURE";	
					$cfg->pagingURL		= $cfg->base .	($cfg->searchField ? "/sf:$departures" : '').
															($cfg->keyword ? "/kw:$cfg->keyword" : '').
															($cfg->sortField ? "/ob:$cfg->sortField@$cfg->sortMethod" : '');
						
															
					return $cfg;
		
		} else {
			$cfg->pagingURL		= $cfg->base .	($cfg->searchField ? "/sf:$cfg->searchField" : '').
			($cfg->keyword ? "/kw:$cfg->keyword" : '').
			($cfg->sortField ? "/ob:$cfg->sortField@$cfg->sortMethod" : '');

			
		return $cfg;

		}
		
	}

	public function parseParameterNotifikasi($numArgs, $args){
		$searchMethod		= array('ASC', 'DESC');
		
		$cfg = new StdClass();
		
		$cfg->rowPerPage 	= 10;
		$cfg->sortField 	= NULL;
		$cfg->sortMethod 	= NULL;
		$cfg->currPage		= 1;
		$cfg->base			= $this->router->fetch_class().'/'.$this->router->fetch_method();
		
		if($this->router->fetch_directory()){
			$cfg->base = str_replace('/', '', $this->router->fetch_directory()).'/'.$cfg->base;
		}

		
		
		// Parameter parsing
		for($i = 0; $i < $numArgs; $i++){
			$param = $args[$i];
			$subParam = explode(':', $param, 2);
			// print_r($subParam);
			if(count($subParam) == 2){
				switch($subParam[0]){
					case 'p':
						$cfg->currPage = (int) $subParam[1];
						if($cfg->currPage < 1) $cfg->currPage = 1;
						break;	
					case 'ob':
						$microParam = explode('@', $subParam[1], 2);
						if(count($microParam) == 1){
							$microParam[1] = 'ASC';	
						}
						if(isset($this->sortable[$microParam[0]]) && in_array($microParam[1], $searchMethod)){
							$cfg->sortField 	= $microParam[0];
							$cfg->sortMethod 	= $microParam[1];
						}
						break;
					case 'sf':					
						if(!$this->input->post('year') && isset($this->searchable[$subParam[1]])){
							$_POST['year'] = $subParam[1];
						}						
						break;
					case 'kw':
						if(!$this->input->post('month')){						
							$_POST['month'] = $subParam[1];
						}
						break;
				}
			}
		}
		
		$cfg->searchField 	= $this->input->post('year');
		$cfg->keyword 		= strtoupper($this->input->post('month'));
		
		// Paging URL
		$class 	= $this->router->fetch_class();
		$method = $this->router->fetch_method();
		$cfg->pagingURL		= $cfg->base .	($cfg->searchField ? "/sf:$cfg->searchField" : '').
											($cfg->keyword ? "/kw:$cfg->keyword" : '').
											($cfg->sortField ? "/ob:$cfg->sortField@$cfg->sortMethod" : '');
		
		return $cfg;
	}

}