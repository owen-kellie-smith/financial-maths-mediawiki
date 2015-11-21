<?php


class FinMathFormXML extends FinMathForm{

private $referred_obj;
private $tag_name="dummy_tag_set_in_FinMathFormXML";

public function getTagName(){
		return $this->tag_name;
}

public function get_concept_label(){
	return array(
				'concept_xml'=>self::myMessage(  'fm-label-xml'), 
 );
} 

public function setTagName( $s ){
		$this->tag_name = $s;
}

public function __construct(CT1_Object $obj=null){
	if (null === $obj){
		$obj = new CT1_XML();
	}
	parent::__construct($obj);
	$this->set_request( 'process_xml' );
}

public function get_calculator($parameters){
	$p = array('exclude'=>$parameters,'request'=>$this->get_request(), 'submit'=>self::myMessage( 'fm-calculate'), ); // intro is  superfluous
	return parent::get_calculator($p);
}

public function get_controller( $_INPUT ){
  $return=array();
	$unused=array();
	if (isset($_INPUT['request'])){
		if ($this->get_request() == $_INPUT['request']){
			$x = json_decode(json_encode((array) simplexml_load_string(urldecode($_INPUT['xml']))), 1);
			$m = new FinMathConceptAll();
			$return = $m->get_controller($x['parameters']) ;
		}
	}
	if ($this->set_text($_INPUT)){
		$return['output']['unrendered']['forms'][] = array( 
			'content'=>$this->get_calculator( $unused ),
			'type'=>'',
		);
	}
  return $return;
}

public function set_text($_INPUT = array()){
		$xml="";
    if (isset($_INPUT['xml'])){
    	if (!empty($_INPUT['xml'])){
				$xml=$_INPUT['xml'];
			}
		}
		if (empty($xml)){
			$xml_data = new SimpleXMLElement('<?xml version="1.0"?><' . $this->getTagName() . '></'. $this->getTagName() . '>');
			$this->array_to_xml( array('parameters'=>$this->get_filtered_input($_INPUT)),$xml_data);
			$xml=  (string)$xml_data->asXML();
			// http://php.net/manual/en/function.preg-replace.php
			$string = $xml;
			$pattern = '/(.?)<\?xml version="1.0"\?>(.*)/i';
			$replacement = '${1}$2';
			$xml = preg_replace($pattern, $replacement, $string);
	  }
		$a = array();

		$a['xml'] = html_entity_decode(urldecode($xml));
		$this->set_received_input($a);
		$this->obj->set_from_input($a);
		return ($this->obj->set_from_input($a));
}

private function get_filtered_input($_INPUT){
		$this->set_referred_obj($_INPUT);
		if ($this->referred_obj instanceof CT1_Object){
			return $this->referred_obj->get_valid_inputs($_INPUT);
		}
		return $_INPUT;
}

private function set_referred_obj($_INPUT = array()){
		$this->referred_obj=null;
		foreach( $this->candidate_concepts() AS $c ){
				if (isset($_INPUT['request'])){
					if ($c->get_request() == $_INPUT['request'] ){
						$this->referred_obj = $c->get_obj();
						return;
					}
					if ( in_array( $_INPUT['request'], $c->get_possible_requests() ) ){
						$this->referred_obj = $c->get_obj();
						return;
					}
				} // if (isset($_INPUT['request']))
				if (isset($_INPUT['concept'])){
					$temp =  $c->get_concept_label();
					if ( isset( $temp[ $_INPUT['concept'] ] ) ){
						$this->referred_obj = $c->get_obj();
						return;
					}
				}
			} //foreach( $this->candidate_concepts() AS $c )
}

// http://stackoverflow.com/questions/1397036/how-to-convert-array-to-simplexml
	// function definition to convert array to xml
	private function array_to_xml( $data, &$xml_data ) {
    foreach( $data as $key => $value ) {
        if( is_array($value) ) {
            if( is_numeric($key) ){
                $key = 'item'.$key; //dealing with <0/>..<n/> issues
            }
            $subnode = $xml_data->addChild($key);
            $this->array_to_xml($value, $subnode);
        } else {
            $xml_data->addChild("$key",htmlspecialchars("$value"));
        }
     }
	}

} // end of class


