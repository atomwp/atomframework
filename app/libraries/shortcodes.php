<?php
if (!defined("AT_ROOT")) die('!!!');

class AT_Shortcodes {
	// Insert custom shortcode below the list. Please note, file must have same name as class.
	static protected $_state = array();
	static protected $_shortcodes = array(
		'benefit',
		'blockquote',
		'caption',
		'contact_form',
		'contact_form_block',
		'fancycaption',
		'icon',
		'partners',
		'posts',
		'portfolio',
		'products_recent',
		'products_bestsellers',
		'separator',
		'steps',
		'staff',
		'gmap',
		'skill_meter',
		'testimonials',
	);

	static public function setState($shortcode) {
		return self::$_state[$shortcode] = true;
	}
	static public function getState($shortcode) {
		if (!isset(self::$_state[$shortcode])) {
			self::$_state[$shortcode] = false;
		}
		return self::$_state[$shortcode];
	}
	static public function init() {
		define( 'AT_JS_COMPOSER_PATH', AT_ROOT . '/libraries/js_composer' );
		define( 'AT_JS_COMPOSER__JS',  'assets/js/js_composer' );
		define( 'AT_JS_COMPOSER__CSS', 'assets/css/js_composer' );

		if ( is_admin() ) {
			wp_enqueue_script( THEME_PREFIX . '-jsc-global', AT_URI . '/' . AT_JS_COMPOSER__JS .'/global.js', array('wpb_js_composer_js_custom_views'), THEME_VERSION, true );
		}

		require_once AT_JS_COMPOSER_PATH . "/params.php";
		require_once AT_JS_COMPOSER_PATH . "/shortcode.php";
		require_once AT_JS_COMPOSER_PATH . "/helper.php";
		self::_init_shortcodes();
	}


	static protected function _init_shortcodes(){
		if ( function_exists('vc_map') ) {
			asort( self::$_shortcodes );

			foreach( self::$_shortcodes as $shortcodes ) {
				require_once AT_ROOT . '/libraries/js_composer/' . $shortcodes . '.php';
				$class = 'AT_' . ucfirst( preg_replace( '/[0-9-_]/', '', $shortcodes ) ) . '_VC_ShortCode';
				$class_methods = get_class_methods( $class );
				if (isset($class_methods)) {
					foreach( $class_methods as $shortcode ) {
						if( $shortcode[0] != '_') {
							call_user_func(AT_Common::integrate(), $shortcode, array( $class, $shortcode ) );
							if( is_admin() ) {
								if ( function_exists( 'vc_map' ) ) {
									vc_map( call_user_func( array( $class, '_options' ), $shortcode ) ); //static method
								}
							}
						}
					}
				}
			}
			AT_Shortcode_params::init();
		}
	}
}
