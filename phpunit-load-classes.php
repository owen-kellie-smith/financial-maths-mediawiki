<?php   

$_dir = "/FinancialMathematics";
$path_to_pear = dirname(__FILE__) . $_dir . PATH_SEPARATOR;
set_include_path(get_include_path() . PATH_SEPARATOR . $path_to_pear);
$path_to_pear = dirname(__FILE__) . $_dir . '/PEAR/' . PATH_SEPARATOR;
set_include_path(get_include_path() . PATH_SEPARATOR . $path_to_pear);
$path_to_pear = dirname(__FILE__) . $_dir . '/PEAR/HTML/' . PATH_SEPARATOR;
set_include_path(get_include_path() . PATH_SEPARATOR . $path_to_pear);
$path_to_pear = dirname(__FILE__) . $_dir . '/PEAR/HTML/QuickForm2' . PATH_SEPARATOR;
set_include_path(get_include_path() . PATH_SEPARATOR . $path_to_pear);

function CT1_autoloader($class, $file){
	if (!class_exists($class)){
		if (!include($file)){ 
			throw new Exception("Can't instantiate " . $class . " in " . __FILE__ );
		}
		else{
			require_once ($file);
			if (!class_exists($class)){
				throw new Exception("Can't instantiate " . $class . " in " . __FILE__ );
			}
		}
	}
}

$_dir = "FinancialMathematics/";
		CT1_autoloader("HTML_QuickForm2",$_dir . "PEAR/HTML/QuickForm2.php");
		CT1_autoloader("Validate", $_dir  . "PEAR/Validate.php");

