<?php   
/**
 * CT1_Object class
 *
 * @package    CT1
 * @author     Owen Kellie-Smith
 */


/**
 * CT1 Object class
 *
 * @package    CT1
 */
abstract class CT1_Object {

    /**
     * List validation constraints suitable for use with PEAR::Validate
     * Also effectively used to define the elements of any input forms
     *
     * @return array
     *
     * @access public
     */
	public function get_valid_options(){ 

		$r = array( 'request'=>'' );
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
		$r = array();
		return $r; 
	}

	public function get_valid_inputs($_INPUT){
//echo __FILE__ . "\r\n";
//echo " get_valid_inputs \r\n";
//echo " raw input " . print_r($_INPUT,1) . "\r\n";
//echo " valid_options " . print_r($this->get_valid_options(),1 ) . "\r\n";

		$r = $_INPUT;
		foreach (array_keys($r) as $key){
			if (!in_array( $key, array_keys($this->get_valid_options()) ) ){
				unset( $r[$key] );
			}
		}
//echo " validated input " . print_r($r,1 ) . "\r\n";
		return $r;
  }


    /**
     * Get validation result (list of parameter keys with boolean values)
     *
     * @param object $candidate  Object to test
     * @return array
     *
     * @access public
     */
	public function get_validation($candidate){
		$v = new Validate();
		$options =  $this->get_valid_options();
		$ret = $v->multiple($candidate, $options);
		return $ret;
	}

    /**
     * List values of defining parameter keys
     *
     * @return array
     *
     * @access public
     */
	public function get_values(){ return array(); }
		
    /**
     * List displayable labels of object
     *
     * @return array
     *
     * @access public
     */
	public function get_labels(){ return array(); }

	protected static function myMessage( $messageKey){
			$m = $messageKey;
			if ( function_exists('wfMessage') ){
				$m=wfMessage( $messageKey)->text();
			}
			return $m;
}
					
} // end of class

