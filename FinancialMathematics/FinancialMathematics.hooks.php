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
		$parser->getOutput()->addModules( 'ext.FinancialMathematics' );
		// from http://webmasters.stackexchange.com/questions/86365/how-to-add-a-page-to-a-category-in-mediawiki-from-a-tag-extension
		$xml=simplexml_load_string($input);
		if (!$xml){
			$_out =  self::warning( wfMessage( 'fm-error-xml')->text() . print_r($input,1) );
		} else {
			$result = self::getResult( $xml );
			$_out = self::outputResult( $result );
			self::addCategories( $parser, $result );
		}
		//From https://www.mediawiki.org/wiki/QINU_fix
		$output = self::renderRawHTML($parser, $_out);
		return $output;
	}

  private static function addCategories( &$parser, $result ){
			$generalCategory =  str_replace(' ', '_', wfMessage( 'fm-category-general', self::getTagName() )->text() );
		  global $wgOut;
		  $title = $wgOut->getPageTitle();
			$parser->getOutput()->addCategory( $generalCategory , $title /* sort key */ );
			if (isset($result['concept'])){
				$temp = array_values( $result['concept']);
				if (isset($temp[0])){
					$tagCategory =  str_replace(' ', '_', wfMessage( 'fm-category-specific', $temp[0] )->text() );
					$parser->getOutput()->addCategory( $tagCategory , $title /* sort key */ );
				}
			}
  }

	private static function getResult( $xml ){
		//http://stackoverflow.com/questions/834875/recursive-cast-from-simplexmlobject-to-array
		$_out = "";
		$xarray = json_decode(json_encode((array) $xml), 1);
		$m = new FinMathConceptAll();
		return $m->get_controller($xarray) ;
	}

	private static function outputResult( $result ){
		$_out = "";
		$render = new CT1_Render();
		if (isset($result['warning'])){
			$_out .=  self::warning( $result['warning'] );
		} else {
			$u = array();
			if (isset($result['output']['unrendered'])){
				$u = $result['output']['unrendered'];
			}
			$res = $render->get_rendered_result( $u, 'unusedPageTitle' );
			if (isset($res['formulae'])){
				$_out .=  $res['formulae'] ;
			}
			if (isset($res['schedule'])){
            $_out .=  $res['schedule'] ;
			}
			if (isset($res['table'])){
//            $out->addHTML( $res['table'] );
			}
			if (isset($res['forms'])){
				foreach ($res['forms'] AS $_f){
//						$out->addHTML( $_f ); 
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
