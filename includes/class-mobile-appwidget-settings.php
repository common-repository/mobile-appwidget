<?php

if ( ! defined( 'ABSPATH' ) ) exit;

class Mobile_AppWidget_Settings {

	/**
	 * The single instance of Mobile_AppWidget_Settings.
	 * @var 	object
	 * @access  private
	 * @since 	1.0.0
	 */
	private static $_instance = null;

	/**
	 * The main plugin object.
	 * @var 	object
	 * @access  public
	 * @since 	1.0.0
	 */
	public $parent = null;

	/**
	 * Prefix for plugin settings.
	 * @var     string
	 * @access  public
	 * @since   1.0.0
	 */
	public $base = '';

	/**
	 * Available settings for plugin.
	 * @var     array
	 * @access  public
	 * @since   1.0.0
	 */
	public $settings = array();
	
	private $mobileAppObj;
	private $campaignAppObj;

	public function __construct ( $parent ) {
		$this->parent = $parent;

		$this->base = 'wpt_';

		// Initialise settings
		add_action( 'admin_init', array( $this, 'init_settings' ) );

		// Register plugin settings
		// TODO: move this page to plugin submenu
		add_action( 'admin_init' , array( $this, 'register_settings' ) );  

		// Add settings page to menu
		//add_action( 'admin_menu' , array( $this, 'add_menu_item' ) );
		add_action( 'admin_menu' , array( $this, 'register_dashboard' ) );

		// Add settings link to plugins page
		add_filter( 'plugin_action_links_' . plugin_basename( $this->parent->file ) , array( $this, 'add_settings_link' ) );
		
		global $wpdb;
		$this->mobileAppObj = new MobileAppClass($wpdb);
		$this->campaignAppObj = new CampaignsAppClass($wpdb);
	}

	/**
	 * Initialise settings
	 * @return void
	 */
	public function init_settings () {
		$this->settings = $this->settings_fields();
	}

	/**
	 * Add settings page to admin menu
	 * @return void
	 */
	 /*
	public function add_menu_item () {
		$page = add_options_page( __( 'Plugin Settings', 'wordpress-plugin-template' ) , __( 'Plugin Settings', 'wordpress-plugin-template' ) , 'manage_options' , 'wordpress_plugin_template_settings' ,  array( $this, 'settings_page' ) );
		add_action( 'admin_print_styles-' . $page, array( $this, 'settings_assets' ) );
	}
	*/

	/**
	 * Load settings JS & CSS
	 * @return void
	 */
	public function settings_assets () {

		// We're including the farbtastic script & styles here because they're needed for the colour picker
		// If you're not including a colour picker field then you can leave these calls out as well as the farbtastic dependency for the wpt-admin-js script below
		wp_enqueue_style( 'farbtastic' );
    	wp_enqueue_script( 'farbtastic' );

    	// We're including the WP media scripts here because they're needed for the image upload field
    	// If you're not including an image upload then you can leave this function call out
    	wp_enqueue_media();

    	wp_register_script( $this->parent->_token . '-settings-js', $this->parent->assets_url . 'js/settings' . $this->parent->script_suffix . '.js', array( 'farbtastic', 'jquery' ), '1.0.0' );
    	wp_enqueue_script( $this->parent->_token . '-settings-js' );
		
		//
	}

	/**
	 * Add settings link to plugin list table
	 * @param  array $links Existing links
	 * @return array 		Modified links
	 */
	public function add_settings_link ( $links ) {
		$settings_link = '<a href="options-general.php?page=wordpress_plugin_template_settings">' . __( 'Settings', 'wordpress-plugin-template' ) . '</a>';
  		array_push( $links, $settings_link );
  		return $links;
	}

	/**
	 * Build settings fields
	 * @return array Fields to be displayed on settings page
	 */
	private function settings_fields () {

		$settings['standard'] = array(
			'title'					=> __( 'Standard', 'wordpress-plugin-template' ),
			'description'			=> __( 'These are fairly standard form input fields.', 'wordpress-plugin-template' ),
			'fields'				=> array(
				array(
					'id' 			=> 'text_field',
					'label'			=> __( 'Some Text' , 'wordpress-plugin-template' ),
					'description'	=> __( 'This is a standard text field.', 'wordpress-plugin-template' ),
					'type'			=> 'text',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', 'wordpress-plugin-template' )
				),
				array(
					'id' 			=> 'password_field',
					'label'			=> __( 'A Password' , 'wordpress-plugin-template' ),
					'description'	=> __( 'This is a standard password field.', 'wordpress-plugin-template' ),
					'type'			=> 'password',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', 'wordpress-plugin-template' )
				),
				array(
					'id' 			=> 'secret_text_field',
					'label'			=> __( 'Some Secret Text' , 'wordpress-plugin-template' ),
					'description'	=> __( 'This is a secret text field - any data saved here will not be displayed after the page has reloaded, but it will be saved.', 'wordpress-plugin-template' ),
					'type'			=> 'text_secret',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text', 'wordpress-plugin-template' )
				),
				array(
					'id' 			=> 'text_block',
					'label'			=> __( 'A Text Block' , 'wordpress-plugin-template' ),
					'description'	=> __( 'This is a standard text area.', 'wordpress-plugin-template' ),
					'type'			=> 'textarea',
					'default'		=> '',
					'placeholder'	=> __( 'Placeholder text for this textarea', 'wordpress-plugin-template' )
				),
				array(
					'id' 			=> 'single_checkbox',
					'label'			=> __( 'An Option', 'wordpress-plugin-template' ),
					'description'	=> __( 'A standard checkbox - if you save this option as checked then it will store the option as \'on\', otherwise it will be an empty string.', 'wordpress-plugin-template' ),
					'type'			=> 'checkbox',
					'default'		=> ''
				),
				array(
					'id' 			=> 'select_box',
					'label'			=> __( 'A Select Box', 'wordpress-plugin-template' ),
					'description'	=> __( 'A standard select box.', 'wordpress-plugin-template' ),
					'type'			=> 'select',
					'options'		=> array( 'drupal' => 'Drupal', 'joomla' => 'Joomla', 'wordpress' => 'WordPress' ),
					'default'		=> 'wordpress'
				),
				array(
					'id' 			=> 'radio_buttons',
					'label'			=> __( 'Some Options', 'wordpress-plugin-template' ),
					'description'	=> __( 'A standard set of radio buttons.', 'wordpress-plugin-template' ),
					'type'			=> 'radio',
					'options'		=> array( 'superman' => 'Superman', 'batman' => 'Batman', 'ironman' => 'Iron Man' ),
					'default'		=> 'batman'
				),
				array(
					'id' 			=> 'multiple_checkboxes',
					'label'			=> __( 'Some Items', 'wordpress-plugin-template' ),
					'description'	=> __( 'You can select multiple items and they will be stored as an array.', 'wordpress-plugin-template' ),
					'type'			=> 'checkbox_multi',
					'options'		=> array( 'square' => 'Square', 'circle' => 'Circle', 'rectangle' => 'Rectangle', 'triangle' => 'Triangle' ),
					'default'		=> array( 'circle', 'triangle' )
				)
			)
		);

		$settings['extra'] = array(
			'title'					=> __( 'Extra', 'wordpress-plugin-template' ),
			'description'			=> __( 'These are some extra input fields that maybe aren\'t as common as the others.', 'wordpress-plugin-template' ),
			'fields'				=> array(
				array(
					'id' 			=> 'number_field',
					'label'			=> __( 'A Number' , 'wordpress-plugin-template' ),
					'description'	=> __( 'This is a standard number field - if this field contains anything other than numbers then the form will not be submitted.', 'wordpress-plugin-template' ),
					'type'			=> 'number',
					'default'		=> '',
					'placeholder'	=> __( '42', 'wordpress-plugin-template' )
				),
				array(
					'id' 			=> 'colour_picker',
					'label'			=> __( 'Pick a colour', 'wordpress-plugin-template' ),
					'description'	=> __( 'This uses WordPress\' built-in colour picker - the option is stored as the colour\'s hex code.', 'wordpress-plugin-template' ),
					'type'			=> 'color',
					'default'		=> '#21759B'
				),
				array(
					'id' 			=> 'an_image',
					'label'			=> __( 'An Image' , 'wordpress-plugin-template' ),
					'description'	=> __( 'This will upload an image to your media library and store the attachment ID in the option field. Once you have uploaded an imge the thumbnail will display above these buttons.', 'wordpress-plugin-template' ),
					'type'			=> 'image',
					'default'		=> '',
					'placeholder'	=> ''
				),
				array(
					'id' 			=> 'multi_select_box',
					'label'			=> __( 'A Multi-Select Box', 'wordpress-plugin-template' ),
					'description'	=> __( 'A standard multi-select box - the saved data is stored as an array.', 'wordpress-plugin-template' ),
					'type'			=> 'select_multi',
					'options'		=> array( 'linux' => 'Linux', 'mac' => 'Mac', 'windows' => 'Windows' ),
					'default'		=> array( 'linux' )
				)
			)
		);

		$settings = apply_filters( 'wordpress_plugin_template_settings_fields', $settings );

		return $settings;
	}

	/**
	 * Register plugin settings
	 * @return void
	 */
	public function register_settings () {
		if( is_array( $this->settings ) ) {
			foreach( $this->settings as $section => $data ) {

				// Add section to page
				add_settings_section( $section, $data['title'], array( $this, 'settings_section' ), 'wordpress_plugin_template_settings' );

				foreach( $data['fields'] as $field ) {

					// Validation callback for field
					$validation = '';
					if( isset( $field['callback'] ) ) {
						$validation = $field['callback'];
					}

					// Register field
					$option_name = $this->base . $field['id'];
					register_setting( 'wordpress_plugin_template_settings', $option_name, $validation );

					// Add field to page
					add_settings_field( $field['id'], $field['label'], array( $this, 'display_field' ), 'wordpress_plugin_template_settings', $section, array( 'field' => $field ) );
				}
			}
		}
	}
	
	public function register_dashboard(){
	
		$capability = 'manage_options';
		
		add_menu_page(
			'Mobile AppWidget - Mobile Applications List', 
			'Mob AppWidget', 
			$capability, 
			'mobile-appwidget-handle', 
			array($this, 'mobile_Appwidget_List')
		);
		
		$menu = array();
		$menu['1'] = add_submenu_page(
			'mobile-appwidget-handle', 
			'Mobile AppWidget - Mobile Applications List', 
			'Mobile Apps', 
			$capability, 
			'mobile-appwidget-handle', 
			array($this, 'mobile_Appwidget_List')
		);
			
		$menu['2'] = add_submenu_page('mobile-appwidget-handle', 'Mobile AppWidget - Campaigns', 'Campaigns', $capability, 'mobile-appwidget-campaigns', array($this, 'mobile_Appwidget_Campaigns') );
		// TODO: in the next version
		//$menu['3'] = add_submenu_page('mobile-appwidget-handle', 'Mobile AppWidget - Campaigns Statistics', 'Statistics', $capability, 'mobile-appwidget-stats', array($this, 'mobile_Appwidget_Stats') );

		//$s = $menu['4'] = add_submenu_page('mobile-appwidget-handle', 'Mobile AppWidget - Settings', 'Plugin Settings', 'administrator', 'mobile-appwidget-settings', array($this, 'settings_page') );
		//add_action( 'admin_print_styles-' . $s, array( $this, 'settings_assets' ) );
	}
	

	public function mobile_Appwidget_Campaigns(){
		wp_enqueue_style( 'bootstrap',  plugins_url().'/mobile-appwidget/assets/vendors/bootstrap-3.2.0-dist/css/bootstrap.min.css');
    	wp_enqueue_script( 'bootstrap', plugins_url().'/mobile-appwidget/assets/vendors/bootstrap-3.2.0-dist/js/bootstrap.min.js');

		wp_enqueue_style( 'datetime',  plugins_url().'/mobile-appwidget/assets/vendors/bootstrap-datetimepicker/bootstrap-datetimepicker.min.css');
    	wp_enqueue_script( 'datetime', plugins_url().'/mobile-appwidget/assets/vendors/bootstrap-datetimepicker/bootstrap-datetimepicker.min.js');
		
		global $wpdb;
		
		$campaign = array(
			'id' => '',
			'name' => '',
			'start_day' => date('Y-m-d H:i'),
			'end_day' => '',
			'total_clicks' => '',
			'daily_clicks' => '',
			'total_views' => '',
			'daily_views' => '',
		);
		
		// convert to array encoded apps
		if (isset($_POST['apps'])){
			parse_str($_POST['apps'], $appsArray);
			$_POST['apps'] = $appsArray;
			unset($appsArray);
		}
		
		if (isset($_POST['updateCampaign'])){
			$this->campaignAppObj->updateCampaign($_POST);
		}else if (isset($_POST['addCampaign'])){
			$this->campaignAppObj->saveNewCampaign($_POST);
			$_GET['action'] = 'default';
		}		
		
		$defaultSave = 'add';
		$action = 'default';
		if (isset($_GET['action']))
			$action = $_GET['action'];
			
		$campaignApps = array();
			
		switch($action){
			case 'edit':
				//TODO: load app

				$defaultSave = 'edit';				
				$campaign = $this->campaignAppObj->getCampaignById($_GET['id']);
				$campaignApps = $this->campaignAppObj->getCampaignApps($_GET['id']);

				//TODO: fill $campaignApps with already selected apps
				//$campaignApps
			case 'add-campaign':

				$allMobileApps = $this->mobileAppObj->getAllMobileApplications();
				include(WP_PLUGIN_DIR.'/mobile-appwidget/views/addEditCampaign.php');
				break;
				
			case 'start_campaign':
				$this->campaignAppObj->setCampaignStatus($_GET['id'], 'start');
				$results = $this->campaignAppObj->getCampaigns(); 

				include(WP_PLUGIN_DIR.'/mobile-appwidget/views/campaignsList.php');				
				break;
				
			case 'pause_campaign':
				$this->campaignAppObj->setCampaignStatus($_GET['id'], 'pause');
				$results = $this->campaignAppObj->getCampaigns(); 

				include(WP_PLUGIN_DIR.'/mobile-appwidget/views/campaignsList.php');				
				break;
				
			case 'delete':
				//TODO: delete app
				$this->campaignAppObj->deleteCampaign($_GET['id']);
			default:
				$results = $this->campaignAppObj->getCampaigns(); 

				include(WP_PLUGIN_DIR.'/mobile-appwidget/views/campaignsList.php');
		}		
	}
	
	public function mobile_Appwidget_List(){
		wp_enqueue_style( 'bootstrap',  plugins_url().'/mobile-appwidget/assets/vendors/bootstrap-3.2.0-dist/css/bootstrap.min.css');
    	wp_enqueue_script( 'bootstrap', plugins_url().'/mobile-appwidget/assets/vendors/bootstrap-3.2.0-dist/js/bootstrap.min.js');
		//wp_enqueue_script( 'myuploader', plugins_url().'/mobile-appwidget/assets/vendors/droparea.js');
		
		global $wpdb;
		
		$app = array(
			'id' => '',
            'dev_id' => '',
			'dev_name' => '',
            'title' => '',
            'description' => '',
            'price' => '',
            'currency' => '',
            'thumb'	=> '',
		);
		
		if (isset($_POST['updateApp'])){
			$this->mobileAppObj->updateApplication($_POST);
		}else if (isset($_POST['addApp'])){
			$this->mobileAppObj->saveNewApplication($_POST);
			$_GET['action'] = 'default';
		}

		$defaultSave = 'add';
		
		$action = 'default';
		if (isset($_GET['action']))
			$action = $_GET['action'];
			
		switch($action){
			case 'edit':
				//TODO: load app

				$defaultSave = 'edit';				
				$app = $this->mobileAppObj->getAppById($_GET['id']);
				$app['links'] = $this->mobileAppObj->getDownloadLinks($_GET['id']);
			case 'add-app':
			
				//wp_enqueue_script('media-upload');
				//wp_enqueue_script('thickbox');
				//wp_enqueue_style('thickbox');
				
				$devsList = $wpdb->get_results( '
					SELECT * FROM '.$wpdb->prefix.TB_DEVLIST.';', ARRAY_A );

				include(WP_PLUGIN_DIR.'/mobile-appwidget/includes/configs/currency.inc.php');
				include(WP_PLUGIN_DIR.'/mobile-appwidget/views/addEditApp.php');
				break;
			case 'delete':
				//TODO: delete app
				$this->mobileAppObj->deleteApp($_GET['id']);
			default:
				$results = $this->mobileAppObj->getAllMobileApplications();

				include(WP_PLUGIN_DIR.'/mobile-appwidget/views/appsList.php');
		}
	}

	public function mobile_Appwidget_Stats(){
		echo('stats');
	}
	
	public function settings_section ( $section ) {
		$html = '<p> ' . $this->settings[ $section['id'] ]['description'] . '</p>' . "\n";
		echo $html;
	}

	/**
	 * Generate HTML for displaying fields
	 * @param  array $args Field data
	 * @return void
	 */
	public function display_field ( $args ) {

		$field = $args['field'];

		$html = '';

		$option_name = $this->base . $field['id'];
		$option = get_option( $option_name );

		$data = '';
		if( isset( $field['default'] ) ) {
			$data = $field['default'];
			if( $option ) {
				$data = $option;
			}
		}

		switch( $field['type'] ) {

			case 'text':
			case 'password':
			case 'number':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value="' . $data . '"/>' . "\n";
			break;

			case 'text_secret':
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="text" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '" value=""/>' . "\n";
			break;

			case 'textarea':
				$html .= '<textarea id="' . esc_attr( $field['id'] ) . '" rows="5" cols="50" name="' . esc_attr( $option_name ) . '" placeholder="' . esc_attr( $field['placeholder'] ) . '">' . $data . '</textarea><br/>'. "\n";
			break;

			case 'checkbox':
				$checked = '';
				if( $option && 'on' == $option ){
					$checked = 'checked="checked"';
				}
				$html .= '<input id="' . esc_attr( $field['id'] ) . '" type="' . $field['type'] . '" name="' . esc_attr( $option_name ) . '" ' . $checked . '/>' . "\n";
			break;

			case 'checkbox_multi':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( in_array( $k, $data ) ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="checkbox" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '[]" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'radio':
				foreach( $field['options'] as $k => $v ) {
					$checked = false;
					if( $k == $data ) {
						$checked = true;
					}
					$html .= '<label for="' . esc_attr( $field['id'] . '_' . $k ) . '"><input type="radio" ' . checked( $checked, true, false ) . ' name="' . esc_attr( $option_name ) . '" value="' . esc_attr( $k ) . '" id="' . esc_attr( $field['id'] . '_' . $k ) . '" /> ' . $v . '</label> ';
				}
			break;

			case 'select':
				$html .= '<select name="' . esc_attr( $option_name ) . '" id="' . esc_attr( $field['id'] ) . '">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( $k == $data ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '">' . $v . '</option>';
				}
				$html .= '</select> ';
			break;

			case 'select_multi':
				$html .= '<select name="' . esc_attr( $option_name ) . '[]" id="' . esc_attr( $field['id'] ) . '" multiple="multiple">';
				foreach( $field['options'] as $k => $v ) {
					$selected = false;
					if( in_array( $k, $data ) ) {
						$selected = true;
					}
					$html .= '<option ' . selected( $selected, true, false ) . ' value="' . esc_attr( $k ) . '" />' . $v . '</label> ';
				}
				$html .= '</select> ';
			break;

			case 'image':
				$image_thumb = '';
				if( $data ) {
					$image_thumb = wp_get_attachment_thumb_url( $data );
				}
				$html .= '<img id="' . $option_name . '_preview" class="image_preview" src="' . $image_thumb . '" /><br/>' . "\n";
				$html .= '<input id="' . $option_name . '_button" type="button" data-uploader_title="' . __( 'Upload an image' , 'wordpress-plugin-template' ) . '" data-uploader_button_text="' . __( 'Use image' , 'wordpress-plugin-template' ) . '" class="image_upload_button button" value="'. __( 'Upload new image' , 'wordpress-plugin-template' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '_delete" type="button" class="image_delete_button button" value="'. __( 'Remove image' , 'wordpress-plugin-template' ) . '" />' . "\n";
				$html .= '<input id="' . $option_name . '" class="image_data_field" type="hidden" name="' . $option_name . '" value="' . $data . '"/><br/>' . "\n";
			break;

			case 'color':
				?><div class="color-picker" style="position:relative;">
			        <input type="text" name="<?php esc_attr_e( $option_name ); ?>" class="color" value="<?php esc_attr_e( $data ); ?>" />
			        <div style="position:absolute;background:#FFF;z-index:99;border-radius:100%;" class="colorpicker"></div>
			    </div>
			    <?php
			break;

		}

		switch( $field['type'] ) {

			case 'checkbox_multi':
			case 'radio':
			case 'select_multi':
				$html .= '<br/><span class="description">' . $field['description'] . '</span>';
			break;

			default:
				$html .= '<label for="' . esc_attr( $field['id'] ) . '"><span class="description">' . $field['description'] . '</span></label>' . "\n";
			break;
		}

		echo $html;
	}

	/**
	 * Validate individual settings field
	 * @param  string $data Inputted value
	 * @return string       Validated value
	 */
	public function validate_field ( $data ) {
		if( $data && strlen( $data ) > 0 && $data != '' ) {
			$data = urlencode( strtolower( str_replace( ' ' , '-' , $data ) ) );
		}
		return $data;
	}

	/**
	 * Load settings page content
	 * @return void
	 */
	public function settings_page () {

		// Build page HTML
		$html = '<div class="wrap" id="wordpress_plugin_template_settings">' . "\n";
			$html .= '<h2>' . __( 'Plugin Settings' , 'wordpress-plugin-template' ) . '</h2>' . "\n";
			$html .= '<form method="post" action="options.php" enctype="multipart/form-data">' . "\n";

				// Setup navigation
				$html .= '<ul id="settings-sections" class="subsubsub hide-if-no-js">' . "\n";
					$html .= '<li><a class="tab all current" href="#all">' . __( 'All' , 'wordpress-plugin-template' ) . '</a></li>' . "\n";

					foreach( $this->settings as $section => $data ) {
						$html .= '<li>| <a class="tab" href="#' . $section . '">' . $data['title'] . '</a></li>' . "\n";
					}

				$html .= '</ul>' . "\n";

				$html .= '<div class="clear"></div>' . "\n";

				// Get settings fields
				ob_start();
				settings_fields( 'wordpress_plugin_template_settings' );
				do_settings_sections( 'wordpress_plugin_template_settings' );
				$html .= ob_get_clean();

				$html .= '<p class="submit">' . "\n";
					$html .= '<input name="Submit" type="submit" class="button-primary" value="' . esc_attr( __( 'Save Settings' , 'wordpress-plugin-template' ) ) . '" />' . "\n";
				$html .= '</p>' . "\n";
			$html .= '</form>' . "\n";
		$html .= '</div>' . "\n";

		echo $html;
	}

	/**
	 * Main Mobile_AppWidget_Settings Instance
	 *
	 * Ensures only one instance of Mobile_AppWidget_Settings is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @see WordPress_Plugin_Template()
	 * @return Main Mobile_AppWidget_Settings instance
	 */
	public static function instance ( $parent ) {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self( $parent );
		}
		return self::$_instance;
	} // End instance()

	/**
	 * Cloning is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __clone () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __clone()

	/**
	 * Unserializing instances of this class is forbidden.
	 *
	 * @since 1.0.0
	 */
	public function __wakeup () {
		_doing_it_wrong( __FUNCTION__, __( 'Cheatin&#8217; huh?' ), $this->parent->_version );
	} // End __wakeup()

}