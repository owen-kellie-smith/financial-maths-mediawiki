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

class FinMathAnnuityIncreasing extends FinMathAnnuity{
// returns value of (Ia)n or (Da)n
// i.e. annuity paying 1, 2, 3...n-2, n-1, n   
// or n, n-1, n-2, ... 1 @ times 1, 2, 3, .. n (if in arrears)

	protected $increasing;

	public function get_valid_options(){ 
		$r = parent::get_valid_options();
		$r['term']['decimal'] = false;  // allow only whole numbers of years term
		$r['increasing'] = $r['advance'];
		return $r; 
	}

	public function get_parameters(){ 
		$r = parent::get_parameters();
		$r['term']['label'] = self::myMessage( 'fm-label-term-whole') ;
		$r['increasing'] = array( 'name'=>'increasing',
			'label'=>self::myMessage( 'fm-label-increasing'),
		);
		return $r; 
	}

	public function get_values(){ 
		$r = parent::get_values();
		$r['increasing'] = $this->get_increasing();
		return $r; 
	}

	public function __construct( $m = 1, 
		$advance = false, 
		$delta = 0, 
		$term = 1, 
		$increasing = true 
	){
		parent::__construct( $m, $advance, $delta, $term );
		$this->set_increasing( $increasing );
	}

	protected function get_clone_this(){
		$a = new FinMathAnnuityIncreasing( $this->get_m(),
			$this->get_advance(),
			$this->get_delta(),
			$this->get_term(),
			$this->get_increasing() 
		);
		return $a;
	}

	public function set_increasing( $b ){
		$this->increasing = $this->my_bool($b);
	}

	public function get_increasing(){
		return $this->increasing;
	}

	private function get_annuity_certain_nil_interest(){
		$n = $this->get_term();
		return $n * ($n + 1) / 2;
	}

	public function get_annuity_certain(){
		if ( 0 == $this->get_delta() ){
			return $this->get_annuity_certain_nil_interest();
		}
		if ( $this->get_increasing() ){
			return $this->get_annuity_certain_increasing();
		} else {
			return $this->get_annuity_certain_decreasing();
		}
	}

	private function get_a_flat(){
		return $this->get_rate_in_form( $this->i_arr() ) / 
			$this->get_rate_in_form( $this->i_flat() );
	}

	private function explain_a_flat(){
		return "\\frac{ " . $this->i_arr()->label_interest_format()  . 
			"}{" . $this->i_flat()->label_interest_format()  . "}";
	}
	
	private function explain_a_flat_numbers(){
		return "\\frac{ " . 
			$this->explain_format( $this->get_rate_in_form( $this->i_arr() ) ) . "}{" . 
			$this->explain_format( $this->get_rate_in_form( $this->i_flat() ) )  . "}";
	}

	private function explain_annuity_certain_increasing(){
		$a_flat = $this->explain_a_flat();
		$a_n_due = $this->a_n_due()->label_annuity();
		$i = "i";
		$n = "n";
		$vn = "v^n";
		$sub[0]['left'] = $this->label_annuity_increasing();
		$sub[0]['right'] = "$a_flat  \\times \\frac{ $a_n_due  -  $n $vn }{ $i}";
		$sub[1]['right']['summary'] = $this->explain_a_flat_numbers() . 
			"  \\times \\frac{ " . 
			$this->explain_format( $this->a_n_due()->get_annuity_certain() ) . 
			" - " . $this->get_term() . " \\times " . 
			$this->explain_format( 1+$this->get_i_effective() ) . 
			"^{-" . $this->get_term() . "}}{" . 
			$this->explain_format( $this->get_i_effective()) . "}";
		$sub[1]['right']['detail'][] = $this->explain_rate_in_form( $this->i_flat() );
		$sub[1]['right']['detail'][] = $this->a_n_due()->explain_annuity_certain();
		$sub[2]['right'] = $this->explain_format( $this->get_annuity_certain() );
		return $sub;
	}

	private function explain_annuity_certain_decreasing(){
		$a_flat = $this->explain_a_flat();
		$a_n = $this->a_n()->label_annuity();
		$i = "i";
		$n = "n";
		$sub[0]['left'] = $this->label_annuity_increasing();
		$sub[0]['right'] = "$a_flat  \\times \\frac{ $n - $a_n }{ $i}";
		$sub[1]['right']['summary'] = $this->explain_a_flat_numbers() . 
			"  \\times \\frac{ " . $this->get_term() . " - " . 
			$this->explain_format( $this->a_n()->get_annuity_certain() ) . "}{" . 
			$this->explain_format( $this->get_i_effective()) . "}";
		$sub[1]['right']['detail'][] = $this->explain_rate_in_form( $this->i_flat() );
		$sub[1]['right']['detail'][] = $this->a_n()->explain_annuity_certain();
		$sub[2]['right'] = $this->explain_format( $this->get_annuity_certain() );
		return $sub;
	}

	private function explain_annuity_certain_nil_interest(){
		$n = $this->get_term();
		$sub[0]['left'] = $this->label_annuity_increasing();
		$sub[0]['right'] = "\\sum_{j = 1}^{n} j";
		$sub[1]['right'] = "\\frac{n (n+1)}{2}";
		$sub[2]['right'] = "\\frac{ " . $n  . "\\times " . ($n+1) . "}{2}";
		$sub[3]['right'] = $this->get_annuity_certain();
		return $sub;
	}

	private function i_flat(){
		return  new FinMathInterest( $this->get_m(), 
			$this->get_advance(), $this->get_delta() 
		);
	}

	private function i_arr(){
		return  new FinMathInterest( 1, false, $this->get_delta() );
	}

	private function a_n_due(){
		return  new FinMathAnnuity(1, true, $this->get_delta(), $this->get_term());
	}

	private function a_n(){
		return  new FinMathAnnuity(1, false, $this->get_delta(), $this->get_term());
	}

	private function get_annuity_certain_increasing(){
		$a_flat = $this->get_a_flat();
		$a_n_due = $this->a_n_due()->get_annuity_certain();
		$i = $this->get_i_effective();
		$n = $this->get_term();
		$vn = exp(-$this->get_delta() * $n );
		return $a_flat * ( $a_n_due - $n * $vn ) / $i;
	}

	private function get_annuity_certain_decreasing(){
		$a_flat = $this->get_a_flat();
		$a_n = $this->a_n()->get_annuity_certain();
		$i = $this->get_i_effective();
		$n = $this->get_term();
		return $a_flat * ( $n - $a_n ) / $i;
	}

	public function explain_annuity_certain(){
		if ( 0 == $this->get_delta() ){
			return $this->explain_annuity_certain_nil_interest();
		}
		if ( $this->get_increasing() ){
			return $this->explain_annuity_certain_increasing();
		} else {
			return $this->explain_annuity_certain_decreasing();
		}
	}

	public function set_from_input($_INPUT = array(), $pre = ''){
		try{
			if (parent::set_from_input($_INPUT, $pre)){
				if ( isset( $_INPUT[$pre. 'increasing'] ) ){
					$this->set_increasing(	$_INPUT[$pre. 'increasing'] );
				} else {
					$this->set_increasing(	false  );
				}
				return true;
			}
			else{
				return false;
			}
		}
		catch( Exception $e ){ 
			throw new Exception( self::myMessage( 'fm-exception-in')  . 
				__FILE__ . ": " . $e->getMessage() 
			);
		}
	}

	public function get_label(){
		return $this->label_annuity_increasing();
	}

	protected function label_annuity_increasing(){
		if ( $this->is_continuous() ){
			$return = "\\bar{a}";
		} else {
			if ($this->advance){
				$out="\\ddot{a}";
			} else { 
				$out="a";
			}
			if (1!=$this->m){ 
				$out.="^{(" . $this->m . ")}";
			}
			$return = $out;
		}
		if ( $this->get_increasing()  ){ 
			$head = "I";
		} else {
			$head = "D";
		}
		$label = "(" . $head . $return . ")" . $this->sub_n();
		return $label;
	}

	public function get_labels(){
		$labels = parent::get_labels();
		$labels['FinMathAnnuityIncreasing'] = $this->label_annuity_increasing();
		return $labels;
	}

}

