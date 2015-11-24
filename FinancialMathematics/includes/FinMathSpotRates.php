<?php   
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Owen Kellie-Smith
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 * 
 * @file
 */

/**
 * This class provides a FinMathCollection for FinMathSpotRate objects. 
 * 
 * The class stores spot rates (equivalent to the current price of zero coupon bonds over various terms)
 * and calculates (where possible) forward rates and par yields.
 * To calculate a par yield (which is stored in a FinMathParYield object), it is necessary
 * to know the spot rates at each integer term up to the term of the notional bond.
 * I.e. to calculate the n-year par-yield, you need the 1, 2, 3, .., n-1, n-year spot rates.
 *
 * To calculate the forward rate which applies from time t to time T, all you need is the 
 * spot rate for term t, and the spot rate for term T.
 */
class FinMathSpotRates extends FinMathCollection {

protected $explanation_forward_rates;
protected $explanation_par_yields;

	public function get_valid_options(){ 
		$r = parent::get_valid_options();
		$r['FinMathSpotRates'] = array();
		$r['forward_start_time'] = array();
		$r['forward_end_time'] = array();
		$r['par_term'] = array();
		return $r; 
	}

	public function get_clone_this(){
		$a_calc = new FinMathSpotRates();
		$a_calc->set_objects( $this->get_objects() );
		return $a_calc;
	}

	protected function is_acceptable_class( $c ){
		return ( 'FinMathSpotRate' == get_class( $c ) );
	}

	private function get_sorted_terms(){
		if ( $this->get_objects() ){
			$terms = array_keys( $this->get_objects() );
			sort( $terms );
			return $terms;
		}
	}

/**
 * The explain_par_yield() function returns the explanation of the calculation of
 * a FinMathParYield calculated by the get_par_yields() function.
 * 
 * @param FinMathParYield $f  (must match one of the par yields already calculated)
 * @return array of Latex
 *
 */
	public function explain_par_yield( FinMathParYield $f ){
		if ( !$this->get_par_yields()->is_in_collection( $f ) ){
			throw new Exception( __FILE__ . 
				self::myMessage( 'fm-error-explain-paryield', $f )  );
		}
		return $this->explanation_par_yields[ $f->get_term() ];
	}

/**
 * The explain_forward_rate() function returns the explanation of the calculation of
 * a FinMathForwardRate calculated by the get_forward_rates() function.
 * 
 * @param FinMathForwardRate $f  (must match a calculated forward rate)
 * @return array of Latex
 *
 */
	public function explain_forward_rate( FinMathForwardRate $f ){
		if ( !$this->get_forward_rates()->is_in_collection( $f ) ){
			throw new Exception( __FILE__ .  
				self::myMessage( 'fm-error-explain-forward', $f )  );
		}
		return $this->explanation_forward_rates[ $f->get_end_time() ];
	}

/**
 * The get_all_rates() function returns an array of terms, spot rates, forward rates, and par yields
 * 
 * @return array 
 *
 */
	public function get_all_rates(){
		$out = array();
		$out[ 'header' ] = array(
			'spot term','spot rate',
			'forward period','forward rate',
			'par term','par yield',
		);
		$spots = $this->get_objects();
		$forwards = $this->get_forward_rates()->get_objects();
		$pars = $this->get_par_yields()->get_objects();
		$key_spot = array_keys( $spots );
		$key_forward = array_keys( $forwards );
		$key_par = null;
		if ( $pars ){
			$key_par = array_keys( $pars );
		}
		for ($i = 0, $ii = $this->get_count(); $i < $ii; $i++) {
			$f = null;
			$p = null;
			$row = array(); $objects = array();
			$s = $spots[ $key_spot[ $i ] ];
			$row[0] = $s->get_label();
			$row[1] = $s->get_i_effective();
			$objects[get_class($s)] = $s;
			if ( count( $forwards ) > $i ){
				$f = $forwards[ $key_forward[ $i ]];
				$row[2] = $f->get_label() ;
				$row[3] = $f->get_i_effective();
				$objects[get_class($f)] = $f;
			}
			if ( count( $pars ) > $i ){
				$p = $pars[ $key_par[ $i ]];
				$row[4] = $p->get_label();
				$row[5] = $p->get_coupon();
				$objects[get_class($p)] = $p;
			}
			$out['data'][] = $row;
			$out['objects'][] = $objects;
		}
		return $out;
	}

/**
 * The get_forward_rates() function returns the forward rates implied by the spot rates,
 * and stores the explanation of how the forward rates are derived.
 * 
 * The forward rates are objects of the FinMathForwardRate class.
 * If P is the current price (i.e. discounting factor) of a future payment of 1 at time t 
 * (so that Q is the current price of Q/P at t),
 * and Q is the current price of a future payment of 1 at time u,
 * then (assuming no arbitrage and no expenses) 
 * Q/P is the implied forward price at time t for a future payment of 1 at time u.
 * 
 * 
 * Forward rates are forward prices expressed as annual effective rates,
 * and spot rates are current prices expressed as annual effective rates.
 *
 * If we express spot rate as continuously compounded rates s(), and 
 * if P = exp( t s(t) ), Q = exp( u s(u) ), then Q/P = exp( u s(u) - t s(t) )
 * 
 * \f$ = \exp \left( (u - t) \frac{ u s(u) - t s(t) }{ u - t } \right). \f$
 * 
 * @return FinMathForwardRates forward rates implied by spot rates
 *
 */
	public function get_forward_rates(){
		$spot_rates = $this->get_objects();
		$terms = $this->get_sorted_terms();
		$fs = new FinMathForwardRates();
		for ($i = 0, $ii = $this->get_count(); $i < $ii; $i++){
			$f = null;
			$end = $terms[ $i ]; 	
			if ( 0 == $i ){
				$start = 0; $i = $spot_rates[ $end ]->get_i_effective();	
				$f = new FinMathForwardRate( $i, $start, $end );
				$explanation_algebra = $spot_rates[ $end ]->get_label();
				$exp[0]['right'] = $explanation_algebra;
				$exp[1]['right'] = $f->get_i_effective();
			} else {
				$start = $terms[ $i - 1 ]; 
				$phi = $spot_rates[ $end ]->get_delta() * $end 
					- $spot_rates[ $start ]->get_delta() * $start;	
				$phi = $phi / ( $end - $start );
				$f = new FinMathForwardRate( exp( $phi ) - 1, $start, $end );
				$explanation_top = "\\left( 1 + " . $spot_rates[ $end ]->get_label() .
				  " \\right)^{" . $end . "}";
				$explanation_top_n = (1 + $spot_rates[ $end ]->get_i_effective() ) .
				 "^{" . $end . "}";
				$explanation_bot = "\\left( 1 + " . $spot_rates[ $start ]->get_label() .
				 " \\right)^{" . $start . "}";
				$explanation_bot_n = (1 + $spot_rates[ $start ]->get_i_effective()) . 
				"^{" . $start . "}";
				$explanation = "\\frac{ " . $explanation_top . "}{" . $explanation_bot . "}";
				$explanation_n = "\\frac{ " . $explanation_top_n . "}{" . 
					$explanation_bot_n . "}";
				$explanation_algebra = "\\left[ " . $explanation . 
					"\\right]^{\\frac{1}{" . $end . "-" . $start . "}} - 1";
				$explanation_numbers = "\\left[ " . $explanation_n . 
					"\\right]^{\\frac{1}{" . $end . "-" . $start . "}} - 1";
				$exp[0]['right'] = $explanation_algebra;
				$exp[1]['right'] = $explanation_numbers;
				$exp[2]['right'] = $f->get_i_effective();
			}
			$exp[0]['left'] = $f->get_label();
			$this->explanation_forward_rates[ $f->get_end_time() ] = $exp;
			$fs->add_object( $f );
		}
		return $fs;
	}

/**
 * The get_par_yields() function returns the par yields implied by the spot rates,
 * and stores the explanation of how the par yields are derived.
 * 
 * The par yields are objects of the FinMathParYield class.
 * Par yields are only available for terms up to the maximum integer N
 * for which there are spot rates at terms 1, 2, .., N-1, N.
 *
 * The par yield for term n is the coupon rate for an annual bond of term n priced at par.
 * I.e. it is the coupon C such that
 * 
 * CP(1) + CP(2) + ... + CP(n-1) + (1+C)P(n) = 1,
 * 
 * where P(t) is the current price of a payment of 1 at time t.
 *
 * I.e. C = [ 1 - P(n) ] / [ P(1) + P(2) + ... + P(n-1) + P(n) ].
 * 
 * Thus to find C you need to know P(1), P(2), .., P(n-1), P(n)
 * or equivalently, to know the spot rates for terms 1, 2, ..., n.
 * 
 * @return FinMathParYields par yields implied by spot rates
 *
 */
	public function get_par_yields(){
		$spot_rates = $this->get_objects();
		$terms = $this->get_sorted_terms();
		$ps = new FinMathParYields();
		for ($i = 0, $ii = $this->maximum_contiguous_term(); $i < $ii; $i++){
			$end = $terms[ $i ]; 	
			$c = (1 - $spot_rates[ $end ]->get_vn() ) / $this->annuity_value( $end );
			$p = new FinMathParYield( $c, $end );
			$ps->add_object( $p );
			$exp[0]['left'] = $p->get_label();
			$exp_ann = $this->explain_par_yield_annuity_value( $p->get_term() );
			$exp[0]['right'] = "\\frac{1 - (1 + i_{" . $p->get_term() . 
				"})^{-" . $p->get_term() . "}}{" . $exp_ann['algebra'] . "}";
			$exp[1]['right'] = "\\frac{1 - " . 
				(1 + $spot_rates[ $end ]->get_i_effective()) . 
				"^{-" . $p->get_term() . "}}{" . $exp_ann['numbers'] . "}";
			$exp[2]['right'] = $p->get_coupon();
			$this->explanation_par_yields[ $p->get_term() ] = $exp;
		}
		return $ps;
	}

	private function explain_par_yield_annuity_value( $term ){
		// returns sum for discounted value of 1 payable at terms 1, 2, .. $term
		// provided spot rates exist for terms 1, 2, ... $term
		if ( $term > $this->maximum_contiguous_term() ){
			throw new Exception ( __FILE__ . 
				self::myMessage( 'fm-error-explain-annuity-value-term', 
					$term,  $this->maximum_contiguous_term()  
				)   
			);
		}
		$spot_rates = $this->get_objects();
		$terms = $this->get_sorted_terms();
		$result = array();
		$algebra = "(1 + i_1)^{-1}";
		$numbers = (1 + $spot_rates[ $terms[0] ]->get_i_effective()) . "^{-1}";
		for ($i = 2, $ii = $term; $i <= $ii; $i++){
			$algebra .= " + (1 + i_" . $i . ")^{-" . $i . "}";
			$numbers .= " + " . (1 + $spot_rates[ $terms[$i - 1] ]->get_i_effective()) . 
				"^{-" . $terms[ $i - 1] . "}";
		}
		return array( 'algebra' => $algebra, 'numbers' => $numbers );
	}

	private function annuity_value( $term ){
		// returns discounted value of 1 payable at terms 1, 2, .. $term
		// provided spot rates exist for terms 1, 2, ... $term
		if ( $term > $this->maximum_contiguous_term() ){
			throw new Exception ( __FILE__ . 
				self::myMessage( 'fm-error-annuity-value-term', 
					$term,  $this->maximum_contiguous_term()  
				)  
			);
		}
		$spot_rates = $this->get_objects();
		$terms = $this->get_sorted_terms();
		$value = 0;
		for ($i = 1, $ii = $term; $i <= $ii; $i++){
			$value += $spot_rates[ $terms[ $i-1 ] ]->get_vn();
		}
		return $value;
	}

		
	private function maximum_contiguous_term(){
		$i = 1;
		while ($this->term_is_set( $i )){
			$i++;
		}
		$i--;
		return $i;
	}
	
	private function term_is_set( $i ){
		if ( 0 < $this->get_count() ){
			foreach ($this->get_objects() as $c ) {
				if ( $i == $c->get_effective_time() ){
					return true;
				}
			}
		}
		return false;
	}
			
	public function set_from_input($_INPUT = array(), $pre = ''){
		try{
			$c_new = new FinMathSpotRates();
			if ( count($_INPUT) > 0 ){
				foreach ($_INPUT as $i){
					if( is_array($i) ){
						$c = new FinMathSpotRate( exp( $i['delta'] ) - 1 , $i['effective_time'] );
						$c_new->add_object( $c );
						$c = null;
					}
				}
				$this->set_objects( $c_new->get_objects() );
				$this->class = $c_new->class;
				return true;
			} else {
				return false;
			}
		} catch( Exception $e ) { 
			throw new Exception( self::myMessage( 'fm-exception-in' )  . 
				__FILE__ . ": " . $e->getMessage() 
			);
		}
	}
}

