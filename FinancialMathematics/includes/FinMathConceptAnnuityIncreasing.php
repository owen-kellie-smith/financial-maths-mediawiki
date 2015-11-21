<?php


class FinMathConceptAnnuityIncreasing extends FinMathConceptAnnuity{

public function __construct(FinMathObject $obj=null){
	if (null === $obj){ 
		$obj = new FinMathAnnuityIncreasing();
	}
	parent::__construct($obj);
	$this->set_request( 'get_annuity_increasing' );
}

public function get_concept_label(){
	return array('concept_annuity_increasing'=> self::myMessage(  'fm-annuity-increasing'), 
 );
} 

} // end of class


