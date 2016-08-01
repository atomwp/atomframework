<?php
if (!defined("AT_ROOT")) die('!!!');
class AT_Admin {
	
	static public function init() {
		add_action( 'init', array( 'AT_Posttypes', 'register') );
		// if ( AT_Core::get_instance()->get_option( 'theme_is_activated', false ) ) {

			AT_Loader::get_instance()->library( 'Metaboxes', false);

			if( isset($_GET['page']) && $_GET['page'] != 'at_theme_install' ) {
				$update_model = AT_Loader::get_instance()->model( 'admin/update_model' );
				if ( !$update_model->is_updated() ){
					$update_model->update();
				}
			}

			add_action( 'init', array( 'AT_Metaboxes', 'register') );
			add_action( 'widgets_init', array( 'AT_Sidebars', 'register') );
			add_action( 'widgets_init', array( 'AT_Widgets', 'register') );
			add_action( 'init', array( 'AT_Shortcodes', 'init' ) );
			add_action( 'init', array('AT_Supports','load'));
		// }

		add_action( 'init', array( 'AT_Admin', 'menus_init') );
		add_action( 'init', array( 'AT_Admin', 'route') );
		add_action( 'init', array( 'AT_Menu', 'init_admin' ) );

		self::_actions();
		self::_filters();
		self::_notices();
	}	


	public static function route() {
		global $pagenow;
		if ( isset($_GET['page']) || ( $pagenow === 'themes.php' && isset( $_GET['activated'] ) ) ){
			AT_Router::route();
			AT_Core::get_instance()->view->render();

			// if ( AT_Core::get_instance()->get_option( 'theme_is_activated', false ) ){
				// Init media gallery
				wp_enqueue_media();
			// }
		}
		// echo AT_Router::get_instance()->segments();
		if ( $pagenow === 'widgets.php' ){
			// Init media gallery
			wp_enqueue_media();
		}
	}

	static public function _notices() {
		if (version_compare(PHP_VERSION, '5.3.0') < 0) {
			add_action('admin_notices', array('AT_Notices','php_version_check'));
		}
		if( !function_exists( 'vc_map' ) ) {
			add_action('admin_notices', array('AT_Notices','wpb_installation_check'));
		}
		if ( !extension_loaded('gd') && !function_exists('gd_info') ) {
			add_action('admin_notices', array('AT_Notices','missing_gd'));	
		}
	}

	static protected function _actions(){
		AT_Loader::get_instance()->library( 'CustomizerDefaults', false);
		add_action('customize_register',array('AT_Customizer','init'));
		add_action( 'admin_menu', array( 'AT_Admin', 'options_init' ) );
		add_action( 'admin_enqueue_scripts', array( AT_Core::get_instance()->view, 'render_admin_statics') );

		AT_Loader::get_instance()->library( 'Customizer', false );
		AT_Loader::get_instance()->library( 'Extra_profile_fields' );
	}

	static protected function _filters(){
		add_filter( 'admin_body_class', array( 'AT_Admin', 'filter_add_theme_admin_body_class') );
	}

	public static function filter_add_theme_admin_body_class( $classes ) {
		if( implode('_', AT_Router::get_instance()->segments()) != 'welcome_index' )
	    	$classes .= 'at_theme_options_styled';
		return $classes;
	}

	// INIT MENU
	public static function menus_init() {
		register_nav_menu( 'main-navigation',	esc_html__( 'Primary Menu', 'atom' ) );
		register_nav_menu( 'footer-menu',		esc_html__( 'Footer Menu', 'atom' ) );
		register_nav_menu( 'header-links',		esc_html__( 'Header Links', 'atom' ) );
	}
	public static function options_init() {
		/**
		 * Sking Management
		 */
		$menu_page = array('add','menu','page');
		$sub_menu_page = array('add','submenu','page');

		// Theme Skinning
		add_theme_page(esc_html__( 'Design' , 'atom' ),esc_html__( 'Design and Skinning' , 'atom' ), 'edit_themes', THEME_PREFIX . 'theme_options_styled', array( 'AT_Admin', 'options'), 'dashicons-art', 102);

		// Theme Tweaks
		add_theme_page(esc_html__( 'Theme Tweaks', 'atom' ),esc_html__( 'Theme Tweaks', 'atom' ), 'edit_themes', THEME_PREFIX . 'theme_options_general', array( 'AT_Admin', 'options' ), 'dashicons-art', 103 );

		// Theme Setup
		add_theme_page(esc_html__( 'Theme Setup' , 'atom' ),esc_html__( 'Theme Setup' , 'atom' ), 'edit_themes', THEME_PREFIX . 'theme_install', array( 'AT_Admin', 'options'), 'dashicons-toolkit', 102);
	}

	public static function options() { 
		if (isset($_GET['page'])){
			AT_Core::get_instance()->view->display();
		}
	}

	private function _remove_submenu( $menu_name, $submenu_name ) {
	    global $submenu;
	    $menu = $submenu[$menu_name];
	    if (!is_array($menu)) return;
	    foreach ($menu as $submenu_key => $submenu_object) {
	        if (in_array($submenu_name, $submenu_object)) {// remove menu object
	            unset($submenu[$menu_name][$submenu_key]);
	            return;
	        }
	    }
	}

	/**
	* gets the current post type in the WordPress Admin
	*/
	public static function get_current_post_type() {
		global $post, $typenow, $current_screen, $pagenow;
		if( $post && $post->post_type )
			$post_type = $post->post_type;
		elseif( $typenow )
			$post_type = $typenow;
		elseif( $current_screen && $current_screen->post_type )
			$post_type = $current_screen->post_type;
		elseif( isset( $_REQUEST['post_type'] ) )
			$post_type = sanitize_key( $_REQUEST['post_type'] );
		elseif ( 'post.php' == $pagenow && isset($_GET['post']) )
			$post_type = get_post_type($_GET['post']);
		elseif ( 'post-new.php' == $pagenow ){
			$post_type = 'post';
		}
		else
			$post_type = null;
		return $post_type;
	}
}
