<?php

require 'test-constants.php';
require_once $class_directory . 'class-ct1-annuity-increasing.php';
class CT1_Annuity_Increasing_Test extends PHPUnit_Framework_TestCase
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
    $this->acalc = new CT1_Annuity_Increasing();
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
}
