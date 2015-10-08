<?php

require_once 'class-ct1-concept-interest.php';
require_once 'class-ct1-concept-annuity.php';
require_once 'class-ct1-concept-mortgage.php';
require_once 'class-ct1-concept-annuity-increasing.php';
require_once 'class-ct1-concept-cashflows.php';
require_once 'class-ct1-concept-spot-rates.php';
require_once 'class-ct1-form-xml.php';

/**
 * CT1_Concept_All class
 *
 * @package    CT1
 * @author     Owen Kellie-Smith
 */
class CT1_Concept_All {

	private $concepts;

  private $messages;

  private static function myMessage( $messageKey){
			$m = $messageKey;
			if ( function_exists('wfMessage') ){
				$m=wfMessage( $messageKey)->text();
			}
			return $m;
	}

	public function __construct(CT1_Object $obj=null){
//		$this->set_concepts();
	}

	private function candidate_concepts(){
		return array( 
				new CT1_Concept_Interest(),
				new CT1_Concept_Annuity(), 
				new CT1_Concept_Mortgage(), 
				new CT1_Concept_Annuity_Increasing(), 
				new CT1_Concept_Cashflows(),
				new CT1_Concept_Spot_Rates(),
				 );
	}

	private function get_concept_labels(){
		$return = array();
		foreach( $this->candidate_concepts() AS $c ){
				$return = array_merge($return, $c->get_concept_label());
		}
		return $return;
	}


	public function get_calculator( $unused ){
		$p = array('method'=> 'GET', 'submit'=>self::myMessage(  'fm-get-calculator') , self::myMessage(  'fm-select-calculator'));
		$p['select-options'] = $this->get_concept_labels() ;
		$p['select-name'] = 'concept';
		$p['select-label'] = self::myMessage(  'fm-select-calculator');
		return $p;
	}

	private function get_parameters($_INPUT){
		return; 
//		return print_r($_INPUT,1);
	}

	public function get_controller($_INPUT ){
    $return = $this->get_controller_no_xml($_INPUT );
		if (isset($return['formulae'])){
			$c = new CT1_Form_XML();
			$return['xml-form'] = $c->get_controller( $_INPUT );
		}
		return $return;
	}
	
	public function get_controller_no_xml($_INPUT ){
	$return['arrayInput']=$_INPUT;
	try{
		foreach( $this->candidate_concepts() AS $c ){
				if (isset($_INPUT['request'])){
					if ($c->get_request() == $_INPUT['request'] ){
						$return = array_merge($return, $c->get_controller( $_INPUT ));
						return $return;
					}
					if ( in_array( $_INPUT['request'], $c->get_possible_requests() ) ){
						$return = array_merge($return, $c->get_controller( $_INPUT ));
						return $return;
					}
				} // if (isset($_INPUT['request']))
				if (isset($_INPUT['concept'])){
					if ( isset( $c->get_concept_label()[ $_INPUT['concept'] ] ) ){
						$return = array_merge($return, $c->get_controller( $_INPUT ));
						return $return;
					}
				}
			} //foreach( $this->candidate_concepts() AS $c )
		$render = new CT1_Render();
		$return['form']= $render->get_select_form( $this->get_calculator( NULL ) ) ;
		return $return;
	}
	catch( Exception $e ){
		$return['warning']=$e->getMessage();
		return $return;
	}
}

} // end of class

