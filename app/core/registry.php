<?php
if (!defined("AT_ROOT")) die('!!!');
class AT_Registry {

	private static $_instance = null;
	private static $_data = array();

	private function __construct() {

	}

	public function set( $key, $value ) {
		$this->data[$key] = $value;
	}
	
	public function get( $key ) {
		return isset($this->data[$key]) ? $this->data[$key] : false;
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
