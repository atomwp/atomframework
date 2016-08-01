<?php
if (!defined("AT_ROOT")) die('!!!');
class AT_Breadcrumbs {

	private $_data = array();
    private static $_instance;	
	
	public function __construct() {
		$this->add_item(esc_html__('Home', 'atom'), '/' );
	}

	public static function get_instance() {
        self::$_instance = self::$_instance instanceof AT_Breadcrumbs ? self::$_instance : new AT_Breadcrumbs();
        return self::$_instance;
    }

    public function add_item($name, $url) {
        $this->_data[] = array('name' => $name,'url' => AT_Common::site_url($url));
    }

    public function erase() {
        $this->_data = array();
    }

    public function get_all() {
        return $this->_data;
    }
}
