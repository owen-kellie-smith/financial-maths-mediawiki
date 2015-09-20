<?php

require_once 'class-ct1-interest.php';
require_once 'class-ct1-form.php';
require_once 'class-ct1-render.php';

class CT1_Concept_Interest extends CT1_Form{

public function __construct(CT1_Object $obj=null){
	if (null === $obj) $obj = new CT1_Interest();
	parent::__construct($obj);
	$this->set_request( 'get_interest' );
}

public function get_solution(){
	$render = new CT1_Render();
	$return = $render->get_render_latex( $this->obj->explain_rate_in_form( $this->obj ) );
	return $return;
}
	
public function get_calculator($parameters){
	$p = array('exclude'=>$parameters,'request'=>$this->get_request(), 'submit'=>'Calculate', 'introduction' => 'Express an annual effective interest rate in an alternative, equivalent form.');
	return parent::get_calculator($p);
}

public function get_controller($_INPUT ){
	if (isset($_INPUT['request'])){
		if ($this->get_request() == $_INPUT['request']){
			if ($this->set_interest($_INPUT))
				return $this->get_solution();
			else
				return "<p>Error setting interest from:<pre>" . print_r($_INPUT,1) .  "</pre>";
		}
	}
	else{
		$render = new CT1_Render();
		return $render->get_render_form($this->get_calculator(array("delta")));
	}
}

public function set_interest($_INPUT = array()){
	$this->set_received_input($_INPUT);
	return ($this->obj->set_from_input($_INPUT));
}

} // end of class


