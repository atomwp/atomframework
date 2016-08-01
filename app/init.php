<?php
/**
 * MIT License
 * ===========
 *
 * Copyright (c) 2012 J. Spector
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be included
 * in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY
 * CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE
 * SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @category   [ MicroCMF ]
 * @package    [ Atom ]
 * @author     J. Spector
 * @copyright  2015 
 * @license    http://www.opensource.org/licenses/mit-license.php  MIT License
 * @version    [ 1.0 ]
 * @link       https://github.com/atomwp/atomframework
 */

class AT_Module_Init {

	static public function init() {

		self::_defined();
		$files = glob(get_template_directory() . "/app/core/*.php");
		foreach($files as $file) if (!is_dir($file)) {
			require_once($file);
		}

		AT_Loader::get_instance()->library( 'CustomizerDefaults', false);
		AT_Loader::get_instance()->library( 'Customizer', false );
		AT_Loader::get_instance()->helper('common');
		AT_Loader::get_instance()->helper('post_common');
		AT_Loader::get_instance()->helper('route');

		AT_Loader::get_instance()->library( 'Shortcodes', false );
		AT_Loader::get_instance()->library( 'Menu', false );
		AT_Loader::get_instance()->library( 'Widgets', false );
		AT_Loader::get_instance()->library( 'photo_cache', false );

		require_once get_template_directory() . '/app' . "/admin/options/at-options-setup.php";
		require_once get_template_directory() . '/app' . "/admin/meta-boxes/at-meta-boxes-setup.php";
		include_once get_template_directory().'/assets/custom-styles/general-custom-styles.php';
		include_once get_template_directory().'/assets/custom-styles/general-custom-styles-responsive.php';

		// setup_theme
		if ( is_admin() ) {
			AT_Admin::init();
			self::_support();
			return;
		}

		self::_support();
		self::_navigation();
		self::_filters();
		self::_actions();
	}

	static protected function _support(){
        // add support for feed links
        add_theme_support('automatic-feed-links');

        // customizer support
        add_theme_support( 'at_customizer_support' );

        add_theme_support( 'control-panel-default' );


        //add theme support for post thumbnails
        add_theme_support('post-thumbnails');

        //add theme support for title tag

        // Default thumbnails
        add_image_size('atom_square', 550, 550, true);

		// Carousel
		add_image_size('atom_landscape', 600, 400, true);

        // Masonry Lists
        add_image_size('atom_portrait', 600, 730, true);

        // Splitscreen
		add_image_size('atom_split_column_landscape', 900, 600, true);
		
        load_theme_textdomain( 'atom', get_template_directory().'/languages' );
	}

	static protected function _navigation(){
		register_nav_menu( 'main-navigation',esc_html__( 'Primary Menu', 'atom' ) );
		register_nav_menu( 'footer-menu',esc_html__( 'Footer Menu', 'atom' ) );
		register_nav_menu( 'header-links',esc_html__( 'Header Links', 'atom' ) );
	}

	static protected function _actions(){
		add_action('after_setup_theme', array('AT_Supports','load'));
		add_action('admin_bar_menu', array( 'AT_Menu', 'toolbar' ),999 );
		add_action('widgets_init', array( 'AT_Sidebars', 'register') );
		add_action('widgets_init', array( 'AT_Widgets', 'register') );
		add_action('init', array( 'AT_Shortcodes', 'init' ) );
		add_action('init', array( 'AT_Menu', 'init' ) );
		add_action('init', array( 'AT_Router', 'add_rewrite_rules' ) );
		add_action('init', array( 'AT_Module_Init', 'init_actions' ) );
		add_action('wp', array( 'AT_Router', 'route' ) );

		add_action('customize_register',array('AT_Customizer', 'init'));

		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );

		AT_Loader::get_instance()->library( 'CommentsBuilder' );
	}

	static public function init_actions() {
		add_action( 'wp_enqueue_scripts', array( AT_Core::get_instance()->view, 'render_styles') );
		add_action( 'wp_enqueue_scripts', array( AT_Core::get_instance()->view, 'render_scripts') );
		add_action( 'wp_print_footer_scripts', array( AT_Core::get_instance()->view, 'render_inline_scripts'),1 );
		add_action( 'template_redirect', array( AT_Core::get_instance()->view, 'render' ) );
		add_action( 'template_redirect', array( AT_Core::get_instance()->view, 'display' ) );
	}

	static protected function _defined(){
		$theme_data = wp_get_theme();

		define( 'AT_ROOT', get_template_directory() . '/app' );
		define( 'AT_SITE_ROOT', get_template_directory() . '/site' );
		define( 'AT_THEME_URI', get_template_directory_uri() );
		define( 'AT_THEME_ROOT', get_template_directory() );

		define( 'AT_URI', AT_THEME_URI . '/app' );
		define( 'AT_SITE_URI', AT_THEME_URI . '/site' );
		define( 'AT_ASSETS_URI', AT_THEME_URI . '/assets' );
		define( 'AT_ASSETS_ROOT', get_template_directory() . '/assets' );

		define('AT_MODULES_URI', AT_URI.'/modules');
		define('AT_MODULES_ROOT', AT_ROOT.'/modules');

		define('AT_THEME_ENV', 'dev');


		define( 'THEME_NAME', $theme_data->name );
		define( 'THEME_VERSION', $theme_data->version );
		define( 'THEME_PREFIX', 'at_' );
		define( 'THEME_SLUG', get_template() );
		define( 'THEME_ADMIN_ASSETS_URI', AT_ASSETS_URI );
		
		define( 'THEME_ACTIVE_SKIN', 'light.aqua' );

		define( 'AT_TEXTDOMAIN', THEME_SLUG );

	}

	static protected function _filters(){
		add_filter( 'body_class', array( 'AT_Filters', 'add_body_class' ) );

		$header_data = AT_Registry::get_instance()->get( 'header_data');

		add_filter('media_view_strings',array( 'AT_Filters', 'remove_media_tab' ));
		add_filter('media_upload_tabs', array( 'AT_Filters', 'remove_medialibrary_tab' ) );
		add_action('pre_get_posts',array( 'AT_Filters', 'users_own_attachments'));
		
		// I will kill you if you remove this. I died two days for this line
		add_filter('request', array( 'AT_Filters', 'remove_per_page_from_query_string') );

		// optimize js/css src
		add_filter('script_loader_src', array( 'AT_Filters', 'optimize_script_url' ));
		add_filter('style_loader_src', array( 'AT_Filters', 'optimize_script_url' ));

	}
}
