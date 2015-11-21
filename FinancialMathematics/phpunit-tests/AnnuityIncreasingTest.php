<?php

class FinMathAnnuityIncreasing_Test extends PHPUnit_Framework_TestCase
{
  private $debug = false;
  private $acalc;
  private $term = 11;
  private $i = 0.04;
  private $freq = 1;
  private $adv = true;
  private $inc = true;
  private $neg = 0.00001;
  
  public function setup(){
    $this->acalc = new FinMathAnnuityIncreasing();
    $this->reset();
  }
  public function tearDown(){}
  
  private function reset(){
  	$this->acalc->set_m($this->freq);
  	$this->acalc->set_advance($this->adv);
  	$this->acalc->set_delta(log(1+$this->i));
  	$this->acalc->set_term($this->term);
  	$this->acalc->set_increasing($this->inc);
  }
  	
  private function aval(){
    return $this->acalc->get_annuity_certain();
  }

  public function test_simple_annuity()
  {
	$val = 49.137638 * 1.04;
    if ($this->debug) $this->assertEquals( $this->aval() , $val);
    $this->assertTrue( abs($this->aval() - $val ) < $this->neg );
    // source of numbers -- yellow book
  }  

  public function test_decreasing_annuity()
  {
  	$this->acalc->set_increasing(false);
  	$this->acalc->set_advance(false);
  	$this->acalc->set_m(1);
  	$this->acalc->set_delta(log(1.06));
  	$this->acalc->set_term(10);
	$val = 43.9985;
    if ($this->debug) $this->assertEquals( $this->aval() , $val);
    $this->assertTrue( abs($this->aval() - $val ) < 0.0001 );
    // source of numbers -- yellow book
  }  

  public function test_simple_arrears()
  {
  	$this->acalc->set_advance(false);
	$val = 49.137638 ;
    if ($this->debug) $this->assertEquals( $this->aval() , $val);
    $this->assertTrue( abs($this->aval() - $val ) < $this->neg );
    // source of numbers -- yellow book
  }
 
  public function test_one_more()
  {
  	$this->acalc->set_advance(false);
  	$this->acalc->set_term(12);
	$val = 49.137638 + pow(1.04,-12)*12;
    if ($this->debug) $this->assertEquals( $this->aval() , $val);
    $this->assertTrue( abs($this->aval() - $val ) < $this->neg );
    // source of numbers -- yellow book
  }  

  public function test_concepts()
  {
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 'concept'=>'concept_annuity_increasing' ) );
	  $this->assertTrue( isset($c['output']['unrendered']['forms']) ) ;
  }  

  public function test_xml_GIVES_SAME_XML()
  {
		$XML = '<fin-math><parameters><request>get_annuity_increasing</request><m>12</m><advance>1</advance><i_effective>0.1</i_effective><term>25</term><value/><increasing>1</increasing></parameters></fin-math>';
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


  public function test_CT1_A2015_Q11()
  {
$xml="<fin-math><parameters><request>get_annuity_increasing</request><m>1</m><i_effective>0.0125</i_effective><term>60</term><value/><increasing>1</increasing></parameters></fin-math>";
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$xml ));
//	  $this->assertEquals( array(), $c['output']['unrendered']) ;
	  $this->assertEquals( number_format(1127,0), number_format($c['output']['unrendered']['summary']['result'],0)) ;
}


  public function test_CT1_S2014_Q5()
  {
$xml="<fin-math><parameters><request>value_cashflows</request><CT1_Cashflows><item0><m>1</m><advance>1</advance><source_rate/><source_format/><delta>0</delta><i_effective>0</i_effective><term>10</term><value>55</value><increasing/><rate_per_year>1</rate_per_year><effective_time>0</effective_time><cashflow_value>55</cashflow_value></item0><item1><m>1</m><advance>1</advance><source_rate/><source_format/><delta>0</delta><i_effective>0</i_effective><term>10</term><value>10</value><rate_per_year>2</rate_per_year><effective_time>0</effective_time><cashflow_value>20</cashflow_value></item1></CT1_Cashflows><i_effective>0.05</i_effective><value/></parameters></fin-math>";
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$xml ));
//	  $this->assertEquals( array(), $c['output']['unrendered']) ;
	  $this->assertEquals( number_format(64.0592,4), number_format($c['output']['unrendered']['summary']['result'],4)) ;
}


  public function test_CT1_S2014_Q4()
  {
$xml="<fin-math><parameters><request>value_cashflows</request><CT1_Cashflows><item0><m>1</m><advance>1</advance><source_rate/><source_format/><delta>0</delta><i_effective>0</i_effective><term>1</term><value>1</value><rate_per_year>2.0</rate_per_year><effective_time>0</effective_time><cashflow_value>2</cashflow_value></item0><item1><m>1</m><advance>1</advance><source_rate/><source_format/><delta>0</delta><i_effective>0</i_effective><term>1</term><value>1</value><rate_per_year>2.5</rate_per_year><effective_time>0.3333333</effective_time><cashflow_value>2.5</cashflow_value></item1><item2><m>1</m><advance>1</advance><source_rate/><source_format/><delta>0</delta><i_effective>0</i_effective><term>1</term><value>1</value><rate_per_year>-4.2</rate_per_year><effective_time>1</effective_time><cashflow_value>-4.2</cashflow_value></item2></CT1_Cashflows><i_effective/><value>0</value></parameters></fin-math>";
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$xml ));
//	  $this->assertEquals( array(), $c['output']['unrendered']) ;
	  $this->assertEquals( number_format(-0.081,3), number_format($c['output']['unrendered']['summary']['result'],3)) ;
}

  public function test_CT1_S2014_Q10()
  {
$xml="<fin-math><parameters><request>value_cashflows</request><CT1_Cashflows><item0><m>12</m><advance/><source_rate/><source_format/><delta>0</delta><i_effective>0</i_effective><term>1</term><value>1</value><rate_per_year>15000</rate_per_year><effective_time>0</effective_time><cashflow_value>15000</cashflow_value></item0><item1><m>12</m><advance/><source_rate/><source_format/><delta>0</delta><i_effective>0</i_effective><term>1</term><value>1</value><rate_per_year>18000</rate_per_year><effective_time>1</effective_time><cashflow_value>18000</cashflow_value></item1><item2><m>12</m><advance/><source_rate/><source_format/><delta>0</delta><i_effective>0</i_effective><term>38</term><value>45.952723614177</value><escalation_delta>0.0099503308531681</escalation_delta><escalation_rate_effective>0.01</escalation_rate_effective><escalation_frequency>1</escalation_frequency><rate_per_year>20000</rate_per_year><effective_time>2</effective_time><cashflow_value>919054.47228354</cashflow_value></item2></CT1_Cashflows><i_effective>0.07</i_effective><value/></parameters></fin-math>";
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$xml ));
//	  $this->assertEquals( array(), $c['output']['unrendered']) ;
	  $this->assertEquals( number_format(297537.30,2), number_format($c['output']['unrendered']['summary']['result'],2)) ;
}


  public function test_CT1_A2014_Q1()
  {
$xml="<fin-math><parameters><request>value_cashflows</request><CT1_Cashflows><item0><m>1</m><advance>1</advance><source_rate/><source_format/><delta>0</delta><i_effective>0</i_effective><term>1</term><value>1</value><rate_per_year>870000</rate_per_year><effective_time>0</effective_time><cashflow_value>870000</cashflow_value></item0><item1><m>1</m><advance>1</advance><source_rate/><source_format/><delta>0</delta><i_effective>0</i_effective><term>1</term><value>1</value><rate_per_year>26000</rate_per_year><effective_time>0.5</effective_time><cashflow_value>26000</cashflow_value></item1><item2><m>1</m><advance>1</advance><source_rate/><source_format/><delta>0</delta><i_effective>0</i_effective><term>1</term><value>1</value><rate_per_year>27000</rate_per_year><effective_time>1.5</effective_time><cashflow_value>27000</cashflow_value></item2><item3><m>1</m><advance>1</advance><source_rate/><source_format/><delta>0</delta><i_effective>0</i_effective><term>1</term><value>1</value><rate_per_year>33000</rate_per_year><effective_time>2.5</effective_time><cashflow_value>33000</cashflow_value></item3><item4><m>1</m><advance>1</advance><source_rate/><source_format/><delta>0</delta><i_effective>0</i_effective><term>1</term><value>1</value><rate_per_year>-990000</rate_per_year><effective_time>3</effective_time><cashflow_value>-990000</cashflow_value></item4></CT1_Cashflows><i_effective/><value>0</value></parameters></fin-math>";
	  $x = new FinMathConceptAll();
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>$xml ));
//	  $this->assertEquals( array(), $c['output']['unrendered']) ;
	  $this->assertEquals( number_format(0.012,3), number_format($c['output']['unrendered']['summary']['result'],3)) ;
}



}
