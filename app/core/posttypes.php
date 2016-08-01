<?php
if (!defined("AT_ROOT")) die('!!!');
class AT_Posttypes {
	
	//////////////////////////////////////////////////////////////////////////////////////////////
	// support wp posttypes
	//////////////////////////////////////////////////////////////////////////////////////////////
	static protected $_wp_post_types = array( 'post' );
	
	//////////////////////////////////////////////////////////////////////////////////////////////
	// registered posttypes
	//////////////////////////////////////////////////////////////////////////////////////////////
	static protected $_register_post_type = array( 'staff' );

	static function get_custom_post_types(){
		return self::$_register_post_type;
	}
	static public function register(){
		foreach( self::$_register_post_type as $key => $value) {
			$method = '_' . $value;
			self::$method();
		}
	}

	static public function get_post_types(){
		return array_merge( self::$_wp_post_types, self::$_register_post_type );
	}

	static private function _testimonials(){
	}

	static private function _staff(){
	}

	static private function _portfolio(){
	}

	static private function _partners(){
	}

	static private function _price_tables(){
	}

	static private function _news(){
	}
}
