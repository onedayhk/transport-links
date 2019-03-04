<?php
// Silence is golden.

/*
Plugin Name: Hong Kong Transport Links
Description: This is a FREE Hong Kong transport plugin that you can embed on your website. We have a database of all transport links for every address in Hong Kong. Any web developer or web master can easily generate transport links for any address in Hong Kong and embed on your website. 
Author: OneDay Group Ltd.
Author URI: https://www.oneday.com.hk/
Version: 1.0
*/

if ( ! defined( 'ABSPATH' ) ) exit;

/*
*	Config Files 
*/
include_once('odt-config.php');


// for wp plugin ajax requests
add_action( 'wp_ajax_odt_action', 'odt_actions' );
add_action('wp_ajax_nopriv_odt_action', 'odt_actions'); // added this for none logged in users.

function odt_actions() {

	if(isset($_REQUEST['odt_action'])){
		include_once('odt-actions.php');
	}

	wp_die(); // this is required to terminate immediately and return a proper response
}

add_filter("mce_external_plugins", "enqueue_odt_button_plugin_scripts");

function enqueue_odt_button_plugin_scripts($plugin_array)
{
    //enqueue TinyMCE plugin script with its ID.
    $plugin_array["odt_button_plugin"] =  plugin_dir_url(__FILE__) . 'odt-editor-button.js';
    return $plugin_array;
}

add_filter("mce_buttons", "register_odt_button_plugin_editor");

function register_odt_button_plugin_editor($buttons)
{
    //register buttons with their id.
    array_push($buttons, "odt_button");
    return $buttons;
}

wp_enqueue_script('odt_main_js', plugin_dir_url(__FILE__) . ODT_DS . 'assets/js/main.js');
wp_localize_script('odt_main_js', 'mainJS', array(
    'odtUrl' => plugin_dir_url(__FILE__),
));

wp_register_style('odt_main_css', plugin_dir_url(__FILE__) . ODT_DS . 'assets/css/main.css');
wp_enqueue_style('odt_main_css');

/* adding the shortcode for hong kong tranportations links*/
add_shortcode( 'odt', 'odt_populate_script' );

function odt_populate_script( $attr ) {

    if(!isset($attr['data_id']) || !is_numeric($attr['data_id'])){
        return false;
    }

    $data_id = $attr['data_id'];

    if(isset($attr['data_lang'])){
        $data_lang = $attr['data_lang'];
        switch ($data_lang) {
            case 'zh_HK':
                return odt_get_script('odt_script_zh_'.$data_id);
                break;
            
            default:
                return odt_get_script('odt_script_en_'.$data_id);
                break;
        }
    }else{
        return odt_get_script('odt_script_en_'.$data_id);
    }
}