<?php   

class CT1_Par_Yields extends CT1_Collection {

	protected function is_acceptable_class( $c ){
		if ( 'CT1_Par_Yield' == get_class( $c ) )
			return true;
		return false;
	}

}

