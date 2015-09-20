<?php   

require_once 'interface-ct1-concept.php';
//require_once 'functions.php';
//CT1_autoloader('HTML_QuickForm2','HTML/QuickForm2.php');

abstract class CT1_Form implements CT1_Concept {

protected $obj;
protected $request;


public function get_delete_buttons( $request = ""){
	$out = "";
	if ( $this->obj instanceof CT1_Collection ){
		if ( $this->obj->get_count() > 0 ){
			$render = new CT1_Render();
			$cfs = $this->obj->get_objects();
			foreach ( $this->obj->get_objects() as $o ) {
				if (!method_exists( $this->obj, 'get_clone_this' ))
					throw new Exception('get_clone_this method not defined for ' . get_class( $this->obj ) . " in " . __FILE__ );
				$clone = $this->obj->get_clone_this();
//				$label = $o->get_label() ;
				$label = "";
				$clone->remove_object($o);
				$button = $render->get_form_collection( $clone, 'Delete '.  $o->get_label() ,'', $request );
				$out .= $label . $button;
			}
		}
	}
	return $out;
}




public function __construct(CT1_Object $obj){
	$this->set_obj($obj);
}

public function set_obj(CT1_Object $obj){
	$this->obj = $obj;
}

public function get_solution(){
	return;
}

public function get_request(){
	return $this->request;
}

public function get_possible_requests(){
	return array( $this->request );
}

protected function set_request($s){
	$this->request = $s;
}


public function get_calculator( $parameters) {
		// returns associative array which can be passed to a form renderer e.g. QuickForm2
	$p = $parameters;
	$this->get_form_parameters($p);
	$return = array();
	$return['method'] = $p['method'];
	$return['parameters'] = $this->obj->get_parameters();
	$return['valid_options'] = $this->obj->get_valid_options();
	$return['values'] = $this->obj->get_values();
	$return['request'] = $p['request'];
	$return['submit'] = $p['submit'];
	$return['type'] = $p['type'];
	$return['special_input'] = $p['special_input'];
	$return['action'] = $p['action'];
	$return['exclude'] = $p['exclude'];
	$return['render'] = $p['render'];
	$return['introduction'] = $p['introduction'];
	return $return;			
}

private function current_page(){
	if (isset($_GET['page_id'])) return $_GET['page_id'];
}

private function hidden_page(){
	return "<input type='hidden' name='page_id' value='" .$this->current_page() . "' />" . "\r\n";
}

protected function set_received_input(&$_INPUT = array()){
	foreach (array_keys($this->obj->get_parameters()) as $p){
		if (!isset($_INPUT[$p])) $_INPUT[$p] = NULL;
	}
}

protected function get_form_parameters(&$_parameters = array()){
	$def = $this->get_form_parameters_default();
	foreach (array_keys($def) as $p){
		if (!array_key_exists($p, $_parameters)) $_parameters[ $p] = $def[$p];
	}
}

protected function get_form_parameters_default(){
	return array( 'exclude' =>array(), 
		'request'=> '',
		'submit'=>"Submit",
		'type'=>'', 
		'special_input'=>'',
		'action'=>'', 
		'method'=>'GET', 
		'render'=>'HTML', 
		'introduction'=>'', 
		);
}

}



