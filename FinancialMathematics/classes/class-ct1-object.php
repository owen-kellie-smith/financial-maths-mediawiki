<?php   
/**
 * CT1_Object class
 *
 * @package    CT1
 * @author     Owen Kellie-Smith
 */

//require_once 'functions.php';
CT1_autoloader('Validate','Validate.php');

/**
 * CT1 Object class
 *
 * @package    CT1
 */
abstract class CT1_Object {

    /**
     * List validation constraints suitable for use with PEAR::Validate
     *
     * @return array
     *
     * @access public
     */
	public function get_valid_options(){ return array(); }

    /**
     * List defining parameter keys, descriptions, labels of object
     *
     * @return array
     *
     * @access public
     */
	public function get_parameters(){ return array(); }

    /**
     * Get validation result (list of parameter keys with boolean values)
     *
     * @param object $candidate  Object to test
     * @return array
     *
     * @access public
     */
	public function get_validation($candidate){
		return Validate::multiple($candidate, $this->get_valid_options());
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
					
} // end of class

