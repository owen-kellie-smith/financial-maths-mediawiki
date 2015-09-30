<?php

$path = __DIR__ . "/pear/HTML/QuickForm2" ;
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
