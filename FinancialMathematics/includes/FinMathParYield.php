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
 *
 * @file
 */


/**
 * Par yield object.
 *
 * Defining parameters are term and coupon.  The object represents a loan with annual
 * interest payments in arrears of the coupon amount, with a capital redemption of 1 at the term.
 * I.e. the cashflows are coupon @ t=1, 2, ..., term-1, and (1 + coupon) @ term. 
 * 
 */
class FinMathParYield extends FinMathObject {

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
            'label'=>self::myMessage( 'fm-label-coupon'),
            );
        $r['term'] = array(
            'name'=>'term',
            'label'=>self::myMessage( 'fm-label_term'), 
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
        return self::myMessage( 'fm-par-yield')  . "(" . $this->get_term() . ")";
	}

            
    public function get_annuity_label(){
	$a = new FinMathAnnuity();
	$a->set_term( $this->get_term() );
        return $this->get_coupon() . $a->get_label() . " + v^{ " . $this->get_term() . "}";
    }

    public function get_labels(){
        $labels['FinMathParYield'] = $this->get_label();
        $labels['Annuity'] = $this->get_annuity_label();
        return $labels;
    }

}

