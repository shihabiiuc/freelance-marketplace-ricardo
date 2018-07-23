<?php
/*
Plugin Name: FrE Braintree
Plugin URI: http://enginethemes.com/
Description: Integrates the Braintree payment gateway to your FreelanceEngine site
Version: 1.1
Author: EngineThemes
Author URI: http://enginethemes.com/
License: GPLv2
Text Domain: enginetheme
*/

/* Run this plugin after setup theme
 * @param void
 * @return void
 * @since 1.1
 * @package AE_Braintree
 * @category BRAINTREE
 * @author ThanhTu
 */
function require_plugin_braintree(){
    if( !class_exists('AE_Base') ){
        return 0;
    }
    require_once dirname(__FILE__) . '/settings.php';
    require_once dirname(__FILE__) . '/braintree.php';
    require_once dirname(__FILE__) . '/update.php';
    $ae_braintree = AE_Braintree::getInstance();
    $ae_braintree->init();
    define( 'BRAINTREE_DIR_URL', plugin_dir_url( __FILE__ ) );
}
add_action('after_setup_theme', 'require_plugin_braintree');

/**
 * hook to add translate string to plugins
 *
 * @param Array $entries Array of translate entries
 * @return Array $entries
 * @since 1.0
 * @author ThanhTu
 */
function ae_braintree_add_translate_string ($entries) {
    $lang_path = dirname(__FILE__).'/lang/default.po';
    if(file_exists($lang_path)) {
        $pot        =   new PO();
        $pot->import_from_file($lang_path);
        return  array_merge($entries, $pot->entries);
    }
    return $entries;
}
add_filter( 'et_get_translate_string', 'ae_braintree_add_translate_string' );