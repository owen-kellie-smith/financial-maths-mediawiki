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
 */

class FinMathCashflows extends FinMathCollection {

	private $value;
	protected $max_dp = 2;

	public function get_valid_options(){ 
		$r = parent::get_valid_options();
		$r['FinMathCashflows'] = array();
		$r['i_effective'] = array();
		$r['value'] = array();
		return $r; 
	}

	public function unset_value(){
		unset( $this->value );
	}

	public function set_value( $v){
		$this->value = $v;
	}

	public function get_value(){
		if ( isset( $this->value ) ){
			return $this->value;
		} else {
			return $this->get_discounted_value();
		}
	}

	public function get_delta_for_value( $v = 0 ){
		$this->set_value( $v );
		if ( !isset( $this->value ) ){
			throw new Exception( self::myMessage( 'fm-exception-get_delta_for_value')  . __FILE . self::myMessage( 'fm-exception-no-value-set')  );
		} else {
			return $this->get_interpolated_delta_for_value();
		}
	}

	public function explain_interest_rate_for_value( $v = 0, $with_detail = true ){
		$i = new FinMathInterest();
		$return = array();
		$a_calc = $this->get_clone_this();
		$delta = $this->get_delta_for_value( $v );
		$a_calc->set_delta( $delta );
		$return[0]['left'] = "i";
		$return[0]['right'] = $i->explain_format( exp( $delta ) - 1) . "." . "\\ \\mbox{ ".self::myMessage( 'fm-verification').":}";
		return array_merge( $return, $a_calc->explain_discounted_value( $with_detail ) );
	}

	protected function get_interpolated_value( $guesses ){
		// return linear interpolation for f(x) = 0
		$x0 = $guesses[0]['x'];
		$f0 = $guesses[0]['f'];
		$x1 = $guesses[1]['x'];
		$f1 = $guesses[1]['f'];
		if ($f1 == $f0 ) {
			return $x0;
		} else {
			return $x0 - $f0 * ($x1 - $x0 ) / ( $f1 - $f0 );
		}
	}
 
	public function get_clone_this(){
		$a_calc = new FinMathCashflows();
		$a_calc->set_cashflows( $this->get_cashflows() );
		return $a_calc;
	}

	protected function get_interpolated_delta_for_value(){
		$a_calc = $this->get_clone_this();
		$max_loop = 100;
		$min_diff_x = 0.00000000000001;
		$start_diff = 0.001; // anything more than min_diff_x
		$diff_x = 99999;
		$loop_count = 0;
		$x0 = $this->get_approx_yield();
		$x1 = $x0 + $start_diff;
		while ( $loop_count < $max_loop && $diff_x > $min_diff_x ) {
			$g[0]['x'] = $x0;
			$g[1]['x'] = $x1;
			$a_calc->set_delta( $x0 );
			$a_calc->unset_value();	
			$g[0]['f'] = $a_calc->get_discounted_value() - $this->get_value();
			$a_calc->set_delta( $x1 );
			$g[1]['f'] = $a_calc->get_discounted_value() - $this->get_value();
			$x2 = $this->get_interpolated_value( $g );
			$x0 = $x1;
			$x1 = $x2;
			$diff_x = abs( $x0 - $x1 );
			$loop_count++;
		}
		if ( $loop_count >= $max_loop ){ 
			throw new Exception (self::myMessage( 'fm-exception-max-iterations-exceeded')  . $this->get_value() . ". " .  self::myMessage( 'fm-exception-query-cashflows-possible')  );
		}
		return $x1;
	}
		
	private function get_approx_yield(){
		return 0;
		return $this->get_weighted_mean_yield();
	}

	private function get_weighted_mean_yield(){
		$c_old = $this->get_cashflows();
		$mean = 0;
		$sum_cash = 0;
		$sum_cash_delta = 0;
		if (count( $c_old ) > 0 ){
			foreach ( $c_old as $c ){
				$sum_cash += $c->get_value();
				$sum_cash_delta += $c->get_value() * $c->get_annuity()->get_delta();
			}
			if ( 0 != $sum_cash ){
				$mean = $sum_cash_delta / $sum_cash;
			}
		}
		return $mean;
	}
 
	public function set_delta( $d ){
		$this->set_i_effective( exp( $d ) - 1 );
	}

	public function set_i_effective( $i ){
		$c_new = array();
		$c_old = $this->get_cashflows();
		if (count( $c_old ) > 0 ){
			foreach ( $c_old as $c ){
				$a = $c->get_annuity();
				$a->set_i_effective( $i );
				$a->unset_value();
				$c->set_annuity( $a );
				$c_new[] = $c;
			}
			$this->set_cashflows( $c_new );
		}
	}

		
	private function annuity_type( $i = array() ){
		if( is_array($i) ){
			if ( in_array( 'escalation_frequency', array_keys($i) ) ){
				return new FinMathAnnuityEscalating(); 
			}
			if ( in_array( 'increasing', array_keys($i) ) ){
				return new FinMathAnnuityIncreasing(); 
			}
		}
		return new FinMathAnnuity();
	}

	public function set_from_input($_INPUT = array(), $pre = ''){
		try{
			$c_new = new FinMathCashflows();
			if ( count($_INPUT) > 0 ){
				foreach ($_INPUT as $i){
					if( is_array($i) ){
						$c = new FinMathCashflow();
						$a = $this->annuity_type( $i );
						$a->set_from_input( $i );
						$c->set_annuity( $a );
						$c->set_rate_per_year( $i['rate_per_year'] );
						$c->set_effective_time( $i['effective_time'] );
						$c_new->add_object( $c );
					}
				}
				$this->set_objects( $c_new->get_objects() );
				$this->class = $c_new->class;
				return true;
			} else {
				return false;
			}
		}
		catch( Exception $e ){ 
			throw new Exception( self::myMessage( 'fm-exception-in') . __FILE__ . ": " . $e->getMessage() );
		}
	}

	public function get_cashflow_indices(){ 
		return array_keys( $this->get_cashflows() );
	}

	public function get_values(){ 
		$r = array();
		if (count( $this->get_cashflows() ) > 0 ){
			foreach ( $this->get_cashflows() as $c ){
				$r[] = $c->get_values();
			}
		}
		$r['cashflows_value'] = $this->get_value();
		return $r; 
	}
	
	private function get_discounted_value(){
		$val = 0;
		if (count( $this->get_cashflows() ) > 0 ){
			foreach ( $this->get_cashflows() as $c ){
				$val += $c->get_value();
			}
		}
		return $val;
	}


	protected function cashflow_format($d){
		return number_format($d, $this->max_dp);
	}

	public function explain_discounted_value( $with_detail = true ){
		$return = array();
		$return[0]['left'] = "\\mbox{".self::myMessage( 'fm-value') ."}";
		$return[0]['right'] = $this->get_label();
		$top_line = "";
		$sub_top = array();
		$det = array();
		if (count( $this->get_cashflows() ) > 0 ){
			$i = 0;
			foreach ( $this->get_cashflows() as $c ){
				if ( 0 != $c->get_rate_per_year() ){
					$detail=array();
					$sub = ""; $sub_split="";
					$sub = $this->get_sign( $c->get_rate_per_year() ) . " ";
					$sub_split = $sub;
					$detail['right']['relation'] = ''; //default is '='
					if ( 0 == $i ){
						$detail['right']['relation'] = '='; 
					}
					$sub .= $this->cashflow_format(abs( $c->get_value() ) );
					$sub_split .=  $c->get_abs_label_with_annuity_evaluated() ;
					$detail['right']['summary'] = $sub_split;
					if ( $with_detail ){
						if ( !( $c->get_annuity()->get_is_single_payment() ) ){
							$detail['right']['detail'] = $c->get_annuity()->explain_annuity_certain() ;
						}
					}
					$det[] = $detail;
					$top_line .= $sub;
				}
				$i++;
			}
		}
		$return	 = array_merge( $return, $det );
		$next_to_last['right'] = $top_line;
		$last['right'] = $this->cashflow_format( $this->get_discounted_value());
		if ( $with_detail ){
			$return[] = $next_to_last;
			$return[] = $last;
		}
		return $return;
	}



	public function __construct() {
		;
	}

	public function get_cashflows(){
		return $this->get_objects();
	}

	private function set_cashflows( $cashflow_array ){
		$this->set_objects( $cashflow_array );
	}

	public function add_cashflow( FinMathCashflow $c ){
		$this->add_object( $c );
	}

	public function remove_cashflow( FinMathCashflow $c ){
		$this->remove_object( $c );
	}
	
	private function get_sign( $d ){
		if ( 0 > $d ){
			return "-";
		} else {
			return "+";
		}
	}

	public function get_label(){
		$label = "";
		if (count( $this->get_cashflows() ) > 0 ){
			$i = 0;
			foreach ( $this->get_cashflows() as $c ){
				if ( 0 != $c->get_rate_per_year() ){
					$label .= " " . $this->get_sign( $c->get_rate_per_year() ) . " ";
					$label .= $c->get_label( true );
				}
				$i++;
			}
		}
		return $label;
	}

	public function get_labels(){
		$labels = array();
		$labels['FinMathCashflows'] = $this->get_label();
		return $labels;
	}

}

