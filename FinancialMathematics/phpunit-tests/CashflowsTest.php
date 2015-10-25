<?php

class CT1_Cashflows_Test extends PHPUnit_Framework_TestCase
{
  private $debug = false;
  private $ccalc;
  private $neg = 0.00001;
  
  public function setup(){
    $this->ccalc = new CT1_Cashflows();
  }
  public function tearDown(){}
  
  public function test_setup()
  {
    $this->assertEquals( $this->ccalc->get_values(), array('cashflows_value'=>0));
  }  
 
  public function test_concepts()
  {
	  $x = new CT1_Concept_All();
		$c = $x->get_controller( array( 'concept'=>'concept_cashflows' ) );
	  $this->assertTrue( isset($c['output']['unrendered']['forms']) ) ;
  }  

  public function test_input_returns_expected_add_cashflow()
  {
	  $x = new CT1_Concept_All();
	$c = $x->get_controller( array( 'request' => 'add_cashflow','rate_per_year' => 0,'effective_time' => 0, 'm' => 1, 'term' => 1,'escalation_rate_effective' => 0,'escalation_frequency' => 1));
//	  $this->assertEquals( array('some-stuff-just-to-show-whats-there'),$c) ;  
	  $this->assertTrue( isset($c['output']['unrendered']['forms']) ) ;
	  $this->assertTrue( isset($c['output']['unrendered']['formulae']) ) ;
	  $this->assertFalse( isset($c['output']['unrendered']['xml-form']) ) ;
  }  


  public function test_input_returns_expected_VIEW_cashflowS()
  {
	  $x = new CT1_Concept_All();
	$c = $x->get_controller( array(
    'CT1_Cashflows' => Array
        (
            0 => Array
                (
                    'm' => 1,
                    'term' => 10,
                    'rate_per_year' => 123,
                    'effective_time' => 0,
                ),
        ),
    'request' => 'view_cashflows',
		));
//    $this->assertEquals( array('some-stuff-just-to-show-whats-there'),$c['output']['unrendered']) ;  
//    $this->assertEquals( 'some-stuff-just-to-show-whats-there',$c['warning']) ;  
	  $this->assertTrue( isset($c['output']['unrendered']['forms']) ) ;
	  $this->assertTrue( isset($c['output']['unrendered']['formulae']) ) ;
	  $this->assertFalse( isset($c['output']['unrendered']['xml-form']) ) ; //don't want XML until getting value or i_effective
  }  


  public function test_input_XML_cashflows()
  {
	  $x = new CT1_Form_XML();
		$x->set_text( array( 'request'=>'value_cashflows',  'CT1_Cashflows'=>array('item0'=>array('m'=>1, 'advance'=>1, 'delta'=>0, 'i_effective'=>0, 'term'=>1,'value'=>1, 'rate_per_year'=>999,'effective_time'=>1) ) ) );
		$c = $x->get_calculator( array());
		$expected="\n<dummy_tag_set_in_CT1_Form_XML><parameters><request>value_cashflows</request><CT1_Cashflows><item0><m>1</m><advance>1</advance><delta>0</delta><i_effective>0</i_effective><term>1</term><value>1</value><rate_per_year>999</rate_per_year><effective_time>1</effective_time></item0></CT1_Cashflows></parameters></dummy_tag_set_in_CT1_Form_XML>\n";
		$this->assertEquals( $expected, $c['values']['xml'] ) ;
  }  

  public function test_xml_GIVES_SAME_XML()
  {
	  $x = new CT1_Concept_All();
	$c = $x->get_controller( array(
    'CT1_Cashflows' => Array
        (
            0 => Array
                (
                    'm' => 1,
                    'term' => 10,
                    'rate_per_year' => 123,
                    'effective_time' => 0,
                ),
        ),
		'i_effective' => 0,
    'request' => 'value_cashflows',
		));
//    $this->assertEquals( array('some-stuff-just-to-show-whats-there'),$c['output']['unrendered']['xml-form']['forms'][0]['content']['values']['xml']) ;  
//    $this->assertEquals( 'some-stuff-just-to-show-whats-there',$c['output']['unrendered']['xml-form']['forms'][0]['content']['values']['xml']) ;  
//    $this->assertEquals( 'some-stuff-just-to-show-whats-there',$c['warning']) ;  
	  $this->assertTrue( isset($c['output']['unrendered']['forms']) ) ;
	  $this->assertTrue( isset($c['output']['unrendered']['formulae']) ) ;
	  $this->assertTrue( isset($c['output']['unrendered']['xml-form']) ) ; 
	  $this->assertTrue( isset($c['output']['unrendered']['summary']) ) ; 
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
    'CT1_Cashflows' => Array
        (
            0 => Array
                (
                    'm' => 1,
                    'term' => 10,
                    'rate_per_year' => 123,
                    'effective_time' => 0,
                ),
        ),
		'i_effective' => 0,
    'request' => 'value_cashflows',
		));
//    $this->assertEquals( array('some-stuff-just-to-show-whats-there'),$c['output']['unrendered']['xml-form']['forms'][0]['content']['values']['xml']) ;  
//    $this->assertEquals( 'some-stuff-just-to-show-whats-there',$c['output']['unrendered']['xml-form']['forms'][0]['content']['values']['xml']) ;  
//    $this->assertEquals( 'some-stuff-just-to-show-whats-there',$c['warning']) ;  
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
