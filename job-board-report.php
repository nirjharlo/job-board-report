<?php
/**
 Plugin Name: Job Board Report
 Plugin URI: http://jb.p2l.site
 Description: Report genaration plugin for Job Board theme.
 Version: 1.0
 Author: Nirjhar Lo
 Author URI: http://nirjharlo.com
 Text Domain: jbr
 Domain Path: /asset/ln
 License: GPLv2
 License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
if (!defined('ABSPATH')) exit;


//Define basic names
//Edit the "_JBR" in following namespaces for compatibility with your desired name.
defined('JBR_DEBUG') or define('JBR_DEBUG', false);

defined('JBR_PATH') or define('JBR_PATH', plugin_dir_path(__FILE__));
defined('JBR_FILE') or define('JBR_FILE', plugin_basename(__FILE__));

defined('JBR_EXECUTE') or define('JBR_EXECUTE', plugin_dir_path(__FILE__).'src/');
defined('JBR_HELPER') or define('JBR_HELPER', plugin_dir_path(__FILE__).'helper/');
defined('JBR_TRANSLATE') or define('JBR_TRANSLATE', plugin_basename( plugin_dir_path(__FILE__).'asset/ln/'));

//change /wp-plugin-framework/ with your /plugin-name/
defined('JBR_JS') or define('JBR_JS', plugins_url().'/wp-plugin-framework/asset/js/');
defined('JBR_CSS') or define('JBR_CSS', plugins_url().'/wp-plugin-framework/asset/css/');


//The Plugin
require_once('autoload.php');
if ( class_exists( 'JBR_BUILD' ) ) new JBR_BUILD(); ?>