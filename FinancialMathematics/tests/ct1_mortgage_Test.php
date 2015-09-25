<?php

require 'test-constants.php';
require_once $class_directory . 'class-ct1-mortgage.php';
class CT1_Mortgage_Test extends PHPUnit_Framework_TestCase
{
  private $debug = false;
  private $schedule;
  private $mcalc;
  private $neg = 0.00001;
  
  public function setup(){
    $this->mcalc = new CT1_Mortgage(12, true, log(1.06), 10, 1000000);
    $this->schedule = $this->scal();
  }
  public function tearDown(){}
  
  private function aval(){
    return $this->mcalc->get_annuity_certain();
  }

  private function scal(){
    return $this->mcalc->get_mortgage_schedule();
  }

  public function test_mortgageSchedule()
  {
    $inst = 1/12 * 1000000 * 1/(1.032211 * 7.3601);
    $int = (1000000 - $inst) * 0.058411/12;
    // source of numbers: Formulae and tables 6% p.58   i/d(12), a10, i(12)
    $this->assertEquals( $this->schedule[1]['count'],1 );
    $this->assertEquals( $this->schedule[1]['oldPrincipal'],1000000 );
    if ($this->debug){
	    $this->assertEquals( $this->schedule[1]['instalment'], $inst) ;
	    $this->assertEquals( $this->schedule[1]['interest'] , $int ) ;
  	  $this->assertEquals( $this->schedule[1]['capRepay'] , ($inst - $int) );
  	  $this->assertEquals( $this->schedule[1]['newPrincipal'] , (1000000 - $inst + $int) ) ;
    }
    $this->assertTrue( abs($this->schedule[1]['instalment']- $inst) < 0.1);
    $this->assertTrue( abs($this->schedule[1]['interest'] - $int ) < 0.1);
    $this->assertTrue( abs($this->schedule[1]['capRepay'] - ($inst - $int) ) < 0.1);
    $this->assertTrue( abs($this->schedule[1]['newPrincipal'] - (1000000 - $inst + $int) ) < 0.1);
    // test half way through
    $remain = $inst * 12 * 1.032211 * 4.2124;
    $int = ($remain - $inst) * 0.058411/12;
    // source of new numbers: Formulae and tables 6% p.58   a5
    $this->assertEquals( $this->schedule[61]['count'],61 );
    if ($this->debug){
    $this->assertEquals( $this->schedule[61]['oldPrincipal'] , $remain);
    $this->assertEquals( $this->schedule[61]['instalment'], $inst) ;
    $this->assertEquals( $this->schedule[61]['interest'] , $int ) ;
    $this->assertEquals( $this->schedule[61]['capRepay'] , ($inst - $int) ) ;
    $this->assertEquals( $this->schedule[61]['newPrincipal'] , ($remain - $inst + $int) );
    }
    $this->assertTrue( abs($this->schedule[61]['oldPrincipal'] - $remain) < 10 );
    $this->assertTrue( abs($this->schedule[61]['instalment']- $inst) < 0.1);
    $this->assertTrue( abs($this->schedule[61]['interest'] - $int ) < 0.1);
    $this->assertTrue( abs($this->schedule[61]['capRepay'] - ($inst - $int) ) < 0.1);
    $this->assertTrue( abs($this->schedule[61]['newPrincipal'] - ($remain - $inst + $int) ) < 10);
  }  
 
  public function test_mortgageScheduleNilInterest()
  {
    $this->mcalc->set_delta(0);
    $this->mcalc->set_principal(1200000);
    $this->schedule = $this->scal();
    $inst = 10000;
    $int = 0;
    // source of numbers: Formulae and tables 6% p.58   i/d(12), a10, i(12)
    $this->assertEquals( $this->schedule[1]['count'],1 );
    $this->assertEquals( $this->schedule[1]['oldPrincipal'],1200000 );
    $this->assertTrue( abs($this->schedule[1]['instalment']- $inst) < 0.1);
    $this->assertTrue( abs($this->schedule[1]['interest'] - $int ) < 0.1);
    $this->assertTrue( abs($this->schedule[1]['capRepay'] - ($inst - $int) ) < 0.1);
    $this->assertTrue( abs($this->schedule[1]['newPrincipal'] - (1200000 - $inst + $int) ) < 0.1);
    // test half way through
    $remain = 600000;
    // source of new numbers: Formulae and tables 6% p.58   a5
    $this->assertEquals( $this->schedule[61]['count'],61 );
    $this->assertTrue( abs($this->schedule[61]['oldPrincipal'] - $remain) < 10 );
    $this->assertTrue( abs($this->schedule[61]['instalment']- $inst) < 0.1);
    $this->assertTrue( abs($this->schedule[61]['interest'] - $int ) < 0.1);
    $this->assertTrue( abs($this->schedule[61]['capRepay'] - ($inst - $int) ) < 0.1);
    $this->assertTrue( abs($this->schedule[61]['newPrincipal'] - ($remain - $inst + $int) ) < 10);
  }  
  
}
