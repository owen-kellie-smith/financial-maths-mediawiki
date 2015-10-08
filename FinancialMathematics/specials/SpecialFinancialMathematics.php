<?php
/**
 * HelloWorld SpecialPage for FinancialMathematics extension
 * Hack of BoilerPlate
 *
 */

$path = dirname(dirname(__FILE__)) ;
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
require_once "FinancialMathematics.hooks.php";


$path = dirname(dirname(__FILE__)) . "/PEAR";
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
		$m->setTagName( FinancialMathematicsHooks::getTagName() );
		$result = $m->get_controller($_GET) ;
		if (isset($result['warning'])){
			$out->addHTML( "<span class='fin-math-warning'>" . $result['warning'] . "</span>");
		}else{
			if (isset($result['formulae'])){
				$out->addHTML( $result['formulae'] );
			}
			if (isset($result['table'])){
				$out->addHTML( $result['table'] );
			}
			if (isset($result['form'])){
				$out->addHTML( $result['form'] );
			}
			if (isset($result['xml-form']['form'])){
				$out->addHTML( $result['xml-form']['form'] );
			}
		}
	}

	protected function getGroupName() {
		return 'other';
	}
}
