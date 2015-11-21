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
	if (!class_exists($class) && !interface_exists($class)){
		require_once ($file);
		if (!class_exists($class) && !interface_exists($class)){
			throw new Exception("Can't instantiate " . $class . " from " . $file . " in ". __FILE__ );
		}
//		$o = new $class;
	}
}

// if class A requires class B then put the CT1_autoloader call to class A below the call to class B
CT1_autoloader("HTML_QuickForm2",$_dir . "/PEAR/HTML/QuickForm2.php");
CT1_autoloader("Validate", $_dir  . "/PEAR/Validate.php");
CT1_autoloader(  		"HTML_Table", "PEAR/HTML/Table.php");
CT1_autoloader(		"Validate", "PEAR/Validate.php");
CT1_autoloader(		"CT1_Object", "includes/class-ct1-object.php");
CT1_autoloader(		"CT1_Concept",	"includes/interface-ct1-concept.php");
CT1_autoloader(		"CT1_Form", "includes/class-ct1-form.php");
CT1_autoloader(		"CT1_XML", "includes/class-ct1-xml.php");
CT1_autoloader(		"CT1_Form_XML", "includes/class-ct1-form-xml.php");
CT1_autoloader(		"CT1_Interest_Format", "includes/class-ct1-interest-format.php");
CT1_autoloader(		"CT1_Interest",	"includes/class-ct1-interest.php");
CT1_autoloader(		"CT1_Annuity", "includes/class-ct1-annuity.php");
CT1_autoloader(		"FinMathSingleCashflow", "includes/class-ct1-single-cashflow.php");
CT1_autoloader(		"FinMathAnnuityEscalating", "includes/class-ct1-annuity-escalating.php");
CT1_autoloader(		"FinMathAnnuityIncreasing", "includes/class-ct1-annuity-increasing.php");
CT1_autoloader(		"FinMathCashflow", "includes/class-ct1-cashflow.php");
CT1_autoloader(		"CT1_Collection", "includes/class-ct1-collection.php");
CT1_autoloader(		"FinMathCashflows", "includes/class-ct1-cashflows.php");
CT1_autoloader(		"FinMathConceptAll", "includes/class-ct1-concept-all.php");
CT1_autoloader(		"FinMathConceptAnnuity", "includes/class-ct1-concept-annuity.php");
CT1_autoloader(		"FinMathConceptAnnuityIncreasing", "includes/class-ct1-concept-annuity-increasing.php");
CT1_autoloader(		"FinMathConceptCashflows", "includes/class-ct1-concept-cashflows.php");
CT1_autoloader(		"FinMathConceptInterest", "includes/class-ct1-concept-interest.php");
CT1_autoloader(		"FinMathConceptMortgage", "includes/class-ct1-concept-mortgage.php");
CT1_autoloader(		"FinMathConceptSpotRates", "includes/class-ct1-concept-spot-rates.php");
CT1_autoloader(		"CT1_Forward_Rate", "includes/class-ct1-forward-rate.php");
CT1_autoloader(		"FinMathForwardRates", "includes/class-ct1-forward-rates.php");
CT1_autoloader(		"CT1_Interest", "includes/class-ct1-interest.php");
//CT1_autoloader(		"CT1_Marker", "includes/class-ct1-marker.php");
CT1_autoloader(		"CT1_Mortgage", "includes/class-ct1-mortgage.php");
CT1_autoloader(		"FinMathParYield", "includes/class-ct1-par-yield.php");
CT1_autoloader(		"FinMathParYields", "includes/class-ct1-par-yields.php");
CT1_autoloader(		"FinMathRender", "includes/class-ct1-render.php");
CT1_autoloader(		"FinMathSpotRate", "includes/class-ct1-spot-rate.php");
CT1_autoloader(		"FinMathSpotRates", "includes/class-ct1-spot-rates.php");

	
