<?php

require 'test-constants.php';
require_once $class_directory . 'class-ct1-marker.php';
class CT1_Marker_Test extends PHPUnit_Framework_TestCase
{
  private $marker;
  
  public function setup(){
    $this->marker = new CT1_Marker();
  }
  public function tearDown(){}
  
  public function test_score()
  {
    $actual = 12.345678;
    $guess = 12.346;
    $score = $this->marker->score($actual, $guess);
    $this->assertEquals( $score['credit'], 5); // 5 correct sig fig
    $this->assertEquals( $score['available'], 8); // 8 sig fig

    $guess = 12.345;
    $score = $this->marker->score($actual, $guess);
    $this->assertEquals( $score['credit'], 4); // 4 correct sig fig
    
    $actual = -12.3456;
    $guess = -12.3;
    $score = $this->marker->score($actual, $guess);
    $this->assertEquals( $score['credit'], 3); // 5 correct sig fig
    $this->assertEquals( $score['available'], 6); // 8 sig fig

    $actual = -12.345678;
    $guess = 12.346;
    $score = $this->marker->score($actual, $guess);
    $this->assertEquals( $score['credit'], 0); // 0 correct sig fig
    $this->assertEquals( $score['available'], 8); // 8 sig fig

  }  

  public function test_dps(){
    $this->assertEquals( $this->marker->no_dps(0.123), 3);
    $this->assertEquals( $this->marker->no_dps(987.123), 3);
    $this->assertEquals( $this->marker->no_dps(-987.123), 3);
    $this->assertEquals( $this->marker->no_dps(10), 0);
  }


  public function test_sigfig(){
    $this->assertEquals( $this->marker->no_sig_fig(0.123), 3);
    $this->assertEquals( $this->marker->no_sig_fig(987.123), 6);
    $this->assertEquals( $this->marker->no_sig_fig(-987.123), 6);
    $this->assertEquals( $this->marker->no_sig_fig(10), 1);
    $this->assertEquals( $this->marker->no_sig_fig(1230000000), 3);
  }

}
