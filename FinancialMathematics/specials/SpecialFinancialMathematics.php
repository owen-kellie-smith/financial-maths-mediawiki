<?php
/**
 * SpecialPage for FinancialMathematics extension
 * Hack of BoilerPlate
 *
 */

class SpecialFinancialMathematics extends SpecialPage {

	private $showUglyDebugMessagesOnRenderedPage=false;

	public function __construct() {
		parent::__construct( 'FinancialMathematics' );
	}

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
		$m = new FinMathConceptAll();
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
			$res = $render->get_rendered_result( $u, $this->getSkin()->getTitle()->getLinkUrl() );
			if (isset($res['formulae'])){
				$out->addHTML( $res['formulae'] );
			}
			if (isset($res['schedule'])){
            $out->addHTML( $res['schedule'] );
			}
			if (isset($res['table'])){
            $out->addHTML( $res['table'] );
			}
			if (isset($res['forms'])){
				foreach ($res['forms'] AS $_f){
						$out->addHTML( $_f ); 
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
