<?php

/***** Fetch Theme Data *****/

$mh_magazine_lite_data = wp_get_theme('mh-magazine-lite');
$mh_magazine_lite_version = $mh_magazine_lite_data['Version'];
$mh_sportsmagazine_data = wp_get_theme('mh-sportsmagazine');
$mh_sportsmagazine_version = $mh_sportsmagazine_data['Version'];

/***** Load Google Fonts *****/

function mh_sportsmagazine_fonts() {
	wp_dequeue_style('mh-google-fonts');
	wp_enqueue_style('mh-sportsmagazine-fonts', 'https://fonts.googleapis.com/css?family=Glegoo:400,700%7cTitillium+Web:300,400,400italic,600,700', array(), null);
}
add_action('wp_enqueue_scripts', 'mh_sportsmagazine_fonts', 11);

/***** Load Stylesheets *****/

function mh_sportsmagazine_styles() {
	global $mh_magazine_lite_version, $mh_sportsmagazine_version;
    wp_enqueue_style('mh-magazine-lite', get_template_directory_uri() . '/style.css', array(), $mh_magazine_lite_version);
    wp_enqueue_style('mh-sportsmagazine', get_stylesheet_uri(), array('mh-magazine-lite'), $mh_sportsmagazine_version);
    if (is_rtl()) {
		wp_enqueue_style('mh-magazine-lite-rtl', get_template_directory_uri() . '/rtl.css', array(), $mh_magazine_lite_version);
	}
}
add_action('wp_enqueue_scripts', 'mh_sportsmagazine_styles');

/***** Load Translations *****/

function mh_sportsmagazine_theme_setup(){
	load_child_theme_textdomain('mh-sportsmagazine', get_stylesheet_directory() . '/languages');
}
add_action('after_setup_theme', 'mh_sportsmagazine_theme_setup');

/***** Change Defaults for Custom Colors *****/

function mh_sportsmagazine_custom_colors() {
	remove_theme_support('custom-background');
	add_theme_support('custom-background', array('default-color' => '3c236e'));
}
add_action('after_setup_theme', 'mh_sportsmagazine_custom_colors');

/***** Remove Functions from Parent Theme *****/

function mh_sportsmagazine_remove_parent_functions() {
    remove_action('mh_before_header', 'mh_magazine_boxed_container_open');
    remove_action('mh_after_footer', 'mh_magazine_boxed_container_close');
}
add_action('wp_loaded', 'mh_sportsmagazine_remove_parent_functions');

/***** Enable Wide Layout *****/

function mh_sportsmagazine_wide_container_open() {
	echo '<div class="mh-container mh-container-outer">' . "\n";
}
add_action('mh_after_header', 'mh_sportsmagazine_wide_container_open');

function mh_sportsmagazine_wide_container_close() {
	mh_before_container_close();
	echo '</div><!-- .mh-container-outer -->' . "\n";
}
add_action('mh_before_footer', 'mh_sportsmagazine_wide_container_close');

?>