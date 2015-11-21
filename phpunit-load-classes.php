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

function FinMath_autoloader($class, $file){
	if (!class_exists($class) && !interface_exists($class)){
		require_once ($file);
		if (!class_exists($class) && !interface_exists($class)){
			throw new Exception("Can't instantiate " . $class . " from " . $file . " in ". __FILE__ );
		}
//		$o = new $class;
	}
}

// if class A requires class B then put the FinMath_autoloader call to class A below the call to class B
FinMath_autoloader("HTML_QuickForm2",$_dir . "/PEAR/HTML/QuickForm2.php");
FinMath_autoloader("Validate", $_dir  . "/PEAR/Validate.php");
FinMath_autoloader(  		"HTML_Table", "PEAR/HTML/Table.php");
FinMath_autoloader(		"Validate", "PEAR/Validate.php");
FinMath_autoloader(		"FinMathObject", "includes/FinMathObject.php");
FinMath_autoloader(		"FinMathConcept",	"includes/FinMathConcept.php");
FinMath_autoloader(		"FinMathForm", "includes/FinMathForm.php");
FinMath_autoloader(		"FinMathXML", "includes/FinMathXML.php");
FinMath_autoloader(		"FinMathFormXML", "includes/FinMathFormXML.php");
FinMath_autoloader(		"FinMathInterestFormat", "includes/FinMathInterestFormat.php");
FinMath_autoloader(		"FinMathInterest",	"includes/FinMathInterest.php");
FinMath_autoloader(		"FinMathAnnuity", "includes/FinMathAnnuity.php");
FinMath_autoloader(		"FinMathSingleCashflow", "includes/FinMathSingleCashflow.php");
FinMath_autoloader(		"FinMathAnnuityEscalating", "includes/FinMathAnnuityEscalating.php");
FinMath_autoloader(		"FinMathAnnuityIncreasing", "includes/FinMathAnnuityIncreasing.php");
FinMath_autoloader(		"FinMathCashflow", "includes/FinMathCashflow.php");
FinMath_autoloader(		"FinMathCollection", "includes/FinMathCollection.php");
FinMath_autoloader(		"FinMathCashflows", "includes/FinMathCashflows.php");
FinMath_autoloader(		"FinMathConceptAll", "includes/FinMathConceptAll.php");
FinMath_autoloader(		"FinMathConceptAnnuity", "includes/FinMathConceptAnnuity.php");
FinMath_autoloader(		"FinMathConceptAnnuityIncreasing", "includes/FinMathConceptAnnuityIncreasing.php");
FinMath_autoloader(		"FinMathConceptCashflows", "includes/FinMathConceptCashflows.php");
FinMath_autoloader(		"FinMathConceptInterest", "includes/FinMathConceptInterest.php");
FinMath_autoloader(		"FinMathConceptMortgage", "includes/FinMathConceptMortgage.php");
FinMath_autoloader(		"FinMathConceptSpotRates", "includes/FinMathConceptSpotRates.php");
FinMath_autoloader(		"FinMathForwardRate", "includes/FinMathForwardRate.php");
FinMath_autoloader(		"FinMathForwardRates", "includes/FinMathForwardRates.php");
FinMath_autoloader(		"FinMathInterest", "includes/FinMathInterest.php");
FinMath_autoloader(		"FinMathMortgage", "includes/FinMathMortgage.php");
FinMath_autoloader(		"FinMathParYield", "includes/FinMathParYield.php");
FinMath_autoloader(		"FinMathParYields", "includes/FinMathParYields.php");
FinMath_autoloader(		"FinMathRender", "includes/FinMathRender.php");
FinMath_autoloader(		"FinMathSpotRate", "includes/FinMathSpotRate.php");
FinMath_autoloader(		"FinMathSpotRates", "includes/FinMathSpotRates.php");

	
