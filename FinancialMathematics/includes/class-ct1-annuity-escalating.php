<?php   
require_once 'class-ct1-annuity.php';

class CT1_Annuity_Escalating extends CT1_Annuity{

	protected $escalation_delta;
	protected $escalation_frequency;

	public function get_valid_options(){ 
		$r = parent::get_valid_options();
		$r['escalation_delta'] = $r['delta'];
		$r['escalation_rate_effective'] = $r['i_effective'];
		$r['escalation_frequency'] = $r['m'];
		return $r; 
	}

	public function get_parameters(){ 
		$r = parent::get_parameters();
		$r['escalation_delta'] = array(
			'name'=>'escalation_delta',
			'label'=>'Escalation rate (continuously compounded) per year',
			);
		$r['escalation_rate_effective'] = array(
			'name'=>'escalation_rate_effective',
			'label'=>'Effective annual escalation rate',
			);
		$r['escalation_frequency'] = array(
			'name'=>'escalation_frequency',
			'label'=>'Escalation frequency (number of times per year that escalations are made)',
			);
		return $r; 
	}

	public function get_values(){ 
		$r = parent::get_values();
		$r['escalation_delta'] = $this->get_escalation_delta();
		$r['escalation_rate_effective'] = $this->get_escalation_rate_effective();
		$r['escalation_frequency'] = $this->get_escalation_frequency();
		return $r; 
	}

	public function __construct( $m = 1, $advance = false, $delta = 0, $term = 1, $escalation_frequency = 1, $escalation_delta = 0 ){
		parent::__construct( $m, $advance, $delta, $term );
		$this->set_escalation_delta( $escalation_delta );
		$this->set_escalation_frequency( $escalation_frequency );
	}

	protected function get_clone_this(){
		$a_calc = new CT1_Annuity_Escalating( $this->get_m(), $this->get_advance(), $this->get_delta(), $this->get_term(), $this->get_escalation_frequency(), $this->get_escalation_delta() );
		return $a_calc;
	}

	public function set_escalation_delta( $r ){
		$candidate = array( 'escalation_delta'=>$r );
		$valid = $this->get_validation( $candidate );
		if ( $valid['escalation_delta'] ) $this->escalation_delta = $r;
	}

	public function get_escalation_delta(){
		return $this->escalation_delta;
	}

	public function set_escalation_rate_effective( $r ){
		$candidate = array( 'escalation_rate_effective'=>$r );
		$valid = $this->get_validation( $candidate );
		if ( $valid['escalation_rate_effective'] ) 
			$this->set_escalation_delta( log( 1 + $r) );
	}

	public function get_escalation_rate_effective(){
		return exp( $this->escalation_delta ) - 1;
	}

	public function set_escalation_frequency( $r ){
		if ( NULL == $r ) $r=1;
		$candidate = array( 'escalation_frequency'=>$r );
		$valid = $this->get_validation( $candidate );
		if ( $valid['escalation_frequency'] && $this->is_valid_escalation_frequency( $r ) ) { 
			if ( $this->is_valid_escalation_frequency( $r ) ) 
				$this->escalation_frequency = $r;
		} else {
			throw new Exception("Attempt to set escalating annuity where escalations don't coincide with annuity payment instalments.  Annuity payment frequency is " . $this->get_m() . ", attempted escation frequency is " . $r . ".");
		}
	}

	private function is_valid_escalation_frequency( $f ){
		// valid if continuous or $f/m integer or m/$f integer
		$escalation_format = new CT1_Interest_Format( $f );
		if ( $escalation_format->is_continuous() ) 
			return true;
		$close_enough = 0.00001;
		$trial = $f / $this->get_m();
		if ( $close_enough > abs( intval( $trial ) - $trial ) ) 
			return true;
		$trial = $this->get_m() / $f;
		if ( $close_enough > abs( intval( $trial ) - $trial ) ) 
			return true;
		return false;
	}

	public function get_escalation_frequency(){
		return $this->escalation_frequency;
	}

	public function get_annuity_certain(){
		$escalation_format = new CT1_Interest_Format( $this->get_escalation_frequency() );
		if ( $escalation_format->is_continuous() || $this->get_escalation_frequency() >= $this->get_m() ){ 
			return $this->get_annuity_certain_escalation_continual();
		} else {
			return $this->get_annuity_certain_escalation_stepped();
		}
	}

	private function explain_net_interest_rate(){
		$return[0]['left'] = "\\mbox{Net interest rate } \\delta";
		$return[0]['right'] = "\\log \\left( \\frac{1 + \\mbox{ effective interest rate}}{ 1 + \\mbox{ effective escalation rate} } \\right) ";
		$return[1]['right'] = "\\log \\left( \\frac{ " . (1 + $this->get_i_effective()) . "}{ " . (1 + $this->get_escalation_rate_effective()) . " } \\right) ";
		$return[2]['right'] = $this->explain_format( $this->get_delta_net()  ) ;
		return $return;
	}

	private function explain_annuity_certain_escalation_stepped(){
		$a_flat = $this->a_flat();
		$a_inc = $this->a_inc();
		$sub[0]['left'] = "\\mbox{Annuity value}";
		$sub[0]['right'] = $a_flat->label_annuity() . " (i_{gross})" . " \\times  m_{esc} \\times " . $a_inc->label_annuity() . "(\\delta_{net})";
		$sub[1]['right']['summary'] = $this->explain_format( $a_flat->get_annuity_certain() ) . " \\times " . $this->get_escalation_frequency() . " \\times " . $this->explain_format( $a_inc->get_annuity_certain() );
		$sub[2]['right'] = $this->explain_format( $this->get_annuity_certain() );
		$sub[1]['right']['detail'][] = $a_flat->explain_annuity_certain();
		$sub[1]['right']['detail'][] = $a_inc->explain_annuity_certain();
		return array_merge( $sub, $this->explain_net_interest_rate() );
	}

	private function explain_annuity_certain_escalation_continual(){
		$a = new CT1_Annuity($this->get_m(), $this->get_advance(), $this->get_delta_net(), $this->get_term());
		if ( $a->is_continuous() || $a->get_advance() ){
			return array_merge( $this->explain_net_interest_rate(), $a->explain_annuity_certain() );
		} else {
			// deflate by implied escalation to first payment
			$del_esc[0]['left'] = "\\delta_{\mbox{esc}}";
			$del_esc[0]['right'] = "\\log \\left( 1 + \\mbox{ effective escalation rate} \\right)";
			$del_esc[1]['right'] = "\\log \\left( " . (1 + $this->get_escalation_rate_effective() ) . " \\right)";
			$del_esc[2]['right'] = $this->explain_format( $this->get_escalation_delta() );
			$sub[0]['left'] = "\\mbox{Annuity value}";
			$sub[0]['right']['summary'] = "\\exp \\left( - \\delta_{\mbox{esc}} / m \\right) \\times " . $this->label_annuity();
			$sub[1]['right']['summary'] = "\\exp \\left( - " . $this->explain_format( $this->get_escalation_delta() ) . " / " . $this->get_m() . " \\right) \\times " . $this->explain_format( $a->get_annuity_certain() );
			$sub[0]['right']['detail'][] = $del_esc;
			$sub[1]['right']['detail'][] = $a->explain_annuity_certain();
			$sub[2]['right'] = $this->explain_format( $this->get_annuity_certain() );
			return array_merge( $sub, $this->explain_net_interest_rate() );
		}
	}

	private function get_annuity_certain_escalation_continual(){
		$a = new CT1_Annuity($this->get_m(), $this->get_advance(), $this->get_delta_net(), $this->get_term());
		$raw = $a->get_annuity_certain();
		if ( $a->is_continuous() || $a->get_advance() ){
			return $raw;
		} else {
			// deflate by implied escalation to first payment
			return $raw * exp( -$this->get_escalation_delta() / $this->get_m() );
		}	
	}

	private function a_flat(){
		return  new CT1_annuity($this->get_m(), 
				$this->get_advance(), 
				$this->get_delta(), 
				1.0 / $this->get_escalation_frequency()
				);
	}

	private function a_inc(){
		return  new CT1_annuity($this->get_escalation_frequency(), 
				true, 
				$this->get_delta_net(), 
				$this->get_term()
				);
	}


	private function get_annuity_certain_escalation_stepped(){
		$a_flat = $this->a_flat();
		$a_inc = $this->a_inc();
		return $a_inc->get_annuity_certain() * $this->get_escalation_frequency() * $a_flat->get_annuity_certain();
	}

	private function get_delta_net(){
		return $this->get_delta() - $this->get_escalation_delta();
	}


	public function explain_annuity_certain(){
		$escalation_format = new CT1_Interest_Format( $this->get_escalation_frequency() );
		if ( $escalation_format->is_continuous() || $this->get_escalation_frequency() >= $this->get_m() ){ 
			return $this->explain_annuity_certain_escalation_continual();
		} else {
			return $this->explain_annuity_certain_escalation_stepped();
		}
	}


	public function set_from_input($_INPUT = array(), $pre = ''){
		try{
			if (parent::set_from_input($_INPUT, $pre)){
				if ( isset( $_INPUT[$pre. 'escalation_delta'] ) )
					$this->set_escalation_delta(	$_INPUT[$pre. 'escalation_delta'] );
				if ( isset( $_INPUT[$pre. 'escalation_rate_effective'] ) )
					$this->set_escalation_rate_effective(	$_INPUT[$pre. 'escalation_rate_effective'] );
				$this->set_escalation_frequency(	$_INPUT[$pre. 'escalation_frequency'] );
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
		return $this->label_annuity();
	}

	public function get_labels(){
		$labels = parent::get_labels();
		$labels['CT1_Annuity'] = $this->label_annuity();
		return $labels;
	}

}

// example 
//$a = new CT1_Annuity_Escalating(998, true, 0.1, 13, 1, 0.1);
//print_r($a->get_values());
//print_r($a->get_annuity_certain());
//print_r($a->explain_annuity_certain());

