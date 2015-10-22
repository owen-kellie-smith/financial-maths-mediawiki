<?php   

class CT1_Forward_Rates extends CT1_Collection {

	protected function is_acceptable_class( $c ){
		if ( 'CT1_Forward_Rate' == get_class( $c ) )
			return true;
		return false;
	}

}

