<?php   


define("CT1_MAXIMUM_LEVELS_DETAIL", 10);

/**
 * CT1_Render class
 *
 * @package    CT1
 * @author     Owen Kellie-Smith
 */
class CT1_Render  {

	/**
	 * @param integer $eqref   Equation counter
	 *
	 * @access private
	 */
	private $eqref = 0;

public function __construct(CT1_Object $obj=null){
	$this->eqref=(int)mt_rand();
}

	/**
	 * Get rendering of form as string that can be echoed to screen
	 *
	 * @param array $form form parameters
	 * @return string
	 *
	 * @access public
	 */
	public function get_render_form( $form ){
		if ('HTML'==$form['render'] ){
			return $this->get_form_html( $form );
		} else {
			return $this->get_form_plain( $form );
		}
	}

	/**
	 * Get string of &,= pairs suitable for writing a URL that can ge read via $_GET
	 *
	 * @param array $hidden list of hidden fields
	 * @return string
	 *
	 * @access public
	 */
	public function get_link( $hidden ){
		$out = "";
		foreach(array_keys( $hidden) as $key ){
			$value = $hidden[$key];
			$out .= "&" . $key . "=" . $value;
		}
		return $out;
	}

	/**
	 * Get rendering of form as string.  Form includes as hidden fields all the features of a collection
	 *
	 * @param CT1_Collection $cf collection of objects
	 * @param string $submit text of submit button
	 * @param string $intro text of sentence (if any) to put at top of form.
	 * @param string $request value to include as hidden field of form (which passes form's main commend)
	 * @return string
	 *
	 * @access public
	 */
	public function get_form_collection( CT1_Collection $cf, $submit = 'Submit', $intro = "" , $request = ""){
		$out = "";
		if ( !empty( $intro ) )
			$out.= "<p>" . $intro . "</p>" . "\r\n";
		$form = new HTML_QuickForm2( 'name-for-collection','GET', '');
		$form->addDataSource(new HTML_QuickForm2_DataSource_Array() );
		$fieldset = $form->addElement('fieldset');
		$hidden = $cf->get_values_as_array(  get_class( $cf ) );
		$this->add_hidden_fields_to_fieldset( $fieldset, $hidden );
		$fieldset->addElement('hidden', 'request')->setValue( $request );
		if (isset($_GET['page_id'])){
			$fieldset->addElement('hidden', 'page_id')->setValue($_GET['page_id']);
		}
		$fieldset->addElement('submit', null, array('value' => $submit));
		$out.= $form;
		return $out;
	}

	/**
	 * Get latex of equation plus array of equation details(which can be used to write new latex)
	 *
	 * @param array $equation_array
	 * @return array
	 *
	 * @access public
	 */
	public function get_render_latex( $equation_array, $newline=false ){
	// would be better if this were just recursive but I don't know how
		$out = "";
		$_nl="";
		if ($newline) $_nl="\r\n ";
		if (count($equation_array) > 0 ) {
			$out  = $this->get_mathjax_header() .  $_nl;
			$out .= $_nl . "\\begin{align} " . $_nl;
			$output = $this->get_render_latex_sentence( $equation_array );
			$out .= $output['output'] . $_nl;
			$count_levels = 1;
			while ( $count_levels < CT1_MAXIMUM_LEVELS_DETAIL && isset( $output['detail'] ) ) {
				$count_levels++;
				if ( 0 < count( $output['detail'] ) ){
					$new_detail = $this->get_a_layer_of_equation_detail( $output, $newline );
					$output['detail'] = $new_detail['detail'];
					$out .= $new_detail['out_new'];
				}
			}
			$out .= $_nl . "\\end{align} " . $_nl . $_nl;
		}
		return $out;
	}


	/**
	 * Get HTML table (as string that can be echoed)
	 *
	 * @param array $row_data (to appear in rows of table)
	 * @param array $column_headers (to appear in head of table
	 * @return string
	 *
	 * @access public
	 */
	public function get_table( $row_data, $column_headers ){
		// see http://pear.php.net/manual/en/package.html.html-table.intro.php
		$table = new HTML_Table();
		$table->setAutoGrow(true);
		$table->setAutoFill('n/a');
		for ( $nr = 0, $maxr = count( $row_data ); $nr < $maxr; $nr++ ){
			for ($i =0, $ii = count( $column_headers ); $i < $ii; $i++ ){
				if ('' != $row_data[$nr][$i] ){
					$table->setCellContents( $nr+1, $i, $row_data[$nr][$i] );
				}
			}
		}
		for ($i =0, $ii = count( $column_headers ); $i < $ii; $i++ ){
			$table->setHeaderContents(0, $i , $column_headers[ $i ]);
		}
		$header_attribute = array( 'class' => 'header' );
		$table->setRowAttributes(0, $header_attribute, true);
		$table->setColAttributes(0, $header_attribute);
		$altRow = array( 'class' => 'alt_row' );
		$table->altRowAttributes( 1, null, $altRow );
		return $table->toHtml();
	}

	/**
	 * Get (in form of string) html for form which has just one <select> element
	 *
	 * @param array $return form features
	 * @return string
	 *
	 * @access public
	 */
	public function get_select_form( $return ){
		$out="";
		if ( !empty( $return['introduction'] ) )
			$out = "<p>" . $return['introduction'] . "</p>" . "\r\n";
		foreach (array('name','method','action','select-name','select-label','select-options','submit') as $key){
			$temp[$key]='';
			if ( isset( $return[$key] ) ){
				$temp[$key] = $return[$key];
			}
		}
		$return = $temp;
			$form = new HTML_QuickForm2($return['name'],$return['method'], $return['action']);
			$fieldset = $form->addElement('fieldset');
			$calculator = $fieldset->addSelect( $return['select-name'] )
				->setLabel( $return['select-label'] )
				->loadOptions( $return['select-options']);
			$temp_page_id='';
			if( isset($_GET['page_id'])){
				$temp_page_id = $_GET['page_id'];
			}
			$fieldset->addElement('hidden', 'page_id')->setValue($temp_page_id);
			$fieldset->addElement('submit', null, array('value' => $return['submit']));
			$out.= $form;
		return $out;
	}

	protected static function myMessage( $messageKey){
			$m = $messageKey;
			if ( function_exists('wfMessage') ){
				$m=wfMessage( $messageKey)->text();
			}
			return $m;
  }
			
	/**
	 * Get rendering of one latex sentence and return an array of its details
	 *
	 * @param array $equation_array
	 * @param string $label
	 * @return array
	 *
	 * @access private
	 */
	private function get_render_latex_sentence( $equation_array, &$label = '', $newline=false ){
		$_nl="";
		if ($newline) $_nl="\r\n ";
		$out = "";
		$detail = array();
		if ( !empty($label) ){
			$out.= "\\label{eq:" . $label . "} ";
		}
		$d = 1;
		foreach ($equation_array as $e) {
			$out .= $this->get_render_latex_sentence_line( $e, $detail, $newline );
			if ($d < count($equation_array)) 
				$out.= " \\\\ " . $_nl;
			if ($d ==count($equation_array)) 
				$out.= ". \\\\ " . $_nl . "\\nonumber " ;
			$d++;
			$this->eqref++;
		}
		return array('output'=>$out, 'detail'=>$detail);
	}

	/**
	 * Get rendering of one latex sentence line and return an array of its details
	 *
	 * @param array $e
	 * @param array $detail
	 * @return array
	 *
	 * @access private
	 */
	private function get_render_latex_sentence_line( $e, &$detail ){
		$out = "";
		if (array_key_exists('left', $e)){
			$out.= $e['left'] . " & ";
		} else {
			$out.= " & ";
		}
		if (array_key_exists('right', $e)){
			if ( is_array( $e['right'] ) ){
				if (array_key_exists('summary', $e['right'])){
					if (array_key_exists('relation', $e['right']) ){
						$out .= " " . $e['right']['relation'] . " " . $e['right']['summary'] ;
					} else {
						$out.= " = " . $e['right']['summary'] ;
					}
					if (array_key_exists('detail', $e['right'])){
						$out .= $this->get_render_latex_sentence_detail( $e, $detail);
					}
				}
			} else {
				$out.= " = " . $e['right'] ;
			}
		}
		return $out;
	}
	/**
	 * Get rendering of one latex sentence and return an array of its details
	 *
	 * @param array $e
	 * @param array $detail
	 * @return array
	 *
	 * @access private
	 */
	private function get_render_latex_sentence_detail( $e, &$detail ){
		$out = "";
		if ( $this->is_sentence( $e['right']['detail'] ) ) {
			$out .= " \\mbox{ ".self::myMessage( 'fm-by')." \\eqref{eq:" . $this->eqref . "}}";
			$detail[] = array(
				'equation' => $e['right']['detail'],
				'label' => $this->eqref,
				);
		} else {
			$count_refs = count( $e['right']['detail'] );
			$eqlist = "";
			for ($subeq = 0; $subeq < ($count_refs - 1); $subeq++){
				$eqlist.= "\\eqref{eq:" . $this->eqref . "." . $subeq . "}, ";
				$detail[] = array(
					'equation' => $e['right']['detail'][$subeq],
					'label' => $this->eqref . "." . $subeq,
					);
			}
			$eqlist.= "\\eqref{eq:" . $this->eqref . "." . ($count_refs-1) . "}";
			$detail[] = array(
				'equation' => $e['right']['detail'][$count_refs-1],
				'label' => $this->eqref . "." . ($count_refs-1),
				);
			$out .= " \\mbox{ ".self::myMessage( 'fm-by') ." " . $eqlist . "}";
		}
		return $out;
	}

	/**
	 * Get latex of layer of detail plus array of further details(which can be used to write new latex)
	 *
	 * @param array $output
	 * @return array
	 *
	 * @access private
	 */
	private function get_a_layer_of_equation_detail( $output, $newline=false ){
	// $sub_count is redundant?
	$_nl="";
	if ($newline) $_nl="\r\n ";
		$out = '';
		$ret_out = array();
		$ret_out['detail'] = array();
		foreach ($output['detail'] as $e) {
			if ( $this->is_sentence( $e['equation'] ) ) {
				$sub_output = $this->get_render_latex_sentence( $e['equation'], $e['label'] , $newline);
				$out .= " \\\\" . $_nl; // close off the last line
				$out .= $sub_output['output'] . $_nl;
				$ret_out['detail'] = array_merge( $ret_out['detail'], $sub_output['detail']);
			} else {
				$sub_count = 0;
				foreach ($e['equation'] as $e_detail) {
					$sub_count++;
					$sub_output = $this->get_render_latex_sentence( $e_detail, $e['label'] . "." . $sub_count, $newline );
					$out .= " \\\\" . "\r\n"; // close off the last line
					$out .= $sub_output['output'] . "\r\n";
					$ret_out['detail'] = array_merge( $ret_out['detail'], $sub_output['detail']);
				}
			}
		}
		$output['detail'] = $ret_out['detail'];
		return array( 'detail'=> $output['detail'], 'out_new'=> $out );
	}



	/**
	 * Get whether equation is a sentence (and so needs a full stop after it)
	 *
	 * @param array $e equation array
	 * @return boolean
	 *
	 * @access private
	 */
	private function is_sentence( $e ){
		if ( is_array( $e ) ){
			if ( count($e) > 0 ){
				if ( is_array( $e[0] ) ){
					if( isset( $e[0]['left'] ) || isset( $e[0]['right'] ) ){
						return true;
					}
				}
			}
		}
		return false;
	}


	/**
	 * Get mathjax header (static string that enables mathjax to be rendered)
	 *
	 * @return string
	 *
	 * @access private
	 */
	private function get_mathjax_header(){
		return "
		<script type='text/x-mathjax-config'>
			MathJax.Hub.Config({ TeX: { equationNumbers: {autoNumber: 'all'} } });
		</script>
		<script type='text/javascript' src='//cdn.mathjax.org/mathjax/latest/MathJax.js?config=TeX-AMS-MML_HTMLorMML'>
		</script>
		";
	}

	/**
	 * Get form rendered as plain text
	 *
	 * @param array $return  form parameters
	 * @return string
	 *
	 * @access private
	 */
	private function get_form_plain( $return ){
		return print_r($return, 1);
	}

	

	/**
	 * Get form rendered as html
	 *
	 * @param array $return  form parameters
	 * @return string
	 *
	 * @access private
	 */
	private function get_form_html( $return ){
		// returns html based on form parameters in $return
		$out = "<p>" . $return['introduction'] . "</p>" . "\r\n";
		if (!isset($return['name'])) $return['name']='';
		if (!isset($return['action'])) $return['action']='';
		$form = new HTML_QuickForm2($return['name'],$return['method'], $return['action']);
		$form->addDataSource(new HTML_QuickForm2_DataSource_Array( $return['values'] ) );
		if (count($return['parameters']) > 0){
			$fieldset = $form->addElement('fieldset');
			foreach(array_keys($return['parameters']) as $key){
				if (isset($return['exclude'])){
				  if (!in_array($key, $return['exclude'])){
					$parameter = $return['parameters'][$key];
					$valid_option = array();
					if (array_key_exists($key,$return['valid_options'])){
						$valid_option = $return['valid_options'][$key];
						if ('string'==$valid_option['type']) 
							$input_type='textarea';
						if ('number'==$valid_option['type']) 
							$input_type='text';
						if ('boolean'==$valid_option['type']) 
							$input_type='checkbox';
					}
					$value = '';
					$fieldset->addElement($input_type, $key)->setLabel($parameter['label']);
				  }
				}
			}
		}
		if (isset($return['hidden'])){
		  if (count($return['hidden']) > 0){
			$fieldset_hidden = $form->addElement('fieldset');
			foreach(array_keys( $return['hidden']) as $key ){
				$value = $return['hidden'][$key];
				$fieldset_hidden->addElement('hidden', $key)->setValue( $value );
			}
		  }
		}
		// add page_id
		$fieldset->addElement('hidden', 'request')->setValue($return['request']);
		if (isset($_GET['page_id'])){
			$fieldset->addElement('hidden', 'page_id')->setValue($_GET['page_id']);
		}
		$fieldset->addElement('submit', null, array('value' => $return['submit']));
		$out.= $form;
		return $out;
	}

	/**
	 * Modify (input) fieldset so it includes hidden fields
	 *
	 * @param fieldset $fieldset HTML/QuickForm2 fieldset to modify
 	 * @param array $hidden array of hidden fieldnames (keys) and values
	 * @return null
 	 *
	 * @access private
	 */
	private function add_hidden_fields_to_fieldset( &$fieldset, $hidden ){
		foreach(array_keys( $hidden) as $key ){
			$value = $hidden[$key];
			$fieldset->addElement('hidden', $key)->setValue( $value );
		}
	}

/*
public function add_hidden_fields( &$fieldset, CT1_Collection $cf ){
	$collection_name = get_class( $cf );
	$hidden = $cf->get_values_as_array(  $collection_name );
	$this->add_hidden_fields_to_fieldset( $fieldset, $hidden );
	
}
*/

/*
public function get_form_cashflow( CT1_Cashflows $cf, $submit = 'Submit', $intro = "" ){
	$render = new CT1_Render();
	return $render->get_form_collection( $cf, $submit, $intro, 'view_cashflows');
}
*/

/*
public function test_popup(){
    return $this->get_popup_head() . '<A HREF="' . $this->get_popup_latex("a fraction $$ <a href=''>\\frac{1}{2}</a>$$ and Some text linking to <a href='http://www.bbc.co.uk'>bbc</a> and a fraction <a href='http://cnn.com'>$$ \\frac{1}{2}$$</a> that links to cnn") . '" onClick="return popup(this, ' . "'stevie'" .')">my popup</A>';
}

private function get_popup_head(){
    // source: http://www.htmlcodetutorial.com/linking/popup_test_a.html
    return '<SCRIPT TYPE="text/javascript">
        <!--
        function popup(mylink, windowname)
        {
            if (! window.focus)return true;
            var href;
            if (typeof(mylink) == "string")
                href=mylink;
            else
                href=mylink.href;
            window.open(href, windowname, "width=400,height=200,scrollbars=yes");
            return false;
        }
        //-->
        </SCRIPT>
';
}
*/
/*
private function get_popup($string){
    return $this->get_data_uri($string);
}

private function get_data_uri($string){
// source: http://davidwalsh.name/data-uri-php
    return 'data: text/html;base64,'.base64_encode($string);
}

private function get_popup_latex($string){
    $page = "<html>" . "\r\n";
    $page.= "<head>" . "\r\n";
    $page.= $this->get_mathjax_header();
    $page.= "</head>" . "\r\n";
    $page.= "<body>" . "\r\n";
    $page.= $string . "\r\n";
    $page.= "</body>" . "\r\n";
    $page.= "</html>" . "\r\n";    
    return $this->get_data_uri($page);
}
*/


} // end of class

