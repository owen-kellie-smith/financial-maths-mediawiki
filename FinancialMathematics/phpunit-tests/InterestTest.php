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

class FinMathInterest_Test extends PHPUnit_Framework_TestCase
{
  private $debug=false;
  private $icalc;
  private $i;
  private $freq;
  private $f;

	public function setup(){
		$this->icalc = new FinMathInterest(1, false, log(1.06));
    $this->f = new FinMathInterestFormat();
    $this->f->set_m(2);
    $this->f->set_advance(true);
  }

  public function tearDown(){
	}
  
  public function test_set_get()  {
    $this->assertEquals( $this->f->get_m(), 2);
    $this->assertEquals( $this->f->get_advance(), true);
  }  

  public function test_set_invalid()  {
    $this->f->set_m(-1);
    $this->assertEquals( $this->f->get_m(), 2);
    $this->f->set_m('cats');
    $this->assertEquals( $this->f->get_m(), 2);
    $this->f->set_m(4);
    $this->assertEquals( $this->f->get_m(), 4);
    $this->f->set_advance('banana');
    $this->assertEquals( $this->f->get_advance(), true);
    $this->f->set_advance(99);
    $this->assertEquals( $this->f->get_advance(), true);
    $this->f->set_advance(false);
    $this->assertEquals( $this->f->get_advance(), false);
  }  

  public function test_equals()  {
    $t = new FinMathInterestFormat(2, true);
    $this->assertTrue( $this->f->equals($t) );
    $t->set_m(4);
    $this->assertFalse( $this->f->equals($t) );
  }  

  public function test_iconverti()  {
    $f = new FinMathInterestFormat();
    $f->set_m(12);
    $f->set_advance(false);
    $this->assertTrue( abs($this->icalc->get_rate_in_form($f)-  0.058411) < 0.000001);
    // source of numbers: Formulae and tables 6% p.58  i(12)
  }  

  public function test_iconvertd()  {
    $t = new FinMathInterestFormat();
    $t->set_m(12);
    $t->set_advance(true);
    if ($this->debug){ 
			$this->assertEquals( $this->icalc->get_rate_in_form($t), 0.06 / 1.032211);
		}
    $this->assertTrue( 
			abs($this->icalc->get_rate_in_form($t)-  0.06 / 1.032211) < 0.000001
		);
    // source of numbers: Formulae and tables 6% p.58   i/d(12)
  }  

  public function test_iconvertdel()  {
    $t = new FinMathInterestFormat();
    $t->set_m(367); // anything greater than 366 is treated as continuous
    $t->set_advance(true);
    $this->assertTrue( 	
			abs($this->icalc->get_rate_in_form($t)-  0.058269) < 0.000001
		);
    // source of numbers: Formulae and tables 6% p.58   delta
  }  

  public function test_i_effective()  {
    $this->icalc->set_i_effective(0.10);
    $this->assertEquals( $this->icalc->get_delta(), log(1.10));
  }

  public function test_concepts()  {
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 'concept'=>'concept_interest' ) );
	  $this->assertTrue( isset($c['output']['unrendered']['forms']) ) ;
  }  

  public function test_input_returns_expected_get_interest()  {
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 
			'request'=>'get_interest','m'=>1,'advance'=>1,'i_effective'=>0.1) 
		);
	  $this->assertTrue( isset($c['output']['unrendered']['formulae']) ) ;
	  $this->assertTrue( isset($c['output']['unrendered']['xml-form']) ) ;
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 
			'request'=>'get_interest','m'=>1,'advance'=>1,'i_effective'=>0.1) 
		);
	  $this->assertTrue( isset($c['output']['unrendered']['formulae']) ) ;
		$x = new FinMathConceptInterest();
		$c = $x->get_controller( array( 
			'request'=>'get_interest','m'=>6,'advance'=>1, 'i_effective'=>0.1) 
		);
    $temp =  $x->get_obj()->get_values();
    $this->assertEquals( $temp['delta'], log(1.1));
  }  

  public function test_input_not_annual_effective_i12_delta()  {
		$x = new FinMathConceptInterest();
		$c = $x->get_controller( array( 
		'request'=>'get_interest',
		'source_m'=>12, 'source_advance'=>1, 'source_rate'=>0.1) 
		);
	  $this->assertTrue( isset($c['output']['unrendered']['formulae']) ) ;
	  $this->assertFalse( isset($c['warning']) ) ;
		$delta=12*log(1/(1-0.1/12));
    $temp =  $x->get_obj()->get_values();
    $this->assertEquals( $delta, $temp['delta']);
  }  

  public function test_input_not_annual_effective_delta(){
		$x = new FinMathConceptInterest();
		$c = $x->get_controller( array( 
			'request'=>'get_interest',
			'm'=>1,'advance'=>false,'source_m'=>2, 'source_rate'=>0.1) 
		);
		$delta=2*log(1.05);
    $o = $x->get_obj();
    $temp =  $o->get_values();
    $this->assertEquals( $delta, $temp['delta']);
  }  

  public function test_input_not_annual_effective_i2_i()  {
		$x = new FinMathConceptInterest();
		$c = $x->get_controller( array( 
			'request'=>'get_interest',
			'm'=>1,'advance'=>false,'source_m'=>2, 'source_rate'=>0.1) 
		);
		$delta=2*log(1.05);
    $o = $x->get_obj();
		$r = $o->get_rate_in_form($o);
    $this->assertEquals(  exp($delta)-1, $r);
  }  

  public function test_input_not_annual_effective_d12_i6() {
		$x = new FinMathConceptInterest();
		$c = $x->get_controller( array( 
			'request'=>'get_interest',
			'm'=>6,'advance'=>false,
			'source_m'=>12, 'source_advance'=>1, 'source_rate'=>0.1) 
		);
		$delta=12*-log(1-0.1/12);
    $o = $x->get_obj();
		$r = $o->get_rate_in_form($o);
    $this->assertEquals( 6*( exp($delta/6)-1), $r);
  }  

  public function test_input_not_annual_effective_d12_d12() {
		$x = new FinMathConceptInterest();
		$c = $x->get_controller( array( 
			'request'=>'get_interest',
			'm'=>12,'advance'=>1,
			'source_m'=>12, 'source_advance'=>1, 'source_rate'=>0.1) 
		);
    $o = $x->get_obj();
		$r = $o->get_rate_in_form($o);
    $this->assertEquals( 0.1, $r);
  }  

  public function test_input_not_annual_effective_i12_i12()  {
		$x = new FinMathConceptInterest();
		$c = $x->get_controller( array( 
			'request'=>'get_interest','m'=>12,'source_m'=>12, 'source_rate'=>0.1) 
		);
    $o = $x->get_obj();
		$r = $o->get_rate_in_form($o);
    $this->assertEquals( 0.1, $r);
  }  

  public function test_input_not_annual_effective_i1_i12()  {
		$x = new FinMathConceptInterest();
		$c = $x->get_controller( array( 
			'request'=>'get_interest','m'=>12,'source_m'=>1, 'source_rate'=>0.1) 
		);
    $o = $x->get_obj();
		$r = $o->get_rate_in_form($o);
    $this->assertEquals( 12*(exp(log(1.1)/12)-1), $r);
  }  

  public function test_input_not_annual_effective_i2_i1()  {
		$x = new FinMathConceptInterest();
		$c = $x->get_controller( array( 
			'request'=>'get_interest','m'=>1,'source_m'=>2, 'source_rate'=>0.12) 
		);
    $o = $x->get_obj();
		$r = $o->get_rate_in_form($o);
    $this->assertEquals( (1.06*1.06-1), $r);
  }  

  public function test_input_not_annual_effective_d2_i4()  {
		$x = new FinMathConceptInterest();
		$c = $x->get_controller( array( 
			'request'=>'get_interest',
			'm'=>4,
			'source_m'=>2,'source_advance'=>true, 'source_rate'=>0.12) 
		);
    $o = $x->get_obj();
		$r = $o->get_rate_in_form($o);
    $this->assertEquals( (1/(sqrt(0.94))-1)*4, $r);
  }  

  public function test_full_input_form()  {
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 
			'request'=>'get_interest','m'=>1,'advance'=>1,'i_effective'=>0.1) 
		);
	  $this->assertTrue( isset($c['output']['unrendered']['xml-form']) ) ;
  }  

  public function test_full_input_form_xml()  {
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 
			'request'=>'get_interest','m'=>1,'advance'=>1,'i_effective'=>0.1) 
		);
	  $this->assertTrue( isset($c['output']['unrendered']['xml-form']) ) ;
  }  

  public function test_full_input_XML()  {
	  $x = new FinMathFormXML();
		$x->set_text( array( 
			'request'=>'get_interest','m'=>1,'advance'=>1,'i_effective'=>0.1) 
		);
		$c = $x->get_calculator( array());
		$expected="\n<dummy_tag_set_in_FinMathFormXML><parameters><request>get_interest</request><m>1</m><advance>1</advance><i_effective>0.1</i_effective></parameters></dummy_tag_set_in_FinMathFormXML>\n";
		$this->assertEquals( $expected, $c['values']['xml'] ) ;
  }  

  public function test_XS_input_XML()  {
	  $x = new FinMathFormXML();
		$x->set_text( array( 
			'title'=>'a page title', 
			'request'=>'get_interest','m'=>1,'advance'=>1,'i_effective'=>0.1) 
		);
		$c = $x->get_calculator( array());
		$expected="\n<dummy_tag_set_in_FinMathFormXML><parameters><request>get_interest</request><m>1</m><advance>1</advance><i_effective>0.1</i_effective></parameters></dummy_tag_set_in_FinMathFormXML>\n";
		$this->assertEquals( $expected, $c['values']['xml'] ) ;
  }  

  public function test_full_xmlinput_formulae()  {
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 
			'request'=>'process_xml', 
			'xml'=>'<fin-math><parameters><request>get_interest<%2Frequest><m>1<%2Fm><i_effective>0<%2Fi_effective><%2Fparameters><%2Ffin-math>%0D%0A' )
		);
	  $this->assertTrue( isset($c['output']['unrendered']['formulae']) ) ;
  }  

  public function test_xml_GIVES_SAME_XML()  {
		$XML = '<fin-math><parameters><request>get_interest</request><m>12</m><advance>1</advance><i_effective>0.1</i_effective></parameters></fin-math>';
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$XML ));
		$c_forms = $c['output']['unrendered']['forms'];
		$candidate_xml ='';
		foreach ($c_forms as $f){
			if ('process_xml'==$f['content']['request']){
				$candidate_xml = $f['content']['values']['xml'];
			}
		}
	  $this->assertEquals( urldecode($XML), $candidate_xml) ;
  }  

  public function test_CT1_A2015_Q5()  {
		$xml="<fin-math><parameters><request>get_interest</request><m>1</m><source_m>2</source_m><source_rate>0.06</source_rate><i_effective/></parameters></fin-math>";
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$xml ));
	  $this->assertEquals( 
			number_format(0.0609,4), 
			number_format($c['output']['unrendered']['summary']['result'],4)
		) ;
	}

  public function test_CT1_S2013_Q8()  {
		$xml="<fin-math><parameters><request>get_interest</request><m>1</m><source_m>2</source_m><source_rate>0.04</source_rate><i_effective></i_effective></parameters></fin-math>";
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$xml ));
	  $this->assertEquals( 
			number_format(0.0404,4), 
			number_format($c['output']['unrendered']['summary']['result'],4)
		) ;
	}

  public function test_CT1_A2014_Q3()  {
		$xml="<fin-math><parameters><request>get_interest</request><m>2</m><source_m/><source_rate/><i_effective>0.08566958161</i_effective></parameters></fin-math>";
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$xml ));
	  $this->assertEquals( 
			number_format(0.0839,4), 
			number_format($c['output']['unrendered']['summary']['result'],4)
		) ;
	}

  public function test_CT1_A2014_Q3ii()  {
		$xml="<fin-math><parameters><request>get_interest</request><m>4</m><advance>1</advance><source_m/><source_rate/><i_effective>0.08566958161</i_effective></parameters></fin-math>";
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$xml ));
	  $this->assertEquals( 
			number_format(0.0814,4), 
			number_format($c['output']['unrendered']['summary']['result'],4)
		) ;
	}

  public function test_CT1_S2013_Q1()  {
	  $x = new FinMathConceptAll();
		$xml="<fin-math><parameters><request>get_interest</request><m>1</m><advance>1</advance><source_m/><source_rate/><i_effective>0.045</i_effective></parameters></fin-math>";
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$xml ));
	  $this->assertEquals( 
			number_format(0.043062,6), 
			number_format($c['output']['unrendered']['summary']['result'],6)
		) ;

		$xml="<fin-math><parameters><request>get_interest</request><m>12</m><advance>1</advance><source_m/><source_rate/><i_effective>0.045</i_effective></parameters></fin-math>";
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$xml ));
	  $this->assertEquals( 
			number_format(0.043936,6), 
			number_format($c['output']['unrendered']['summary']['result'],6)
		) ;

		$xml="<fin-math><parameters><request>get_interest</request><m>4</m><advance>0</advance><source_m/><source_rate/><i_effective>0.045</i_effective></parameters></fin-math>";
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$xml ));
	  $this->assertEquals( 
			number_format(0.044260,6), 
			number_format($c['output']['unrendered']['summary']['result'],6)
		) ;

		$xml="<fin-math><parameters><request>get_interest</request><m>0.2</m><advance>0</advance><source_m/><source_rate/><i_effective>0.045</i_effective></parameters></fin-math>";
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$xml ));
	  $this->assertEquals( 
			number_format(0.24618,5), 
			number_format(5*$c['output']['unrendered']['summary']['result'],5)
		) ;
	}

}
