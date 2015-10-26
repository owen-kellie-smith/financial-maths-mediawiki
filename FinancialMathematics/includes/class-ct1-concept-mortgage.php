<?php


class CT1_Concept_Mortgage extends CT1_Form{

public function __construct(CT1_Object $obj=null){
	if (null === $obj) $obj = new CT1_Mortgage();
	parent::__construct($obj);
	$this->set_request( 'get_mortgage_instalment' );
}

public function get_concept_label(){
	return array(	
		'concept_mortgage'=>self::myMessage( 'fm-mortgage'),
 );
} 


private function get_unrendered_solution(){
	return $this->obj->explain_instalment();
}


private function get_unrendered_interest_rate(){
	return $this->obj->explain_interest_rate_for_instalment();
}


	private function get_unrendered_summary( $_INPUT ){
		$ret=array();
			if (empty( $_INPUT['instalment']  )  ){
				$ret['sought']='instalment';
		} else {
				$ret['sought']='i_effective';
		}
		return $ret;
	}

	
public function get_calculator($parameters){
	$p = array('exclude'=>$parameters,'request'=>$this->get_request(), 'submit'=>self::myMessage( 'fm-calculate'), 'introduction' => self::myMessage( 'fm-intro-mortgage') );
	$c = parent::get_calculator($p);
	$c['values']['instalment'] = NULL;
	return $c;
}

public function get_controller($_INPUT ){
  $return=array();
	if (isset($_INPUT['request'])){
		if ($this->get_request() == $_INPUT['request']){
			if ($this->set_mortgage($_INPUT)){
				if (empty( $_INPUT['instalment'] ) ){
				  $return['output']['unrendered']['formulae'] = $this->get_unrendered_solution();
				  $return['output']['unrendered']['summary'] = $this->get_unrendered_summary($_INPUT);
                                  $return['output']['unrendered']['table'] = $this->obj->get_mortgage_table();

					return $return;
				} else {
				  $return['output']['unrendered']['formulae'] = $this->get_unrendered_interest_rate();
				  $return['output']['unrendered']['summary'] = $this->get_unrendered_summary($_INPUT);
                                  $return['output']['unrendered']['table'] = $this->obj->get_mortgage_table();
					return $return;
				}
			} else{
				$return['warning']=self::myMessage( 'fm-exception-setting-mortgage');
				return $return;
			}
		}
	}
	else{
		$return['output']['unrendered']['forms'][] = 	array(
			'content'=>$this->get_calculator(array("delta",  "value")),
			'type'=>'',
		);

		return $return;
	}
}

public function set_mortgage($_INPUT = array()){
	$this->set_received_input($_INPUT);
	return ($this->obj->set_from_input($_INPUT));
}

} // end of class

