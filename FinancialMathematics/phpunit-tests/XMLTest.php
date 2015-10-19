<?php

require 'TestConstants.php';
require_once $class_directory . 'class-ct1-xml.php';
class CT1_XML_Test extends PHPUnit_Framework_TestCase
{
  
	private $unused;

  public function setup(){}

  public function tearDown(){}
  
  public function test_valid_input()
  {
	  $x = new CT1_XML("<parameters><something>A thing</something></parameters>");
		$temp = $x->get_values();
		$this->assertEquals( $temp['xml'],"<parameters><something>A thing</something></parameters>" );
  }  

  public function test_invalid_overwrite()
  {
	  $x = new CT1_XML("<parameters><something>A thing</something></parameters>");
	  $x->set_xml("some junk");
		$temp = $x->get_values();
		$this->assertEquals( $temp['xml'],"<parameters><something>A thing</something></parameters>" );
  }

  public function test_valid_overwrite()
  {
	  $x = new CT1_XML("<parameters><something>A thing</something></parameters>");
	  $x->set_xml("some junk");
	  $x->set_xml("<parameters><something>Something else</something></parameters>");
		$temp = $x->get_values();
		$this->assertEquals( $temp['xml'],"<parameters><something>Something else</something></parameters>" );

  }  

}
