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
//////// TEMP ////// just return args
//	$out = "ARGS " . print_r($args,1);
	$xml=simplexml_load_string($input) or die("Error: Cannot create xml from " . print_r($input,1));
	//http://stackoverflow.com/questions/834875/recursive-cast-from-simplexmlobject-to-array
	$x = json_decode(json_encode((array) simplexml_load_string($input)), 1);
//	$out_in = htmlentities($xml->asXML());
//	$out .= "INPUT as xml" . "<pre>" . $out_in . "</pre>";
//	$out .= "INPUT as array" . "<pre>" . print_r($x,1) . "</pre>";
	$m = new CT1_Concept_All();
	$_out = $m->get_controller($x) ;
	//From https://www.mediawiki.org/wiki/QINU_fix
	$localParser = new Parser();
	$output = FinancialMathematicsHooks::renderRawHTML($localParser, $_out);
	return $output;
}

// http://webcache.googleusercontent.com/search?q=cache:5lwjHlnXAmkJ:jimbojw.com/wiki/index.php%3Ftitle%3DRaw_HTML_Output_from_a_MediaWiki_Parser_Function
public static function renderRawHTML( &$parser, $input='' ) {
    return array( $input, noparse => true, isHTML => true );
}

}
