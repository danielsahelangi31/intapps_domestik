<?php
class XML_Traverse extends CI_Controller{
	public function pull(){
		set_time_limit(3000);
	
		$URI = 'D:\PROJECTS\ILCS Master Reference Project\Documents\HSCODE\Applied_nonMFN_Tariffs.xml';
		
		$xml = new XMLReader();
		$xml->open($URI);
		
		$temp_obj = new StdClass();
		$hscodes = array();
	
		$start = microtime(true);
		
		$db = $this->load->database('ilcs_master_reference', TRUE);
		
		$i = 0;
		while($xml->read()){
			if($xml->nodeType == XMLReader::ELEMENT){
				if($xml->name == 'DataTable'){
					$temp_obj = new StdClass();
				}else if($xml->name != 'DocumentElement'){
					$temp_obj->{$xml->name} = $xml->readInnerXML();
				}
			}else if($xml->nodeType == XMLReader::END_ELEMENT && $xml->name == 'DataTable'){
				$i++;
				/*
				if(isset($hscodes[$temp_obj->Product])){
					
				}else if($db->where('hscode', $temp_obj->Product)->get('hscode')->row()){
					$hscodes[$temp_obj->Product] = true;
				}else{
					$hscode = array(
						'hscode' => $temp_obj->Product,
						'description' => $temp_obj->Description,
						'base_nomenclature' => $temp_obj->BaseNomenclature,
						'number_of_subheading' => $temp_obj->NumberOfSubheadings,
						'year' => $temp_obj->Year,
					);
					
					$db->insert('hscode', $hscode);
					
					echo 'INPUT: '.$temp_obj->Product.'<br>';
				}*/
				
			}
		}
		
		echo '<h1>'.$i.'</h1>';
		
		$end = microtime(true);
		echo 'EXECUTED IN: '.(($end - $start) * 1000).'ms';
		
	}

	public function brekele(){
		$URI = './application/data/BREKELE_CUSCAR.xml';
		//$URI = './application/data/sip.xml';
		
		
		$search = "Message to transmit information about equipment and goods on a means of transport, including their location on the means of transport. The message can be exchanged between (liner's) agents, tonnage centers, stevedores and ships masters/operators.";
		$search = 'Undetectable';
		//$search = 'Kwek';
		
		$nonProcessedType = array(); //array(XMLReader::COMMENT, XMLReader::CDATA, XMLReader::WHITESPACE, XMLReader::SIGNIFICANT_WHITESPACE);
	
		$xml = new XMLReader();
		$xml->open($URI);
		
		$pathBuffer = array();
		
		
		$last_el = NULL;
		while($xml->read()){
			if($xml->nodeType == XMLReader::ELEMENT){
				if($xml->getAttribute() == $search){
					foreach($pathBuffer as $item){
						if($item->pos > 0){
							echo $item->name.'['.$item->pos.']';
						}else{
							echo $item->name.'[0]';
						}
						
						echo '/';
					}
					
					break;
				}
			
				$temp = new StdClass();
				$temp->name = $xml->name;
				
				if($last_el && $last_el->name == $xml->name){
					$temp->pos = $last_el->pos + 1;
				}else{
					$temp->pos = 0;
				}
				
				array_push($pathBuffer, $temp);
			}else if($xml->nodeType == XMLReader::END_ELEMENT){
				$last_el = array_pop($pathBuffer);
			}
		}
		
		$xml->close();
	}
	
	/*
	public function find_node(){
		$path = 'root[0]/people[3]/grade[0]/value[0]/grade[0]/value[0]/grade[0]/value[0]/grade[0]/value[0]/grade[0]/value[0]/grade[0]/value[0]/grade[2]';
				
		$URI = './application/data/BREKELE_CUSCAR.xml';
		//$URI = './application/data/sip.xml';
		
		$xml = new XMLReader();
		$xml->open($URI);
		
		while($xml->read()){
			
		}
		
		
		$xml->close();
		
	}
	
	*/
	
	public function test_next(){
		
		$URI = './application/data/sip2.txt';
		
		$xml = new XMLReader();
		$xml->open($URI);
		
		$xml->read();
		$xml->read();
		$xml->read();
		$xml->read();
		
		while($xml->next()){
			echo '<h2>'.$xml->nodeType.' '.$xml->name.'</h2>';
		}
		
		$xml->close();
		
	}
	
	
	public function find_node(){
		$start = microtime(true);
	
		$path = 'root[0]/people[3]/grade[0]/value[0]/grade[0]/value[0]/grade[0]/value[0]/grade[0]/value[0]/grade[0]/value[0]/grade[0]/value[0]/grade[2]';
		$path = 'root[1]/people[1]';
		//$path = 'Standard[0]/MessageDirectory[0]/Message[0]/SegmentRef[0]/DataElements[2]/CompositeRef[0]/DataElements[1]/DataElementRef[0]/Codes[0]/Code[3]/Text[1]';
		//$path = 'Standard[0]/MessageDirectory[0]/Message[0]/Group[0]/SegmentRef[2]/DataElements[1]/CompositeRef[0]/DataElements[3]/DataElementRef[0]/Codes[0]';
		
		//$path = 'Standard[0]/MessageDirectory[0]/Message[0]/Group[1]'; ///SegmentRef[2]/DataElements[2]/CompositeRef[0]/DataElements[3]/DataElementRef/Codes';
		//$path = 'Standard/MessageDirectory/Message/Group[2]/SegmentRef/DataElements[2]/DataElementRef/Codes';
		
		//$URI = './application/data/BREKELE_CUSCAR.xml';
		$URI = './application/data/sip.xml';
		
		
		$path_arr = explode('/', $path);
		
		echo($path);
		
		for($i = 0; $i < count($path_arr); $i++){
			$temp = $path_arr[$i];
			$parts = explode('[', $temp);
			
			if(count($parts) == 2){
				$node = new StdClass();
				$node->name = $parts[0];
				$node->pos = (int) str_replace(']', '', $parts[1]) - 1;
				
				$path_arr[$i] = $node;
			}else{
				$node = new StdClass();
				$node->name = $parts[0];
				$node->pos = 0;
				
				$path_arr[$i] = $node;
			}
			
		}
		
		$xml = new XMLReader();
		$xml->open($URI);
		
		$found = NULL;
		$curr_path = $path_arr[0];
		$curr_pos = 0;
		
		while(true){
			if($xml->nodeType == XMLReader::ELEMENT){
				if($xml->name == $curr_path->name){
					echo '<h2>PASS on '.$xml->name.'</h2>';
					
					if($curr_path->pos > 0){
						$i = 0;
						$relative_depth = 0;
						
						while($i < $curr_path->pos){
							if($xml->nodeType == XMLReader::END_ELEMENT || !$xml->next()){
								echo 'EXIT BREAK';
								break 2;
							}
							
							if($xml->name == $curr_path->name){
								$i++;
							}
						}
					}
					
					$curr_pos++;
					if($curr_pos == count($path_arr)){
						$found = $xml->readOuterXML();
						break;
					}else{
						$curr_path = $path_arr[$curr_pos];
						$xml->read();
					}
				}else{
					$xml->next();
				}
			}else{
				if(!$xml->read()) break;
			}
		}
		
		$end = microtime(true);
		
		if($found !== NULL){
			echo "\nEXECUTED IN: ".round((($end - $start) / 1000), 4)."ms\n";
			var_dump(new SimpleXMLElement($found));
		}else{
			echo '<h1>BREKELE BOS!</h1>';
		}
		
		$xml->close();
	}
	
	
	
	
}