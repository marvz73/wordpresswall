<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://
 * @since      1.0.0
 *
 * @package    Shift_Wall
 * @subpackage Shift_Wall/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Shift_Wall
 * @subpackage Shift_Wall/admin
 * @author     Marvin Ayaay <marvz73@gmail.com>
 */
class Shift_Wall_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * A reference to an instance of cherry framework core class.
	 *
	 * @since 1.0.0
	 * @access private
	 * @var   object
	 */
	private $core = null;

	/**
	 * HTML spinner.
	 *
	 * @since 1.0.0
	 * @var string
	 * @access private
	 */
	private $spinner = '<span class="loader-wrapper"><span class="loader"></span></span>';

	/**
	 * Dashicons.
	 *
	 * @since 1.0.0
	 * @var string
	 * @access private
	 */
	private $button_icon = '<span class="dashicons dashicons-yes icon"></span>';

	/**
	 * Dashicons.
	 *
	 * @since 1.0.0
	 * @var string
	 * @access private
	 */
	private $button_auth = '<span class="dashicons dashicons-admin-users icon"></span>';

	/**
	 * Dashicons.
	 *
	 * @since 1.0.0
	 * @var string
	 * @access private
	 */
	private $sw_host;
	private $sw_organization;
	private $sw_client_id;
	private $sw_client_secret;



	/**
	 * Dashicons.
	 *
	 * @since 1.0.0
	 * @var string
	 * @access private
	 */
	private $options;


	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $defaults = array() ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;


		$this->options = get_option('shift_wall_options');
		
		//Cherry init
		$this->get_core()->init_module( 'cherry-interface-builder', array() );
		$this->get_core()->init_module( 'cherry-toolkit', array() );
		$this->get_core()->init_module( 'cherry-utility', array() );

		//Auth2
		$this->sw_host				= $defaults['sw_host'];
		$this->sw_organization 		= '';
		$this->sw_client_id 		= !empty($this->options['sw_client_id']) ? $this->options['sw_client_id'] : '' ;
		$this->sw_client_secret 	= !empty($this->options['sw_client_secret']) ? $this->options['sw_client_secret'] : '';


		//Ajax handlers
		$this->init_handlers();

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Shift_Wall_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Shift_Wall_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/shift-wall-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/shift-wall-admin.js',
	 		array( 'jquery', 'wp-util', 'cherry-js-core', 'cherry-handler-js' ), 
			$this->version,
			false
		);


	}


	/**
	 * Load the functon view.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function _view($template, $params = array()) {
		ob_start();
		extract($params);
		require(plugin_dir_path(dirname( __FILE__ )) . '/admin/partials/' . $template . '.php');
		$var = ob_get_contents();
		ob_end_clean();
		return $var;
	}

	/**
	 * Loads the core functions. These files are needed before loading anything else in the
	 * plugin because they have required functions for use.
	 *
	 * @since  1.0.0
	 * @access public
	 * @return object
	 */
	public function get_core() {

		// global $chery_core_version;

		if ( null !== $this->core ) {
			return $this->core;
		}

		// if ( 0 < sizeof( $chery_core_version ) ) {
		// 	$core_paths = array_values( $chery_core_version );
		// 	require_once( $core_paths[0] );
		// } else {
		// 	die( 'Class Cherry_Core not found' );
		// }

		$this->core = new Cherry_Core( array(
			'base_dir' => trailingslashit( plugin_dir_path( __FILE__ ) ) . 'cherry-framework',
			'base_url' => trailingslashit( plugin_dir_url( __FILE__ ) ) . 'cherry-framework',
			'modules'  => array(
				'cherry-js-core'           => array(
					'autoload'  => true,
				),
				'cherry-toolkit'           => array(
					'autoload' => false,
				),
				'cherry-utility'           => array(
					'autoload' => false,
				),
				'cherry-ui-elements'       => array(
					'autoload' => false,
				),
				'cherry-post-meta' => array(
					'autoload' => false,
				),
				'cherry-term-meta' => array(
					'autoload' => false,
				),				
				'cherry-interface-builder' => array(
					'autoload' => false,
				),
				'cherry-handler'           => array(
					'autoload' => false,
				),
				// 'cherry-template-manager' => array(
				// 	'autoload' => false,
				// ),
				// 'cherry-dynamic-css' => array(
				// 	'autoload' => false,
				// ),
			),
		) );
		
		$this->core->load_all_modules();

		return $this->core;

	}

	/**
	 * Register the menu for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function menu(){

		//----Administrator----//
		add_options_page(
			'Shift Wall Feeds Page',
			'Shift Wall Settings',
			'manage_options',
			'shift-wall-settings',
			array( $this, '_settings_page' )
		);


	}

	/**
	 * Register the function _register_post_type in admin area.
	 *
	 * @since    1.0.0
	 */
	public function _register_post_type(){

		//------ FEEDS

		$labels = array(
			'name'               => __( 'Shift Feeds', 'shift_feed' ),
			'singular_name'      => __( 'Shift Feeds list', 'shift_feed' ),
			'add_new'            => __( 'Add Shift Feeds', 'shift_feed' ),
			'add_new_item'       => __( 'Add Shift Feeds', 'shift_feed' ),
			'edit_item'          => __( 'Edit Shift Feeds', 'shift_feed' ),
			'new_item'           => __( 'New Shift Feeds', 'shift_feed' ),
			'view_item'          => __( 'View Shift Feeds', 'shift_feed' ),
			'search_items'       => __( 'Search Shift Feeds', 'shift_feed' ),
			'not_found'          => __( 'No Shift Feeds found', 'shift_feed' ),
			'not_found_in_trash' => __( 'No Shift Feeds found in trash', 'shift_feed' ),
		);

		$supports = array(
			'title',
			'editor',
			// 'thumbnail',
			// 'revisions',
			// 'page-attributes',
			'post-formats',
			'comments',
			// 'cherry-layouts',
			// 'page-attributes',
			'custom-fields',
			'author',
		);

		$post_type = array(
			'labels'          => $labels,
			'supports'        => $supports,
			'public'          => true,
			'rewrite'         => array( 'slug' => 'shift-wall-archive', ), // Permalinks format
			'menu_position'   => 5,
			'menu_icon'       => (version_compare( $GLOBALS['wp_version'], '3.8', '>=')) ? 'dashicons-rss' : '',
			'can_export'      => true,
			'has_archive'     => true,
			'show_in_menu'	  => true,
			'show_in_rest' 	  => true,
			'taxonomies'      => array( 'post_format' ),
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'map_meta_cap'	  => true,
			// 'capability_type'    => 'shift_wall',
			// 'capabilities' => array(
			//     'publish_posts' 		=>	 'publish_shift_wall',
			//     'create_post' 			=>	 'create_shift_wall',
			//     'edit_post' 			=>	 'edit_shift_wall',
			//     'delete_post' 			=>	 'delete_shift_wall',
			//     // 'edit_posts' 			=>	 'edit_shift_wall',
			//     // 'edit_others_posts' 	=>	 'edit_other_shift_wall',
			//     // 'read_post' 			=>	 'read_shift_wall',
			//     // 'read_private_posts' 	=>	 'read_private_shift_wall',
			// ),

		);


		$args = array(
	        'labels'              => $labels,
	        // 'supports'            => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'custom-fields', ),
	        // 'taxonomies'          => array( 'portfolio_cat' ),
	        // 'hierarchical'        => true,
	        'public'              => true,
	        // 'show_ui'             => true,
	        // 'show_in_menu'        => true,
	        // 'show_in_nav_menus'   => true,
	        // 'show_in_admin_bar'   => true,
	        // 'menu_position'       => 5,
	        // 'can_export'          => true,
	        // 'has_archive'         => true,
	        // 'exclude_from_search' => false,
	        // 'publicly_queryable'  => true,
	        // 'rewrite'   =>  array('slug'    =>  'portfolio'),
	        // 'capability_type'     => 'page',
	    );

		flush_rewrite_rules();

		register_post_type( 'shift_wall', $post_type );


		$post_meta = array( // Validate and sanitize the meta value.
		    // Note: currently (4.7) one of 'string', 'boolean', 'integer',
		    // 'number' must be used as 'type'. The default is 'string'.
		    'type'         => 'integer',
		    // Shown in the schema for the meta key.
		    'description'  => 'Shift Wall custom post type meta.',
		    // Return a single value of the type.
		    'single'       => true,
		    // Show in the WP REST API response. Default: false.
		    'show_in_rest' => true,
		);

		register_meta( 'shift_wall', '_shift_wall_liked', $post_meta );

		$comment_meta = array( // Validate and sanitize the meta value.
		    // Note: currently (4.7) one of 'string', 'boolean', 'integer',
		    // 'number' must be used as 'type'. The default is 'string'.
		    'type'         => 'integer',
		    // Shown in the schema for the meta key.
		    'description'  => 'A meta key associated with a string meta value.',
		    // Return a single value of the type.
		    'single'       => true,
		    // Show in the WP REST API response. Default: false.
		    'show_in_rest' => true,
		);

		register_meta( 'comment', '_shift_wall_liked', $comment_meta );





		//------ NOTIFICATIONS

		$notification_labels = array(
			'name'               => __( 'Shift Notifications', 'shift_feed' ),
			'singular_name'      => __( 'Shift Notifications list', 'shift_feed' ),
			'add_new'            => __( 'Add Shift Notifications', 'shift_feed' ),
			'add_new_item'       => __( 'Add Shift Notifications', 'shift_feed' ),
			'edit_item'          => __( 'Edit Shift Notifications', 'shift_feed' ),
			'new_item'           => __( 'New Shift Notifications', 'shift_feed' ),
			'view_item'          => __( 'View Shift Notifications', 'shift_feed' ),
			'search_items'       => __( 'Search Shift Notifications', 'shift_feed' ),
			'not_found'          => __( 'No Shift Notifications found', 'shift_feed' ),
			'not_found_in_trash' => __( 'No Shift Notifications found in trash', 'shift_feed' ),
		);

		$notification_supports = array(
			'title',
			'editor',
			// 'thumbnail',
			// 'revisions',
			// 'page-attributes',
			'post-formats',
			// 'comments',
			// 'cherry-layouts',
			// 'page-attributes',
			'custom-fields',
			'author',
		);

		$notification_type = array(
			'labels'          => $notification_labels,
			'supports'        => $notification_supports,
			'public'          => true,
			'rewrite'         => array( 'slug' => 'shift-notification-archive', ), // Permalinks format
			'menu_position'   => 5,
			'menu_icon'       => (version_compare( $GLOBALS['wp_version'], '3.8', '>=')) ? 'dashicons-rss' : '',
			'can_export'      => true,
			'has_archive'     => true,
			'show_in_menu'	  => true,
			'show_in_rest' 	  => true,
			'taxonomies'      => array( 'post_format' ),
			'exclude_from_search' => false,
			'publicly_queryable'  => true,
			'map_meta_cap'	  => true,
		);

		flush_rewrite_rules();

		register_post_type( 'shift_notification', $notification_type );

		// user_id - user to be notify
		// readed - status if notification is already read
		// post_content(builtin) - Notification message e.g Marvin Aya-ay just like your post., Yeng Atetot just commented on your post.
		// type - type of notification e.g. posted, post_comment, post_like, comment_like

		$notification_user_id_meta = array(
		    'type'         => 'integer',
		    'description'  => 'Shift Notification custom post type meta.',
		    'single'       => true,
		    'show_in_rest' => true,
		);

		register_meta( 'shift_notification', 'user_id', $notification_user_id_meta );

		$notification_readed_meta = array(
		    'type'         => 'boolean',
		    'description'  => 'Shift Notification custom post type meta.',
		    'single'       => true,
		    'show_in_rest' => true,
		);

		register_meta( 'shift_notification', 'readed', $notification_readed_meta );

		$notification_type_meta = array(
		    'type'         => 'boolean',
		    'description'  => 'Shift Notification custom post type meta.',
		    'single'       => true,
		    'show_in_rest' => true,
		);

		register_meta( 'shift_notification', 'type', $notification_type_meta );

		$notification_object_id_meta = array(
		    'type'         => 'boolean',
		    'description'  => 'Shift Notification custom post type meta.',
		    'single'       => true,
		    'show_in_rest' => true,
		);

		register_meta( 'shift_notification', 'object_id', $notification_object_id_meta );

		$notification_object_type_meta = array(
		    'type'         => 'string',
		    'description'  => 'Shift Notification custom post type meta.',
		    'single'       => true,
		    'show_in_rest' => true,
		);

		register_meta( 'shift_notification', 'object_type', $notification_object_type_meta );

	}

	/**
	 * Register the function _get_pages in admin area.
	 *
	 * @since    1.0.0
	 */
	public function _get_pages(){
		$pages = get_pages();
		$page = array();

		foreach($pages as $i=>$p){
			$page[$p->ID] = $p->post_title;
		}	
			
		return $page;

	}

	/**
	 * Register the function _settings_page in admin area.
	 *
	 * @since    1.0.0
	 */
	public function _settings_page(){

		// cherry modules
		$builder = $this->get_core()->modules['cherry-interface-builder'];
		$utility = $this->get_core()->modules['cherry-utility']->utility;

		$data = array();
		$data['builder'] = $builder;

		//form
		$data['form'] = array(
			'shift_wall_settings_form' => array(),
		);

		$data['info'] = array(
			'shift-settings-section'  => array(
				'type'       => 'section',
				'parent'     => 'shift_wall_settings_form',
				'class'      => 'cherry-control info-block shift-settings-section',
			),
		);

		$default_settings = array(
			'sw_page'  => array(
				'type'       => 'select',
				'parent'     => 'shift-settings-section',
				'class'      => 'cherry-control info-block',
				'title'		 => 'Show on ',
				'options'	 =>	$this->_get_pages()
			),
			'sw_app_id'  => array(
				'type'       => 'text',
				'parent'     => 'shift-settings-section',
				'class'      => 'cherry-control info-block',
				'title'		 => 'App ID',
				// 'readonly'	 => true,
			),

			'sw_client_id'  => array(
				'type'       => 'text',
				'parent'     => 'shift-settings-section',
				'class'      => 'cherry-control info-block',
				'title'		 => 'Client ID'
			),

			'sw_client_secret'  => array(
				'type'       => 'text',
				'parent'     => 'shift-settings-section',
				'class'      => 'cherry-control info-block',
				'title'		 => 'Client Secret'
			)

		);

		$data['controls'] = $default_settings;

		$data['buttons'] = array(

			'shift-save-buttons'  => array(
				'type'          => 'button',
				'class'			=> '',
				'parent'        => 'shift-settings-section',
				'style'         => 'success',
				'content'       => '<span class="text">' . esc_html__( 'Save', 'shift-label' ) . '</span>' . $this->spinner . $this->button_icon,
				'view_wrapping' => false,
				'form'          => 'shift_wall_settings_form',
			),
			'shift-auth-buttons'  => array(
				'type'          => 'button',
				'class'			=> '',
				'parent'        => 'shift-settings-section',
				'style'         => 'default',
				'content'       => '<span class="text">' . esc_html__( 'Test Connection', 'shift-label' ) . '</span>' . $this->spinner,
				'view_wrapping' => false,
				'form'          => 'shift_wall_settings_form',
			),

		);
		
		if( ! empty($this->options) ){
			
			foreach($data['controls'] as $key => $control){
				if( ! empty($this->options[$key]) )
					$data['controls'][$key]['value'] = $this->options[$key];
			}

		}

		echo $this->_view('shift-wall-admin-display', $data);

	}

	/**
	 * Register the function init_handlers in admin area. 
	 *
	 * @since    1.0.0
	 */
	public function init_handlers(){

		$this->get_core()->init_module(
			'cherry-handler' ,
			array(
				'id'           => 'shift_wall_settings_form',
				'action'       => 'shift_wall_settings_form',
				'capability'   => 'manage_options',
				'callback'     => array( $this , 'ajax_save_setting' ),
				'sys_messages' => array(
					'invalid_base_data' => esc_html__( 'Unable to process the request without nonche or server error', 'shift-wall' ),
					'no_right'          => esc_html__( 'No right for this action', 'shift-wall' ),
					'invalid_nonce'     => esc_html__( 'Stop CHEATING!!!', 'shift-wall' ),
					'access_is_allowed' => esc_html__( 'Options save successfully.','shift-wall' ),
				),
			)
		);

		$this->get_core()->init_module(
			'cherry-handler' ,
			array(
				'id'           => 'shift_wall_settings_auth',
				'action'       => 'shift_wall_settings_auth',
				'capability'   => 'manage_options',
				'callback'     => array( $this , 'ajax_authorize' ),
				'sys_messages' => array(
					'invalid_base_data' => esc_html__( 'Unable to process the request without nonche or server error', 'shift-wall' ),
					'no_right'          => esc_html__( 'No right for this action', 'shift-wall' ),
					'invalid_nonce'     => esc_html__( 'Stop CHEATING!!!', 'shift-wall' ),
					'access_is_allowed' => esc_html__( 'Options save successfully.','shift-wall' ),
				),
			)
		);

	}

	/**
	 * Register the function ajax_save_setting in admin area. 
	 *
	 * @since    1.0.0
	 */
	function ajax_save_setting(){

		if ( ! empty( $_REQUEST['data'] ) ) {

			$data = $_REQUEST['data'];

			update_option('shift_wall_options', $data);

		} else {
			wp_send_json(
				array(
					"message"=>'Unable to process the request.',
					"type"=>"info-notice"
				)
			);
		}

	}

	/**
	 * Register the function ajax_authorize in admin area. 
	 *
	 * @since    1.0.0
	 */
	public function ajax_authorize(){



		print_r($_REQUEST);

		// if(!empty($this->options['sw_client_id']) && !empty($this->options['sw_client_secret'])){

		// 	$this->client = new OAuth2\Client($this->options['sw_client_id'], $this->options['sw_client_secret'], OAuth2\Client::AUTH_TYPE_AUTHORIZATION_BASIC);
		//     $authUrl = $this->client->getAuthenticationUrl($this->authorizeUrl, $this->redirectUrl, array("scope" => "identity"));

		// 	wp_send_json(
		// 		array(
		// 			"message"	=> 'Redirecting to authorize URL.',
		// 			'data'		=> array('auth_url'	=>	$authUrl),
		// 			"type"		=> "info-notice"
		// 		)
		// 	);

		// }
		// else
		// {
		// 	wp_send_json(
		// 		array(
		// 			"message"=>'Client ID and Client Secret is required.',
		// 			"type"=>"info-notice"
		// 		)
		// 	);
		// }

	}



}











// "client_secret": "mKR7KtDvHfupIREAIu8D1vsCbHxmQszwHCQjvoFHGgFi5Mp81P",
// "client_id": "y58iyjdPEQMBsdr6iwWzFH8zO5YfWEW1dhpEazAnTjNw3KoKCm",