<?php

require_once 'class-ct1-cashflows.php';
require_once 'class-ct1-form.php';
require_once 'class-ct1-render.php';

/**
 * CT1_Concept_Cashflows class
 *
 * Provides input/output for multiple cashflows
 *
 * @package    CT1
 * @author     Owen Kellie-Smith
 */
class CT1_Concept_Cashflows extends CT1_Form{

	/**
	 * Constructor
	 *
	 * @param CT1_Object $obj 
	 * @return CT1_Concept_Cashflows
	 *
	 * @access public
	 */
	public function __construct(CT1_Object $obj=null){
		if (null === $obj) $obj = new CT1_Cashflows();
		parent::__construct($obj);
		$this->set_request( 'get_cashflows' );
	}

	/**
	 * Get string to render as HTML after new page GET request
	 *
	 * @param array $_INPUT  Probably $_GET
	 * @return string
	 *
	 * @access public
	 */
	public function get_controller($_INPUT ){
	//echo "<pre> INPUT" . __FILE__ . print_r($_INPUT,1) . "</pre>";
		try{
			if (isset($_INPUT[ get_class( $this->obj ) ])){
				if (!$this->set_cashflows( $_INPUT[ get_class( $this->obj ) ] ) ) 
					return "<p>Error setting cashflows from:<pre>" . print_r($_INPUT,1) .  "</pre>";
			}
			if (isset($_INPUT['request'])){
				if ('add_cashflow' == $_INPUT['request']){
					$this->add_cashflow_from_input( $_INPUT );
					return $this->get_solution_no_detail() .  $this->get_val_delete_add()  ;
				}
				if ($this->get_request() == $_INPUT['request']){
					return $this->get_calculated_value( $_INPUT ) . $this->get_val_delete_add()  ;
				}
			}
			if (isset($_INPUT[ get_class( $this->obj ) ]))
				return $this->get_solution_no_detail() .  $this->get_val_delete_add()  ;
			return $this->get_form_add_cashflow()  ;
		} catch( Exception $e ){
			return "Exception in " . __FILE__ . print_r($e->getMessage(),1) ;
		}
	}

	/**
	 * Get string to render explanation of value or interest rate
	 *
	 * @param array $_INPUT  
	 * @return string
	 *
	 * @access private
	 */
	private function get_calculated_value( $_INPUT ){
		if ( $this->ignore_value( $_INPUT ) ){
			if (isset( $_INPUT['i_effective'] ) )
				return $this->get_solution( (float)$_INPUT['i_effective'] ); 
		} else {
			return $this->get_interest_rate_for_value( (float)$_INPUT['value'] );
		}
	}


	/**
	 * Get string to render all the inputs forms (new valuation, add / delete cashflows)
	 *
	 * @return string
	 *
	 * @access private
	 */
	private function get_val_delete_add(){
		return  $this->get_form_valuation() . $this->get_delete_buttons() .  $this->get_form_add_cashflow()  ;
	}

	/**
	 * Get string to render form requesting explanation of value or interest rate
	 *
	 * @param CT1_Cashflows $cf
	 * @param string $submit caption for sumbit button
	 * @param string $intro text to place above form (if any)
	 * @return string
	 *
	 * @access private
	 */
	private function get_render_form_cashflow( CT1_Cashflows $cf, $submit = 'Submit', $intro = "" ){
		$render = new CT1_Render();
		return $render->get_form_collection( $cf, $submit, $intro, 'view_cashflows');
	}

	/**
	 * Get list of cashflows (with no value)
	 *
	 * @return string
	 *
	 * @access private
	 */
	private function get_solution_no_detail(){
		$render = new CT1_Render();
		$return = $render->get_render_latex($this->obj->explain_discounted_value(false));
		return $return;
	}

	/**
	 * Get explanation of valuation of cashflows
	 *
	 * @param float $new_i_effective
	 * @return string
	 *
	 * @access public
	 */
	public function get_solution( $new_i_effective = 0 ){
		$this->obj->set_i_effective( $new_i_effective );	
		$render = new CT1_Render();
		$return = $render->get_render_latex($this->obj->explain_discounted_value());
		return $return;
	}


	/**
	 * Get delete buttons (forms which contain as hidden fields the undeleted cashflows)
	 *
	 * @return string
	 *
	 * @access public
	 */
	public function get_delete_buttons(){
		return parent::get_delete_buttons('view_cashflows');
	}
	

	/**
	 * Get explanation of interest rate that satisfies sought net present value
	 *
	 * @param float $v sought net present value
	 * @return string
	 *
	 * @access private
	 */
	private function get_interest_rate_for_value( $v = 0 ){
		$render = new CT1_Render();
		$return = $render->get_render_latex($this->obj->explain_interest_rate_for_value( $v ));
		return $return;
	}

	/**
	 * Set additional cashflow based on input
	 *
	 * @param array $IN new cashflow features
	 * @return NULL
	 *
	 * @access private
	 */
	private function add_cashflow_from_input( $IN ){
		$cf = new CT1_Cashflow();
		if ($cf->set_from_input( $IN ) )
			$this->obj->add_cashflow( $cf );
		return;
	}


	/**
	 * Get HTML for form to add a cashflow (including as hidden fields all the existing cashflows)
	 *
	 * @return string
	 *
	 * @access private
	 */
	private function get_form_add_cashflow(){
		$render = new CT1_Render();
		return $render->get_render_form( $this->get_add_cashflow() );
	}
	

	/**
	 * Get array of parameters for existing cashflows
	 *
	 * @return array
	 *
	 * @access private
	 */
	private function get_hidden_cashflow_fields(){
		return $this->obj->get_values_as_array( get_class( $this->obj ) );
	}

	/**
	 * Get array of parameters for form to add a new cashflow
	 *
	 * @return array
	 *
	 * @access private
	 */
	private function get_add_cashflow(){
		$a_e = new CT1_Annuity_Escalating();
		$a_i = new CT1_Annuity_Increasing();
		$c_e = new CT1_Cashflow( 0, 0,  $a_e );
		$c_i = new CT1_Cashflow( 0, 0, $a_i );
		$parameters = array();
		$parameters['single_payment'] = array(
			'name'=> 'single_payment',
			'label' => 'Treat as single payment (ignore parameters apart from rate and effective time)?',
			);
		$parameters_c = array_merge( $c_e->get_parameters(), $c_i->get_parameters() );
		$parameters = array_merge( $parameters, $parameters_c );
		$valid_options = array_merge( $c_e->get_valid_options(), $c_i->get_valid_options() );
		$valid_options['single_payment'] = array( 'type' => boolean );
		$valid_options['consider_increasing'] = array( 'type' => boolean );
		$parameters['consider_increasing'] = array(
			'name'=> 'consider_increasing',
			'label' => 'Treat as increasing / decreasing (stepped) annuity?',
			);
		foreach ( array('value','delta', 'escalation_delta') as $p ){
			unset( $parameters[ $p ] );
			unset( $valid_options[ $p ] );
		}
		$values = array_merge( $c_e->get_values(), $c_i->get_values() );
		$form = array();
		$form['method'] = 'GET';
		$form['parameters'] = $parameters;
		$form['valid_options'] = $valid_options;
		$form['request'] = 'add_cashflow';
		$form['render'] = 'HTML';
		$form['introduction'] = 'Add a cashflow.';
		$form['submit'] = 'Add';
		$form['exclude'] = array( "i_effective" );
		$form['values'] = $values;
		$form['hidden'] = $this->get_hidden_cashflow_fields();
		return $form;
	}

	/**
	 * Get rendered form to request a valuation of cashflows (or interest rate that satisfies a value)
	 *
	 * @return string
	 *
	 * @access private
	 */
	private function get_form_valuation(){
		$calc = $this->get_calculator( $unused );
		$render = new CT1_Render();
		return $render->get_render_form( $calc );
	}


	/**
	 * Get array of parameters for form to value cashflows (get value or ineterst rate satisfying value)
	 *
	 * @return array
	 *
	 * @access public
	 */
	public function get_calculator($parameters){
		$parameters['i_effective'] = array(
			'name'=> 'i_effective',
			'label' => 'Effective annual rate of return',
			);
		$parameters['value'] = array(
			'name'=> 'value',
			'label' => 'Total present (discounted) value (leave blank if you want the value for a particular rate of return)',
			);
		$valid_options = array();
		$valid_options['i_effective'] = array(
						'type'=>'number',
						'decimal'=>'.',
						'min' => -0.99,
					);
		$valid_options['value'] = array(
						'type'=>'number',
						'decimal'=>'.',
					);
		$values = array();
		$form = array();
		$form['method'] = 'GET';
		$form['parameters'] = $parameters;
		$form['valid_options'] = $valid_options;
		$form['request'] = $this->get_request();
		$form['render'] = 'HTML';
		$form['introduction'] = 'Value cashflows.  Enter an effective annual rate of return (to get a present value) or a present value (to get an implicit rate of return, if one exists.)';
		$form['submit'] = 'Calculate';
		$form['exclude'] = array();
		$form['values'] = $values;
		$form['hidden'] = $this->get_hidden_cashflow_fields();
		return $form;
	}

	/**
	 * Get value of request parameter in input that indicates a request belonging to this object
	 *
	 * @return string
	 *
	 * @access public
	 */
	public function get_request(){
		return "value_cashflows";
	}

	/**
	 * Get array of request parameter-value in input that indicates a request belonging to this object
	 *
	 * @return array
	 *
	 * @access public
	 */
	public function get_possible_requests(){
		return array( 
			'view_cashflows',
			'add_cashflow',
			$this->get_request(),
		);
	}

	/**
	 * Decide whether to ignore the value parameter in input
	 *
	 * @return boolean
	 *
	 * @access private
	 */
	private function ignore_value( $_INPUT ){
		if (!isset( $_INPUT['value'] ) )
			return true;
		if (!is_numeric( $_INPUT['value'] ) )
			return true;
		return false;
	}


	/**
	 * Set existing cashflows from input
	 *
	 * @return boolean
	 *
	 * @access private
	 */
	private function set_cashflows( $_INPUT = array() ){
		return ($this->obj->set_from_input($_INPUT));
	}

} // end of class

