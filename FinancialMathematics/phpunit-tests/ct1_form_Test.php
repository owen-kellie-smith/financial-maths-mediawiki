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
		$expected="\n<parameters><request>get_interest</request><m>1</m><advance>1</advance><i_effective>0.1</i_effective></parameters>\n";
		$this->assertEquals( $expected, $c['values']['xml'] ) ;
  }  

  public function test_XS_input_XML()
  {
	  $x = new CT1_Form_XML();
		$x->set_text( array( 'title'=>'a page title', 'request'=>'get_interest','m'=>1,'advance'=>1,'i_effective'=>0.1) );
		$c = $x->get_calculator( array());
		$expected="\n<parameters><request>get_interest</request><m>1</m><advance>1</advance><i_effective>0.1</i_effective></parameters>\n";
		$this->assertEquals( $expected, $c['values']['xml'] ) ;
  }  

}
