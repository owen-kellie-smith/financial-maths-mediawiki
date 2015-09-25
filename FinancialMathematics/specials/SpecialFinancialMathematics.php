<?php
/**
 * HelloWorld SpecialPage for FinancialMathematics extension
 * Hack of BoilerPlate
 *
 * @file
 * @ingroup Extensions
 */


//$path = dirname(dirname(__FILE__)) . "/pear/HTML/QuickForm2/";
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

		$m = new CT1_Concept_All();
		$out->addHTML( $m->get_controller($_GET) );
	}

	protected function getGroupName() {
		return 'other';
	}
}
