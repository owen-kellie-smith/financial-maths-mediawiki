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

	public static function onParserFirstCallInit( Parser $parser ) {
		$parser->setHook( self::$tag_name, 'FinancialMathematicsHooks::fmRender' );
		return true;
	}

	public static function fmRender( $input, array $args, Parser $parser, PPFrame $frame ) {
		global $wgOut;
		$title = $wgOut->getPageTitle();
		$parser->getOutput()->addModules( 'ext.FinancialMathematics' );
		// from http://webmasters.stackexchange.com/questions/86365/how-to-add-a-page-to-a-category-in-mediawiki-from-a-tag-extension
		$parser->getOutput()->addCategory( 'Pages_with_the_'. self::getTagName() . '_tag', $title /* sort key */ );
		$xml=simplexml_load_string($input);
		if (!$xml){
			$_out =  self::warning( wfMessage( 'fm-error-xml')->text() . print_r($input,1) );
		} else {
			$_out = self::outputResult( self::getResult( $xml ) );
		}
		//From https://www.mediawiki.org/wiki/QINU_fix
		$output = self::renderRawHTML($parser, $_out);
		return $output;
	}

	private static function getResult( $xml ){
		//http://stackoverflow.com/questions/834875/recursive-cast-from-simplexmlobject-to-array
		$_out = "";
		$xarray = json_decode(json_encode((array) $xml), 1);
		$m = new CT1_Concept_All();
		return $m->get_controller($xarray) ;
	}

	private static function outputResult( $result ){
		$_out = "";
		$render = new CT1_Render();
		if (isset($result['warning'])){
			$_out .=  self::warning( $result['warning'] );
		} else {
			if (isset($result['output']['unrendered'])){
				$u = $result['output']['unrendered'];
				if (isset($u['formulae'])){
					$_out .=  $render->get_render_latex($u['formulae']) ;
				}
   			if (isset($u['table']['schedule'])){
   				$_out .= $render->get_table(
    				$u['table']['schedule']['data'],
   					$u['table']['schedule']['header']);
   			}
			}
		} // if (isset($result['warning']))
		if (empty($_out)){
				$_out .=  self::warning( wfMessage( 'fm-error-no-output')->text());
		}
		return $_out;
	}

	// http://webcache.googleusercontent.com/search?q=cache:5lwjHlnXAmkJ:jimbojw.com/wiki/index.php%3Ftitle%3DRaw_HTML_Output_from_a_MediaWiki_Parser_Function
	private static function renderRawHTML( &$parser, $input='' ) {
    		return array( $input, 'noparse' => true, 'isHTML' => true );
	}

	private static function warning( $message ){
				return "<span class='fin-math-warning'>" . $message  . "</span>";
	}
	
}
