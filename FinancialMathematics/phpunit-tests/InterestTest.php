<?php

class CT1_Interest_Test extends PHPUnit_Framework_TestCase
{
  private $debug=false;
  private $icalc;
  private $i;
  private $freq;
  private $f;

  
  public function setup(){
    $this->icalc = new CT1_Interest(1, false, log(1.06));
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

  public function test_concepts()
  {
	  $x = new CT1_Concept_All();
		$c = $x->get_controller( array( 'concept'=>'concept_interest' ) );
	  $this->assertTrue( isset($c['output']['unrendered']['forms']) ) ;
  }  

  public function test_input_returns_expected_get_interest()
  {
	  $x = new CT1_Concept_All();
		$c = $x->get_controller( array( 'request'=>'get_interest','m'=>1,'advance'=>1,'i_effective'=>0.1) );
	  $this->assertTrue( isset($c['output']['unrendered']['formulae']) ) ;
	  $this->assertTrue( isset($c['output']['unrendered']['xml-form']) ) ;
  }  

  public function test_full_input_form()
  {
	  $x = new CT1_Concept_All();
		$c = $x->get_controller( array( 'request'=>'get_interest','m'=>1,'advance'=>1,'i_effective'=>0.1) );
	  $this->assertTrue( isset($c['output']['unrendered']['xml-form']) ) ;
  }  

  public function test_full_input_form_xml()
  {
	  $x = new CT1_Concept_All();
		$c = $x->get_controller( array( 'request'=>'get_interest','m'=>1,'advance'=>1,'i_effective'=>0.1) );
	  $this->assertTrue( isset($c['output']['unrendered']['xml-form']) ) ;
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

  public function test_full_xmlinput_formuae()
  {
	  $x = new CT1_Concept_All();
		$c = $x->get_controller( array( 'request'=>'process_xml', 'xml'=>'<fin-math><parameters><request>get_interest<%2Frequest><m>1<%2Fm><i_effective>0<%2Fi_effective><%2Fparameters><%2Ffin-math>%0D%0A' ));
	  $this->assertTrue( isset($c['output']['unrendered']['formulae']) ) ;
  }  

}
