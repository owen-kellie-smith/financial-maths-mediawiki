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

class FinMathSpotRate_Test extends PHPUnit_Framework_TestCase
{
  private $sr;

  public function setup(){
	  $_INPUT =  Array ( 0 => Array ( 'delta' => 0.01, 'effective_time' => 1 ), 
	  1 => Array ( 'delta' => 0.02, 'effective_time' => 2 ) 
		);
    $this->sr = new FinMathSpotRates();
    $this->sr->set_from_input($_INPUT, $pre = '');
  }
  public function tearDown(){
		$this->sr = null;
	}
  
  public function test_forward_rates()
  {
	  $fr=$this->sr->get_forward_rates();
	  $myfr = $fr-> get_values();
	  $f01=$myfr['0,1'];
	  $f12=$myfr['1,2'];
    $this->assertTrue( abs($f01['delta'] - 0.01) < 0.000001);
    $this->assertTrue( abs($f12['delta'] - (2*0.02-0.01)) < 0.000001);
  }  

  public function test_concepts()
  {
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 'concept'=>'concept_spot_rates' ) );
	  $this->assertTrue( isset($c['output']['unrendered']['forms']) ) ;
  }  

  public function test_add_spot_rate()
  {
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array(
			'request' => 'add_spot_rate',
			'i_effective' => .1,
    	'effective_time' => 1));
	  $this->assertTrue( isset($c['output']['unrendered']['forms']) ) ;
	  $this->assertTrue( isset($c['output']['unrendered']['table']) ) ;
	}

  public function test_explain_par()
  {
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array(
			'request' => 'explain_par',
    	'par_term' => 1,
			'FinMathSpotRates' => Array
        (
            '0' => Array
                (
                    'delta' => 0.095310179804325,
                    'effective_time' => 1
                )

        )));
	  $this->assertTrue( isset($c['output']['unrendered']['forms']) ) ;
	  $this->assertTrue( isset($c['output']['unrendered']['table']) ) ;
	  $this->assertTrue( isset($c['output']['unrendered']['formulae']) ) ;
	}

  public function test_view_spotrates()
  {
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array(
    'request' => 'view_spotrates',
    'FinMathSpotRates' => Array
        (
            '0' => Array
                (
                    'delta' => 0.18232155679395,
                    'effective_time' => 2
                ),

            '1' => Array
                (
                    'delta' => 0.18232155679395,
                    'effective_time' => 4
                )

        )));
	  $this->assertTrue( isset($c['output']['unrendered']['forms']) ) ;
	  $this->assertTrue( isset($c['output']['unrendered']['table']) ) ;
	}

  public function test_xml_GIVES_SAME_result()
  {
	  $x = new FinMathConceptAll();
	$c = $x->get_controller( array(
			'FinMathSpotRates'=>array(array('delta'=>0.095310179804325,'effective_time'=>1)),
			'request'=>'explain_forward',
			'forward_start_time'=>0,
			'forward_end_time'=>1,
		));
	  $original_formulae = $c['output']['unrendered']['formulae'];
		$c_forms = $c['output']['unrendered']['xml-form']['forms'];
		$produced_xml ='';
		foreach ($c_forms as $f){
			if ('process_xml'==$f['content']['request']){
				$produced_xml = $f['content']['values']['xml'];
			}
		}
//    $this->assertEquals( 'some-stuff-just-to-show-whats-there',$produced_xml) ;  
		$XML = $produced_xml;
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$XML ));
	  $processed_formulae = null;
		if (isset($c['output']['unrendered']['formulae'])){
		  $processed_formulae = $c['output']['unrendered']['formulae'];
		}
	  $this->assertEquals( $original_formulae, $processed_formulae) ;

  }  

  public function test_forward_rate_notation()
  {
	  $x = new FinMathConceptAll();
	$c = $x->get_controller( array(
			'FinMathSpotRates'=> array(
				array('delta'=>0.035367143837291, 'effective_time'=>1),
				array('delta'=>0.03633192924739, 'effective_time'=>2),
				array('delta'=>0.037295784743697, 'effective_time'=>3),
			),
			'request'=>'explain_forward',
			'forward_start_time'=>2,
			'forward_end_time'=>3,
		));
	  $original_formulae = $c['output']['unrendered']['formulae'];
	  $this->assertEquals( $original_formulae[0]['left'], 'f_{2, 1}') ;
  }

  public function test_forward_rate_notation01()
  {
	  $x = new FinMathConceptAll();
	$c = $x->get_controller( array(
			'FinMathSpotRates'=> array(
				array('delta'=>0.035367143837291, 'effective_time'=>1),
				array('delta'=>0.03633192924739, 'effective_time'=>2),
				array('delta'=>0.037295784743697, 'effective_time'=>3),
			),
			'request'=>'explain_forward',
			'forward_start_time'=>0,
			'forward_end_time'=>1,
		));
	  $original_formulae = $c['output']['unrendered']['formulae'];
	  $this->assertEquals( $original_formulae[0]['left'], 'f_{0, 1}') ;
  }

  public function test_forward_rate_notation12()
  {
	  $x = new FinMathConceptAll();
	$c = $x->get_controller( array(
			'FinMathSpotRates'=> array(
				array('delta'=>0.035367143837291, 'effective_time'=>1),
				array('delta'=>0.03633192924739, 'effective_time'=>2),
				array('delta'=>0.037295784743697, 'effective_time'=>3),
			),
			'request'=>'explain_forward',
			'forward_start_time'=>1,
			'forward_end_time'=>2,
		));
	  $original_formulae = $c['output']['unrendered']['formulae'];
	  $this->assertEquals( $original_formulae[0]['left'], 'f_{1, 1}') ;
  }  

  public function test_CT1_A2015_Q7iii()
  {
$xml="<fin-math><parameters><FinMathSpotRates><item0><delta>0.067658648473815</delta><effective_time>3.5</effective_time></item0><item1><delta>0.086177696241052</delta><effective_time>4.5</effective_time></item1></FinMathSpotRates><request>explain_forward</request><forward_start_time>3.5</forward_start_time><forward_end_time>4.5</forward_end_time></parameters></fin-math>";
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$xml ));
//	  $this->assertEquals( array(), $c['output']['unrendered']) ;
	  $this->assertEquals( number_format(0.163,3), number_format($c['output']['unrendered']['summary']['result'],3)) ;
}

  public function test_CT1_A2014_Q9i()
  {
$xml="<fin-math><parameters><FinMathSpotRates><item0><delta>0.035367143837291</delta><effective_time>1</effective_time></item0><item1><delta>0.03633192924739</delta><effective_time>2</effective_time></item1><item2><delta>0.037295784743697</delta><effective_time>3</effective_time></item2></FinMathSpotRates><request>explain_forward</request><forward_start_time>1</forward_start_time><forward_end_time>2</forward_end_time></parameters></fin-math>";
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$xml ));
//	  $this->assertEquals( array(), $c['output']['unrendered']) ;
	  $this->assertEquals( number_format(0.038,3), number_format($c['output']['unrendered']['summary']['result'],3)) ;
}


  public function test_CT1_A2014_Q9ii()
  {
$xml="<fin-math><parameters><FinMathSpotRates><item0><delta>0.035367143837291</delta><effective_time>1</effective_time></item0><item1><delta>0.03633192924739</delta><effective_time>2</effective_time></item1><item2><delta>0.037295784743697</delta><effective_time>3</effective_time></item2></FinMathSpotRates><request>explain_par</request><par_term>2</par_term></parameters></fin-math>";
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$xml ));
//	  $this->assertEquals( array(), $c['output']['unrendered']) ;
	  $this->assertEquals( number_format(0.036982,6), number_format($c['output']['unrendered']['summary']['result'],6)) ;
}

}
