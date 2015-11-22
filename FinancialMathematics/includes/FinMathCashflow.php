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
 * Cashflow object representing a single cashflow, or an annuity.
 * The properties of the cashflow are what kind of annuity it is, the rate of payment, and 
 * the effective time the cashflows start.
 * 
 * E.g. amounts paid out of 100 at time 3, 110 at time 4, 121 at time 5
 * could be represented as an escalating 3 year annuity payable in advance, starting at time 3,
 * escalating at 10% per year, at (initial) rate of -100 per year.
 *
 * Each single cashflow is used within the FinMathCashflows collection to calculate general
 * equations of value.
 *
 */
class FinMathCashflow extends FinMathObject {

    private $annuity;
    private $rate_per_year;
    private $effective_time;

	/**
	 * Set cashflow from input
	 *
	 * @param array $IN new cashflow features
	 * @param string $pre possible prefix
	 * @return boolean
	 *
	 */
	public function set_from_input( $IN = array(), $pre = '' ){
	try{
		$advance=0; $rate_per_year = 0; $effective_time = 0;
		$increasing = 0; $escalation_rate_effective=0;
		$escalation_frequency=0;
		foreach ( array(
			'advance',
			'rate_per_year',
			'effective_time',
			'increasing',
			'escalation_rate_effective',
			'escalation_frequency',
			) AS $_in){
			if ( isset( $IN[$pre . $_in] ) ){
				$$_in = $IN[$pre . $_in];
			}
		}
		if ( isset( $IN[$pre . 'single_payment'] ) ){
			$a = new FinMathAnnuity(1, true, 0, 1);
			$IN[$pre . 'm']  = 1;
			$IN[$pre . 'term']  = 1;
		} else {
			if ( isset( $IN['consider_increasing'] ) ){
				$a = new FinMathAnnuityIncreasing();
				$a->set_increasing( $increasing );
			} else {
				if (empty($escalation_rate_effective) || 0==$escalation_rate_effective){
					$a = new FinMathAnnuity();
				} else {
					$a = new FinMathAnnuityEscalating();
					$a->set_escalation_rate_effective( $escalation_rate_effective );
					$a->set_escalation_frequency( $escalation_frequency );
				}
			}
		}
		if ($a->set_from_input( $IN ) ){
			$this->set_rate_per_year( $rate_per_year );
			$this->set_effective_time( $effective_time );
			$this->set_annuity( $a );
			return true;
		} else {
			return false;
		}
	 } catch( Exception $e ){ 
		throw new Exception( self::myMessage( 'fm-exception-in') . 
			__FILE__ . ": " . $e->getMessage() 
		);
	 }
	}

	public function get_valid_options(){ 
		$r = $this->annuity->get_valid_options();
		$r['rate_per_year'] = array(
			'type'=>'number',
			'decimal'=>'.',
		);
		$r['effective_time'] = array(
			'type'=>'number',
			'decimal'=>'.',
		);
		$r['single_payment'] = array( 'type'=>'boolean', );
		$r['consider_increasing'] = array( 'type'=>'boolean',);
 		return $r; 
	}

	public function get_parameters(){ 
		$r = array();
		$r['rate_per_year'] = array(
			'name'=>'rate_per_year',
			'label'=>self::myMessage( 'fm-rate_per_year'),
		);
		$r['effective_time'] = array(
			'name'=>'effective_time',
			'label'=>self::myMessage( 'fm-effective_time'),
		);
		$r['single_payment'] = array(
			'name'=> 'single_payment',
			'label' => self::myMessage( 'fm-single_payment'),
		);

		$r['consider_increasing'] = array(
			'name'=> 'consider_increasing',
			'label' => self::myMessage( 'fm-consider_increasing'),
		);

    $r = array_merge( $r, $this->annuity->get_parameters() );
		return $r; 
	}

	public function get_values(){ 
		$r = $this->annuity->get_values();
		$r['rate_per_year'] = $this->get_rate_per_year();
		$r['effective_time'] = $this->get_effective_time();
		$r['cashflow_value'] = $this->get_value();
		return $r; 
	}

    /**
     * Get discounted value of cashflow
     *
     * @return number
     *
     */    
	public function get_value(){
		return $this->get_rate_per_year() 
			* exp( -$this->get_annuity()->get_delta() 
			* $this->get_effective_time() ) 
			* $this->get_annuity()->get_value();
	}

    /**
     * @param number $rate_per_year
     * @param number $effective_time time annuity starts
     * @param FinMathAnnuity $annuity
     */    
  public function __construct( $rate_per_year = 0, $effective_time = 0, 
		FinMathAnnuity $annuity = null ) {
		if ( null === $annuity ){
			$annuity = new FinMathAnnuity();
		}
		$this->set_annuity( $annuity );
		$this->set_rate_per_year( $rate_per_year );
		$this->set_effective_time( $effective_time );
  }

    /**
     *
     * @param FinMathAnnuity $a
     *
     */    
	public function set_annuity( FinMathAnnuity $a ){
		$this->annuity = $a;
	}

    /**
     * @param number $n
     */    
	public function set_rate_per_year($n){
		$candidate = array('rate_per_year'=>$n);
		$valid = $this->get_validation($candidate);
		if ($valid['rate_per_year']){
			$this->rate_per_year = $n;
		}
	}
    
    /**
     * @param number $n time annuity starts
     */    
	public function set_effective_time($n){
		$candidate = array('effective_time'=>$n);
		$valid = $this->get_validation($candidate);
		if ($valid['effective_time']){
			$this->effective_time = $n;
 	 	}
	}

    /**
     * @return FinMathAnnuity $a
     */    
	public function get_annuity(){
		return $this->annuity;
	}

	public function get_rate_per_year(){
		return $this->rate_per_year;
	}

	public function get_effective_time(){
		return $this->effective_time;
	}

    /**
     * @return bool
     */    
	private function is_single_payment(){
		if ( 1 != $this->get_annuity()->get_term() ){
			return false;
		}
		if ( 1 != $this->get_annuity()->get_m() ){
			return false;
		}
		if ( !$this->get_annuity()->get_advance() ){
			return false;
		}
		return true;
	}

    /**
     * @return string
     */    
	public function get_abs_label_with_annuity_evaluated(){
		if ( $this->is_single_payment() ){ 
			$ann_label = "";
		} else {
			$ann_label = "\\times " . 
				$this->get_annuity()->explain_format( 
					$this->get_annuity()->get_annuity_certain() 
				);
		}
		if ( 0 == $this->get_effective_time() ){
			$time_label = "";
		} else {
			$time_label = " \\times " . 
				(1 + $this->get_annuity()->explain_format( 
					$this->get_annuity()->get_i_effective() 
					) 
				) . "^{ - " . $this->get_effective_time() . " }";
		}
		$rate_label = $this->rate_format( abs( $this->get_rate_per_year()) );
		return $rate_label . $time_label . $ann_label;
	}

    /**
     * @return string
     */    
	public function get_label( $abs = false ){
		if ( $this->is_single_payment() ){ 
			$ann_label = "";
		} else {
			$ann_label = $this->get_annuity()->get_label();
		}
		if ( 0 == $this->get_effective_time() ){
			$time_label = "";
		} else {
			$time_label = "v^{ " . $this->get_effective_time() . " }";
		}
		if ($abs ) {
			$rate_label = $this->rate_format( abs($this->get_rate_per_year()) );
		} else {
			$rate_label = $this->rate_format( $this->get_rate_per_year() );
		}
		return $rate_label . $time_label . $ann_label;
	}

	private function rate_format( $d, $unused = 2 ){
		return $d;
	}

    /**
     * @return array
     */    
	public function get_labels(){
		$labels = $this->annuity->get_labels();
		$labels['FinMathCashflow'] = $this->get_label();
		return $labels;
	}

}

