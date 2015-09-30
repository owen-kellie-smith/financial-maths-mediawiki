<?php

require 'test-constants.php';
require_once $class_directory . 'class-ct1-annuity-escalating.php';
class CT1_Annuity_Escalating_Test extends PHPUnit_Framework_TestCase
{
  private $debug = false;
  private $acalc;
  private $term = 19;
  private $i = 0.19;
  private $e = 0.19;
  private $freq_escalating = 12;
  private $freq = 12;
  private $adv = true;
  private $neg = 0.00001;
  
  public function setup(){
    $this->acalc = new CT1_Annuity_Escalating();
    $this->reset();
  }
  public function tearDown(){}
  
  private function reset(){
  	$this->acalc->set_m($this->freq);
  	$this->acalc->set_advance($this->adv);
  	$this->acalc->set_delta(log(1+$this->i));
  	$this->acalc->set_term($this->term);
  	$this->acalc->set_escalation_rate_effective($this->e);
  	$this->acalc->set_escalation_frequency($this->freq_escalating);
  }
  	
  private function aval(){
    return $this->acalc->get_annuity_certain();
  }

  public function test_simple_annuity()
  {
//	print_r( $this->acalc->get_values() );
    $this->assertTrue( $this->acalc->get_advance() );
    if ($this->debug) $this->assertEquals( $this->aval() , 19);
    $this->assertTrue( abs($this->aval() - 19 ) < $this->neg );
    // source of numbers -- common sense. net interest rate 0
  }  
 
  public function test_simple_annuity_arrears()
  {
  	$this->acalc->set_advance( false );
//	print_r( $this->acalc->get_values() );
    $this->assertTrue( !$this->acalc->get_advance() );
    if ($this->debug) $this->assertEquals( $this->aval() , 19 * exp(-log(1.19)/12));
    $this->assertTrue( abs($this->aval() - 19*exp(-log(1.19)/12) ) < $this->neg );
    // source of numbers -- common sense. net interest rate 0.  1st payment 1 not with implied inc.
  }  

  public function test_complex_annuity_arrears()
  {
  	$this->acalc->set_advance( false );
  	$this->acalc->set_m(12);
  	$this->acalc->set_delta(log(1.1));
  	$this->acalc->set_term(20);
  	$this->acalc->set_escalation_rate_effective(0.05);
  	$this->acalc->set_escalation_frequency(4);
//	print_r( $this->acalc->get_values() );
    $this->assertTrue( !$this->acalc->get_advance() );
    if ($this->debug) $this->assertEquals( $this->aval() , 12.887905873);
    $this->assertTrue( abs($this->aval() - 12.887905873 ) < $this->neg );
	// source numbers: spreadsheet (cashflow discounted)
	}
  
public function test_adjusted_int_arrears()
  {
  	$this->acalc->set_advance( false );
  	$this->acalc->set_m(12);
  	$this->acalc->set_delta(log(1.1));
  	$this->acalc->set_term(20);
  	$this->acalc->set_escalation_rate_effective(0.05);
  	$this->acalc->set_escalation_frequency(999);
//	print_r( $this->acalc->get_values() );
    $this->assertTrue( !$this->acalc->get_advance() );
    if ($this->debug) $this->assertEquals( $this->aval() , 12.940205516);
    $this->assertTrue( abs($this->aval() - 12.940205516 ) < $this->neg );
	// source numbers: spreadsheet (cashflow discounted)
	}

public function test_adjusted_int_advance()
  {
  	$this->acalc->set_advance( true );
  	$this->acalc->set_m(12);
  	$this->acalc->set_delta(log(1.1));
  	$this->acalc->set_term(20);
  	$this->acalc->set_escalation_rate_effective(0.05);
  	$this->acalc->set_escalation_frequency(999);
//	print_r( $this->acalc->get_values() );
    $this->assertTrue( $this->acalc->get_advance() );
    if ($this->debug) $this->assertEquals( $this->aval() , 13.04339);
    $this->assertTrue( abs($this->aval() - 13.04339 ) < $this->neg );
	// source numbers: spreadsheet (cashflow discounted)
	}
}
