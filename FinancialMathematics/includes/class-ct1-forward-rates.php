<?php   

class FinMathForwardRates extends FinMathCollection {

	protected function is_acceptable_class( $c ){
		return ( 'FinMathForwardRate' == get_class( $c ) );
	}

}

