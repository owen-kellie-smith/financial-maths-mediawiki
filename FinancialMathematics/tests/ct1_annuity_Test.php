<?php

require 'test-constants.php';
require_once $class_directory . 'class-ct1-annuity.php';
class CT1_Annuity_Test extends PHPUnit_Framework_TestCase
{
  private $debug = false;
  private $acalc;
  private $term = 10;
  private $i;
  private $freq = 12;
  private $adv = true;
  private $neg = 0.00001;
  
  public function setup(){
    $this->acalc = new CT1_Annuity(10, true, log(1.06), 12);
    $this->i = 0.06;
  }
  public function tearDown(){}
  
  private function reset(){
  	$this->acalc->set_m($this->freq);
  	$this->acalc->set_advance($this->adv);
  	$this->acalc->set_delta(log(1+$this->i));
  	$this->acalc->set_term($this->term);
  }
  	
  private function aval(){
  	$this->reset();
    return $this->acalc->get_annuity_certain();
  }

  private function ival(){
  	$this->reset();
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


}
