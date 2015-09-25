<?php

require_once 'class-ct1-concept-mortgage.php';
require_once 'class-ct1-concept-annuity.php';
require_once 'class-ct1-concept-annuity-increasing.php';
require_once 'class-ct1-concept-interest.php';
require_once 'class-ct1-concept-cashflows.php';
require_once 'class-ct1-concept-spot-rates.php';

/**
 * CT1_Concept_All class
 *
 * @package    CT1
 * @author     Owen Kellie-Smith
 */
class CT1_Concept_All {

	private $concepts;

	public function __construct(CT1_Object $obj=null){
		$this->set_concepts();
	}

	private function set_concepts(){
		$this->concepts = array( 
				'concept_annuity'=>new CT1_Concept_Annuity(), 
				'concept_mortgage'=>new CT1_Concept_Mortgage(), 
				'concept_annuity_increasing'=>new CT1_Concept_Annuity_Increasing(), 
				'concept_interest'=>new CT1_Concept_Interest(),
				'concept_cashflows'=>new CT1_Concept_Cashflows(),
				'concept_spot_rates'=>new CT1_Concept_Spot_Rates(),
				 );
	}

	private function get_concept_labels(){
		return array( 
				'concept_interest'=>'Interest rate format',
				'concept_annuity'=>'Annuity (escalating or level)', 
				'concept_mortgage'=>'Mortgage (level)', 
				'concept_annuity_increasing'=> 'Annuity (increasing or decreasing)', 
				'concept_cashflows'=> 'Multiple cashflows', 
				'concept_spot_rates'=> 'Spot rates to forward rates and par yields', 
				 );
	}


	public function get_calculator( $unused ){
		$p = array('method'=> 'GET', 'submit'=>'Get calculator', 'introduction' => 'Select a calculator.');
		$p['select-options'] = $this->get_concept_labels() ;
		$p['select-name'] = 'concept';
		return $p;
	}

	
	public function get_controller($_INPUT ){
	try{
		if (isset($_INPUT['request'])){
			foreach( $this->concepts AS $c ){
				if ($c->get_request() == $_INPUT['request'])
					return $c->get_controller( $_INPUT );
			}
			foreach( $this->concepts AS $c ){
				if ( in_array( $_INPUT['request'], $c->get_possible_requests() ) )
					return $c->get_controller( $_INPUT );
			}
		}
		if (isset($_INPUT['concept'])){
			if ( isset( $this->concepts[ $_INPUT['concept'] ] ) ){
				$c = $this->concepts[ $_INPUT['concept'] ];
				return $c->get_controller( $_INPUT );
			}
		}
		$render = new CT1_Render();
		return $render->get_select_form( $this->get_calculator( NULL ) );
	}
	catch( Exception $e ){
		return $e->getMessage();
	}
}

} // end of class

