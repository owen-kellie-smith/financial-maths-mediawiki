<?php 
/*

Copyright (C) 2015 Owen Kellie-Smith

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
*/

// plugin provides shortcodes (only)
add_shortcode( 'fin-math', 'concept_all_proc' );  // this is the only one you need

// what shortcodes do
function concept_all_proc($attr){
	try{
		CT1_autoloader('CT1_Concept_All', 'class-ct1-concept-all.php');
		$m = new CT1_Concept_All();
		return $m->get_controller($_GET);
	}
	catch (Exception $e){
		return "Exception in " . __FILE__ . ": " . $e->getMessage();
	}
}

// load helper functions (autoloader)
require_once 'functions.php';
$path_to_class = dirname( __FILE__ ) . "/classes";
set_include_path(get_include_path() . PATH_SEPARATOR . $path_to_class);

// load css for plugin
add_action('wp_head', 'callbackToAddCSS');
// source: http://stackoverflow.com/questions/5805888/
function callbackToAddCSS(){
  echo "\t  <link rel='stylesheet' type='text/css' href='" . plugin_dir_url(__FILE__) ."ct1.css'> \n";
}

