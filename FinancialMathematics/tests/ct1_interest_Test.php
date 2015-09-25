<?php

require 'test-constants.php';
require_once $class_directory . 'class-ct1-interest.php';
class CT1_Interest_Test extends PHPUnit_Framework_TestCase
{
  private $debug=false;
  private $icalc;
  private $i;
  private $freq;
  
  public function setup(){
    $this->icalc = new CT1_Interest(1, false, log(1.06));
  }

  public function tearDown(){}
  
  public function test_iconverti()
  {
    $f = new CT1_Interest_Format();
    $f->set_m(12);
    $f->set_advance(false);
    $this->assertTrue( abs($this->icalc->get_rate_in_form($f)-  0.058411) < 0.000001);
    // source of numbers: Formulae and tables 6% p.58  i(12)
  }  

  public function test_iconvertd()
  {
    $t = new CT1_Interest_Format();
    $t->set_m(12);
    $t->set_advance(true);
    if ($this->debug) $this->assertEquals( $this->icalc->get_rate_in_form($t), 0.06 / 1.032211);
    $this->assertTrue( abs($this->icalc->get_rate_in_form($t)-  0.06 / 1.032211) < 0.000001);
    // source of numbers: Formulae and tables 6% p.58   i/d(12)
  }  


  public function test_iconvertdel()
  {
    $t = new CT1_Interest_Format();
    $t->set_m(367); // anything greater than 366 is treated as continuous
    $t->set_advance(true);
    $this->assertTrue( abs($this->icalc->get_rate_in_form($t)-  0.058269) < 0.000001);
    // source of numbers: Formulae and tables 6% p.58   delta
  }  

  public function test_i_effective()
  {
    $this->icalc->set_i_effective(0.10);
    $this->assertEquals( $this->icalc->get_delta(), log(1.10));
  }
}
