<?php

class CT1_Concept_Annuity extends CT1_Form{

public function __construct(CT1_Object $obj=null){
	if (null === $obj) $obj = new CT1_Annuity_Escalating();
	parent::__construct($obj);
	$this->set_request( 'get_annuity_escalating' );
}

public function get_concept_label(){
	return array(
				'concept_annuity'=>self::myMessage(  'fm-annuity'), 
 );
} 

private function get_unrendered_solution(){
	return $this->obj->explain_annuity_certain();
} 

private function get_unrendered_interest_rate(){
	return $this->obj->explain_interest_rate_for_value();
}

	private function get_unrendered_summary( $_INPUT ){
		$ret=array();
			if (empty( $_INPUT['value'] )  ){
				$ret['sought']='value';
		} else {
				$ret['sought']='i_effective';
		}
		return $ret;
	}


	
public function get_calculator($parameters){
	$p = array('exclude'=>$parameters,'request'=> $this->get_request(), 'submit'=>self::myMessage( 'fm-calculate'), 'introduction' => self::myMessage( 'fm-intro-annuity-certain') );
	$c = parent::get_calculator($p);
	$c['values']['value'] = NULL;
	return $c;
}

public function get_controller($_INPUT ){
  $return=array();
	if (isset($_INPUT['request'])){
		if ($this->get_request() == $_INPUT['request']){
			if ($this->set_annuity($_INPUT)){
				if (empty( $_INPUT['value'] ) ){
				  $return['output']['unrendered']['formulae'] = $this->get_unrendered_solution();
				  $return['output']['unrendered']['summary'] = $this->get_unrendered_summary($_INPUT);
					return $return;
				} else {
				  $return['output']['unrendered']['formulae'] = $this->get_unrendered_interest_rate();
				  $return['output']['unrendered']['summary'] = $this->get_unrendered_summary($_INPUT);
					return $return;
				}
			} else {
				$return['warning']=self::myMessage( 'fm-exception-setting-annuity');
				return $return;
			}
		}
	}
	else{
		$return['output']['unrendered']['forms'][] = array(
			'content'=>  $this->get_calculator(array(
					"delta", "escalation_delta","source_m","source_advance","source_rate"
					)),
			'type'=>  ''
		);
    return $return;
	}
  return $return;
}

public function set_annuity($_INPUT = array()){
	$this->set_received_input($_INPUT);
	$this->obj->set_from_input($_INPUT);
	return ($this->obj->set_from_input($_INPUT));
}

} // end of class


