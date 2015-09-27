<?php   

$path_to_pear = dirname(__FILE__) . '/pear/' . PATH_SEPARATOR;
set_include_path(get_include_path() . PATH_SEPARATOR . $path_to_pear);
$path_to_pear = dirname(__FILE__) . '/pear/HTML/' . PATH_SEPARATOR;
set_include_path(get_include_path() . PATH_SEPARATOR . $path_to_pear);
$path_to_pear = dirname(__FILE__) . '/pear/HTML/QuickForm2' . PATH_SEPARATOR;
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

		CT1_autoloader("HTML_QuickForm2","pear/HTML/QuickForm2.php");
		CT1_autoloader("Validate", "pear/Validate.php");
/*
		"CT1_Concept": 						"includes/interface-ct1-concept.php",
		"CT1_Interest": 					"includes/class-ct1-interest.php",
		"CT1_Annuity_Escalating": "includes/class-ct1-annuity-escalating.php",
		"CT1_Annuity_Increqasing": "includes/class-ct1-annuity-increasing.php",
		"CT1_Annuity": "includes/class-ct1-annuity.php",
		"CT1_Cashflow": "includes/class-ct1-cashflow.php",
		"CT1_Cashflows": "includes/class-ct1-cashflows.php",
		"CT1_Collection": "includes/class-ct1-collection.php",
		"CT1_Concept_All": "includes/class-ct1-concept-all.php",
		"CT1_Concept_Annuity_Increasing": "includes/class-ct1-concept-annuity-increasing.php",
		"CT1_Concept_Annuity": "includes/class-ct1-concept-annuity.php",
		"CT1_Concept_Cashflows": "includes/class-ct1-concept-cashflows.php",
		"CT1_Concept_Interest": "includes/class-ct1-concept-interest.php",
		"CT1_Concept_Mortgage": "includes/class-ct1-concept-mortgage.php",
		"CT1_Concept_Spot_Rates": "includes/class-ct1-concept-spot-rates.php",
		"CT1_Form": "includes/class-ct1-form.php",
		"CT1_Forward_Rate": "includes/class-ct1-forward-rate.php",
		"CT1_Forward_Rates": "includes/class-ct1-forward-rates.php",
		"CT1_Interest_Format": "includes/class-ct1-interest-format.php",
		"CT1_Interest": "includes/class-ct1-interest.php",
		"CT1_Marker": "includes/class-ct1-marker.php",
		"CT1_Mortgage": "includes/class-ct1-mortgage.php",
		"CT1_Object": "includes/class-ct1-object.php",
		"CT1_Par_Yield": "includes/class-ct1-par-yield.php",
		"CT1_Par_Yields": "includes/class-ct1-par-yields.php",
		"CT1_Render": "includes/class-ct1-render.php",
		"CT1_Spot_Rate": "includes/class-ct1-spot-rate.php",
		"CT1_Spot_Rates": "includes/class-ct1-spot-rates.php"
*/
