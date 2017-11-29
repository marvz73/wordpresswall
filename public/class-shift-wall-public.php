<?php

/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://
 * @since      1.0.0
 *
 * @package    Shift_Wall
 * @subpackage Shift_Wall/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Shift_Wall
 * @subpackage Shift_Wall/public
 * @author     Marvin Ayaay <marvz73@gmail.com>
 */
class Shift_Wall_Public extends WP_REST_Posts_Controller{

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
	 * 
	 *
	 * @since 1.0.0
	 * @var string
	 * @access private
	 */
	private $sw_host;
	private $pushlish;
	private $sw_app_id;
	private $sw_client_id;
	private $sw_client_secret;
	private $parent;

	/**
	 * The option of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private $options;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version, $parent) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		$this->parent = $parent;
		$this->post_type = 'shift_wall';
		$this->options = get_option('shift_wall_options');
		$this->sw_host				= $parent->defaults('sw_host');
		$this->pushlish 			= '/api/v1/publish';
		$this->sw_app_id 			= !empty($this->options['sw_app_id']) ? $this->options['sw_app_id'] : '' ;;
		$this->sw_client_id 		= !empty($this->options['sw_client_id']) ? $this->options['sw_client_id'] : '' ;
		$this->sw_client_secret 	= !empty($this->options['sw_client_secret']) ? $this->options['sw_client_secret'] : '';
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
		require(plugin_dir_path(dirname( __FILE__ )) . '/public/partials/' . $template . '.php');
		$var = ob_get_contents();
		ob_end_clean();
		return $var;
	}



	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		//Semantic
		wp_enqueue_style( $this->plugin_name . '-button', plugin_dir_url( __FILE__ ) . 'css/semantic.css', array(), $this->version, 'all' );

		//Magnify
		wp_enqueue_style( $this->plugin_name . '-magnific', plugin_dir_url( __FILE__ ) . 'css/magnific-popup.css', array(), $this->version, 'all' );

		//Video
		wp_enqueue_style( $this->plugin_name . '-video-js', plugin_dir_url( __FILE__ ) . 'css/video/video-js.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-video-js-record', plugin_dir_url( __FILE__ ) . 'css/video/videojs.record.min.css', array(), $this->version, 'all' );
		wp_enqueue_style( $this->plugin_name . '-vdieo-style', plugin_dir_url( __FILE__ ) . 'css/video/style.css', array(), $this->version, 'all' );


		wp_enqueue_style( $this->plugin_name . '-images-grid', plugin_dir_url( __FILE__ ) . 'css/images-grid.css', array(), $this->version, 'all' );

		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/shift-wall-public.css', array(), $this->version, 'all' );



	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		// //Register Socket.IO
		wp_register_script( $this->plugin_name.'-sockets', $this->sw_host . '/shift.js', '', $this->version, true );

		// //Register ReactJS Framework
		// wp_register_script( $this->plugin_name.'-react', plugin_dir_url( __FILE__ ).'js/react.min.js', '', $this->version, false );
		// wp_register_script( $this->plugin_name.'-react-dom', plugin_dir_url( __FILE__ ).'js/react-dom.min.js', '', $this->version, false );
		// wp_register_script( $this->plugin_name.'-browser', plugin_dir_url( __FILE__ ).'js/browser.min.js', '', $this->version, false );

		// //Timeago
		// wp_register_script( $this->plugin_name.'-timeago', plugin_dir_url( __FILE__ ).'js/timeago.js', array(), $this->version, false );

	


	}

	/**
	 * Register the manual enqueue for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function manual_enqueue() {

		global $wp;

		//SocketJS
		wp_enqueue_script( $this->plugin_name . '-sockets' );

		//Semantic Framework
		wp_enqueue_script( $this->plugin_name . '-semantic', plugin_dir_url( __FILE__ ) . 'js/semantic.js', array( 'jquery' ), $this->version, false );

		//Public script
		wp_enqueue_script( $this->plugin_name . '-public', plugin_dir_url( __FILE__ ) . 'js/shift-wall-public.js', array(), $this->version, false );

		//App JS in Webpack
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/build/bundle.min.js', array(), $this->version, false );

		//Photoset
		wp_enqueue_script( $this->plugin_name . '-images-grid', plugin_dir_url( __FILE__ ) . 'js/images-grid.js', array(), $this->version, false );

		//Video
		wp_enqueue_script( $this->plugin_name . '-video-js', plugin_dir_url( __FILE__ ) . 'js/video/video.min.js', array(), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '-RecordRTC', plugin_dir_url( __FILE__ ) . 'js/video/RecordRTC.js', array(), $this->version, false );
		wp_enqueue_script( $this->plugin_name . '-video-js-record', plugin_dir_url( __FILE__ ) . 'js/video/videojs.record.min.js', array(), $this->version, false );

		// Localize object.
		wp_localize_script( $this->plugin_name, 'shiftwall',
			array(
				'ajax_url'         	=>	 admin_url( 'admin-ajax.php' ),
				'current_url'		=>	 add_query_arg( $wp->query_string, '', home_url( $wp->request ) ),
				'base_url'		   	=>	 home_url(),
				'root' 				=> 	 esc_url_raw( rest_url() ),
    			'nonce' 			=> 	 wp_create_nonce( 'wp_rest' ),
    			'uid'				=>	 get_current_user_id(),
				'token'			   	=>	 time(),
				'refresh'		   	=>	 base64_encode(time() * 100),
				'namespace'			=>	$this->sw_host . '/' . $this->options['sw_app_id'],
				'settings'				=>	 array(
					"characterLength"	=>	600,
					'app_id'			=>	$this->options['sw_app_id'],
				)
			)
		);

	}

	/**
	 * Register the _notification menu icon for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function _notification( $menu_items ) {

	    foreach ( $menu_items as $menu_item ) {

	        if ( '#shift_wall_notification#' == $menu_item->title ) {

	            // global $shortcode_tags;

	            $menu_item->title = "<span id='shift-wall-notification'>Notification</span>";
	            
	        }
	    }

	    if(!empty($this->options['sw_page']) && (int)$this->options['sw_page'] !== get_the_ID()){
	    	// $this->manual_enqueue();
		}

	    return $menu_items;
	}



	public function _create_notification($source, $type, $remove = false){


		// user_id - user to be notify
		// readed - status if notification is already read
		// post_content(builtin) - Notification message e.g Marvin Aya-ay just like your post., Yeng Atetot just commented on your post.
		// type - type of notification e.g. posted, post_comment, post_like, comment_like

		$message = '';

		switch ($type) {

			case 'posted': //New Post
					$message = 'just posted a new post: ';
				break;

			case 'post_comment':
					$message = 'just commented on your post: ';
				break;

			case 'post_like':
					$message = 'likes your post: ';
				break;

			case 'comment_like':
					$message = 'likes your comment: ';
				break;

			default:
				$message = '';
				break;

		}
		
		if( !$remove ){
	
			if($message != ''){

				$notification_id = wp_insert_post(
					array(
						'post_title'	=>	'Notification-' . time(),
						'post_status'	=>	'publish',
						'post_type'		=>	'shift_notification', 
						'post_content'	=>	$message
					)
				);

				if(is_numeric($notification_id)){

					update_post_meta( $notification_id, 'readed', false );
					update_post_meta( $notification_id, 'type', $type );
					update_post_meta( $notification_id, 'object_type', $source['object_type'] );
					update_post_meta( $notification_id, 'object_id', $source['object_id'] );

					//is object a comment or post
					if( is_email($source['author']) ){
						$user = get_user_by('user_email', $source['author']);
						update_post_meta( $notification_id, 'user_id', $user->ID );
					}else{
						update_post_meta( $notification_id, 'user_id', $source['author'] );	
					}

					$object = get_post($notification_id);

					$notification = array(
						'user'		=>	array(
							'id' 			=>	$object->post_author,
							'first_name'	=>	get_user_meta($object->post_author, 'first_name', true),
							'last_name'		=>	get_user_meta($object->post_author, 'last_name', true)
						),
						'notify_id'			=>	$source['author'],
						'object'			=>	strip_tags($message),
						'object_rendered' 	=>	$message,
						'type'				=>	$type,
						'readed'			=>	false,
						'time'				=>	time()
					);

					//Notification
					$this->parent->trigger( 'shift_wall_notification', $notification );

				}

			}

		}else{

			$args = array(
				'post_type' 		=>	'shift_notification', 
		  		'meta_query' => array(
			        'relation' => 'AND',
			        array(
			            'key'     => 'object_id',
			            'value'   => $source['object_id'],
			        ),
			        array(
			            'key'     => 'type',
			            'value'   => $type,
			        ),
			    ),
			    'post_author'		=> 	$source['author']
				// 'type'		=> $type,
				// 'meta_key' 			=>	'object_id', 
				// 'meta_value' 		=>	$source['object_id'],
			);

			$notifications = new WP_Query( $args );

			wp_delete_post($notifications->post->ID, true);

			$notification_remove = array(
				'object_id'				=>	$source['object_id'],
				'user_id'				=>	$source['author'],
				'notificaction_id'		=>	$notifications->post->ID
			);

			//Notification
			$this->parent->trigger( 'shift_wall_unnotification',  $notification_remove);

		}


	}



	/**
	 * Register the _wall for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function _wall( $content )
	{

		if(!empty($this->options['sw_page']) && (int)$this->options['sw_page'] === get_the_ID()){
			$data = array();

			$this->manual_enqueue();

	        $content .= $this->_view('shift-wall-public-feed', $data);
	   
		}

	    return $content;
	}

	/**
	 * Register the _footer_scripts for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function _header_scripts(){

		// echo "<script type='text/javascript'> jQuery( document ).ready(function(){ var shiftRT = shift(shiftwall.namespace); });</script>";

	}

	/**
	 * Register the _footer_scripts for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function _footer_scripts(){

		echo "<script src='".plugin_dir_url( __FILE__ ) . 'js/app.js'."' type='text/babel'></script>";

	}

	//Download wall file
	public function _download_file(){
			
		if(!empty($_GET['f'])){
			$file  = $_GET['f'];
			header("Content-Disposition: attachment; filename=\"" . basename($file) . "\"");
			header("Content-Type: application/force-download");
			header("Content-Length: " . filesize($file));
			header("Connection: close");

			echo "<script> setTimeout (window.close, 5000); </script>";

		}

	}

	//Custom oEmbed Size
	public function wpb_oembed_defaults($embed_size) {
	 	$embed_size['width'] = 400;
        $embed_size['height'] = 550;
	    return $embed_size;
	}

	//Get commented users on post
	public function get_commented_users( $post_id ){
		
		$users = array();

		if( $post_id == '' )
			return $users;

		$args = array(
 			'post_id' 	=> $post_id
 		);

 		$commented_users = get_comments( $args );

 		foreach($commented_users as $user){

 			array_push($users, $user->user_id);

 		}


 		return $users;
	}








	//---------------------------------
	// REST API
	//---------------------------------






	/**
	 * Register the function _rest_api_custom_meta in admin area. 
	 *
	 * @since    1.0.0
	 */
    public function _get_custom_meta($response, $post) {
        
 		$response->data['author_meta'] = array(
 			'first_name'	=>	get_user_meta($post->post_author, 'first_name', true),
 			'last_name'		=>	get_user_meta($post->post_author, 'last_name', true),
 			'avatar'		=>	get_avatar_url($post->post_author)
 		);

 		$response->data['_post_commented_users'] = $this->get_commented_users( $post->ID );


 		// $response->data['showComment'] = false;
 		$response->data['comment_count'] = wp_count_comments( $post->ID );

     	return $response;

    }

	/**
	 * Register the function _rest_api_init in admin area. 
	 *
	 * @since    1.0.0
	 */
    public function _shiftwall_rest_api_init(){

    	// Notifications
		register_rest_route( 'wp/v2', '/notifications', array(
			'methods' => 'GET',
			'callback' => array($this, 'get_shift_wall_notifications'),
			'schema'          => null,
		) );

    	//Canvas the custom upload
		register_rest_route( 'wp/v2', '/canvas', array(
			'methods' => 'POST',
			'callback' => array($this, '_upload_canvas'),
			'schema'          => null,
		) );

		//Feed attachment
    	register_rest_field( 'shift_wall',
	        '_shift_wall_attachment',
	        array(
	            'get_callback'    => array($this, '_get_shift_wall_attachment'),
	            'update_callback' => array($this, '_put_shift_wall_attachment'),
	            'schema'          => null,
	        )
	    );

    	//Post likes
    	register_rest_field( 'shift_wall',
	        '_shift_wall_feed_liked',
	        array(
	            'get_callback'    => array($this, '_get_shift_wall_feed_liked'),
	            // 'update_callback' => array($this, '_put_shift_wall_feed_liked'),
	            'schema'          => null,
	        )
	    );

    	//Comment likes
    	register_rest_field( 'comment',
	        '_shift_wall_comment_liked',
	        array(
	            'get_callback'    => array($this, '_get_shift_wall_comment_liked'),
	            // 'update_callback' => array($this, '_put_shift_wall_comment_liked'),
	            'schema'          => null,
	        )
	    );

    	//Comment 
    	register_rest_field( 'shift_wall',
	        'comment_approved',
	        array(
	            'get_callback'    => array($this, '_get_shift_wall_comment_content'),
	            // 'update_callback' => array($this, '_put_shift_wall_comment_content'),
	            'schema'          => null,
	        )
	    );
    
    }



		// function shiftwall_rest_prepare_post( $post, $request ) {
		    
		//     $post_data = array();
		 
		//     $schema = $this->prefix_get_comment_schema();
		 
		//     // We are also renaming the fields to more understandable names.
		//     if ( isset( $schema['properties']['id'] ) ) {
		//         $post_data['id'] = (int) $post->ID;
		//     }
		 
		//     if ( isset( $schema['properties']['author'] ) ) {
		//         $post_data['author'] = (int) $post->user_id;
		//     }

		//     if ( isset( $schema['properties']['title'] ) ) {
		//         $post_data['title'] = apply_filters( 'post_title', $post->post_title, $post );
		//     }

		//     if ( isset( $schema['properties']['content'] ) ) {
		//         $post_data['content'] = apply_filters( 'post_content', $post->post_content, $post );
		//     }
		 
		//     return rest_ensure_response( $post_data );
		// }

		// function prefix_prepare_for_collection( $response ) {

		//     if ( ! ( $response instanceof WP_REST_Response ) ) {
		//         return $response;
		//     }
		 
		//     $data = (array) $response->get_data();

		//     $server = rest_get_server();
		 
		//     if ( method_exists( $server, 'get_compact_response_links' ) ) {
		//         $links = call_user_func( array( $server, 'get_compact_response_links' ), $response );
		//     } else {
		//         $links = call_user_func( array( $server, 'get_response_links' ), $response );
		//     }
		 
		//     if ( ! empty( $links ) ) {
		//         $data['_links'] = $links;
		//     }
		 
		//     return $data;
		// }
























	/**
	 * Register the function get_request_method in admin area. 
	 *
	 * Desc: Method
	 * @since    1.0.0
	 */
	public function get_request_method() {
	    global $HTTP_RAW_POST_DATA;
	    if(isset($HTTP_RAW_POST_DATA)) {
	    	parse_str($HTTP_RAW_POST_DATA, $_POST);
	    }
	    if (isset($_POST["_method"]) && $_POST["_method"] != null) {
	        return $_POST["_method"];
	    }
	    return $_SERVER["REQUEST_METHOD"];
	}

	/**
	 * Register the function get_shift_wall_notifications in admin area. 
	 *
	 * Desc: Method
	 * @since    1.0.0
	 */
	public function get_shift_wall_notifications($request){

		$notifications_list = array();

		$user = $request->get_query_params();

		$method = $this->get_request_method();
		if ($method != "GET") {
		    return new WP_Error( '405', __( 'message', 'Method Not Allowed.' ) );
		}

		if(empty($user['uid'])){
			return new WP_Error( '500', __( 'message', 'Unauthorize access.' ) );	
		}

		$args = array( 
			'post_type' 		=>	'shift_notification', 
			'posts_per_page' 	=>	10,
			'meta_key' 			=>	'user_id', 
			'meta_value' 		=>	$user['uid']
		);

		$notifications = new WP_Query( $args );

		foreach($notifications->posts as $index => $notification){


			$object_type = get_post_meta( $notification->ID, 'object_type', true );

			// print_r($object_type);

			if( $object_type == 'comment'){

				$notify_id = get_post_meta($notification->ID, 'user_id', true);
				if($notification->post_author != $notify_id){
					$object_id = get_post_meta($notification->ID, 'object_id', true);
					$object = get_comment($object_id);
					array_push($notifications_list,
						array(
							'user'	=>	array(
									'id' 			=>	$notification->post_author,
									'first_name'	=>	get_user_meta($notification->post_author, 'first_name', true),
									'last_name'		=>	get_user_meta($notification->post_author, 'last_name', true)
								),
							'notify_id'	=>	$notify_id,
							// 'object_id'	=>	get_post_meta($notification->ID, 'object_id', true),
							'object'	=>	strip_tags($object->post_content),
							'message' 	=>	$notification->post_content,
							'type'		=>	get_post_meta($notification->ID, 'type', true),
							'readed'	=>	get_post_meta($notification->ID, 'readed', true),
							'time'		=>	$notification->post_date
						)
					);
				}

			}else{

				$notify_id = get_post_meta($notification->ID, 'user_id', true);
				if($notification->post_author != $notify_id){
					$object_id = get_post_meta($notification->ID, 'object_id', true);
					$object = get_post($object_id);
					array_push($notifications_list,
						array(
							'user'	=>	array(
									'id' 			=>	$notification->post_author,
									'first_name'	=>	get_user_meta($notification->post_author, 'first_name', true),
									'last_name'		=>	get_user_meta($notification->post_author, 'last_name', true)
								),
							'notify_id'	=>	$notify_id,
							// 'object_id'	=>	get_post_meta($notification->ID, 'object_id', true),
							'object'	=>	strip_tags($object->post_content),
							'message' 	=>	$notification->post_content,
							'type'		=>	get_post_meta($notification->ID, 'type', true),
							'readed'	=>	get_post_meta($notification->ID, 'readed', true),
							'time'		=>	$notification->post_date
						)
					);
				}

			}

		}// EOF foreach

		return new WP_REST_Response( $notifications_list, 200 );

	}




	/**
	 * Register the function _upload_canvas in admin area. 
	 *
	 * Desc: Upload image capture on camera
	 * @since    1.0.0
	 */
    public function _upload_canvas(WP_REST_Request $request){

    	// sleep(10);

 		$data = array();

		$upload_dir = wp_upload_dir(date("Y/mm"));
		$_REQUEST['qquuid'] = $upload_dir['subdir'];

		$uploader = new UploadHandler();
		// Specify the list of valid extensions, ex. array("jpeg", "xml", "bmp")
		$uploader->allowedExtensions = array(); // all files types allowed by default
		// Specify max file size in bytes.
		$uploader->sizeLimit = null;
		// Specify the input name set in the javascript.
		$uploader->inputName = "qqfile"; // matches Fine Uploader's default inputName value by default
		// If you want to use the chunking/resume feature, specify the folder to temporarily save parts.
		$uploader->chunksFolder = "chunks";
		$method = $this->get_request_method();

		$dir = $upload_dir['basedir'].'/shiftwall/';

		if ( ! file_exists( $dir ) ) {
	 		wp_mkdir_p( $dir );
	 	}

		if ($method == "POST") {

		    // header("Content-Type: text/plain");
		    // Assumes you have a chunking.success.endpoint set to point here with a query parameter of "done".
		    // For example: /myserver/handlers/endpoint.php?done
		    if (isset($_GET["done"])) {
		        $result = $uploader->combineChunks($dir);
		    }
		    // Handles upload requests
		    else {
		        // Call handleUpload() with the name of the folder, relative to PHP's getcwd()
		        $result = $uploader->handleUpload($dir);
		        // To return a name used for uploaded file you can use the following line.
		        $result["uploadName"] = $uploader->getUploadName();
		    }

			$file_dir = $dir . $upload_dir['subdir'] . '/' .  $result["uploadName"];

			$filename = basename($file_dir);

			$upload_file = wp_upload_bits($filename, null, file_get_contents($file_dir));
			
			$attachment = array();

			if (!$upload_file['error']) {

				$wp_filetype = wp_check_filetype($filename, null );
				
				$attachment = array(
					'ID'				=>	'',
					'post_mime_type' 	=> 	$wp_filetype['type'],
					'ext' 				=> 	$wp_filetype['ext'],
					'post_title' 		=> 	preg_replace('/\.[^.]+$/', '', $filename),
					'post_status' 		=> 	'inherit'
				);

				$attachment_id = wp_insert_attachment( $attachment, $upload_file['file'] );

				if (!is_wp_error($attachment_id)) {
					$attachment_data = array();
					if($wp_filetype['ext'] == 'image' || $wp_filetype['ext'] == 'audio' || $wp_filetype['ext'] == 'video'){
						require_once(ABSPATH . "wp-admin" . '/includes/image.php');
						$attachment_data = wp_generate_attachment_metadata( $attachment_id, $upload_file['file'] );
						wp_update_attachment_metadata( $attachment_id,  $attachment_data );
						$attachment = wp_get_attachment_metadata($attachment_id);
					}
				}

				$attachment['ID'] = $attachment_id;

				// if($success){
					return new WP_REST_Response( $attachment, 200 );
				// }else{
					// return new WP_Error( '500', __( 'message', 'Unable to save the file.' ) );	
				// }

			}
			else if(!$success){
				return new WP_Error( '500', __( 'message', 'Unable to save the file.' ) );	
			}
			else{
				return new WP_Error( '500', __( 'message', $upload_file['error'] ) );	
			}

		}		
		// for delete file requests
		else if ($method == "DELETE") {
		    $result = $uploader->handleDelete("file");
		    echo json_encode($result);
		}
		else {
		    return new WP_Error( '405', __( 'message', 'Method Not Allowed.' ) );	
		}


    }


	/**
	 * Register the function _get_shift_wall_attachment in admin area. 
	 *
	 * Desc: get meta
	 * @since    1.0.0
	 */
    public function _get_shift_wall_attachment( $object, $field_name, $request ) {
	    if(!empty($field_name)){

	    	$_attachment = array();

	    	$attachmentIds = get_post_meta( $object[ 'id' ], $field_name, true );

	    	if(!empty($attachmentIds))
	    	{
		    	foreach($attachmentIds  as $thumb_id){

		    		array_push($_attachment, array(
		    				'attachment_id'			=>	$thumb_id,
		    				'ext'					=>	wp_check_filetype( wp_get_attachment_url( $thumb_id ) ),
		    				'type'					=>	get_post_mime_type($thumb_id),
		    				'attachment_thumbnail'	=>	wp_get_attachment_thumb_url($thumb_id),
		    				'attachment'			=>	wp_get_attachment_metadata($thumb_id),
		    				'attachment_url'		=>	wp_get_attachment_url($thumb_id)
		    			)
		    		);

		    	}
	    	}

	    	//groupby type
			$result = array();
			foreach ($_attachment as $index=>$data) {
			  $id = explode('/', $data['type'])[0];

			  if (isset($result[$id])) {
			     $result[$id][] = $data;
			  } else {
			     $result[$id] = array($data);
			  }
			}

		    return $result;
	    }

	}

	/**
	 * Register the function _put_shift_wall_attachment in admin area. 
	 *
	 * Desc: Update meta
	 * @since    1.0.0
	 */
	public function _put_shift_wall_attachment( $value, $object, $field_name ) {

		if(!empty($field_name)){
			$_attachment = get_post_meta($object->ID, $field_name, true);
			
			if(empty($_attachment))
				$_attachment = array();

			$_merge_attachment = array_merge($_attachment, $value);

			return update_post_meta( $object->ID, $field_name, $_merge_attachment);
		}
	}



	/**
	 * Register the function _get_shift_wall_feed_liked in admin area. 
	 *
	 * Desc: get meta
	 * @since    1.0.0
	 */
	public function _get_shift_wall_feed_liked( $object, $field_name, $request ) {

	    $results = get_post_meta( $object[ 'id' ], $field_name, true );

	    if(empty($results))
	    	$results = array();
	    

	    return $results;

	}


	/**
	 * Register the function _put_shift_wall_feed_liked in admin area. 
	 *
	 * Desc: Update meta
	 * @since    1.0.0
	 */
	public function _put_shift_wall_feed_liked( $value, $object, $field_name ) {

	    if ( ! $value || ! is_string( $value ) ) {
	        return;	
	    }

	    $is_liked = get_post_meta( $object->ID, $field_name, true );
	 	   
	   	if( empty($is_liked) ){
	   		$is_liked = array();
	   		array_push( $is_liked, $value );
			
			$this->_create_notification( array('object_type' => 'post', 'object_id' => $object->ID, 'author' => $object->post_author), 'post_like' );
			$this->parent->trigger( 'shift_wall_feed_liked', array( 'id' => $object->ID, '_shift_wall_feed_liked' => $is_liked ) );
	
	   	}else if( !empty ( $is_liked ) && !in_array($value, $is_liked ) ){
	   		array_push( $is_liked, $value );
		
			$this->_create_notification( array('object_type' => 'post', 'object_id' => $object->ID, 'author' => $object->post_author), 'post_like' );	   		
			$this->parent->trigger( 'shift_wall_feed_liked', array( 'id' => $object->ID, '_shift_wall_feed_liked' => $is_liked ) );

	   	}else{
	   		$index = array_search( $value, $is_liked );
	   		array_splice( $is_liked, $index, 1 );

	   		$this->_create_notification( array('object_type' => 'post', 'object_id' => $object->ID, 'author' => $object->post_author), 'post_like', true );
			$this->parent->trigger( 'shift_wall_feed_unliked', array( 'id' => $object->ID, '_shift_wall_feed_liked' => $is_liked ) );

	   	}
	 
	   	$liked = update_post_meta( $object->ID, $field_name, $is_liked );

	    return $liked;
	}


	/**
	 * Register the function _get_shift_wall_comment_liked in admin area. 
	 *
	 * Desc: get meta
	 * @since    1.0.0
	 */
	public function _get_shift_wall_comment_liked( $object, $field_name, $request ) {
	    
	    $results = get_comment_meta( $object[ 'id' ], $field_name, true );

	    if(empty($results))
	    	$results = array();

	    return $results;
	}


	/**
	 * Register the function _put_shift_wall_comment_liked in admin area. 
	 *
	 * Desc: Update meta
	 * @since    1.0.0
	 */
	public function _put_shift_wall_comment_liked( $value, $object, $field_name ) {

	    if ( ! $value || ! is_string( $value ) ) {
	        return;	
	    }

	    $is_liked = get_comment_meta($object->comment_ID, $field_name, true);

	   	if( empty($is_liked) ){

	   		$is_liked = array();
	   		array_push($is_liked, $value);
// $this->parent->trigger( 'shiftwall_comment_like', $is_liked );
	   	}else if(!empty($is_liked) && !in_array($value, $is_liked)){

	   		array_push($is_liked, $value);
// $this->parent->trigger( 'shiftwall_comment_like', $is_liked );
	   	}else{

	   		$index = array_search($value, $is_liked);
	   		array_splice($is_liked, $index, 1);
// $this->parent->trigger( 'shiftwall_comment_unlike', $is_liked );
	   	}
	 
	    return update_comment_meta( $object->comment_ID, $field_name, $is_liked);

	}


	/**
	 * Register the function _get_shift_wall_comment_content in admin area. 
	 *
	 * Desc: get meta
	 * @since    1.0.0
	 */
	public function _get_shift_wall_comment_content( $object, $field_name, $request ) {
	    
	    $results = get_post_meta( $object[ 'id' ], $field_name, true );

	    if(empty($results))
	    	$results = array();

	    return $results;
	}


	/**
	 * Register the function _put_shift_wall_comment_content in admin area. 
	 *
	 * Desc: Update meta
	 * @since    1.0.0
	 */
	public function _put_shift_wall_comment_content( $value, $object, $field_name ) {
	 	
	 	$comment = wp_update_comment( $object->comment_ID, $field_name, 1 );
		$this->parent->trigger( 'wall_comment_content', $comment );
	    return $comment;

	}


	/**
	 * Register the function shiftwall_insert_feed in admin area. 
	 *
	 * Desc: Update meta
	 * @since    1.0.0
	 */
	public function shiftwall_insert_feed( $post, $request, $create ){

		$response = array();
		
		if($create){
			
			$this->_get_custom_meta($request, $post);
			$response = $this->get_response( $post, $request );

//----------------------------------------
// Need to have a followers or friends list
			// $this->_create_notification( array('object_id' => $post->ID, 'author' => $post->post_author), 'posted' );

			$this->parent->trigger( 'shiftwall_create_feed', $response );

		}else{

			$post_type = $request->get_param('actions');

			switch ($post_type) {
				case 'feed_like':
					$field_name = '_shift_wall_feed_liked';
					$this->_put_shift_wall_feed_liked($request->get_param($field_name), $post, $field_name);
					
					break;
				case 'post_edit':
					
					$response = $this->get_response( $post, $request );
					$this->parent->trigger( 'shiftwall_update_feed', $response );

					break;
				default:
					# code...
					break;
			}
	


		}

	}


	/**
	 * Register the function get_response in admin area. 
	 */
	public function get_response( $post, $request, $type = 'post' ){

		global $wp_rest_server;
		$controller = '';
		if( $type == 'post' )
			$controller = new \WP_REST_Posts_Controller( $post->post_type );
		else if( $type == 'comment' )
			$controller = new \WP_REST_Comments_Controller( $post->post_type );

		$response = $wp_rest_server->response_to_data( $controller->prepare_item_for_response( $post, $request ), true );
		return $response;
	}


	/**
	 * Register the function shiftwall_delete_feed in admin area. 
	 *
	 * Desc: Update meta
	 * @since    1.0.0
	 */
	public function shiftwall_delete_feed( $post, $response, $request ){

		$this->get_response( $post, $request );
		$this->parent->trigger( 'shiftwall_delete_feed', $response );

	}


	/**
	 * Register the function shiftwall_insert_comment in admin area. 
	 *
	 * Desc: Update meta
	 * @since    1.0.0
	 */
	public function shiftwall_insert_comment( $comment, $request, $create ){

		$response = array();
		if($create){

			// Comment 
			$this->_get_custom_meta($request, $comment);
			$response = $this->get_response( $comment, $request, 'comment' );
			$this->parent->trigger( 'shiftwall_create_comment', $response );

			$this->_create_notification( array('object_type' => 'comment', 'object_id' => $comment->comment_ID, 'author' => $comment->comment_author_email), 'post_comment' );

		}else{

			$post_type = $request->get_param('actions');

			switch ($post_type) {
				case 'comment_like':
					$field_name = '_shift_wall_comment_liked';
					$this->_put_shift_wall_comment_liked($request->get_param($field_name), $comment, $field_name);
					break;
				default:
					# code...
					break;
			}
						
			// Comment
			$response = $this->get_response( $comment, $request, 'comment' );
			$this->parent->trigger( 'shiftwall_update_comment', $response );

		}

		// 	Feed
		$post = get_post( $comment->comment_post_ID );
		$response = $this->get_response( $post, $request );
		$this->parent->trigger( 'shiftwall_update_feed', $response );

	}


	/**
	 * Register the function shiftwall_delete_comment in admin area. 
	 *
	 * Desc: Update meta
	 * @since    1.0.0
	 */
	public function shiftwall_delete_comment( $comment, $response, $request ){

		// Comment
		$response = $this->get_response( $comment, $request, 'comment' );
		$this->parent->trigger( 'shiftwall_delete_comment', $response );

		// Feed
		$post = get_post( $comment->comment_post_ID );
		$response = $this->get_response( $post, $request );
		$this->parent->trigger( 'shiftwall_update_feed', $response );
	}





}






















// 	   /api/v1/publish

//     method: post
//     data:
//     {
//         "event_name": "message",
//         "data": "sample data",
//         "client_id": "y58iyjdPEQMBsdr6iwWzFH8zO5YfWEW1dhpEazAnTjNw3KoKCm",
//         "organization": "58ef49ea6125510004230e3a",
//         "client_secret": "mKR7KtDvHfupIREAIu8D1vsCbHxmQszwHCQjvoFHGgFi5Mp81P"
//     }

//     return:
//     {
//       "event_name": "message",
//       "data": "sample data"
//     }




// http://shift-realtime.herokuapp.com/api/v1/publish
// {
//     "event_name": "message",
//     "data": "sample data",
//     "appId": 1,
//     "key": "58c7681febbed28893771e40",
//     "secret": "secret"
// }


// "client_secret": "mKR7KtDvHfupIREAIu8D1vsCbHxmQszwHCQjvoFHGgFi5Mp81P",
// "client_id": "y58iyjdPEQMBsdr6iwWzFH8zO5YfWEW1dhpEazAnTjNw3KoKCm",