<?php   

class FinMathSingleCashflow extends CT1_Annuity{

	public function get_valid_options(){ 
		return array();
	}

	public function get_parameters(){ 
		return array();
	}

	public function get_values(){ 
		$r = array();
		$r['value'] = $this->get_value();
		return $r; 
	}

	public function __construct(){
		;
	}

	public function get_value(){
		return 1;
	}

	public function get_annuity_certain(){
		return 1;
	}

	public function explain_annuity_certain(){
		return array();
	}


	public function get_label(){
		return "";
	}

	public function get_labels(){
		$labels = array();
		$labels['FinMathSingleCashflow'] = $this->label_annuity();
		return $labels;
	}

}

// example 
//$a = new CT1_Annuity(12, true, 0.1, 12);
//$a->set_value(11.234567890123456789);
//print_r($a->get_values());
//print_r($a->get_delta_for_value());
//$a->set_delta( $a->get_delta_for_value() );
//print_r($a->explain_interest_rate_for_value());

