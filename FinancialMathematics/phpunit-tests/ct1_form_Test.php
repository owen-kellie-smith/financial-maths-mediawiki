<?php

require 'test-constants.php';
require_once $class_directory . 'class-ct1-concept-all.php';
class CT1_Concept_Test extends PHPUnit_Framework_TestCase
{
  
	private $unused;

  public function setup(){}

  public function tearDown(){}
  
  public function test_empty_input()
  {
		$a = array();
	  $x = new CT1_Concept_All();
		$c = $x->get_controller($a);
	  $this->assertFalse( isset($c['form']) ) ;
  }  

  public function test_concepts()
  {
	  $x = new CT1_Concept_All();
		$c = $x->get_controller( array( 'concept'=>'concept_interest' ) );
	  $this->assertTrue( isset($c['form']) ) ;
		$c = $x->get_controller( array( 'concept'=>'concept_annuity' ) );
	  $this->assertTrue( isset($c['form']) ) ;
		$c = $x->get_controller( array( 'concept'=>'concept_mortgage' ) );
	  $this->assertTrue( isset($c['form']) ) ;
		$c = $x->get_controller( array( 'concept'=>'concept_annuity_increasing' ) );
	  $this->assertTrue( isset($c['form']) ) ;
  }  

  public function test_full_input_formulae()
  {
	  $x = new CT1_Concept_All();
		$c = $x->get_controller( array( 'request'=>'get_interest','m'=>1,'advance'=>1,'i_effective'=>0.1) );
	  $this->assertTrue( isset($c['formulae']) ) ;
  }  

  public function test_full_input_form()
  {
	  $x = new CT1_Concept_All();
		$c = $x->get_controller( array( 'request'=>'get_interest','m'=>1,'advance'=>1,'i_effective'=>0.1) );
	  $this->assertTrue( isset($c['xml-form']) ) ;
  }  

  public function test_full_input_form_xml()
  {
	  $x = new CT1_Concept_All();
		$c = $x->get_controller( array( 'request'=>'get_interest','m'=>1,'advance'=>1,'i_effective'=>0.1) );
	  $this->assertTrue( isset($c['xml-form']['form']) ) ;
  }  

  public function test_full_input_XML()
  {
	  $x = new CT1_Form_XML();
		$x->set_text( array( 'request'=>'get_interest','m'=>1,'advance'=>1,'i_effective'=>0.1) );
		$c = $x->get_calculator( array());
		$expected="\n<dummy_tag_set_in_CT1_Form_XML><parameters><request>get_interest</request><m>1</m><advance>1</advance><i_effective>0.1</i_effective></parameters></dummy_tag_set_in_CT1_Form_XML>\n";
		$this->assertEquals( $expected, $c['values']['xml'] ) ;
  }  

  public function test_XS_input_XML()
  {
	  $x = new CT1_Form_XML();
		$x->set_text( array( 'title'=>'a page title', 'request'=>'get_interest','m'=>1,'advance'=>1,'i_effective'=>0.1) );
		$c = $x->get_calculator( array());
		$expected="\n<dummy_tag_set_in_CT1_Form_XML><parameters><request>get_interest</request><m>1</m><advance>1</advance><i_effective>0.1</i_effective></parameters></dummy_tag_set_in_CT1_Form_XML>\n";
		$this->assertEquals( $expected, $c['values']['xml'] ) ;
  }  

  public function test_input_XML_cashflows()
  {
	  $x = new CT1_Form_XML();
		$x->set_text( array( 'request'=>'value_cashflows',  'CT1_Cashflows'=>array('item0'=>array('m'=>1, 'advance'=>1, 'delta'=>0, 'i_effective'=>0, 'term'=>1,'value'=>1, 'rate_per_year'=>999,'effective_time'=>1) ) ) );
		$c = $x->get_calculator( array());
		$expected="\n<dummy_tag_set_in_CT1_Form_XML><parameters><request>value_cashflows</request><CT1_Cashflows><item0><m>1</m><advance>1</advance><delta>0</delta><i_effective>0</i_effective><term>1</term><value>1</value><rate_per_year>999</rate_per_year><effective_time>1</effective_time></item0></CT1_Cashflows></parameters></dummy_tag_set_in_CT1_Form_XML>\n";
		$this->assertEquals( $expected, $c['values']['xml'] ) ;
  }  

  public function test_full_xmlinput_formuae()
  {
	  $x = new CT1_Concept_All();
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>'<fin-math><parameters><request>get_interest<%2Frequest><m>1<%2Fm><i_effective>0<%2Fi_effective><%2Fparameters><%2Ffin-math>%0D%0A' ));
	  $this->assertTrue( isset($c['formulae']) ) ;
  }  

}

