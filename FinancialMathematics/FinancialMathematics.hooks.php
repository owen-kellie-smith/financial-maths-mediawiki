<?php
/**
 * Hooks for FinancialMathematics extension
 *
 * based on the Charinsert extension
 */

$path = __DIR__ . "/pear" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

class FinancialMathematicsHooks {
	
// Hook our callback function into the parser
public static function onParserFirstCallInit( Parser $parser ) {
	$parser->setHook( 'fin-math', 'FinancialMathematicsHooks::fmRender' );
	return true;
}

public static function fmRender( $input, array $args, Parser $parser, PPFrame $frame ) {
		$m = new CT1_Concept_All();
		$output .= $m->get_controller($args) ;
		global $wgOut;
		$wgOut->addHTML( $output);
}

}
