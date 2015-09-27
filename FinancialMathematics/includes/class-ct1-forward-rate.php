<?php   

require_once 'class-ct1-object.php';

class CT1_Forward_Rate extends CT1_Object {

    private $delta;
    private $end_time;
    private $start_time;

	public function get_index(){
		return $this->get_start_time()  . ","  . $this->get_end_time();
	}

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
        $r['start_time'] = array(
                        'type'=>'number',
                        'decimal'=>'.',
                    );
        $r['end_time'] = array(
                        'type'=>'number',
                        'decimal'=>'.',
                    );
        return $r; 
    }

    public function get_parameters(){ 
        $r = array();
        $r['i_effective'] = array(
            'name'=>'i_effective',
            'label'=>'Effective rate per year',
            );
        $r['start_time'] = array(
            'name'=>'start_time',
            'label'=>'Start time after t=0 (in years)',
            );
        $r['end_time'] = array(
            'name'=>'end_time',
            'label'=>'End time after t=0 (in years)',
            );
        return $r; 
    }

    public function get_values(){ 
        $r = array();
        $r['delta'] = $this->get_delta();
        $r['start_time'] = $this->get_start_time();
        $r['end_time'] = $this->get_end_time();
        return $r; 
    } 

    public function __construct( $i_effective = 0, $start_time = 0, $end_time = 0 ) {
        $this->set_i_effective( $i_effective );
        $this->set_start_time( $start_time );
        $this->set_end_time( $end_time );
    }

    public function get_delta(){
        return $this->delta ;
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
    
    public function set_start_time($n){
        $candidate = array('start_time'=>$n);
        $valid = $this->get_validation($candidate);
        if ($valid['start_time']){
            $this->start_time = $n;
        }
    }

    public function get_start_time(){
        return $this->start_time;
    }
            
    public function set_end_time($n){
        $candidate = array('end_time'=>$n);
        $valid = $this->get_validation($candidate);
        if ($valid['end_time']){
            $this->end_time = $n;
        }
    }

    public function get_end_time(){
        return $this->end_time;
    }
            
    public function get_label(){
        return "f" . "_{" . $this->get_start_time() . ", " . $this->get_end_time() . "}";
    }

    public function get_label_delta(){
        return "\\phi" . "_{" . $this->get_start_time() . ", " . $this->get_end_time() . "}";
    }

    public function get_labels(){
        $labels['CT1_Spot_Delta'] = $this->get_label();
        return $labels;
    }

}

