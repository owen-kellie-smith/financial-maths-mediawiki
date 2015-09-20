<?php   

require_once 'class-ct1-object.php';

class CT1_Spot_Rate extends CT1_Object {

    private $delta;
    private $effective_time;

    public function get_valid_options(){ 
        $r = array();
        $r['delta'] = array(
                        'type'=>'number',
                        'decimal'=>'.',
                    );
        $r['i_effective'] = array(
                        'type'=>'number',
                        'decimal'=>'.',
                        'min'=>'-0.99',
                    );
        $r['effective_time'] = array(
                        'type'=>'number',
                        'decimal'=>'.',
                    );
        return $r; 
    }

    public function get_parameters(){ 
        $r = array();
        $r['i_effective'] = array(
            'name'=>'i_effective',
            'label'=>'Effective rate per year from t=0 to effective time',
            );
        $r['effective_time'] = array(
            'name'=>'effective_time',
            'label'=>'Effective time after t=0 (in years)',
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
        $labels['CT1_Spot_Delta'] = $this->get_label();
        return $labels;
    }

}

