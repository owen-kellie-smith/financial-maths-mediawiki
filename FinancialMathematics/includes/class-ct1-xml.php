<?php   
/**
 * CT1_XML class
 *
 * @package    CT1
 * @author     Owen Kellie-Smith
 */

require_once 'class-ct1-object.php';

class CT1_XML extends CT1_Object{

	private $xml;

    /**
     * List defining parameter keys, descriptions, labels of object
     *
     * @return array
     *
     * @access public
     */
	public function get_valid_options(){ 
		$r = parent::get_valid_options();
		$r['xml'] = array(
							'type'=>'string',
						);
		return $r; 
	}

    /**
     * List defining parameter keys, descriptions, labels of object
     *
     * @return array
     *
     * @access public
     */
	public function get_parameters(){ 
		$r = parent::get_parameters();
		$r['xml'] = array(
			'name'=>'xml',
			'label'=>self::myMessage( 'fm-label-xml'),
			);
		return $r; 
	}

    /**
     * List values of defining parameter keys
     *
     * @return array
     *
     * @access public
     */
	public function get_values(){ 
		$r = parent::get_values();
		$r['xml'] = $this->get_xml();
		return $r; 
	}
		
	public function __construct( $xml='' ){
	  $this->set_xml($xml);
	}

	public function get_xml(){
		return $this->xml;
	}

	public function set_xml($m){
	$m = urldecode($m);
		try{ 
			$xml=simplexml_load_string($m); 
			if ($xml){
		  	$candidate = array('xml'=>$m);
		  	$valid = $this->get_validation($candidate);
				if ($valid['xml']) $this->xml = $m;
			} // otherwise don't change $this->xml
		} 
		catch( Exception $e ){
			return ;
		}
	}

	public function equals($f){
		if(!($f instanceof CT1_XML))        return false;
		if( $f->get_xml()       != $this->get_xml()       ) return false;
		return true;
	}

	public function set_from_input($_INPUT = array(), $pre = ''){
	try{
			$this->set_xml(	$_INPUT[$pre. 'xml'] );
			return true;
		}
		catch( Exception $e ){ 
			throw new Exception( self::myMessage( 'fm-exception-in') . " " . __FILE__ . ": " . $e->getMessage() );
		}
	}

  
} // end of class



