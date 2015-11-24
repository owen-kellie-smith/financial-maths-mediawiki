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

/**
 *
 * FinMathSingleCashflow represents an immediate payment of 1 (with value 1).
 * The representation is via the convoluted means of describing the cashflow as
 * an annual one year annuity certain (payable in one instalment, in advance).
 *
 */
class FinMathSingleCashflow extends FinMathAnnuity{

	public function get_valid_options(){ 
		return array();
	}

	public function get_parameters(){ 
		return array();
	}

	public function get_values(){ 
		$r = array();
		$r['value'] = $this->get_value();
		return $r; 
	}

	public function __construct(){
		;
	}

	public function get_value(){
		return 1;
	}

	public function get_annuity_certain(){
		return 1;
	}

	public function explain_annuity_certain(){
		return array();
	}


	public function get_label(){
		return "";
	}

	public function get_labels(){
		$labels = array();
		$labels['FinMathSingleCashflow'] = $this->label_annuity();
		return $labels;
	}

}


