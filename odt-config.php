<?php

if ( ! defined( 'ABSPATH' ) ) exit;

define('ODT_DS', DIRECTORY_SEPARATOR);
define('ODT_PLUGIN_BASE_URL', plugin_dir_path( __FILE__));

define('ODT_ICON', plugin_dir_url(__FILE__) . ODT_DS . 'assets' . ODT_DS . 'images' . ODT_DS . 'menu_icon_20x20.png');

// Status codes for Response - JSON format responses
define('ODT_API_SUCCESS',200);
define('ODT_API_ERROR_VALIDATING',400);
define('ODT_API_ERROR_UNAUTHORIZED',401);
define('ODT_API_ERROR_FORBIDDEN',403);
define('ODT_API_ERROR',404);
define('ODT_API_ACCESS_TOKEN_EXPIRED',440);
define('ODT_API_REFRESH_TOKEN_EXPIRED',441);
define('ODT_API_RELOGIN',442);
define('ODT_API_REDIRECT',301);

define('ODT_SERVER', 'https://www.oneday.com.hk/');
/*
* Sets the Autodirectory to src
*/
spl_autoload_register( 'odt_autoloader' );
function odt_autoloader( $class_name ) {
    
  if ( preg_match('/^odt/',$class_name)) { 
    $filename = ODT_PLUGIN_BASE_URL . ODT_DS . 'lib' . ODT_DS .'lib_' . $class_name . '.php';
    require_once $filename;
  }
}

function odt_save_scripts($data_id, $script_en, $script_zh){
	$odt_script_en = 'odt_script_en_'.$data_id;
	$odt_script_zh = 'odt_script_zh_'.$data_id;

	if(get_option($odt_script_en)){
		update_option($odt_script_en, $script_en);
	}else{
		add_option($odt_script_en, $script_en);
	}

	if(get_option($odt_script_zh)){
		update_option($odt_script_zh, $script_zh);
	}else{
		add_option($odt_script_zh, $script_zh);
	}
}

$odt_languages = array(
	'English' => 'en_US',
	'Chinese' => 'zh_HK',
);

function odt_get_shortcode($data_id, $odt_language){
	global $odt_languages;

	$data_lang = array_key_exists($odt_language, $odt_languages) ? $odt_languages[$odt_language] : 'en_US';
	return '[odt data_id="'.$data_id.'" data_lang="'.$data_lang.'"]';
}

function odt_get_script($option_name){
	if(!get_option($option_name)){
		return '<p>Invalid Data ID for Hong Kong Transport Links.</p>';
	}
    return get_option($option_name);
}