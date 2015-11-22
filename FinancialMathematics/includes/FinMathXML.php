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
 * The FinMathXML class provides objects that simply store valid XML.
 *
 */
class FinMathXML extends FinMathObject{

	private $xml;

	public function get_valid_options(){ 
		$r = parent::get_valid_options();
		$r['xml'] = array( 'type'=>'string' );
		return $r; 
	}

	public function get_parameters(){ 
		$r = parent::get_parameters();
		$r['xml'] = array(
			'name'=>'xml',
			'label'=>self::myMessage( 'fm-label-xml'),
			);
		return $r; 
	}

	public function get_values(){ 
		$r = parent::get_values();
		$r['xml'] = $this->get_xml();
		return $r; 
	}
		
	public function __construct( $xml='' ){
	  $this->set_xml($xml);
	}

	public function get_xml(){
		return $this->xml;
	}

	public function set_xml($m){
		$m = urldecode($m);
		try{ 
			$xml=simplexml_load_string($m); 
			if ($xml){
		  	$candidate = array('xml'=>$m);
		  	$valid = $this->get_validation($candidate);
				if ($valid['xml']){
					$this->xml = $m;
				}
			} // otherwise don't change $this->xml
		} catch( Exception $e ){
			return ;
		}
	}

	public function equals($f){
		if(!($f instanceof FinMathXML)){
		        return false;
		}
		if( $f->get_xml() != $this->get_xml() ){
			return false;
		}
		return true;
	}

	public function set_from_input($_INPUT = array(), $pre = ''){
		try{
			$this->set_xml(	$_INPUT[$pre. 'xml'] );
			return true;
		} catch( Exception $e ) { 
			throw new Exception( self::myMessage( 'fm-exception-in') . " " . 
				__FILE__ . ": " . $e->getMessage() );
		}
	}
  
} // end of class



