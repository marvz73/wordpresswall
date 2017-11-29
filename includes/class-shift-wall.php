<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       http://
 * @since      1.0.0
 *
 * @package    Shift_Wall
 * @subpackage Shift_Wall/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    Shift_Wall
 * @subpackage Shift_Wall/includes
 * @author     Marvin Ayaay <marvz73@gmail.com>
 */
class Shift_Wall{

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      Shift_Wall_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Post type.
	 *
	 * @since 4.7.0
	 * @access protected
	 * @var string
	 */
	protected $post_type;

	/**
	 * Instance of a post meta fields object.
	 *
	 * @since 4.7.0
	 * @access protected
	 * @var WP_REST_Post_Meta_Fields
	 */
	protected $meta;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'shift-wall';
		$this->version = '1.0.0';

		$this->post_type = 'shift_wall';
		
		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

		$this->meta = new WP_REST_Post_Meta_Fields( $this->post_type );

	}

	public function defaults($index = ''){
		$d = array(
			'sw_host'		=>	 'http://shift-realtime.herokuapp.com',
			'publish' 		=>	 '/api/v1/publish'
		);
		if(!empty($index))
 			return $d[$index];
	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - Shift_Wall_Loader. Orchestrates the hooks of the plugin.
	 * - Shift_Wall_i18n. Defines internationalization functionality.
	 * - Shift_Wall_Admin. Defines all hooks for the admin area.
	 * - Shift_Wall_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-shift-wall-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-shift-wall-i18n.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-shift-wall-admin.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-shift-wall-public.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/cherry-framework/cherry-core.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/fine-uploader.php';

		// require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/auth2/auth2.php';

		$this->loader = new Shift_Wall_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the Shift_Wall_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new Shift_Wall_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Shift_Wall_Admin( $this->get_plugin_name(), $this->get_version(), $this->defaults() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );

		//Plugin Post Type
		$this->loader->add_action('init', $plugin_admin, '_register_post_type');

		//Plugin admin menu
		$this->loader->add_action( 'admin_menu', $plugin_admin, 'menu' );
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Shift_Wall_Public( $this->get_plugin_name(), $this->get_version(), $this);

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		
		$this->loader->add_action( 'init', $plugin_public, '_download_file' );
		

		//load the view
		$this->loader->add_filter( 'the_content', $plugin_public, '_wall');

		//Notifications
		$this->loader->add_filter( 'wp_nav_menu_objects', $plugin_public, '_notification');

		//Init shiftRT
		// $this->loader->add_filter( 'wp_footer', $plugin_public, '_header_scripts');

		//Set the cookie
		$this->loader->add_filter( 'wp_footer', $plugin_public, '_footer_scripts');

		//REST API init
		$this->loader->add_action( 'rest_api_init', $plugin_public, '_shiftwall_rest_api_init', 10);

		//REST API
		$this->loader->add_filter('rest_prepare_shift_wall', $plugin_public, '_get_custom_meta', 10, 3);

		//OEMBED
		$this->loader->add_filter('embed_defaults', $plugin_public, 'wpb_oembed_defaults');


		//Rest API HOOK's
		$this->loader->add_action( 'rest_insert_shift_wall', $plugin_public, 'shiftwall_insert_feed', 10, 3 );
		$this->loader->add_action( 'rest_delete_shift_wall', $plugin_public, 'shiftwall_delete_feed', 10, 3 );

		$this->loader->add_action( 'rest_insert_comment', $plugin_public, 'shiftwall_insert_comment', 10, 3 );
		$this->loader->add_action( 'rest_delete_comment', $plugin_public, 'shiftwall_delete_comment', 10, 3 );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    Shift_Wall_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Register the function trigger in admin area. 
	 *
	 * Desc: Publishing an event
	 * @since    1.0.0
	 */
	public function trigger($event_name='', $data = array()){


		if(empty($event_name) && !function_exists('curl_version'))
			return;

		$options = $this->shift_wall_options();

		$data = array(
	        "event_name"		=>	 $event_name,
	        "data"				=>	 $data,
	        "organization"		=>	 $options['sw_app_id'],
	        "client_id"			=>	 $options['sw_client_id'],
	        "client_secret"		=>	 $options['sw_client_secret']
		);
		
		$content = json_encode($data);

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->defaults('sw_host') . $this->defaults('publish'));
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_POST, TRUE);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $content);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
		$res = curl_exec($ch);
		$status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		$response = json_decode($res);
		curl_close($ch);

		if($status != 200) {
			return $response;
		}else{
			return array();
		}

	}


	public function shift_wall_options(){
		
		$options = get_option('shift_wall_options');

		return $options;

	}




	//-------------------------------------------------------------------
	// REST API Customize													   //
	//-------------------------------------------------------------------





	




}
