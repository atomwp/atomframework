<?php
if (!defined("AT_ROOT")) die('!!!');

class AT_Notices { 
	static private $backend = array();
	static private $frontend = array();

	/* extends AT_Model{*/
	static public function php_version_check() {
		self::_render_notice( $msg =esc_html__( 'Warning: You\'re runing outdated PHP version. Please update server to support PHP 5.3 or above.', 'atom' ), $class='error');
	}
	static public function missing_gd() {
		self::_render_notice( $msg =esc_html__( 'Warning, You must install GD2 library on your server. Otherwise image upload will be impossible.', 'atom' ), $class='error');
	}
	static public function wpb_installation_check() {
		self::_render_notice( $msg =esc_html__( 'Please install Visual Composer plugin from theme pack. Plugin comes with full theme package, and stored inside plugins/js_composer.zip archive.', 'atom' ), $class='error');
	}
	static public function theme_updated_notice() {
		self::_render_notice(
			$msg  = '<h3>' .esc_html__( 'Theme Updated', 'atom' ) . '</h3>' .
				sprintf(__( 'Version has been updated successfuly to %s. You may find release notes inside Theme Options / Release.', 'atom' ), THEME_VERSION),
			$class='updated fade'
		);
	}

	static public function set_frontend_notice($msg, $class = 'notice') {
		self::$frontend[] = array('msg' => $msg, 'class' => $class);
	}
	static public function get_frontend_notice() {
		echo '<div class="notice-dialogue">';
		$notices_loop = '';
		foreach(self::$frontend as $notice) {
			$notices_loop .= '<div class="' . esc_attr($notice['class']) . '">' . $notice['msg'] . '</div>';
		}

		self::_render_notice($notices_loop, $class = 'dialogue', true);

		echo '</div>';
	}

	static private function _render_notice($msg, $class = 'error', $echo = true) {
		$output = '';
		$output .= '<div id="message" class="' . esc_attr($class) .'">';
		$output .= '' . $msg . '';
		$output .= '</div>';
		if ( $echo == true ) {
			echo $output;
		}
	}
}
