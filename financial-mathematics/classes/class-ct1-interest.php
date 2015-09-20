<?php   

//require_once 'class-ct1-marker.php';
require_once 'class-ct1-interest-format.php';
//require_once 'interface-ct1-concept.php';

class CT1_Interest extends CT1_Interest_Format  {

protected $delta = 0;
protected $max_dp = 8;
public function explain_format($d){
	return number_format($d, $this->max_dp);
}

public function get_valid_options(){ 
	$r = parent::get_valid_options();
	$r['delta'] = array(
						'type'=>'number',
						'decimal'=>'.',
					);
	$r['i_effective'] = array(
						'type'=>'number',
						'decimal'=>'.',
						'min'=>'-0.99',
					);
	return $r; 
}

public function get_parameters(){ 
	$r = parent::get_parameters();
	$r['delta'] = array(
			'name'=>'delta',
			'label'=>'Interest rate per year (continuously compounded)',
			);
	$r['i_effective'] = array(
			'name'=>'i_effective',
			'label'=>'Interest rate per year (annual effective rate)',
			);
	return $r; 
}

public function get_values(){ 
	$r = parent::get_values();
	$r['delta'] = $this->get_delta();
	$r['i_effective'] = $this->get_i_effective();
	return $r; 
}

public function __construct( $m = 1, $advance = false, $delta = 0){
	parent::__construct( $m, $advance);
	$this->set_delta($delta);
}

public function get_delta(){
	return $this->delta;
}

public function set_delta($d){
  $candidate = array('delta'=>$d);
  $valid = $this->get_validation($candidate);
	if ($valid['delta']) $this->delta = $d;
}

public function get_i_effective(){
	return exp($this->delta)-1;
}

public function set_i_effective($i){
  $candidate = array('i_effective'=>$i);
  $valid = $this->get_validation($candidate);
	if ($valid['i_effective']) $this->set_delta(log(1+$i));
}

public function get_rate_in_form($f){
	if ($f->is_continuous()) return $this->delta; 
	else{
		$i_m = $f->get_m() * (      exp($this->get_delta() /  $f->get_m()) - 1 );
		$d_m = $f->get_m() * ( 1 -  exp($this->get_delta() / -$f->get_m())     );
	}
	if ($f->get_advance())	return $d_m;
	else	return $i_m;
}

public function explain_rate_in_form($f){
	$return = array();
	$explain_delta[0]['left'] = "\\delta";
	$explain_delta[0]['right'] = "\\log \\left( 1 + i \\right)";
	$explain_delta[1]['right'] = "\\log \\left( " . $this->explain_format( 1 + $this->get_i_effective() ) . " \\right)";
	$explain_delta[2]['right'] = $this->explain_format($this->delta); 
	if (!$f->is_continuous()){ 
		if ($f->get_advance()){
			$return[0]['left'] = $f->label_interest_format();
			$return[0]['right'] =  "m \\left[ 1 - \\exp{ \\left( -\\delta / m \\right) } \\right]";
			$return[1]['right']['summary'] =  $f->m . " \\times \\left[ 1 - \\exp{ \\left( " . $this->explain_format(-$this->delta) . " / ". $f->m ." \\right) } \\right]";
			$return[1]['right']['detail'] =  $explain_delta;
			$return[2]['right'] =  $this->explain_format($this->get_rate_in_form($f));
		}
		else{
			$return[0]['left'] = $f->label_interest_format();
			$return[0]['right'] =  "m \\left\{ \\exp{ \\left( \\delta / m \\right) }  - 1 \\right\}";
			$return[1]['right']['summary'] =  $f->m . " \\times \\left\{ \\exp{ \\left( " . $this->explain_format($this->delta) . " / ". $f->m ." \\right) }  - 1 \\right\}";
			$return[1]['right']['detail'] =  $explain_delta;
			$return[2]['right'] =  $this->explain_format($this->get_rate_in_form($f));
		}
	} else {
		$return = $explain_delta;
	}
	return $return;
}

public function set_from_input($_INPUT = array(), $pre = ''){
	try{
		if (parent::set_from_input($_INPUT, $pre)){
			if (isset($_INPUT[$pre . 'delta'])){
				$this->set_delta(	$_INPUT[$pre. 'delta'] );
			} 
			if (isset($_INPUT[$pre . 'i_effective'])){
				$this->set_i_effective($_INPUT[$pre . 'i_effective']);
			} 
			return true;
		}
		else{
			return false;
		}
	}
	catch( Exception $e ){ 
		throw new Exception( "Exception in " . __FILE__ . ": " . $e->getMessage() );
	}
}


} // end of class CT1_Interest

// example
//$s = new CT1_Interest(1,false,0.06);
//$f = new CT1_Interest_Format(1,true);
//$f = new CT1_Interest_Format(12,true);
//$f = new CT1_Interest_Format(4,false);
//print_r($s->explain_rate_in_form($f));
