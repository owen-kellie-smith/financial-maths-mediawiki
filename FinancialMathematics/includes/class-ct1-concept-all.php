<?php

/**
 * CT1_Concept_All class
 *
 * @package    CT1
 * @author     Owen Kellie-Smith
 */
class CT1_Concept_All extends CT1_Form{

	private $concepts;

  private $messages;

  private $tag_name="dummy_tag_set_in_ct1_concept_all";

  public function getTagName(){
		return $this->tag_name;
  }

  public function setTagName( $s ){
		$this->tag_name = $s;
  }


	public function __construct(CT1_Object $obj=null){
//		$this->set_concepts();
	}

	private function get_concept_labels(){
		$return = array();
		foreach( $this->candidate_concepts() AS $c ){
				$return = array_merge($return, $c->get_concept_label());
		}
//echo __FILE__ . " get_concept_labels " . print_r($return,1);
		return $return;
	}

	public function get_calculator( $unused ){
		$p = array('method'=> 'GET', 'submit'=>self::myMessage(  'fm-get-calculator') , self::myMessage(  'fm-select-calculator'));
		$p['select-options'] = $this->get_concept_labels() ;
		$p['select-name'] = 'concept';
		$p['select-label'] = self::myMessage(  'fm-select-calculator');
		return $p;
	}

	private function get_parameters($_INPUT){
		return; 
//		return print_r($_INPUT,1);
	}

	public function get_controller($_INPUT ){
    $return = $this->get_controller_no_xml($_INPUT );
//echo __FILE__ . " get_controller return return1 " . print_r($return,1);
		if (isset($return['formulae'])){
			$c = new CT1_Form_XML();
			$c->setTagName( $this->getTagName() );
			$temp = $c->get_controller( $_INPUT );
			$return['xml-form'] = $c->get_controller( $_INPUT );
//echo __FILE__ . " get_controller return temp " . print_r($temp,1);
		  if (isset($temp['output']['unrendered'])){
				$return['output']['unrendered']['xml-form'] = $temp['output']['unrendered'];
			}
		}
//echo __FILE__ . " get_controller return " . print_r($return,1);
		return $return;
	}
	
	public function get_controller_no_xml($_INPUT ){
	$return['arrayInput']=$_INPUT;
	try{
		foreach( $this->candidate_concepts() AS $c ){
				if (isset($_INPUT['request'])){
					if ($c->get_request() == $_INPUT['request'] ){
						$return = array_merge($return, $c->get_controller( $_INPUT ));
						return $return;
					}
					if ( in_array( $_INPUT['request'], $c->get_possible_requests() ) ){
						$return = array_merge($return, $c->get_controller( $_INPUT ));
						return $return;
					}
				} // if (isset($_INPUT['request']))
				if (isset($_INPUT['concept'])){
					$temp = $c->get_concept_label();
					if ( isset( $temp[ $_INPUT['concept'] ] ) ){
						$return = array_merge($return, $c->get_controller( $_INPUT ));
						return $return;
					}
				}
			} //foreach( $this->candidate_concepts() AS $c )
		$return['output']['unrendered']['forms'][] = array(
			'content'=> $this->get_calculator( NULL ),
			'type'=>  'select'
		);
		return $return;
	}
	catch( Exception $e ){
		$return['warning']=$e->getMessage();
		return $return;
	}
}

} // end of class
