<?php   


$path_to_pear = plugin_dir_path(__FILE__) . 'pear/' . PATH_SEPARATOR;
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

