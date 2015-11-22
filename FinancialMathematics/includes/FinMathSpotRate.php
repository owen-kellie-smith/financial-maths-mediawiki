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
 * @file
 *
 */


/**
 * Spot rate object.
 * The defining parameters are the annual effetive rate, and the term.
 * I.e. the spot rate represents the available interest rate which applies over the next term years.
 
 * The rate is stored as a continuously compounded rate.
 */

class FinMathSpotRate extends FinMathObject {

	private $delta;
	private $effective_time;

	public function get_valid_options(){ 
		$r = array();
		$r['delta'] = array( 'type'=>'number', 'decimal'=>'.', );
		$r['i_effective'] = array(
			'type'=>'number',
			'decimal'=>'.',
			'min'=>'-0.99',
		);
		$r['effective_time'] = array( 'type'=>'number', 'decimal'=>'.', );
		return $r; 
  }

	public function get_parameters(){ 
		$r = array();
		$r['i_effective'] = array(
			'name'=>'i_effective',
			'label'=>self::myMessage( 'fm-label-i_effective-timed'),
		);
		$r['effective_time'] = array(
			'name'=>'effective_time',
			'label'=>self::myMessage( 'fm-label-effective-time'),
		);
		return $r; 
	}

	public function get_index(){
		return $this->get_effective_time();
	}

	public function get_values(){ 
		$r = array();
		$r['delta'] = $this->get_delta();
		$r['effective_time'] = $this->get_effective_time();
		return $r; 
	} 

	public function __construct( $i_effective = 0, $effective_time = 0 ) {
		$this->set_i_effective( $i_effective );
		$this->set_effective_time( $effective_time );
	}

	public function get_delta(){
		return $this->delta ;
	}

	public function get_vn(){
		return exp( -$this->get_delta() * $this->get_effective_time() );
	}

	public function set_delta($n){
		$candidate = array('delta'=>$n);
		$valid = $this->get_validation($candidate);
		if ($valid['delta']){
			$this->delta = $n;
		}
	}

	public function get_i_effective(){
		return exp( $this->delta ) -1;
	}

	public function set_i_effective($n){
		$candidate = array('i_effective'=>$n);
		$valid = $this->get_validation($candidate);
		if ($valid['i_effective']){
			$this->delta = log( 1 + $n);
		}
	}
    
	public function set_effective_time($n){
		$candidate = array('effective_time'=>$n);
		$valid = $this->get_validation($candidate);
		if ($valid['effective_time']){
			$this->effective_time = $n;
		}
	}

	public function get_effective_time(){
		return $this->effective_time;
	}
            
	public function get_label(){
		return "i" . "_{" . $this->get_effective_time() . "}";
	}

	public function get_label_delta(){
		return "\\delta" . "_{" . $this->get_effective_time() . "}";
	}

	public function get_labels(){
		$labels['Spot_Delta'] = $this->get_label();
		return $labels;
	}

}

