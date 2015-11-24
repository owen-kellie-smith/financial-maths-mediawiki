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
 * @file
 */

class FinMathConceptInterest extends FinMathForm{

public function __construct(FinMathObject $obj=null){
	if (null === $obj){
		$obj = new FinMathInterest();
	}
	parent::__construct($obj);
	$this->set_request( 'get_interest' );
}

public function get_concept_label(){
	return array(	
				'concept_interest'=>self::myMessage( 'fm-interest-rate-format'),
 );
} 

private function get_rate_in_form(){
	return  $this->obj->get_rate_in_form( $this->obj ) ;
}

protected function get_unrendered_solution(){
	return  $this->obj->explain_rate_in_form( $this->obj ) ;
}
	
protected function get_calculator($parameters){
	$p = array('exclude'=>$parameters,'request'=>$this->get_request(), 'submit'=>self::myMessage( 'fm-calculate'), 'introduction' => self::myMessage( 'fm-intro-interest'));
	return parent::get_calculator($p);
}

public function get_controller($_INPUT ){
  $return=array();
	if (isset($_INPUT['request'])){
		if ($this->get_request() == $_INPUT['request']){
			if ($this->set_interest($_INPUT)){
				$return['output']['unrendered']['formulae'] = $this->get_unrendered_solution();
				$return['output']['unrendered']['summary'] = array('sought'=>'rate', 'result'=>$this->get_rate_in_form());
				return $return;
			}
			else{
				$return['warning']=self::myMessage( 'fm-error-interest');
				return $return;
			}
		}
	}
	else{
		$return['output']['unrendered']['forms'][] = 	array(
			'content'=>$this->get_calculator(array("delta")),
			'type'=>'',
		);
		return $return;
	}
}

private function set_interest($_INPUT = array()){
	$this->set_received_input($_INPUT);
	return ($this->obj->set_from_input($_INPUT));
}

} // end of class


