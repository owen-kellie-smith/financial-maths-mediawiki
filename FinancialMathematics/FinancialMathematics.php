<?php


$wgHooks['ParserFirstCallInit'][] = 'wfSampleParserInit';

// Hook our callback function into the parser
function wfSampleParserInit( Parser $parser ) {
	// When the parser sees the <sample> tag, it executes 
	// the wfSampleRender function (see below)
	$parser->setHook( 'sample', 'wfSampleRender' );
        // Always return true from this function. The return value does not denote
        // success or otherwise have meaning - it just must always be true.
	return true;
}

// Execute 
function wfSampleRender( $input, array $args, Parser $parser, PPFrame $frame ) {
	// Nothing exciting here, just escape the user-provided
	// input and throw it back out again
	return htmlspecialchars( $input );
}


$path = __DIR__ . "/PEAR/HTML/QuickForm2" ;
$path = __DIR__ . "/PEAR/HTML/Table" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

if ( function_exists( 'wfLoadExtension' ) ) {
	wfLoadExtension( 'FinancialMathematics' );
	$wgMessagesDirs['FinancialMathematics'] = __DIR__ . '/i18n';
	$wgExtensionMessagesFiles['FinancialMathematicsAlias'] = __DIR__ . '/FinancialMathematics.i18n.alias.php';
	wfWarn(
		'Deprecated PHP entry point used for FinancialMathematics extension. Please use wfLoadExtension ' .
		'instead, see https://www.mediawiki.org/wiki/Extension_registration for more details.'
	);
	return true;
} else {
	die( 'This version of the FinancialMathematics extension requires MediaWiki 1.25+' );
}

/* Based on BoilerPlate extension */
