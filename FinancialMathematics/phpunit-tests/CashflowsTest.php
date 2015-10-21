<?php

require 'TestConstants.php';
require_once $class_directory . 'class-ct1-annuity.php';
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
 
}
