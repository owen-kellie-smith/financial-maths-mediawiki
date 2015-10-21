<?php

//require 'TestConstants.php';
//require_once $class_directory . 'class-ct1-concept-all.php';
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
	  $this->assertFalse( isset($c['formulae']) ) ;
  }  

  public function test_concepts()
  {
	  $x = new CT1_Concept_All();
		$c = $x->get_controller( array( 'concept'=>'concept_mortgage' ) );
	  $this->assertTrue( isset($c['form']) ) ;
		$c = $x->get_controller( array( 'concept'=>'concept_annuity_increasing' ) );
	  $this->assertTrue( isset($c['form']) ) ;
		$c = $x->get_controller( array( 'concept'=>'concept_spot_rates' ) );
	  $this->assertTrue( isset($c['form']) ) ;
  }  


}

