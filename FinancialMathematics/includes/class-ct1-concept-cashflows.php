<?php


/**
 * FinMathConceptCashflows class
 *
 * Provides input/output for multiple cashflows
 *
 * @package    CT1
 * @author     Owen Kellie-Smith
 */
class FinMathConceptCashflows extends CT1_Form{

	/**
	 * Constructor
	 *
	 * @param CT1_Object $obj 
	 * @return FinMathConceptCashflows
	 *
	 * @access public
	 */
	public function __construct(CT1_Object $obj=null){
		if (null === $obj){
			$obj = new CT1_Cashflows();
		}
		parent::__construct($obj);
		$this->set_request( 'get_cashflows' );
	}

public function get_concept_label(){
	return array(	
				'concept_cashflows'=> self::myMessage(  'fm-multiple-cashflows'), 
 );
} 

	/**
	 * Get string to render as HTML after new page GET request
	 *
	 * @param array $_INPUT  
	 * @return string
	 *
	 * @access public
	 */
	public function get_controller($_INPUT ){
  $return=array();
		try{
			$tempClass = get_class( $this->obj ) ;
			if (isset($_INPUT[ $tempClass ])){
				if (!$this->set_cashflows( $_INPUT[ $tempClass ] ) ){ 
					$return['warning']=self::myMessage( 'fm-error-cashflows');
					return $return;
				}
			}
			if (isset($_INPUT['request'])){
				if ('add_cashflow' == $_INPUT['request']){
					$this->add_cashflow_from_input( $_INPUT );
				  $return['output']['unrendered']['formulae'] = $this->get_unrendered_solution_no_detail();
				  $return['output']['unrendered']['forms'] = $this->get_unrendered_val_delete_add();
				  return $return;
				}
				if ($this->get_request() == $_INPUT['request']){
				  $return['output']['unrendered']['formulae'] = $this->get_unrendered_calculated_value( $_INPUT );
				  $return['output']['unrendered']['summary'] = $this->get_unrendered_summary( $_INPUT );
					$return['output']['unrendered']['forms'] = $this->get_unrendered_val_delete_add();
				  	return $return;
				}
			}
			if (isset($_INPUT[ $tempClass ])){
				  $return['output']['unrendered']['formulae'] = $this->get_unrendered_solution_no_detail();
				$return['output']['unrendered']['forms'] = $this->get_unrendered_val_delete_add();
			  	return $return;
			}
			else{
				$return['output']['unrendered']['forms'][] = $this->get_unrendered_form_add_cashflow();
				  return $return;
		  	}
		} catch( Exception $e ){
				$return['warning']=self::myMessage( 'fm-exception-in') . __FILE__ . print_r($e->getMessage(),1);
			return $return;
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
			if (isset( $_INPUT['i_effective'] ) ){
				if (!(array()== $_INPUT['i_effective'] ) ){
					return $this->get_solution( (float)$_INPUT['i_effective'] ); 
				}
			}
		} else {
			return $this->get_interest_rate_for_value( (float)$_INPUT['value'] );
		}
	}

	private function get_unrendered_calculated_value( $_INPUT ){
		if ( $this->ignore_value( $_INPUT ) ){
			if (isset( $_INPUT['i_effective'] ) ){
				if (!(array()==$_INPUT['i_effective'] ) ){
					return $this->get_unrendered_solution( (float)$_INPUT['i_effective'] ); 
				}
			}
		} else {
			return $this->get_unrendered_interest_rate_for_value( (float)$_INPUT['value'] );
		}
	}


	private function get_unrendered_summary( $_INPUT ){
		$ret=array();
		if ( $this->ignore_value( $_INPUT ) ){
			if (isset( $_INPUT['i_effective'] ) ){
				if (!(array()== $_INPUT['i_effective'] ) ){
					$ret['sought']='value';
					$ret['result']=$this->obj->get_value();
				}
			}
		} else {
			$ret['sought']='i_effective';
			$ret['result']=exp($this->obj->get_delta_for_value( $_INPUT['value'] )) - 1;
		}
		return $ret;
	}

	private function get_unrendered_val_delete_add(){
		$return =  array( 
			$this->get_unrendered_form_valuation(),
			$this->get_unrendered_form_add_cashflow(),
		);			 
		$return = array_merge( $return, 			$this->get_unrendered_delete_buttons() );
		return $return;
	}


	private function get_unrendered_solution_no_detail(){
		return $this->obj->explain_discounted_value(false);
	}


	private function get_unrendered_solution( $new_i_effective = 0 ){
		$this->obj->set_i_effective( $new_i_effective );	
		return $this->obj->explain_discounted_value();
	}

	
	public function get_unrendered_delete_buttons($unused=''){
		return parent::get_unrendered_delete_buttons('view_cashflows');
	}

	private function get_unrendered_interest_rate_for_value( $v = 0 ){
		return $this->obj->explain_interest_rate_for_value( $v );
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
		if ($cf->set_from_input( $IN ) ){
			$this->obj->add_cashflow( $cf );
		}
		return;
	}


	private function get_unrendered_form_add_cashflow(){
		return array(
			'content'=>$this->get_add_cashflow(),
			'type'=>'',
		);
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
			'label' => self::myMessage( 'fm-single_payment'),
			);
		$parameters_c = array_merge( $c_e->get_parameters(), $c_i->get_parameters() );
		$parameters = array_merge( $parameters, $parameters_c );
		$valid_options = array_merge( $c_e->get_valid_options(), $c_i->get_valid_options() );
		$valid_options['single_payment'] = array( 'type' => 'boolean' );
		$valid_options['consider_increasing'] = array( 'type' => 'boolean' );
		$parameters['consider_increasing'] = array(
			'name'=> 'consider_increasing',
			'label' => self::myMessage( 'fm-consider_increasing'),
			);
		foreach ( array('value','delta', 'escalation_delta', "source_m","source_advance","source_rate") as $p ){
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
		$form['introduction'] = self::myMessage( 'fm-add-a-cashflow') ;
		$form['submit'] = self::myMessage( 'fm-add') ;
		$form['exclude'] = array( "i_effective" );
		$form['values'] = $values;
		$form['hidden'] = $this->get_hidden_cashflow_fields();
		return $form;
	}



	private function get_unrendered_form_valuation(){
		 return array(
			'content'=> $this->get_calculator( '' ),
			'type'=>  ''
		);
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
			'label' => self::myMessage( 'fm-label_i_effective'),
			);
		$parameters['value'] = array(
			'name'=> 'value',
			'label' => self::myMessage( 'fm-label_value_total'),
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
		$form['introduction'] = self::myMessage( 'fm-value-cashflows') ;
		$form['submit'] = self::myMessage( 'fm-calculate');
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
		if (!isset( $_INPUT['value'] ) ){
			return true;
		}
		if (!is_numeric( $_INPUT['value'] ) ){
			return true;
		}
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

