<?php   

class FinMathForwardRates extends CT1_Collection {

	protected function is_acceptable_class( $c ){
		return ( 'CT1_Forward_Rate' == get_class( $c ) );
	}

}

