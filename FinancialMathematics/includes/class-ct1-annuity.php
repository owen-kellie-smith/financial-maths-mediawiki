<?php   

class CT1_Annuity extends CT1_Interest{

	protected $term;
	protected $value;

	public function get_valid_options(){ 
		$r = parent::get_valid_options();
		$r['term'] = array(
						'type'=>'number',
						'decimal'=>'.',
						'min'=>0,
					);
		$r['value'] = array(
						'type'=>'number',
						'decimal'=>'.',
						'min'=>0.000001,
					);
		return $r; 
	}

	public function get_is_single_payment(){
		return $this->is_single_payment();
	}


	public function get_parameters(){ 
		$r = parent::get_parameters();
		$r['term'] = array(
			'name'=>'term',
			'label'=>self::myMessage( 'fm-label_term'),
			);
		$r['value'] = array(
			'name'=>'value',
			'label'=>self::myMessage( 'fm-label_value'),
			);
		return $r; 
	}

	public function get_values(){ 
		$r = parent::get_values();
		$r['term'] = $this->get_term();
		$r['value'] = $this->get_value();
		return $r; 
	}

	public function __construct( $m = 1, $advance = false, $delta = 0, $term = 1, $debug=false ){
		if ($debug){
			echo __FILE__ . " construct \r\n";
			foreach (array ('m', 'advance','delta','term') as $i){
				echo $i . "  " . print_r($$i, 1) . "\r\n";
			}
		}
		parent::__construct( $m, $advance, $delta);
		$this->set_term($term);
	}

	public function set_term($n){
		$candidate = array('term'=>$n);
		$valid = $this->get_validation($candidate);
		if ($valid['term']){
			if ( $this->is_valid_term_vs_frequency( $n ) ){ 
				$this->term = $n;
			} else {
				throw new Exception(self::myMessage( 'fm-exception-term-frequency')  . self::myMessage( 'fm-attempted-frequency') . $this->get_m() . "." . self::myMessage( 'fm-attempted-term') . $n . ".");
			}
		}
	}

	public function unset_value(){
		unset( $this->value );
	}

	public function set_value($n){
		$candidate = array('value'=>$n);
		$valid = $this->get_validation($candidate);
		if ($valid['value']){
			$this->value = $n;
		}
	}

	private function is_valid_term_vs_frequency( $n ){
		// valid if continuous or $n * m integer
		if ( $this->is_continuous() ) 
			return true;
		$close_enough = 0.00001;
		$trial = $n * $this->get_m();
		if ( $close_enough > abs( intval( $trial ) - $trial ) ) 
			return true;
		return false;
	}

	public function get_term(){
		return $this->term;
	}

	public function get_value(){
		if ( isset( $this->value ) )
			return $this->value;
		else
			return $this->get_annuity_certain();
	}

	public function get_annuity_certain_approx(){
		return $this->term / (1.0 + 0.5 * $this->term * (exp($this->delta)-1) );
	}

	public function get_delta_for_value(){
		if ( !isset( $this->value ) ){
			return $this->get_delta();
		} else {
			return $this->get_interpolated_delta_for_value();
		}
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
 
	protected function get_clone_this(){
		$a_calc = new CT1_Annuity( $this->get_m(), $this->get_advance(), $this->get_delta(), $this->get_term() );
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
			$g[0]['f'] = $a_calc->get_annuity_certain() - $this->get_value();
			$a_calc->set_delta( $x1 );
			$g[1]['f'] = $a_calc->get_annuity_certain() - $this->get_value();
			$x2 = $this->get_interpolated_value( $g );
			$x0 = $x1;
			$x1 = $x2;
			$loop_count++;
			$diff_x = abs( $x0 - $x1 );
		}
		return $x1;
	}
		
	private function get_approx_yield(){
//		$approx_value = n v^(n/2) = n exp(-delta n/2);
		if ( $this->get_value() > 0 && $this->get_term() > 0 ){
			$n = $this->get_term();
			$d = $this->get_delta();
			$V = $this->get_value();
			return -2 / $n * log( $V / $n );
		}
		return false;
	}
 
	public function get_annuity_certain(){
		if (0==$this->get_delta()) return $this->get_term();
		$vn = exp($this->delta * -$this->term);
		return (1 - $vn) / $this->get_rate_in_form($this);
	}

	public function explain_interest_rate_for_value(){
		$return = array();
		$a_calc = $this->get_clone_this();
		$a_calc->set_delta( $this->get_delta_for_value() );
		$return[0]['left'] = "i";
//		$return[0]['right']['summary'] = $this->explain_format( exp( $this->get_delta_for_value() ) - 1);
		if (!($this->is_single_payment())){
			$return[0]['right']['detail'] = $a_calc->explain_annuity_certain();
		}
		$return[0]['right'] = $this->explain_format( exp( $this->get_delta_for_value() ) - 1) . "." . "\\ \\mbox{ "  . self::myMessage( 'fm-verification') . ":}";
		return array_merge( $return, $a_calc->explain_annuity_certain() );
	}


	public function explain_annuity_certain(){
		$return = array();
		$return[0]['left'] = $this->label_annuity();
		if (1==$this->get_annuity_certain() ){
			$return[0]['right'] =  "1";
		} else {

			if (0==$this->get_delta()){
				$return[0]['right'] =  "n";
				$return[1]['right'] =  $this->get_term();
			} else {
				$return[0]['right'] =  "\\frac{ 1 - \\exp{( -\\delta n) } }{ " . $this->label_interest_format() . " } ";
				$return[1]['right']['summary'] =  "\\frac{ 1 - \\exp{ (" . $this->explain_format( -$this->get_delta() ) . " \\times " . $this->get_term() . ") } }{ " . $this->explain_format( $this->get_rate_in_form( $this ) ) . " } ";
				$return[1]['right']['detail'] = $this->explain_rate_in_form( $this );
				$return[2]['right'] = $this->explain_format( $this->get_annuity_certain() ) ;
			}
		}
		return $return;
	}


	public function set_from_input($_INPUT = array(), $pre = ''){
		try{
			if (parent::set_from_input($_INPUT, $pre)){
				if (isset($_INPUT[$pre. 'term'])){
					$this->set_term(	$_INPUT[$pre. 'term'] );
				}
				if (isset($_INPUT[$pre. 'value'])){
					if (!empty($_INPUT[$pre. 'value'])){
						$this->set_value(	$_INPUT[$pre. 'value'] );
					}
				}
				return true;
			}
			else{
				return false;
			}
		}
		catch( Exception $e ){ 
			throw new Exception( self::myMessage( 'fm-exception-in') .  __FILE__ . ": " . $e->getMessage() );
		}
	}

	public function get_label(){
		return $this->label_annuity();
	}

	protected function top_corner($n){
		return "\\require{enclose}{\\enclose{actuarial}{" . $n . "}}";
	}
	
	protected function sub_n(){
		return "_{ " . $this->top_corner( $this->get_term() ) . "}";
	}

	protected function sub_int(){
		return "";
		return "@ " . number_format( 100* $this->get_i_effective(), 2 ) . "\\%";
	}

	protected function label_annuity(){
		if ($this->is_continuous()) $return = "\\bar{a}";
		else{
			if ($this->advance) $out="\\ddot{a}";
			else $out="a";
			if (1!=$this->m) $out.="^{(" . $this->m . ")}";
			$return = $out;
		}
		$return .= $this->sub_n();
		$return .= $this->sub_int();
		return $return;
	}

	public function get_labels(){
		$labels = parent::get_labels();
		$labels['CT1_Annuity'] = $this->label_annuity();
		return $labels;
	}

	private function is_single_payment(){
//echo __FILE__ . " get_values " . print_r( $this->get_values() ) . "\r\n";
		if (!(1==$this->get_term())){
//echo " not one year term   " . "\r\n";
			$return=false;
		} else if (!($this->get_advance())){
			$return=false;
//echo " not advance  " . "\r\n";
		} else if (!(1==$this->get_m())){
			$return=false;
//echo " not 1 per year  " . "\r\n";
		} else if (!(1==$this->get_annuity_certain())){
			$return=false;
//echo " not value 1  " . "\r\n";
		} else {
//echo " a single payment :)   " . "\r\n";
			$return=true;
		}
//echo " conclusion ";
		return $return;
	}

}

// example 
//$a = new CT1_Annuity(12, true, 0.1, 12);
//$a->set_value(11.234567890123456789);
//print_r($a->get_values());
//print_r($a->get_delta_for_value());
//$a->set_delta( $a->get_delta_for_value() );
//print_r($a->explain_interest_rate_for_value());

