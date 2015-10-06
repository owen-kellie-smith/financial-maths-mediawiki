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

		$r = array();
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

