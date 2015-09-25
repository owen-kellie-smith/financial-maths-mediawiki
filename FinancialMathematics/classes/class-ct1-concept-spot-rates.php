<?php

require_once 'class-ct1-spot-rates.php';
require_once 'class-ct1-form.php';
require_once 'class-ct1-render.php';

/**
 * CT1_Concept_Spot_Rates class
 *
 * Provides input/output for spot rates
 *
 * @package    CT1
 * @author     Owen Kellie-Smith
 */
class CT1_Concept_Spot_Rates extends CT1_Form{

	/**
	 * Constructor
	 *
	 * @param CT1_Object $obj 
	 * @return CT1_Concept_Spot_Rates
	 *
	 * @access public
	 */
	public function __construct(CT1_Object $obj=null){
		if (null === $obj) $obj = new CT1_Spot_Rates();
		parent::__construct($obj);
		$this->set_request( 'get_spotrates' );
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
	//echo "<pre> GET" . __FILE__ . print_r($_INPUT, 1) . "</pre>";
		try{
			if (isset($_INPUT[get_class( $this->obj )])){
				if (!$this->set_spotrates( $_INPUT[ get_class( $this->obj )] ) ) 
					return "<p>Error setting spotrates from:<pre>" . print_r($_INPUT,1) .  "</pre>";
			}
			if (isset($_INPUT['request'])){
				if ('add_spot_rate' == $_INPUT['request']){
					$this->add_spot_rate_from_input( $_INPUT );
					return $this->get_solution_no_detail()  . $this->get_delete_add()  ;
				}
				if ('explain_forward' == $_INPUT['request'] )
					return $this->get_explanation_forward( $_INPUT ) . $this->get_delete_add()  ;
				if ( 'explain_par' == $_INPUT['request'] )
					return $this->get_explanation_par( $_INPUT ) . $this->get_delete_add()  ;
			}
			if (isset($_INPUT[get_class( $this->obj )]))
				return $this->get_solution_no_detail()  . $this->get_delete_add()  ;
			return $this->get_form_add_spot_rate()  ;
		} catch( Exception $e ){
			return "Exception in " . __FILE__ . print_r($e->getMessage(),1) ;
		}
	}

	/**
	 * Get parameters for form to add a new spot rate
	 *
	 * @return array
	 *
	 * @access public
	 */
	public function get_add_spot_rate(){
		$sr = new CT1_Spot_Rate();
		$values = $sr->get_values();
		$form = array();
		$form['method'] = 'GET';
		$form['parameters'] = $sr->get_parameters();
		$form['valid_options'] = $sr->get_valid_options();
		$form['request'] = 'add_spot_rate';
		$form['render'] = 'HTML';
	//	$form['introduction'] = 'Add a spot_rate.';
		$form['introduction'] = '';
		$form['submit'] = 'Add';
		$form['exclude'] = array();
		$form['values'] = $values;
		$form['hidden'] = $this->obj->get_values_as_array( get_class($this->obj) );
		return $form;
	}

	/**
	 * Get list of requests which match this concept
	 *
	 * @return array
	 *
	 * @access public
	 */
	public function get_possible_requests(){
		return array( 
			'explain_forward',
			'explain_par',
			'view_spotrates',
			'add_spot_rate',
			);
	}

	/**
	 * Get string to render delete and add buttons below the main output
	 *
	 * @return string
	 *
	 * @access public
	 */
	public function get_delete_add(){
		return  $this->get_delete_buttons() . $this->get_form_add_spot_rate()  ;
	}

	/**
	 * Get string to render delete buttons
	 *
	 * @return string
	 *
	 * @access public
	 */
	public function get_delete_buttons(){
		return parent::get_delete_buttons('view_spotrates');
	}


	/**
	 * Get summary solution (table of results which will provide anchors to detailed calculations)
	 *
	 * @return string
	 *
	 * @access public
	 */
	public function get_solution_no_detail(){
		$render = new CT1_Render();
		$rates = $this->obj->get_all_rates();
		$hidden = $this->obj->get_values_as_array( get_class($this->obj) );
		$link = "?page_id=" . $_GET['page_id'] . $render->get_link($hidden);
		for ( $i = 0, $ii = count( $rates['data'] ); $i < $ii; $i++ ){
			$f = $rates['objects'][$i]['CT1_Forward_Rate'];
			$p = $rates['objects'][$i]['CT1_Par_Yield'];
			if ( is_object( $f ) )
				$rates['data'][$i][3]  = $this->get_anchor_forward( $f, $link );
			if ( is_object( $p ) )
				$rates['data'][$i][5]  = $this->get_anchor_par( $p, $link );
		}
		return $render->get_table( $rates['data'], $rates['header'] );
	}

	/**
	 * Get anchor to detailed forward rate calculation
	 *
	 * @param CT1_Forward_Rate $f
	 * @param string $page_link
	 * @return string
	 *
	 * @access public
	 */
	private function get_anchor_forward( CT1_Forward_Rate $f, $page_link ){
		return "<a href='" . $page_link . "&request=explain_forward&forward_start_time=" . $f->get_start_time() . "&forward_end_time=" . $f->get_end_time() . "'>" . $f->get_i_effective() . "</a>";
	}

	/**
	 * Get anchor to detailed par yield calculation
	 *
	 * @param CT1_Par_Yield $p
	 * @param string $page_link
	 * @return string
	 *
	 * @access public
	 */
	private function get_anchor_par( CT1_Par_Yield $p, $page_link ){
		return "<a href='" . $page_link . "&request=explain_par&par_term=" . $p->get_term() . "'>" . $p->get_coupon() . "</a>";
	}

	private function get_explanation_par( $_INPUT ){
		if ( isset($_INPUT['par_term'])  ){
			$pys = $this->obj->get_par_yields();
			// find par yield
			if ( $pys->get_count()  > 0 ) {
				foreach ( $pys->get_objects() as $p ){
					if ($p->get_term() == $_INPUT['par_term'] ){
						$render = new CT1_Render();
						return $render->get_render_latex( $this->obj->explain_par_yield( $p ) ) . $this->get_solution_no_detail();
					}
				}
			}
		}
		return $this->get_solution_no_detail(); // default result if no matching par yield found
	}

	private function get_explanation_forward( $_INPUT ){
		if ( isset($_INPUT['forward_start_time']) && isset( $_INPUT['forward_end_time'])  ){
			$frs = $this->obj->get_forward_rates();
			// find forward rate
			if ( $frs->get_count()  > 0 ) {
				foreach ( $frs->get_objects() as $f ){
					if ($f->get_start_time() == $_INPUT['forward_start_time'] && $f->get_end_time() ==  $_INPUT['forward_end_time']){
						$render = new CT1_Render();
						return $render->get_render_latex( $this->obj->explain_forward_rate( $f ) ) . $this->get_solution_no_detail();
					}
				}
			}
		}
		return $this->get_solution_no_detail(); // default result if no matching forward rate found
	}

	private function add_spot_rate_from_input( $IN ){
		$i_effective = 0; $effective_time = 0;
		if ( isset( $IN['effective_time'] ) )
			$effective_time = (float)$IN['effective_time'];
		if ( isset( $IN['i_effective'] ) )
			$i_effective = (float)$IN['i_effective'];
		$sr = new CT1_Spot_Rate( $i_effective, $effective_time );
		$this->obj->add_object( $sr, false, true );
		return;
	}

	private function get_form_add_spot_rate(){
		$render = new CT1_Render();
		return $render->get_render_form( $this->get_add_spot_rate() );
	}
	
	private function set_spotrates( $_INPUT = array() ){
		try{
			$this->obj->set_from_input($_INPUT);
			sort( $this->obj );
			return true;
		} catch( Exception $e ){
			return false;
		}
	}

} // end of class
