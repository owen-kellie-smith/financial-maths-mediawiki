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

	public static function getTagName(){
		return self::$tag_name;
	}

	// Hook our callback function into the parser
	public static function onParserFirstCallInit( Parser $parser ) {
		$parser->setHook( self::$tag_name, 'FinancialMathematicsHooks::fmRender' );
		return true;
	}

	public static function fmRender( $input, array $args, Parser $parser, PPFrame $frame ) {
		$_out = "";
//		global 	$wgResourceModules;
//		global 	$wgOut;
//		$wgOut->addModules( 'ext.FinancialMathematics' );
		$parser->getOutput()->addModules( 'ext.FinancialMathematics' );
		$xml=simplexml_load_string($input);
		if (!$xml){
				$_out .=  self::warning( wfMessage( 'fm-error-xml')->text() . print_r($input,1) );
		} else {
				//http://stackoverflow.com/questions/834875/recursive-cast-from-simplexmlobject-to-array
				$x = json_decode(json_encode((array) simplexml_load_string($input)), 1);
				$m = new CT1_Concept_All();
				$result = $m->get_controller($x) ;
				if (isset($result['warning'])){
					$_out .=  self::warning( $result['warning'] );
				} else {
					$u = array();
					if (isset($result['output']['unrendered'])){
						$u = $result['output']['unrendered'];
					}
					if (isset($u['formulae'])){
						$render = new CT1_Render();
						$_out .=  $render->get_render_latex($u['formulae']) ;
					}
      		if (isset($u['table']['schedule'])){
      			$_out .= $render->get_table(
      			$u['table']['schedule']['data'],
      			$u['table']['schedule']['header']);
      		}
				} // if (isset($result['warning']))
		} // if (!$xml)
		if (!isset($_out)){
				$_out .=  self::warning( wfMessage( 'fm-error-no-output')->text());
		}
		//From https://www.mediawiki.org/wiki/QINU_fix
		$localParser = new Parser();
		$output = self::renderRawHTML($localParser, $_out);
		return $output;
	}

	// http://webcache.googleusercontent.com/search?q=cache:5lwjHlnXAmkJ:jimbojw.com/wiki/index.php%3Ftitle%3DRaw_HTML_Output_from_a_MediaWiki_Parser_Function
	private static function renderRawHTML( &$parser, $input='' ) {
    		return array( $input, 'noparse' => true, 'isHTML' => true );
	}

	private static function warning( $message ){
				return "<span class='fin-math-warning'>" . $message  . "</span>";
	}
	
}
