<?php
// SOURCE: http://kovshenin.com/2012/the-wordpress-settings-api/

add_action( 'admin_menu', 'ct1_admin_menu' );
function ct1_admin_menu() {
    add_options_page( 'Financial Mathematics', 
	'Financial Mathematics', 
	'manage_options', 
	'financial-mathematics', 
	'ct1_options_page' );
}

add_action( 'admin_init', 'ct1_admin_init' );
function ct1_admin_init() {
    add_settings_section( 'section-one', 
	'Section One', 
	'ct1_section_one_callback', 
	'financial-mathematics' );
}


function ct1_section_one_callback() {
	include 'ct1-plugin-help.html';
}


function ct1_options_page() {
    ?>
    <div class="wrap">
        <h2>Financial Mathematics Options</h2>
        <form action="options.php" method="POST">
            <?php // settings_fields( 'ct1-settings-group' ); ?>
            <?php do_settings_sections( 'financial-mathematics' ); ?>
            <?php // submit_button(); ?>
        </form>
    </div>
    <?php
}


