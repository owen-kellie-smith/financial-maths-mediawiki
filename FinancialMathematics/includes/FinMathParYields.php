<?php   

class FinMathParYields extends FinMathCollection {

	protected function is_acceptable_class( $c ){
		return ( 'FinMathParYield' == get_class( $c ) );
	}

}

