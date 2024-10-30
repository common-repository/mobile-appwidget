<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Mobile_AppWidget {

	/**
	 * The single instance of Mobile_AppWidget.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * Settings class object
	 * @var     object
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = null;

	/**
	 * The version number.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_version;

	/**
	 * The token.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $_token;

	/**
	 * The main plugin file.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $file;

	/**
	 * The main plugin directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $dir;

	/**
	 * The plugin assets directory.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_dir;

	/**
	 * The plugin assets URL.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $assets_url;

	/**
	 * Suffix for Javascripts.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $script_suffix;

	/**
	 * Constructor function.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function __construct ( $file = '', $version = '1.0.0' ) {
		$this->_version = $version;
		$this->_token = 'mobile_appwidget';

		$this->file = $file;
		$this->dir = dirname( $this->file );
		$this->assets_dir = trailingslashit( $this->dir ) . 'assets';
		$this->assets_url = esc_url( trailingslashit( plugins_url( '/assets/', $this->file ) ) );

		$this->script_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		register_activation_hook( $this->file, array( $this, 'install' ) );

		// Load frontend JS & CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ), 10 );
		//add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ), 10 );

		// Load admin JS & CSS
		//add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ), 10, 1 );
		//add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_styles' ), 10, 1 );

		// Handle localisation
		//$this->load_plugin_textdomain();
		//add_action( 'init', array( $this, 'load_localisation' ), 0 );
		
		add_action( 'widgets_init', create_function('', 'return register_widget("MobileAppWidget_Widget");') );
	}

	function mobile_Appwidget_List(){
		echo('hello list');
	}
	
	/**
	 * Load frontend CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_styles () {
/*
		wp_register_style( 
			$this->_token . '-frontend', 
			esc_url( $this->assets_url ) . 'css/frontend.css', 
			array( ), 
			$this->_version 
		);
		wp_enqueue_style( $this->_token . '-frontend' );
*/

		wp_register_style( 
			'appwidget_css-frontend', 
			esc_url( $this->assets_url ) . 'css/appwidget_css.php', 
			array( ), 
			$this->_version 
		);
		wp_enqueue_style( 'appwidget_css-frontend' );
		
	} // End enqueue_styles()

	/**
	 * Load frontend Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function enqueue_scripts () {

		wp_register_script( $this->_token . '-frontend', esc_url( $this->assets_url ) . 'js/frontend' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-frontend' );
	} // End enqueue_scripts()

	/**
	 * Load admin CSS.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function admin_enqueue_styles ( $hook = '' ) {
		wp_register_style( $this->_token . '-admin', esc_url( $this->assets_url ) . 'css/admin.css', array(), $this->_version );
		wp_enqueue_style( $this->_token . '-admin' );
	} // End admin_enqueue_styles()

	/**
	 * Load admin Javascript.
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function admin_enqueue_scripts ( $hook = '' ) {
		wp_register_script( $this->_token . '-admin', esc_url( $this->assets_url ) . 'js/admin' . $this->script_suffix . '.js', array( 'jquery' ), $this->_version );
		wp_enqueue_script( $this->_token . '-admin' );
	} // End admin_enqueue_scripts()

	/**
	 * Load plugin localisation
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function load_localisation () {
		load_plugin_textdomain( 'wordpress-plugin-template' , false , dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_localisation()

	/**
	 * Load plugin textdomain
	 * @access  public
	 * @since   1.0.0
	 * @return void
	 */
	public function load_plugin_textdomain () {
	    $domain = 'wordpress-plugin-template';

	    $locale = apply_filters( 'plugin_locale' , get_locale() , $domain );

	    load_textdomain( $domain , WP_LANG_DIR . '/' . $domain . '/' . $domain . '-' . $locale . '.mo' );
	    load_plugin_textdomain( $domain , FALSE , dirname( plugin_basename( $this->file ) ) . '/lang/' );
	} // End load_plugin_textdomain()

	/**
	 * Main WordPress_Plugin_Template Instance
	 *
	 * Ensures only one instance of WordPress_Plugin_Template is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WordPress_Plugin_Template()
	 * @return Main WordPress_Plugin_Template instance
	 */
	public static function instance ( $file = '', $version = '1.0.0' ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $file, $version );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->_version );
	} // End __wakeup()

	/**
	 * Installation. Runs on activation.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	public function install () {
		global $wpdb;
		if ($this->_version == '1.0.0'){
			
			$queryStr = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."mobapp_adtemplates` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `name` varchar(255) NOT NULL DEFAULT '',
						  `template_HTML` text NOT NULL,
						  `template_CSS` text NOT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
			
			$wpdb->query($queryStr);
			
			$queryStr = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."mobapp_appslist` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `dev_id` int(11) unsigned NOT NULL DEFAULT '0',
						  `category` varchar(15) NOT NULL DEFAULT 'default',
						  `title` varchar(50) NOT NULL DEFAULT '',
						  `description` varchar(255) NOT NULL DEFAULT '',
						  `price` float NOT NULL DEFAULT '0',
						  `currency` varchar(4) CHARACTER SET utf8 COLLATE utf8_estonian_ci NOT NULL DEFAULT 'usd',
						  `thumb` varchar(255) NOT NULL,
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
			$wpdb->query($queryStr);
			
			$queryStr = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."mobapp_campaigns` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `name` varchar(50) NOT NULL DEFAULT '',
						  `template_id` int(11) NOT NULL,
						  `total_clicks` int(10) NOT NULL DEFAULT '0',
						  `daily_clicks` int(10) NOT NULL DEFAULT '0',
						  `start_day` datetime NOT NULL,
						  `end_day` datetime NOT NULL,
						  `status` varchar(10) NOT NULL DEFAULT 'paused',
						  `total_views` int(10) NOT NULL DEFAULT '0',
						  `daily_views` int(10) NOT NULL DEFAULT '0',
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
			$wpdb->query($queryStr);

			$queryStr = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."mobapp_campaigns_apps` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `app_id` int(11) unsigned NOT NULL,
						  `campaign_id` int(11) unsigned NOT NULL,
						  PRIMARY KEY (`id`),
						  UNIQUE KEY `app_id` (`app_id`,`campaign_id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
			$wpdb->query($queryStr);

			$queryStr = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."mobapp_devlist` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `name` varchar(50) NOT NULL DEFAULT '',
						  `home_url` varchar(255) NOT NULL DEFAULT '#',
						  PRIMARY KEY (`id`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
			$wpdb->query($queryStr);

			$queryStr = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."mobapp_downloadurls` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `app_id` int(11) unsigned NOT NULL DEFAULT '0',
						  `platform` varchar(15) CHARACTER SET utf8 NOT NULL DEFAULT 'ios',
						  `url` varchar(255) CHARACTER SET utf8 NOT NULL DEFAULT '',
						  PRIMARY KEY (`id`),
						  UNIQUE KEY `app_id_platform` (`app_id`,`platform`)
						) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;";
			$wpdb->query($queryStr);

			$queryStr = "CREATE TABLE IF NOT EXISTS `".$wpdb->prefix."mobapp_stats` (
						  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
						  `campaign_id` int(11) unsigned NOT NULL,
						  `app_id` int(11) unsigned NOT NULL,
						  `date` date NOT NULL,
						  `clicks` int(11) NOT NULL DEFAULT '0',
						  `views` int(11) NOT NULL DEFAULT '0',
						  PRIMARY KEY (`id`),
						  UNIQUE KEY `unique_daily` (`campaign_id`,`app_id`,`date`)
						) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;";
			$wpdb->query($queryStr);			
		}
		
		$this->_log_version_number();
	} // End install()

	/**
	 * Log the plugin version number.
	 * @access  public
	 * @since   1.0.0
	 * @return  void
	 */
	private function _log_version_number () {
		update_option( $this->_token . '_version', $this->_version );
	}

}