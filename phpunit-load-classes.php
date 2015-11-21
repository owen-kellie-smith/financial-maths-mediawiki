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
FinMath_autoloader(		"FinMathObject", "includes/class-ct1-object.php");
FinMath_autoloader(		"FinMathConcept",	"includes/interface-ct1-concept.php");
FinMath_autoloader(		"FinMathForm", "includes/class-ct1-form.php");
FinMath_autoloader(		"FinMathXML", "includes/class-ct1-xml.php");
FinMath_autoloader(		"FinMathFormXML", "includes/class-ct1-form-xml.php");
FinMath_autoloader(		"FinMathInterestFormat", "includes/class-ct1-interest-format.php");
FinMath_autoloader(		"FinMathInterest",	"includes/class-ct1-interest.php");
FinMath_autoloader(		"FinMathAnnuity", "includes/class-ct1-annuity.php");
FinMath_autoloader(		"FinMathSingleCashflow", "includes/class-ct1-single-cashflow.php");
FinMath_autoloader(		"FinMathAnnuityEscalating", "includes/class-ct1-annuity-escalating.php");
FinMath_autoloader(		"FinMathAnnuityIncreasing", "includes/class-ct1-annuity-increasing.php");
FinMath_autoloader(		"FinMathCashflow", "includes/class-ct1-cashflow.php");
FinMath_autoloader(		"FinMathCollection", "includes/class-ct1-collection.php");
FinMath_autoloader(		"FinMathCashflows", "includes/class-ct1-cashflows.php");
FinMath_autoloader(		"FinMathConceptAll", "includes/class-ct1-concept-all.php");
FinMath_autoloader(		"FinMathConceptAnnuity", "includes/class-ct1-concept-annuity.php");
FinMath_autoloader(		"FinMathConceptAnnuityIncreasing", "includes/class-ct1-concept-annuity-increasing.php");
FinMath_autoloader(		"FinMathConceptCashflows", "includes/class-ct1-concept-cashflows.php");
FinMath_autoloader(		"FinMathConceptInterest", "includes/class-ct1-concept-interest.php");
FinMath_autoloader(		"FinMathConceptMortgage", "includes/class-ct1-concept-mortgage.php");
FinMath_autoloader(		"FinMathConceptSpotRates", "includes/class-ct1-concept-spot-rates.php");
FinMath_autoloader(		"FinMathForwardRate", "includes/class-ct1-forward-rate.php");
FinMath_autoloader(		"FinMathForwardRates", "includes/class-ct1-forward-rates.php");
FinMath_autoloader(		"FinMathInterest", "includes/class-ct1-interest.php");
FinMath_autoloader(		"FinMathMortgage", "includes/class-ct1-mortgage.php");
FinMath_autoloader(		"FinMathParYield", "includes/class-ct1-par-yield.php");
FinMath_autoloader(		"FinMathParYields", "includes/class-ct1-par-yields.php");
FinMath_autoloader(		"FinMathRender", "includes/class-ct1-render.php");
FinMath_autoloader(		"FinMathSpotRate", "includes/class-ct1-spot-rate.php");
FinMath_autoloader(		"FinMathSpotRates", "includes/class-ct1-spot-rates.php");

	
