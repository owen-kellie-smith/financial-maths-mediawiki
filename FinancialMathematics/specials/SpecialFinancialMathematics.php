<?php
/**
 * SpecialPage for FinancialMathematics extension
 * Hack of BoilerPlate
 *
 */

/**
 * ??? redundant paths?
 *
$path = dirname(dirname(__FILE__)) ;
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
require_once "FinancialMathematics.hooks.php";
$path = dirname(dirname(__FILE__)) . "/PEAR";
set_include_path(get_include_path() . PATH_SEPARATOR . $path);
 *
 *
**/

class SpecialFinancialMathematics extends SpecialPage {
	public function __construct() {
		parent::__construct( 'FinancialMathematics' );
	}

	private $showUglyDebugMessagesOnRenderedPage=false;

	/**
	 * Show the page to the user
	 *
	 * @param string $sub The subpage string argument (if any).
	 *  [[Special:HelloWorld/subpage]].
	 */
	public function execute( $sub ) {
		$result = $this->getResult();
		$out = $this->getOutput();
		$this->outputGreetings( $out );
		$this->outputDebugMessagesIfRequired( $out, $result );
		$this->outputResult( $out, $result );
	}

	protected function getGroupName() {
		return 'other';
	}

	private function getResult(){
		$m = new CT1_Concept_All();
		$m->setTagName( FinancialMathematicsHooks::getTagName() );
		return $m->get_controller($this->getRequest()->getQueryValues()) ; 
	}

	private function outputGreetings( &$out ){
		$out->setPageTitle( $this->msg( 'financialmathematics-helloworld' ) );
		$out->addWikiMsg( 'financialmathematics-helloworld-intro' );
		$out->addHTML( $this->restartForm() );
	}

	private function outputDebugMessagesIfRequired( &$out, $result ){
		if ($this->showUglyDebugMessagesOnRenderedPage){
			$out->addHTML( "getQueryVaues is <pre> " . print_r($this->getRequest()->getQueryValues(), 1) . "</pre>" );
			if (isset($result['output']['unrendered']['table'])){
				$out->addHTML( "result output unrendered table is <pre> " . print_r($result['output']['unrendered']['table'], 1) . "</pre>" );
			}
		}
	}

	private function outputResult( &$out, $result ){
		$render = new CT1_Render();
		if (isset($result['warning'])){
			$out->addHTML( "<span class='fin-math-warning'>" . $result['warning'] . "</span>");
		} else {
			$u = array();
			if (isset($result['output']['unrendered'])){
				$u = $result['output']['unrendered'];
			}
			if (isset($u['formulae'])){
				$out->addHTML( $render->get_render_latex($u['formulae']) );
			}
			if (isset($u['table'])){
                               if (isset($u['table']['schedule'])){
                                       $out->addHTML( $render->get_table(
                                       $u['table']['schedule']['data'],
                                       $u['table']['schedule']['header']
                                       ));
                               }
				$out->addHTML( $render->get_render_rate_table(
					$u['table']['rates'],
					$u['table']['hidden'],
					$this->getSkin()->getTitle()->getLinkUrl() . "?" ));    
			}
			if (isset($u['forms'])){
				foreach ($u['forms'] AS $_f){
					try{	
						$out->addHTML( $render->get_render_form($_f['content'], $_f['type'] )); 
					} catch( Exception $e ){
						$out->addHTML( $e->getMessage() );
					}
				}
			}
			if (isset($u['xml-form']['forms'])){
				foreach ($u['xml-form']['forms'] AS $_f){
					$_f['content']['render']='HTML';
					$out->addHTML( $render->get_render_form($_f['content'], $_f['type'] ));
				}
			}
		}
		return;
	}

	private function restartForm(){
		$_restart_label = wfMessage( 'fm-restart')->text();
		return '<form action="" method=GET><input type="submit" value="' . $_restart_label . '"></form>' ;
	}

}
