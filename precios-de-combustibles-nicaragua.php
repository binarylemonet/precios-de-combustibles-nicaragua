<?php
/*
Plugin Name: Precios de Combustibles Nicaragua
Text Domain: precios-de-combustibles-nicaragua
Domain Path: /languages
Description: Precios de Combustibles en Nicaragua. 
Version: 1.0.0
Author: Binary Lemon
Author URI: https://www.binarylemon.net
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

function precios_combustibles_nicaragua_widget()
{
    include_once(plugin_dir_path( __FILE__ ).'/includes/widget.php');
    register_widget('precios_combustibles_nicaragua_widget');
}

add_action('widgets_init','precios_combustibles_nicaragua_widget');

function precios_combustibles_nicaragua_scripts() 
{
    wp_enqueue_script( 'script-google-chart', plugin_dir_url( __FILE__ ).'includes/js/google.js', array(), '1.0.0');
    wp_enqueue_style( 'style-precios-combustibles-nicaragua', plugin_dir_url( __FILE__ ).'includes/css/precios-combustibles-nicaragua.css', array(), '0.1');    
}

add_action( 'wp_enqueue_scripts', 'precios_combustibles_nicaragua_scripts' );

function precios_combustibles_nicaragua_languages() 
{
    load_plugin_textdomain( 'precios-de-combustibles-nicaragua', false, basename( dirname( __FILE__ ) ) . '/languages' );
}

add_action( 'plugins_loaded', 'precios_combustibles_nicaragua_languages' );