<?php
if (!defined("AT_ROOT")) die('!!!');
/**
 * This class supply custom template load
 **/
class AT_Templates {

	private static $_instance = null;

	private function __construct() {

	}

	static public function load($template) {
		if (file_exists(AT_SITE_ROOT . '/templates/'.$template)) {
			include_once(AT_SITE_ROOT . '/templates/'.$template);
		}
		if (file_exists(AT_SITE_ROOT . '/classes/'.$template)) {
			include_once(AT_SITE_ROOT . '/classes/'.$template);
		}
		if (file_exists(AT_SITE_ROOT . '/functions/'.$template)) {
			include_once(AT_SITE_ROOT . '/functions/'.$template);
		}
	}

	static public function get_instance() {
		if(is_null(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	protected function __clone() {
	}
}
?>
