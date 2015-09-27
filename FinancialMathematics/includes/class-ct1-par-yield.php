<?php   

require_once 'class-ct1-object.php';

class CT1_Par_Yield extends CT1_Object {

    private $coupon;
    private $term;

    public function get_valid_options(){ 
        $r = array();
        $r['coupon'] = array(
                        'type'=>'number',
                        'decimal'=>'.',
                    );
        $r['term'] = array(
                        'type'=>'number',
                        'decimal'=>'.',
                    ); // should be integer
        return $r; 
    }

    public function get_parameters(){ 
        $r = array();
        $r['coupon'] = array(
            'name'=>'coupon',
            'label'=>'Annual coupon payable',
            );
        $r['term'] = array(
            'name'=>'term',
            'label'=>'Term in years',
            );
        return $r; 
    }

	public function get_index(){
		return $this->get_term();
	}

    public function get_values(){ 
        $r = array();
        $r['coupon'] = $this->get_coupon();
        $r['term'] = $this->get_term();
        return $r; 
    } 

    public function __construct( $coupon = 0, $term = 0 ) {
        $this->set_coupon( $coupon );
        $this->set_term( $term );
    }

    public function get_coupon(){
        return $this->coupon ;
	}

    public function set_coupon($n){
        $candidate = array('coupon'=>$n);
        $valid = $this->get_validation($candidate);
        if ($valid['coupon']){
            $this->coupon = $n;
        }
	}

    public function set_term($n){
        $candidate = array('term'=>$n);
        $valid = $this->get_validation($candidate);
        if ($valid['term']){
            $this->term = $n;
        }
    }

    public function get_term(){
        return $this->term;
    }

    public function get_label(){
        return "Par-yield(" . $this->get_term() . ")";
	}

            
    public function get_annuity_label(){
	require_once 'class-ct1-annuity.php';
	$a = new CT1_Annuity();
	$a->set_term( $this->get_term() );
        return $this->get_coupon() . $a->get_label() . " + v^{ " . $this->get_term() . "}";
    }

    public function get_labels(){
        $labels['CT1_Par_Yield'] = $this->get_label();
        $labels['Annuity'] = $this->get_annuity_label();
        return $labels;
    }

}

