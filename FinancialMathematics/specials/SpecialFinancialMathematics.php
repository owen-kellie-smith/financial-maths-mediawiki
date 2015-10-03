<?php
/**
 * HelloWorld SpecialPage for FinancialMathematics extension
 * Hack of BoilerPlate
 *
 */


$path = dirname(dirname(__FILE__)) . "/pear";
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
class SpecialFinancialMathematics extends SpecialPage {
	public function __construct() {
		parent::__construct( 'FinancialMathematics' );
	}

	/**
	 * Show the page to the user
	 *
	 * @param string $sub The subpage string argument (if any).
	 *  [[Special:HelloWorld/subpage]].
	 */
	public function execute( $sub ) {
		$out = $this->getOutput();
		$out->setPageTitle( $this->msg( 'financialmathematics-helloworld' ) );
		$out->addWikiMsg( 'financialmathematics-helloworld-intro' );
		$_restart_label = wfMessage( 'fm-restart')->text();
		$_restart = '<form action="" method=GET><input type="submit" value="' . $_restart_label . '"></form>' ;
		$out->addHTML( $_restart );
		$m = new CT1_Concept_All();
		$out->addHTML( $m->get_controller($_GET) );
		// creating object of SimpleXMLElement
		$xml_data = new SimpleXMLElement('<?xml version="1.0"?><parameters></parameters>');
		$this->array_to_xml($_GET,$xml_data);
		$result = print_r("Input for fin-math tag is " . htmlentities($xml_data->asXML()),1);
		$out->addHTML( $result );

	}

	// http://stackoverflow.com/questions/1397036/how-to-convert-array-to-simplexml
	// function defination to convert array to xml
	private function array_to_xml( $data, &$xml_data ) {
    foreach( $data as $key => $value ) {
        if( is_array($value) ) {
            if( is_numeric($key) ){
                $key = 'item'.$key; //dealing with <0/>..<n/> issues
            }
            $subnode = $xml_data->addChild($key);
            $this->array_to_xml($value, $subnode);
        } else {
            $xml_data->addChild("$key",htmlspecialchars("$value"));
        }
     }
	}

	protected function getGroupName() {
		return 'other';
	}
}
