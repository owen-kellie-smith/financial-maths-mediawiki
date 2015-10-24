<?php
/**
 * Hooks for FinancialMathematics extension
 *
 * based on the Charinsert extension
 */

$path = __DIR__ . "/PEAR" ;
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

class FinancialMathematicsHooks {
	
	private static $tag_name='fin-math';
	private static $to_return = array(
		'form'=>false,
		'warning'=>true,
		'XMLinput'=>false,
		'script'=>true,
		'formulae'=>true,
		'value'=>true,
		'symbol'=>true);

	public static function getTagName(){
		return self::$tag_name;
	}

	// Hook our callback function into the parser
	public static function onParserFirstCallInit( Parser $parser ) {
		$parser->setHook( self::$tag_name, 'FinancialMathematicsHooks::fmRender' );
		return true;
	}

	public static function fmRender( $input, array $args, Parser $parser, PPFrame $frame ) {
		global 	$wgResourceModules;
		global 	$wgOut;
		$wgOut->addModules( 'ext.FinancialMathematics' );
		$parser->getOutput()->addModules( 'ext.FinancialMathematics' );
		$xml=simplexml_load_string($input);
		if (!$xml){
			$_out =  "<span class='fin-math-warning'>" . wfMessage( 'fm-error-xml')->text() . print_r($input,1) . "</span>";
		}
		if ($xml){
			//http://stackoverflow.com/questions/834875/recursive-cast-from-simplexmlobject-to-array
			$x = json_decode(json_encode((array) simplexml_load_string($input)), 1);
			$m = new CT1_Concept_All();
			$result = $m->get_controller($x) ;
			if (isset($result['warning'])){
				$_out =  "<span class='fin-math-warning'>" . $result['warning'] . "</span>";
			}else{
				if (isset($result['output']['unrendered']['formulae'])){
					$render = new CT1_Render();
					$_out =  $render->get_render_latex($result['output']['unrendered']['formulae']) ;
				}
			}
		}
		if (!isset($_out)){
				$_out =  "<span class='fin-math-warning'>" . wfMessage( 'fm-error-no-output')->text()  . "</span>";
		}
		//From https://www.mediawiki.org/wiki/QINU_fix
		$localParser = new Parser();
		$output = FinancialMathematicsHooks::renderRawHTML($localParser, $_out);
		return $output;
	}

	// http://webcache.googleusercontent.com/search?q=cache:5lwjHlnXAmkJ:jimbojw.com/wiki/index.php%3Ftitle%3DRaw_HTML_Output_from_a_MediaWiki_Parser_Function
	public static function renderRawHTML( &$parser, $input='' ) {
    		return array( $input, 'noparse' => true, 'isHTML' => true );
	}

}
