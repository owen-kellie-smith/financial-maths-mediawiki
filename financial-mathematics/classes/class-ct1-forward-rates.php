<?php   
require_once 'class-ct1-forward-rate.php';
require_once 'class-ct1-collection.php';

class CT1_Forward_Rates extends CT1_Collection {

	protected function is_acceptable_class( $c ){
		if ( 'CT1_Forward_Rate' == get_class( $c ) )
			return true;
		return false;
	}

}

