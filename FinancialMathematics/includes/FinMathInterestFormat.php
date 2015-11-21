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
 */
/**
 * FinMathInterestFormat class
 *
 * @package    CT1
 * @author     Owen Kellie-Smith
 */


class FinMathInterestFormat extends FinMathObject{

    /**
     * Frequency (instalments per year)
     *
     * @access protected
     * @var    number
     */
	protected $m = 1;

    /**
     * Timing flag (true means in advance)
     *
     * @access protected
     * @var    boolean 
     */
	protected $advance = false;

    /**
     * List defining parameter keys, descriptions, labels of object
     *
     * @return array
     *
     * @access public
     */
	public function get_valid_options(){ 
		$r = parent::get_valid_options();
		$r['m'] = array(
							'type'=>'number',
							'decimal'=>'.',
							'min'=>0.00001,
						);
		$r['advance'] = array(
							'type'=>'boolean',
						);
		return $r; 
	}

    /**
     * List defining parameter keys, descriptions, labels of object
     *
     * @return array
     *
     * @access public
     */
	public function get_parameters(){ 
		$r = parent::get_parameters();
		$r['m'] = array(
				'name'=>'m',
				'label'=>self::myMessage( 'fm-label-frequency-instalment'),
				);
		$r['advance'] = array(
				'name'=>'advance',
				'label'=>self::myMessage( 'fm-label-advanced') ,
				);
		return $r; 
	}

    /**
     * List values of defining parameter keys
     *
     * @return array
     *
     * @access public
     */
	public function get_values(){ 
		$r = parent::get_values();
		$r['m'] = $this->get_m();
		$r['advance'] = $this->get_advance();
		return $r; 
	}
		
	public function __construct( $m=1, $advance=false ){
	  $this->set_m($m);
	  $this->set_advance($advance);
	}

	public function get_m(){
		return $this->m;
	}

	public function set_m($m){
	  $candidate = array('m'=>$m);
	  $valid = $this->get_validation($candidate);
		if ($valid['m']){
			$this->m = $m;
		}
	}

	public function get_advance(){
		return $this->advance;
	}
 
	protected function my_bool($b){
		return ('on' == $b || '1' == $b || 1 == $b); 
	}

	public function set_advance($b){
		if ('on' == $b || '1' == $b || 1 == $b){
			$b = true;
		}
		if (is_bool($b)){
			$this->advance = $b;
		}
	}

	public function equals($f){
		if(!($f instanceof FinMathInterestFormat)){
		        return false;
		}
		if( $f->get_m()       != $this->get_m()       ){
			return false;
		}
		if( $f->get_advance() != $this->get_advance() ){
			return false;
		}
		return true;
	}

	public function get_description(){
		if ($this->is_continuous()){
			return self::myMessage( 'fm-interest-continuous');
		}
		if ($this->advance){
			$out =  self::myMessage( 'fm-discount-rate');
		} else {
			$out = self::myMessage( 'fm-interest-rate');
		}
		if (1!=$this->m){
			$out.=" " . self::myMessage( 'fm-convertible'). " " . $this->m . self::myMessage( 'fm-times-per-year');
		}
		return $out;
	}

	public function get_label(){
		return $this->label_interest_format();
	}

	protected function label_interest_format(){
		if ($this->is_continuous()){
			$return = "\\delta";
		} else {
			if ($this->advance){
				$out="d";
			} else {
				$out="i";
			}
			if (1!=$this->m){
				 $out.="^{(" . $this->m . ")}";
			}
			$return = $out;
		}
		return $return;
	}

	public function get_labels(){
		$labels = parent::get_labels();
		$labels['FinMathInterestFormat'] = $this->label_interest_format();
		return $labels;
	}

	protected function is_continuous(){
		$m_continuous = 366;
		return ($this->m > $m_continuous);
	}

	public function set_from_input($_INPUT = array(), $pre = ''){
		try{
			if (isset($_INPUT[$pre . 'm'])){
				$this->set_m(	$_INPUT[$pre. 'm'] );
			}
			if (isset($_INPUT[$pre . 'advance'])){
				$this->set_advance($_INPUT[$pre . 'advance']);
			}
			return true;
		}
		catch( Exception $e ){ 
			throw new Exception( self::myMessage( 'fm-exception-in') . " " . __FILE__ . ": " . $e->getMessage() );
		}
	}

  
} // end of class


