<?php

require_once 'class-ct1-xml.php';
require_once 'class-ct1-form.php';
require_once 'class-ct1-render.php';

class CT1_Form_XML extends CT1_Form{

public function __construct(CT1_Object $obj=null){
	if (null === $obj) $obj = new CT1_XML();
	parent::__construct($obj);
	$this->set_request( 'process_xml' );
}

public function get_calculator($parameters){
//	$p = array('exclude'=>$parameters,'request'=>$this->get_request(), 'submit'=>self::myMessage( 'fm-calculate'), 'introduction' => self::myMessage( 'fm-intro-xml'));
	$p = array('exclude'=>$parameters,'request'=>$this->get_request(), 'submit'=>self::myMessage( 'fm-calculate'), ); // intro is  superfluous
	return parent::get_calculator($p);
}

public function get_controller( $_INPUT ){
  $return=array();
	$unused=array();
	if ($this->set_text($_INPUT)){
		$render = new CT1_Render();
		$return['form']= $render->get_render_form( $this->get_calculator( $unused ) );
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
			$xml_data = new SimpleXMLElement('<?xml version="1.0"?><parameters></parameters>');
			$this->array_to_xml($_INPUT,$xml_data);
			$xml=  (string)$xml_data->asXML();
			// http://php.net/manual/en/function.preg-replace.php
			$string = $xml;
			$pattern = '/(.?)<\?xml version="1.0"\?>(.*)/i';
			$replacement = '${1}$2';
			$xml = preg_replace($pattern, $replacement, $string);
	  }
		$a = array();
		$a['xml'] = $xml;
		$this->set_received_input($a);
		$this->obj->set_from_input($a);
		return ($this->obj->set_from_input($a));
}

// http://stackoverflow.com/questions/1397036/how-to-convert-array-to-simplexml
	// function defination to convert array to xml
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


