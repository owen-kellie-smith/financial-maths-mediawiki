<?php   

$_dir = "FinancialMathematics";
foreach ( array( '',
	'/includes/',
	'/PEAR/',
	'/PEAR/HTML/',
	'/PEAR/HTML/QuickForm2',
	) AS $path ){
	set_include_path(get_include_path(). PATH_SEPARATOR. dirname(__FILE__). "/". $_dir. $path );
}

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

CT1_autoloader("HTML_QuickForm2",$_dir . "/PEAR/HTML/QuickForm2.php");
CT1_autoloader("Validate", $_dir  . "/PEAR/Validate.php");
CT1_autoloader("CT1_Concept_All", "class-ct1-concept-all.php");

