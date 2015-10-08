<?php

require_once 'class-ct1-annuity-increasing.php';
require_once 'class-ct1-concept-annuity.php';
require_once 'class-ct1-render.php';

class CT1_Concept_Annuity_Increasing extends CT1_Concept_Annuity{

public function __construct(CT1_Object $obj=null){
	if (null === $obj) $obj = new CT1_Annuity_Increasing();
	parent::__construct($obj);
	$this->set_request( 'get_annuity_increasing' );
}

public function get_concept_label(){
	return array(	
				'concept_annuity_increasing'=> self::myMessage(  'fm-annuity-increasing'), 
 );
} 

} // end of class


