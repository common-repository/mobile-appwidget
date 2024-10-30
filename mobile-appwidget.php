<?php
/*
 * Plugin Name: Mobile AppWidget
 * Version: 1.2
 * Plugin URI: http://www.nisi.ro/
 * Description: wordpress widget for promoting a Google Android/Apple iOS/Windows Phone app in your Wordpress sidebar
 * Author: Nisipeanu Mihai
 * Author URI: <a href="http://www.nisi.ro/">Nisipeanu Mihai</a>
 * Requires at least: 3.8
 * Tested up to: 4.0
 *
 * @package WordPress
 * @author Nisipeanu Mihai
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) exit;

if (!defined('TB_APPSLIST'))
	define('TB_APPSLIST', 'mobapp_appslist');
if (!defined('TB_DEVLIST'))
	define('TB_DEVLIST', 'mobapp_devlist');
if (!defined('TB_DOWNLOADURLS'))
	define('TB_DOWNLOADURLS', 'mobapp_downloadurls');
if (!defined('TB_CAMPAIGNS'))
	define('TB_CAMPAIGNS', 'mobapp_campaigns');
if (!defined('TB_CAMPAIGNS_APPS'))
	define('TB_CAMPAIGNS_APPS', 'mobapp_campaigns_apps');	
if (!defined('TB_STATS'))
	define('TB_STATS', 'mobapp_stats');

// Include plugin class files
require_once( 'includes/class-mobile-appwidget.php' );
require_once( 'includes/class-mobile-appwidget-settings.php' );
require_once( 'includes/mobile-appwidget-widget.php' );
require_once( 'includes/models/mobileappclass.model.php' );
require_once( 'includes/models/campaignappclass.model.php' );

require_once( 'includes/Mobile_AppWidget_PhotoUploader.php' );

/**
 * Returns the main instance of WordPress_Plugin_Template to prevent the need to use globals.
 *
 * @since  1.0.0
 * @return object WordPress_Plugin_Template
 */
 
function mobileAppWidget () {

	$instance = Mobile_AppWidget::instance( __FILE__, '1.0.0' );
	if( is_null( $instance->settings ) ) {
		$instance->settings = Mobile_AppWidget_Settings::instance( $instance );
	}
	return $instance;
}

mobileAppWidget();

// Mobile_AppWidget_PhotoUploader.php
add_action( 'wp_ajax_mobile_app_widget_photo_upload', 'Mobile_AppWidget_PhotoUploader' );

/*
redirect hook
*/
add_action('init', 'redirect_action');
function redirect_action() {
	if (!is_admin() && (isset($_GET['mobile-appwidget-redirect'])))
    if(intval($_GET['mobile-appwidget-redirect']) == 1) {
		$urlValid = true;
		if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $_GET['url'])) {
		  $urlValid = false;
		}else{

			if ((isset($_GET['cid']) && (is_numeric($_GET['cid']))) && (isset($_GET['i']) && (is_numeric($_GET['i'])))){
				global $wpdb;
				$campaignAppObj = new CampaignsAppClass($wpdb);

				$campaignAppObj->addClick(intval($_GET['cid']), intval($_GET['i']));
			}

			header( "HTTP/1.1 301 Moved Permanently" ); 
			header( "Location: ".$_GET['url'] ); 
		}
		exit();
	}
}
	
	