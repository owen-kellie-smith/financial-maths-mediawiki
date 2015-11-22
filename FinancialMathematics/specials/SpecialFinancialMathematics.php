<?php
/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2015 Owen Kellie-Smith
 * 
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */
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
			$out->addHTML( "getQueryVaues is <pre> " . 
				print_r($this->getRequest()->getQueryValues(), 1) . "</pre>" 
			);
			if (isset($result['output']['unrendered']['table'])){
				$out->addHTML( "result output unrendered table is <pre> " . 
					print_r($result['output']['unrendered']['table'], 1) . "</pre>" 
				);
			}
		}
	}

	private function outputResult( &$out, $result ){
		$render = new FinMathRender();
		if (isset($result['warning'])){
			$out->addHTML( "<span class='fin-math-warning'>" . 
				$result['warning'] . "</span>"
			);
		} else {
			$u = array();
			if (isset($result['output']['unrendered'])){
				$u = $result['output']['unrendered'];
			}
			$res = $render->get_rendered_result( 
				$u, $this->getSkin()->getTitle()->getLinkUrl() 
			);
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
		return '<form action="" method=GET><input type="submit" value="' . 
			$_restart_label . '"></form>' ;
	}

}
