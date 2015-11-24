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
 */

/**
 *
 * FinMathMortgage calculates the instalment amount and payment schedule
 * for a level repayment mortgage.
 *
 * Equivalently, if the instalment amount is given, it calculates the effective
 * interest rate. 
 *
 */
class FinMathMortgage extends FinMathAnnuity{

protected $principal;
protected $instalment;
protected $schedule;

public function get_valid_options(){ 
	$r = parent::get_valid_options();
	$r['principal'] = array(
					'type'=>'number',
					'decimal'=>'.',
					);
	$r['instalment'] = $r['principal'];
	$r['schedule'] = array(
							'type'=>'boolean',
						);
	return $r; 
}

public function get_parameters(){ 
	$r = parent::get_parameters();
	$r['principal'] = array(
			'name'=>'principal',
			'label'=>self::myMessage( 'fm-label_principal'),
			);
	$r['instalment'] = array(
			'name'=>'instalment',
			'label'=>self::myMessage( 'fm-label_instalment'),
			);
	$r['schedule'] = array(
			'name'=>'schedule',
			'label'=>self::myMessage( 'fm-label_schedule'),
						);
	return $r; 
}

public function get_values(){ 
	$r = parent::get_values();
	$r['principal'] = $this->get_principal();
	$r['instalment'] = $this->get_instalment();
	$r['schedule'] = $this->get_schedule();
	return $r; 
}

public function __construct( $m = 1, $advance = false, $delta = 0, $term = 1, $principal = 0 ){
	parent::__construct( $m, $advance, $delta, $term);
	$this->set_principal($principal);
}

	protected function get_clone_this(){
		$a_calc = new FinMathMortgage( $this->get_m(), $this->get_advance(), $this->get_delta(), $this->get_term(), $this->get_principal() );
		return $a_calc;
	}

public function set_principal($p){
  $candidate = array('principal'=>$p);
  $valid = $this->get_validation($candidate);
	if ($valid['principal']){
		$this->principal = $p;
	}
}

public function set_instalment($i){
  $candidate = array('instalment'=>$i);
  $valid = $this->get_validation($candidate);
	if ($valid['instalment']){ 
		$this->instalment = $i;
	}
}

public function set_schedule($i){
  $candidate = array('schedule'=>$i);
  $valid = $this->get_validation($candidate);
	if ($valid['schedule']){
		$this->schedule = $i;
	}
}

public function get_schedule(){
	return $this->schedule;
}

public function get_principal(){
	return $this->principal;
}

public function get_instalment($rounding = 2){
	if ( !empty( $this->instalment ) ){
		return round( $this->instalment , $rounding );
	} else {
		return $this->instalment($rounding);
	}
}

	public function explain_interest_rate_for_instalment(){
		$return = array();
		$a_calc = $this->get_clone_this();
		$val = $this->get_principal() / ( $this->get_m() * $this->get_instalment() ) ;
		$this->set_value( $this->get_principal() / ( $this->get_m() * $this->get_instalment() ) );
		$a_calc->set_delta( $this->get_delta_for_value() );
		$return[0]['left'] = "i";
		$return[0]['right'] = $this->explain_format( exp( $this->get_delta_for_value() ) - 1) . "." . "\\ \\mbox{ Verification:}";
		return array_merge( $return, $a_calc->explain_instalment() );
	}


	public function explain_instalment($rounding = 2){
		$return = array();
		$return[0]['left'] = "\\mbox{" . self::myMessage( 'fm-label_instalment-short')  . " }";
		$return[0]['right'] = "\\frac{ \\mbox{" . self::myMessage( 'fm-label_principal') . "}}{ " . $this->get_m() . "  " . $this->label_annuity() . "} ";
		$return[1]['right']['summary'] = "\\frac{ " . number_format( $this->get_principal(), $rounding )  . "}{" . $this->get_m() . " \\times " . $this->explain_format( $this->get_annuity_certain()) . "} ";
		$return[1]['right']['detail'] = $this->explain_annuity_certain();
		$return[2]['right'] = number_format( $this->get_instalment($rounding), $rounding);
		return $return;
	}

	public function get_value(){
		if ( isset( $this->value ) ){
			return $this->value;
		} else {
			return round( $this->get_annuity_certain() * $this->instalment_per_year(), 2 );
		}
	}


private function instalment_per_year(){
	if (0==$this->get_annuity_certain()){
		return NULL;
	}
	return $this->get_principal() / $this->get_annuity_certain();
}

private function instalment($rounding = 2){
	if ($this->is_continuous()){
		throw new Exception(self::myMessage( 'fm-exception-continous-mortgage-instalments'));
	}
	return round($this->instalment_per_year() / $this->get_m(), $rounding);
}

private function interest_per_period(){
 	return exp($this->get_delta() / $this->get_m()) - 1;
}


public function get_mortgage_table(){
       return array( 
               'schedule'=>array(
               'header'=>array(        self::myMessage( 'fm-label-schedule-instalment-count'),
                                       self::myMessage( 'fm-label-schedule-principal-at-start'), 
                                       self::myMessage( 'fm-label-schedule-interest'),
                                       self::myMessage( 'fm-label-schedule-capital-repaid'),
                                       self::myMessage( 'fm-label-schedule-principal-end'),
                                       self::myMessage( 'fm-label-schedule-instalment'),
                                       ),
               'data'=>$this->get_mortgage_schedule_unlabelled(),
               ));
} 


public function get_mortgage_schedule_unlabelled(){
	$s = $this->get_mortgage_schedule();
	$u = array();
	$rounding = 2;
	$format=true;
	for ($i=0, $ii=count($s); $i<$ii; $i++){
		$u[$i] = array(
			$this->format_number($s[$i+1]['count'],$rounding,$format),
			$this->format_number($s[$i+1]['oldPrincipal'],$rounding,$format),
			$this->format_number($s[$i+1]['interest'],$rounding,$format),
			$this->format_number($s[$i+1]['capRepay'],$rounding,$format),
			$this->format_number($s[$i+1]['newPrincipal'],$rounding,$format),
			$this->format_number($s[$i+1]['instalment'],$rounding,$format),
		);
	}
	return $u;
}

private function format_number( $n, $rounding=2, $format=true){
	if ($format){
		return number_format( $n, $rounding);
	} else {
		return $n;
	}
}


public function get_mortgage_schedule(){
	if ($this->is_continuous()){
		throw new Exception(self::myMessage( 'fm-exception-continous-mortgage-schedule'));
	}
	$rounding = 2;
 	$_principal = $this->get_principal();
	$_inst = $this->instalment($rounding);
	for ($i = 1, $ii = $this->get_m() * $this->get_term(); $i <= $ii; $i++){
		$oldPrincipal = $_principal;
		if ($this->get_advance()){
			 $_principal = $_principal - $_inst;
		}
		$int = $this->interest_per_period() * $_principal;
		if (!$this->get_advance()){
			 $_principal = $_principal - $_inst;
		}
		$_principal = $_principal + $int;
		$capRepay = $oldPrincipal - $_principal;
		$schedule[$i] = array(	'count' =>$i, 
					'oldPrincipal'=>$oldPrincipal, 
					'interest'=>$int, 
					'capRepay'=>$capRepay, 
					'newPrincipal' => $_principal, 
					'instalment'=>$_inst,
					);
    }
  return $schedule;
}

public function set_from_input($_INPUT = array(), $pre = ''){
	try{
		if (parent::set_from_input($_INPUT, $pre)){
			$this->set_principal( $_INPUT[$pre. 'principal']);	
			$this->set_instalment( $_INPUT[$pre. 'instalment']);	
			return true;
		}
		else return false;
	}
	catch( Exception $e ){ 
		throw new Exception( self::myMessage( 'fm-exception-in') . " " . __FILE__ . ": " . $e->getMessage() );
	}
}

	public function get_label(){
		return $this->label_mortgage();
	}

	protected function label_mortgage(){
		return number_format($this->instalment_per_year()) . "\\ " . $this->label_annuity();
	}

	public function get_labels(){
		$labels = parent::get_labels();
		$labels['FinMathMortgage'] = $this->label_mortgage();
		return $labels;
	}
}


