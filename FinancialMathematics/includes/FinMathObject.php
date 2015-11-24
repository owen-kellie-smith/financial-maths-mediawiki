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
 * @author     Owen Kellie-Smith
 */

/**
 * Abstract class for financial objects.
 * The inheriting objects are mostly types of cashflow.
 * Each object has constraints on what properties are valid, and has labels for its properties.
 * The constrannts and labels enable the object to be used in a standard form.
 * The values of the object's properties enables the object to be used (e.g. in equations of value).
 * 
 * @class FinMathObject 
 *
 */
abstract class FinMathObject {

    /**
     * List validation constraints suitable for use with PEAR::Validate
     * Also effectively used to define the elements of any input forms
     *
     * @return array
     *
     */
	public function get_valid_options(){ 
		$r = array( 'request'=>'' );
		return $r; 
	}

    /**
     * List defining parameter keys, descriptions, labels of object
     *
     * @return array
     *
     */
	public function get_parameters(){ 
		$r = array();
		return $r; 
	}

    /**
     * Filter input array to valid elements for object
     *
     * @param array $_INPUT
		 * @return array
     *
     */
	public function get_valid_inputs($_INPUT){
		$r = $_INPUT;
		foreach (array_keys($r) as $key){
			if (!in_array( $key, array_keys($this->get_valid_options()) ) ){
				unset( $r[$key] );
			}
		}
		return $r;
  }


    /**
     * Get validation result (list of parameter keys with boolean values)
     *
     * @param array $candidate 
     * @return array
     *
     */
	public function get_validation($candidate){
		foreach (array_keys($candidate) as $key){
			if (array()==$candidate[$key]){
				$candidate[$key]=null;
			}
		}
		$v = new Validate();
		$options =  $this->get_valid_options();
		$ret = $v->multiple($candidate, $options);
		return $ret;
	}

    /**
     * List values of defining parameter keys
     *
     * @return array
     *
     */
	public function get_values(){ 
		return array(); 
	}
		
    /**
     * List displayable labels of object
     *
     * @return array
     *
     */
	public function get_labels(){ 
		return array(); 
	}

    /**
     * List displayable labels of object
     * @param string $messageKey
     * @return string
     *
     */
	protected static function myMessage( $messageKey){
			$m = $messageKey;
			if ( function_exists('wfMessage') ){
				$m = htmlentities(wfMessage( $messageKey)->plain());
			}
			return $m;
	}

    /**
     * Return whether a candidate equals the object
     * @param FinMathObjec $f candidate object
     * @return bool
     *
     */
	public function equals($f){
		return ( $f->get_values()  == $this->get_values()       );
	}

					
} // end of class

