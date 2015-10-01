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
		$_out = $m->get_controller($args) ;

//From https://www.mediawiki.org/wiki/QINU_fix
	$localParser = new Parser();
	$output = FinancialMathematicsHooks::renderRawHTML($localParser, $_out);
	return $output;
//		global $wgOut;
//		$wgOut->addHTML( $output);
}

// http://webcache.googleusercontent.com/search?q=cache:5lwjHlnXAmkJ:jimbojw.com/wiki/index.php%3Ftitle%3DRaw_HTML_Output_from_a_MediaWiki_Parser_Function
public static function renderRawHTML( &$parser, $input='' ) {
//    return $input;
    return array( $input, noparse => true, isHTML => true );
//    return $parser->insertStripItem( $input, $parser->mStripState );
}

}
