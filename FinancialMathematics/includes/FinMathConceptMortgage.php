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


class FinMathConceptMortgage extends FinMathForm{

public function __construct(FinMathObject $obj=null){
	if (null === $obj){
		$obj = new FinMathMortgage();
	}
	parent::__construct($obj);
	$this->set_request( 'get_mortgage_instalment' );
}

public function get_concept_label(){
	return array(	
		'concept_mortgage'=>self::myMessage( 'fm-mortgage'),
 );
} 


private function get_unrendered_solution(){
	return $this->obj->explain_instalment();
}


private function get_unrendered_interest_rate(){
	return $this->obj->explain_interest_rate_for_instalment();
}


	private function get_unrendered_summary( $_INPUT ){
		$ret=array();
			if (empty( $_INPUT['instalment']  )  ){
				$ret['sought']='instalment';
				$ret['result']=$this->obj->get_instalment();
		} else {
				$ret['sought']='i_effective';
				$ret['result']=exp($this->obj->get_delta_for_value())-1;
		}
		return $ret;
	}

	
protected function get_calculator($parameters){
	$p = array('exclude'=>$parameters,'request'=>$this->get_request(), 'submit'=>self::myMessage( 'fm-calculate'), 'introduction' => self::myMessage( 'fm-intro-mortgage') );
	$c = parent::get_calculator($p);
	$c['values']['instalment'] = NULL;
	return $c;
}

public function get_controller($_INPUT ){
  $return=array();
	if (isset($_INPUT['request'])){
		if ($this->get_request() == $_INPUT['request']){
			if ($this->set_mortgage($_INPUT)){
				if (empty( $_INPUT['instalment'] ) ){
				  $return['output']['unrendered']['formulae'] = $this->get_unrendered_solution();
				  $return['output']['unrendered']['summary'] = $this->get_unrendered_summary($_INPUT);
                                  $return['output']['unrendered']['table'] = $this->obj->get_mortgage_table();

					return $return;
				} else {
				  $return['output']['unrendered']['formulae'] = $this->get_unrendered_interest_rate();
				  $return['output']['unrendered']['summary'] = $this->get_unrendered_summary($_INPUT);
                                  $return['output']['unrendered']['table'] = $this->obj->get_mortgage_table();
					return $return;
				}
			} else{
				$return['warning']=self::myMessage( 'fm-exception-setting-mortgage');
				return $return;
			}
		}
	}
	else{
		$return['output']['unrendered']['forms'][] = 	array(
			'content'=>$this->get_calculator(array("delta",  "source_m","source_advance","source_rate","value")),
			'type'=>'',
		);

		return $return;
	}
}

private function set_mortgage($_INPUT = array()){
	$this->set_received_input($_INPUT);
	return ($this->obj->set_from_input($_INPUT));
}

} // end of class

