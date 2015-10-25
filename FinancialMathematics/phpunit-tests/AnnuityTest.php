<?php

class CT1_Annuity_Test extends PHPUnit_Framework_TestCase
{
  private $debug = false;
  private $acalc;
  private $term = 10;
  private $i;
  private $freq = 12;
  private $adv = true;
  private $neg = 0.00001;
  private $acalce;
  private $term_e = 19;
  private $i_eff = 0.19;
  private $e_eff = 0.19;
  private $freq_escalating = 12;

  
  public function setup(){
    $this->acalc = new CT1_Annuity(10, true, log(1.06), 12);
    $this->i = 0.06;
    $this->acalce = new CT1_Annuity_Escalating();
    $this->reseta();
    $this->resete();
  }
  public function tearDown(){}

  private function reseta(){
  	$this->acalc->set_m($this->freq);
  	$this->acalc->set_advance($this->adv);
  	$this->acalc->set_delta(log(1+$this->i));
  	$this->acalc->set_term($this->term);
  }
  
  private function resete(){
  	$this->acalce->set_m($this->freq);
  	$this->acalce->set_advance($this->adv);
  	$this->acalce->set_delta(log(1+$this->i_eff));
  	$this->acalce->set_term($this->term_e);
  	$this->acalce->set_escalation_rate_effective($this->e_eff);
  	$this->acalce->set_escalation_frequency($this->freq_escalating);
  }
  	
  private function aval(){
  	$this->reseta();
    return $this->acalc->get_annuity_certain();
  }

  private function avale(){
    return $this->acalce->get_annuity_certain();
  }

  private function ival(){
  	$this->reseta();
    return $this->acalc->get_rate_in_form($this->acalc);
  }

  private function an(){
    return (1 - exp(-10*log(1.06)))/0.06;
  }

  public function test_an()
  {
    if ($this->debug) $this->assertEquals( $this->an() , 7.3601);
    $this->assertTrue( abs($this->an() - 7.3601) < 0.0001 );
    // source of numbers: Formulae and tables 6% p.58 an 
  }  

  public function test_rate()
  {
    if ($this->debug) $this->assertEquals( $this->acalc->get_rate_in_form($this->acalc) , 0.06/1.032211);
    $this->assertTrue( abs($this->aval() - $this->an()*1.032211) < $this->neg );
    // source of numbers: Formulae and tables 6% p.58  i/d(12)
  }  
 
  public function test_annuityValueAdvance()
  {
    if ($this->debug) $this->assertEquals( $this->aval() , $this->an()*1.032211);
    $this->assertTrue( abs($this->aval() - $this->an()*1.032211) < $this->neg );
    // source of numbers: Formulae and tables 6% p.58  i/d(12)
  }  

  public function test_annuityValueContinuous()
  {
    $this->freq = 999;
//    $this->assertEquals( $this->aval() , $this->an()*1.029709);
    if ($this->debug) $this->assertEquals( $this->aval() , $this->an()*1.029709);
    $this->assertTrue( abs($this->aval() - $this->an()*1.029709) < $this->neg );
    // source of numbers: Formulae and tables 6% p.58   i/delta
  }  
  
  public function test_annuityValueArrears()
  {
    $this->adv = false;
    if ($this->debug) $this->assertEquals( $this->aval() , $this->an()*1.027211);
    $this->assertTrue( abs($this->aval() - $this->an()*1.027211) < $this->neg );
    // source of numbers: Formulae and tables 6% p.58  i/i(12)
  }  

  public function test_annuityValueNilInt()
  {
    $this->i = 0;
    $this->assertEquals( $this->aval(), 10) ;
  }  

  public function test_annuityValueNilTerm()
  {
    $this->term = 0;
    $this->assertEquals( $this->aval(), 0) ;
  }  
  
  public function test_im()
  {
    $this->adv = false;
    if ($this->debug) $this->assertEquals( $this->ival() , 0.058411);
    $this->assertTrue( abs($this->ival() - 0.058411) < 0.000001 );
    // source of numbers: Formulae and tables 6% p.58  i(12)
  }  

  public function test_delta()
  {
    $this->freq = '999';
    if ($this->debug) $this->assertEquals( $this->ival() , 0.058269);
    $this->assertTrue( abs($this->ival() - 0.058269) < 0.000001 );
    // source of numbers: Formulae and tables 6% p.58   delta
  }  

  public function test_setValue()
  {
    $this->acalc->set_value( 8.1109 );
  	$this->acalc->set_m(1);
  	$this->acalc->set_advance( false );
  	$this->acalc->set_term( 10 );
    if ( $this->debug ) $this->assertEquals( $this->acalc->get_delta_for_value(), log(1.04) ) ;
    $this->assertTrue( abs($this->acalc->get_delta_for_value() - log(1.04)) < 0.00001 );
    // source of numbers: Formulae and tables 4% p.56  
  }  

  public function test_setValueComplex()
  {
    $this->acalc->set_value( (8.1109 * 1.021537) );
  	$this->acalc->set_m(12);
  	$this->acalc->set_advance( true );
  	$this->acalc->set_term( 10 );
    if ( $this->debug ) $this->assertEquals( $this->acalc->get_delta_for_value(), log(1.04) ) ;
    $this->assertTrue( abs($this->acalc->get_delta_for_value() - log(1.04)) < 0.00001 );
    // source of numbers: Formulae and tables 4% p.56 a10 and i/d(12)  
  }  

  public function test_concepts()
  {
	  $x = new CT1_Concept_All();
		$c = $x->get_controller( array( 'concept'=>'concept_annuity' ) );
	  $this->assertTrue( isset($c['output']['unrendered']['forms']) ) ;
  }  

  public function test_simple_annuity()
  {
//	print_r( $this->acalce->get_values() );
    $this->assertTrue( $this->acalce->get_advance() );
    if ($this->debug) $this->assertEquals( $this->avale() , 19);
    $this->assertTrue( abs($this->avale() - 19 ) < $this->neg );
    // source of numbers -- common sense. net interest rate 0
  }  
 
  public function test_simple_annuity_arrears()
  {
  	$this->acalce->set_advance( false );
//	print_r( $this->acalce->get_values() );
    $this->assertTrue( !$this->acalce->get_advance() );
    if ($this->debug) $this->assertEquals( $this->avale() , 19 * exp(-log(1.19)/12));
    $this->assertTrue( abs($this->avale() - 19*exp(-log(1.19)/12) ) < $this->neg );
    // source of numbers -- common sense. net interest rate 0.  1st payment 1 not with implied inc.
  }  

  public function test_complex_annuity_arrears()
  {
  	$this->acalce->set_advance( false );
  	$this->acalce->set_m(12);
  	$this->acalce->set_delta(log(1.1));
  	$this->acalce->set_term(20);
  	$this->acalce->set_escalation_rate_effective(0.05);
  	$this->acalce->set_escalation_frequency(4);
//	print_r( $this->acalce->get_values() );
    $this->assertTrue( !$this->acalce->get_advance() );
    if ($this->debug) $this->assertEquals( $this->avale() , 12.887905873);
    $this->assertTrue( abs($this->avale() - 12.887905873 ) < $this->neg );
	// source numbers: spreadsheet (cashflow discounted)
	}
  
public function test_adjusted_int_arrears()
  {
  	$this->acalce->set_advance( false );
  	$this->acalce->set_m(12);
  	$this->acalce->set_delta(log(1.1));
  	$this->acalce->set_term(20);
  	$this->acalce->set_escalation_rate_effective(0.05);
  	$this->acalce->set_escalation_frequency(999);
//	print_r( $this->acalce->get_values() );
    $this->assertTrue( !$this->acalce->get_advance() );
    if ($this->debug) $this->assertEquals( $this->avale() , 12.940205516);
    $this->assertTrue( abs($this->avale() - 12.940205516 ) < $this->neg );
	// source numbers: spreadsheet (cashflow discounted)
	}

public function test_adjusted_int_advance()
  {
  	$this->acalce->set_advance( true );
  	$this->acalce->set_m(12);
  	$this->acalce->set_delta(log(1.1));
  	$this->acalce->set_term(20);
  	$this->acalce->set_escalation_rate_effective(0.05);
  	$this->acalce->set_escalation_frequency(999);
//	print_r( $this->acalce->get_values() );
    $this->assertTrue( $this->acalce->get_advance() );
    if ($this->debug) $this->assertEquals( $this->avale() , 13.04339);
    $this->assertTrue( abs($this->avale() - 13.04339 ) < $this->neg );
	// source numbers: spreadsheet (cashflow discounted)
	}

  public function test_xml_GIVES_SAME_XML()
  {
		$XML = '<fin-math><parameters><request>get_annuity_escalating</request><m>12</m><advance>1</advance><i_effective>0.1</i_effective><term>25</term><value/><escalation_rate_effective>0.03</escalation_rate_effective><escalation_frequency>6</escalation_frequency></parameters></fin-math>';
	  $x = new CT1_Concept_All();
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

  public function test_xml_GIVES_SAME_result()
  {
	  $x = new CT1_Concept_All();
	$c = $x->get_controller( array(
				'request'=>'get_annuity_escalating',
				'm'=>12,
				'i_effective'=>0.1,
				'term'=>25,
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
	  $x = new CT1_Concept_All();
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$XML ));
	  $processed_formulae = $c['output']['unrendered']['formulae'];
	  $this->assertEquals( $original_formulae, $processed_formulae) ;

  }  


}
