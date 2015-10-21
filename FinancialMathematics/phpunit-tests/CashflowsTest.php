<?php

//require 'TestConstants.php';
//require_once $class_directory . 'class-ct1-annuity.php';
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
		$c = $x->get_controller( array( 'concept'=>'concept_interest' ) );
	  $this->assertTrue( isset($c['form']) ) ;
		$c = $x->get_controller( array( 'concept'=>'concept_annuity' ) );
	  $this->assertTrue( isset($c['form']) ) ;
		$c = $x->get_controller( array( 'concept'=>'concept_mortgage' ) );
	  $this->assertTrue( isset($c['form']) ) ;
		$c = $x->get_controller( array( 'concept'=>'concept_annuity_increasing' ) );
	  $this->assertTrue( isset($c['form']) ) ;
		$c = $x->get_controller( array( 'concept'=>'concept_cashflows' ) );
	  $this->assertTrue( isset($c['form']) ) ;
		$c = $x->get_controller( array( 'concept'=>'concept_cashflows' ) );
	  $this->assertTrue( isset($c['form']) ) ;
		$c = $x->get_controller( array( 'concept'=>'concept_spot_rates' ) );
	  $this->assertTrue( isset($c['form']) ) ;
  }  

}
