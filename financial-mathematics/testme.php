<?php
// load helper functions (autoloader)
require_once 'functions.php';
$path_to_class = dirname( __FILE__ ) . "/classes";
set_include_path(get_include_path() . PATH_SEPARATOR . $path_to_class);

require_once 'class-ct1-spot-rates.php';
require_once 'class-ct1-forward-rate.php';
$sda = new CT1_Spot_Rate( 0.15, 1);
$sdb = new CT1_Spot_Rate( 0.05, 2);
$sdc = new CT1_Spot_Rate( 0.02, 4);
$sr = new CT1_Spot_Rates();
$sr->add_object( $sdb );
$sr->add_object( $sdc );
$sr->add_object( $sda );
$sr->sort_objects();
print_r($sr);
foreach ($sr->get_forward_rates()->get_objects() as $f){
	$out.= print_r( $sr->explain_forward_rate( $f ) , 1); 
//	print_r( $out );
}
//foreach ($sr->get_par_yields()->get_objects() as $f){
//	$out.= print_r( $sr->explain_par_yield( $f ) , 1); 
//}
	print_r( $out ); 
?>
