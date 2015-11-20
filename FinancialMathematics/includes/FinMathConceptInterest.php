<?php

class FinMathConceptInterest extends CT1_Form{

public function __construct(CT1_Object $obj=null){
	if (null === $obj){
		$obj = new CT1_Interest();
	}
	parent::__construct($obj);
	$this->set_request( 'get_interest' );
}

public function get_concept_label(){
	return array(	
				'concept_interest'=>self::myMessage( 'fm-interest-rate-format'),
 );
} 

private function get_rate_in_form(){
	return  $this->obj->get_rate_in_form( $this->obj ) ;
}

public function get_unrendered_solution(){
	return  $this->obj->explain_rate_in_form( $this->obj ) ;
}
	
public function get_calculator($parameters){
	$p = array('exclude'=>$parameters,'request'=>$this->get_request(), 'submit'=>self::myMessage( 'fm-calculate'), 'introduction' => self::myMessage( 'fm-intro-interest'));
	return parent::get_calculator($p);
}

public function get_controller($_INPUT ){
  $return=array();
	if (isset($_INPUT['request'])){
		if ($this->get_request() == $_INPUT['request']){
			if ($this->set_interest($_INPUT)){
				$return['output']['unrendered']['formulae'] = $this->get_unrendered_solution();
				$return['output']['unrendered']['summary'] = array('sought'=>'rate', 'result'=>$this->get_rate_in_form());
				return $return;
			}
			else{
				$return['warning']=self::myMessage( 'fm-error-interest');
				return $return;
			}
		}
	}
	else{
		$return['output']['unrendered']['forms'][] = 	array(
			'content'=>$this->get_calculator(array("delta")),
			'type'=>'',
		);
		return $return;
	}
}

public function set_interest($_INPUT = array()){
	$this->set_received_input($_INPUT);
	return ($this->obj->set_from_input($_INPUT));
}

} // end of class


