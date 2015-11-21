<?php   

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
	 * @access public
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
		throw new Exception( self::myMessage( 'fm-exception-in') . __FILE__ . ": " . $e->getMessage() );
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
		$r['single_payment'] = array(
							'type'=>'boolean',
						);
		$r['consider_increasing'] = array(
							'type'=>'boolean',
						);

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
    
    public function get_value(){
        return $this->get_rate_per_year() * exp( -$this->get_annuity()->get_delta() * $this->get_effective_time() ) * $this->get_annuity()->get_value();
    }


    public function __construct( $rate_per_year = 0, $effective_time = 0, FinMathAnnuity $annuity = null ) {
        if ( null === $annuity ){
            $annuity = new FinMathAnnuity();
	}
        $this->set_annuity( $annuity );
        $this->set_rate_per_year( $rate_per_year );
        $this->set_effective_time( $effective_time );
    }

    public function set_annuity( FinMathAnnuity $a ){
        $this->annuity = $a;
    }

    public function set_rate_per_year($n){
        $candidate = array('rate_per_year'=>$n);
        $valid = $this->get_validation($candidate);
        if ($valid['rate_per_year']){
            $this->rate_per_year = $n;
        }
    }
    
    public function set_effective_time($n){
        $candidate = array('effective_time'=>$n);
        $valid = $this->get_validation($candidate);
        if ($valid['effective_time']){
            $this->effective_time = $n;
        }
    }

    public function get_annuity(){
        return $this->annuity;
    }

    public function get_rate_per_year(){
        return $this->rate_per_year;
    }

    public function get_effective_time(){
        return $this->effective_time;
    }

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

    public function get_abs_label_with_annuity_evaluated(){
        if ( $this->is_single_payment() ){ 
            $ann_label = "";
	} else {
            $ann_label = "\\times " . $this->get_annuity()->explain_format( $this->get_annuity()->get_annuity_certain() );
	}
        if ( 0 == $this->get_effective_time() ){
            $time_label = "";
	} else {
            $time_label = " \\times " . (1 + $this->get_annuity()->explain_format( $this->get_annuity()->get_i_effective() ) ) . "^{ - " . $this->get_effective_time() . " }";
	}
        $rate_label = $this->rate_format( abs( $this->get_rate_per_year()) );
        return $rate_label . $time_label . $ann_label;
    }


            
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

    private function rate_format( $d, $dps = 2 ){
        return $d;
        return number_format( $d, $dps );
    }

    public function get_labels(){
        $labels = $this->annuity->get_labels();
        $labels['FinMathCashflow'] = $this->get_label();
        return $labels;
    }

}

