<?php   
require_once 'class-ct1-annuity.php';

class CT1_Annuity_Increasing extends CT1_Annuity{
// returns value of (Ia)n or (Da)n
// i.e. annuity paying 1, 2, 3...n-2, n-1, n   or n, n-1, n-2, ... 1 @ times 1, 2, 3, .. n (if in arrears)

	protected $increasing;

	public function get_valid_options(){ 
		$r = parent::get_valid_options();
		$r['term']['decimal'] = false;  // allow only whole numbers of years term
		$r['increasing'] = $r['advance'];
		return $r; 
	}

	public function get_parameters(){ 
		$r = parent::get_parameters();
		$r['term']['label'] = '(Whole) number of years term';
		$r['increasing'] = array(
			'name'=>'increasing',
			'label'=>'Annuity amount changes by 1 per year: increases?',
			);
		return $r; 
	}

	public function get_values(){ 
		$r = parent::get_values();
		$r['increasing'] = $this->get_increasing();
		return $r; 
	}

	public function __construct( $m = 1, $advance = false, $delta = 0, $term = 1, $increasing = true ){
		parent::__construct( $m, $advance, $delta, $term );
		$this->set_increasing( $increasing );
	}

	protected function get_clone_this(){
		$a = new CT1_Annuity_Increasing( $this->get_m(),
			$this->get_advance(),
			$this->get_delta(),
			$this->get_term(),
			$this->get_increasing() );
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
		return $this->get_rate_in_form( $this->i_arr() ) / $this->get_rate_in_form( $this->i_flat() );
	}

	private function explain_a_flat(){
		return "\\frac{ " . $this->i_arr()->label_interest_format()  . "}{" . $this->i_flat()->label_interest_format()  . "}";
	}
	
	private function explain_a_flat_numbers(){
		return "\\frac{ " . $this->explain_format( $this->get_rate_in_form( $this->i_arr() ) ) . "}{" . $this->explain_format( $this->get_rate_in_form( $this->i_flat() ) )  . "}";
	}


	private function explain_annuity_certain_increasing(){
		$a_flat = $this->explain_a_flat();
		$a_n_due = $this->a_n_due()->label_annuity();
		$i = "i";
		$n = "n";
		$vn = "v^n";
		$sub[0]['left'] = $this->label_annuity_increasing();
		$sub[0]['right'] = "$a_flat  \\times \\frac{ $a_n_due  -  $n $vn }{ $i}";
		$sub[1]['right']['summary'] = $this->explain_a_flat_numbers() . "  \\times \\frac{ " . $this->explain_format( $this->a_n_due()->get_annuity_certain() ) . " - " . $this->get_term() . " \\times " . $this->explain_format( 1+$this->get_i_effective() ) . "^{-" . $this->get_term() . "}}{" . $this->explain_format( $this->get_i_effective()) . "}";
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
		$sub[1]['right']['summary'] = $this->explain_a_flat_numbers() . "  \\times \\frac{ " . $this->get_term() . " - " . $this->explain_format( $this->a_n()->get_annuity_certain() ) . "}{" . $this->explain_format( $this->get_i_effective()) . "}";
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
		return  new CT1_Interest( $this->get_m(), $this->get_advance(), $this->get_delta() );
	}

	private function i_arr(){
		return  new CT1_Interest( 1, false, $this->get_delta() );
	}

	private function a_n_due(){
		return  new CT1_annuity(1, true, $this->get_delta(), $this->get_term());
	}

	private function a_n(){
		return  new CT1_annuity(1, false, $this->get_delta(), $this->get_term());
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
				if ( isset( $_INPUT[$pre. 'increasing'] ) )
					$this->set_increasing(	$_INPUT[$pre. 'increasing'] );
				else
					$this->set_increasing(	false  );
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

	public function get_label(){
		return $this->label_annuity_increasing();
	}

	protected function label_annuity_increasing(){
		if ( $this->is_continuous() ) $return = "\\bar{a}";
		else{
			if ($this->advance) $out="\\ddot{a}";
			else $out="a";
			if (1!=$this->m) $out.="^{(" . $this->m . ")}";
			$return = $out;
		}
		if ( $this->get_increasing()  ) 
			$head = "I";
		else
			$head = "D";
		$label = "(" . $head . $return . ")" . $this->sub_n();
		return $label;
	}

	public function get_labels(){
		$labels = parent::get_labels();
		$labels['CT1_Annuity_Increasing'] = $this->label_annuity_increasing();
		return $labels;
	}

}

// example 
/*
$a = new CT1_Annuity_Increasing(1999, false, 0.1, 13, false);
print_r($a->get_labels());
print_r($a->get_annuity_certain());
print_r($a->explain_annuity_certain());
*/
