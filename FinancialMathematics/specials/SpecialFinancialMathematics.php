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
//		$out->addHTML( "GET array is <pre> " . print_r($this->getRequest()->getRawInput(), 1) . "</pre>" );
		$out->addHTML( $_restart );
		$m = new CT1_Concept_All();
		$m->setTagName( FinancialMathematicsHooks::getTagName() );
		$result = $m->get_controller($this->getRequest()->getRawInput()) ; 
//		$out->addHTML( "result output unredndered formulae is <pre> " . print_r($result['output']['unrendered']['formulae'], 1) . "</pre>" );
//		$out->addHTML( "result output unredndered table is <pre> " . print_r($result['output']['unrendered']['table'], 1) . "</pre>" );
		$render = new CT1_Render();
		if (isset($result['warning'])){
			$out->addHTML( "<span class='fin-math-warning'>" . $result['warning'] . "</span>");
		}else{
			if (isset($result['output']['unrendered']['formulae'])){
				$out->addHTML( $render->get_render_latex($result['output']['unrendered']['formulae']) );
			}
//			if (isset($result['formulae'])){
//				$out->addHTML( $result['formulae'] );
//			}
//			if (isset($result['table'])){
//				$out->addHTML( $result['table'] );
//			}
			if (isset($result['output']['unrendered']['table'])){
//					$out->addHTML( "table  FROM unrendered <pre>" . print_r($result['output']['unrendered']['table'],1) . "</pre>");
				$out->addHTML( $render->get_render_rate_table(
					$result['output']['unrendered']['table']['rates'],
					$result['output']['unrendered']['table']['hidden'],
					$this->getSkin()->getTitle()->getLinkUrl() . "?" ));    
			}
//			if (isset($result['form'])){
//				$out->addHTML( $result['form'] );
//			}
			if (isset($result['output']['unrendered']['forms'])){
				foreach ($result['output']['unrendered']['forms'] AS $_f){
//					$_f['content']['render']='HTML';
//					$out->addHTML( "FORM FROM unrendered <pre>" . print_r($_f,1) . "</pre>");

					try{	$out->addHTML( $render->get_render_form($_f['content'], $_f['type'] )); 
					} catch( Exception $e ){
								$out->addHTML( $e->getMessage() );
					}

				}
			}
//			if (isset($result['xml-form']['form'])){
//				$out->addHTML( $result['xml-form']['form'] );
//			}

			if (isset($result['output']['unrendered']['xml-form']['forms'])){
//		$out->addHTML( "result output unredndered xml-form forms 0 array is <pre> " . print_r($result['output']['unrendered']['xml-form']['forms'], 1) . "</pre>" );
				foreach ($result['output']['unrendered']['xml-form']['forms'] AS $_f){
					$_f['content']['render']='HTML';
					$out->addHTML( $render->get_render_form($_f['content'], $_f['type'] ));
				}
			}
		}
	}

	protected function getGroupName() {
		return 'other';
	}
}
