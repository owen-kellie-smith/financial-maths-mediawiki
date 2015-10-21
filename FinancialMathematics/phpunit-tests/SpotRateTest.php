<?php

class CT1_Spot_Rate_Test extends PHPUnit_Framework_TestCase
{
  private $sr;

  public function setup(){
	  $_INPUT =  Array ( 0 => Array ( 'delta' => 0.01, 'effective_time' => 1 ), 
	  1 => Array ( 'delta' => 0.02, 'effective_time' => 2 ) 
		);
    $this->sr = new CT1_Spot_Rates();
    $this->sr->set_from_input($_INPUT, $pre = '');
  }
  public function tearDown(){
		$this->sr = null;
	}
  
  public function test_forward_rates()
  {
	  $fr=$this->sr->get_forward_rates();
	  $myfr = $fr-> get_values();
	  $f01=$myfr['0,1'];
	  $f12=$myfr['1,2'];
    $this->assertTrue( abs($f01['delta'] - 0.01) < 0.000001);
    $this->assertTrue( abs($f12['delta'] - (2*0.02-0.01)) < 0.000001);
  }  

  public function test_concepts()
  {
	  $x = new CT1_Concept_All();
		$c = $x->get_controller( array( 'concept'=>'concept_spot_rates' ) );
	  $this->assertTrue( isset($c['form']) ) ;
  }  




}
