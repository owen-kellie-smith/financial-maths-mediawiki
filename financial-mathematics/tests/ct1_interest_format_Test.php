<?php

require 'test-constants.php';
require_once $class_directory . 'class-ct1-interest-format.php';
class CT1_Interest_Format_Test extends PHPUnit_Framework_TestCase
{
  private $f;
  
  public function setup(){
    $this->f = new CT1_Interest_Format();
    $this->f->set_m(2);
    $this->f->set_advance(true);
  }
  public function tearDown(){}
  
  public function test_set_get()
  {
    $this->assertEquals( $this->f->get_m(), 2);
    $this->assertEquals( $this->f->get_advance(), true);
  }  

  public function test_set_invalid()
  {
    $this->f->set_m(-1);
    $this->assertEquals( $this->f->get_m(), 2);
    $this->f->set_m('cats');
    $this->assertEquals( $this->f->get_m(), 2);
    $this->f->set_m(4);
    $this->assertEquals( $this->f->get_m(), 4);
    $this->f->set_advance('banana');
    $this->assertEquals( $this->f->get_advance(), true);
    $this->f->set_advance(99);
    $this->assertEquals( $this->f->get_advance(), true);
    $this->f->set_advance(false);
    $this->assertEquals( $this->f->get_advance(), false);
  }  

  public function test_equals()
  {
    $t = new CT1_Interest_Format(2, true);
    $this->assertTrue( $this->f->equals($t) );
    $t->set_m(4);
    $this->assertFalse( $this->f->equals($t) );
  }  



}
