<?php   


class CT1_Interest extends CT1_Interest_Format  {

protected $delta_source;
protected $source_format;
protected $source_rate;
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
		$r['source_m'] = array(
							'type'=>'number',
							'decimal'=>'.',
							'min'=>0.00001,
						);
		$r['source_advance'] = array(
							'type'=>'boolean',
						);

	$r['source_rate'] = array(
						'type'=>'number',
						'decimal'=>'.',
						'min'=>'-0.99',
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
	$r['source_m'] = array(
			'name'=>'source_m',
			'label'=>self::myMessage( 'fm-label_source_m') ,
			);
	$r['source_advance'] = array(
			'name'=>'source_advance',
			'label'=>self::myMessage( 'fm-label_source_advance') ,
			);
	$r['source_rate'] = array(
			'name'=>'source_rate',
			'label'=>self::myMessage( 'fm-label_source_rate') ,
			);
	$r['delta'] = array(
			'name'=>'delta',
			'label'=>self::myMessage( 'fm-label_delta') ,
			);
	$r['i_effective'] = array(
			'name'=>'i_effective',
			'label'=>self::myMessage( 'fm-label_i'),
			);
	return $r; 
}

public function get_values(){ 
	$r = parent::get_values();
	$r['source_rate'] = $this->get_source_rate();
	$r['source_format'] = $this->get_source_format();
	$r['delta'] = $this->get_delta();
	$r['i_effective'] = $this->get_i_effective();
	return $r; 
}

public function __construct( $m = 1, $advance = false, $delta = 0){
	parent::__construct( $m, $advance);
	$this->set_delta($delta);
}

private function get_delta_source(){
	return $this->delta_source;
}

public function get_source_format(){
	return $this->source_format;
}

public function set_source_format($m, $advance){
  $this->source_format = new CT1_Interest_Format( $m, $advance );	
	$i = $this->get_source_rate();
			if ($this->get_source_format()->is_continuous()){
				$delta = $i;
			} else {
				if ($this->get_source_format()->get_advance()){
					$delta = $this->get_source_format()->get_m() * -log( 1 - $i / $this->get_source_format()->get_m() );
				} else {
					$delta = $this->get_source_format()->get_m() * log( 1 + $i / $this->get_source_format()->get_m() );
				}
			}
			$this->set_delta($delta);
}

public function get_source_rate(){
	return $this->source_rate;
}

public function set_source_rate($i){
  $candidate = array('source_rate'=>$i);
  $valid = $this->get_validation($candidate);
	if ($valid['source_rate']) $this->source_rate=$i;
	if (is_object($this->get_source_format())){
		if ($this->get_source_format() instanceof CT1_Interest_Format){
			if ($this->get_source_format()->is_continuous()){
				$delta = $i;
			} else {
				if ($this->get_source_format()->get_advance()){
					$delta = $this->get_source_format()->get_m() * -log( 1 - $i / $this->get_source_format()->get_m() );
				} else {
					$delta = $this->get_source_format()->get_m() * log( 1 + $i / $this->get_source_format()->get_m() );
				}
			}
			$this->set_delta($delta);
		}
	}
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
	if ('source_rate'==$this->get_delta_source() && !$this->get_source_format()->is_continuous()){
		if (!$this->get_source_format()->get_advance()){
			$explain_delta[0]['left'] = "\\delta";
			$explain_delta[0]['right'] = $this->get_source_format()->get_m() . "\ \\log \\left( 1 + \\frac{" . $this->get_source_format()->label_interest_format(). "}{" . $this->get_source_format()->get_m() . "}\\right)";
			$explain_delta[1]['right'] = $this->get_source_format()->get_m() . "\ \\log \\left( " . $this->explain_format( 1 + $this->get_source_rate() / $this->get_source_format()->get_m()) . " \\right)";
			$explain_delta[2]['right'] = $this->explain_format($this->delta) ;
		} else {
			$explain_delta[0]['left'] = "\\delta";
			$explain_delta[0]['right'] = "- " . $this->get_source_format()->get_m() . "\ \\log \\left( 1 - \\frac{" . $this->get_source_format()->label_interest_format(). "}{" . $this->get_source_format()->get_m() . "}\\right)";
			$explain_delta[1]['right'] = "- " . $this->get_source_format()->get_m() . "\ \\log \\left( " . $this->explain_format( 1 - $this->get_source_rate() /  $this->get_source_format()->get_m()) . " \\right)";
			$explain_delta[2]['right'] = $this->explain_format($this->delta);
		}
	} elseif ('i_effective'==$this->get_delta_source()){
		$explain_delta[0]['left'] = "\\delta";
		$explain_delta[0]['right'] = "\\log \\left( 1 + i \\right)";
		$explain_delta[1]['right'] = "\\log \\left( " . $this->explain_format( 1 + $this->get_i_effective() ) . " \\right)";
		$explain_delta[2]['right'] = $this->explain_format($this->delta);
	} else {
			$explain_delta[0]['left'] = "\\delta";
			$explain_delta[0]['right'] = $this->explain_format($this->delta);
	}
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
		if (parent::set_from_input($_INPUT, $pre)){ // the last set rate trumps all the others
			if (isset($_INPUT[$pre . 'source_m']) && isset($_INPUT[$pre . 'source_rate'])){
					$this->set_source_rate($_INPUT[$pre . 'source_rate']);
					if (isset($_INPUT[$pre . 'source_advance'])){
						 $this->set_source_format($_INPUT[$pre . 'source_m'], $_INPUT[$pre . 'source_advance']);
					} else {
						 $this->set_source_format($_INPUT[$pre . 'source_m'], false);
					}
				$this->delta_source = "source_rate";
			} 
			if (isset($_INPUT[$pre . 'i_effective']) ){
				if (!array()==$_INPUT[$pre . 'i_effective']){
					$this->set_i_effective($_INPUT[$pre . 'i_effective']);
					$this->delta_source = "i_effective";
				}
			} 
			if (isset($_INPUT[$pre . 'delta'])){
				$this->set_delta(	$_INPUT[$pre. 'delta'] );
				$this->delta_source = "delta";
			} 
			return true;
		}
		else{
			return false;
		}
	}
	catch( Exception $e ){ 
		throw new Exception( self::myMessage( 'fm-exception-in') . " " . __FILE__ . ": " . $e->getMessage() );
	}

}


} // end of class CT1_Interest

// example
//$s = new CT1_Interest(1,false,0.06);
//$f = new CT1_Interest_Format(1,true);
//$f = new CT1_Interest_Format(12,true);
//$f = new CT1_Interest_Format(4,false);
//print_r($s->explain_rate_in_form($f));
