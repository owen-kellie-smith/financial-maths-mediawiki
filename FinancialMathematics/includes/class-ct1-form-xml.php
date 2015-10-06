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
	$p = array('exclude'=>$parameters,'request'=>$this->get_request(), 'submit'=>self::myMessage( 'fm-calculate'), 'introduction' => self::myMessage( 'fm-intro-xml'));
	return parent::get_calculator($p);
}

public function get_controller( $unused ){
  $return=array();
	$render = new CT1_Render();
	$return['form']= $render->get_render_form( $this->get_calculator( $unused ) );
  return $return;
}

} // end of class


