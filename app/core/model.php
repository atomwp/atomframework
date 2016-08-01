<?php
if (!defined("AT_ROOT")) die('!!!');
abstract class AT_Model {

	public $wpdb = null;
	public $load = null;
	public $core = null;
	public $registry = null;

	protected $_subscribe_table = 'wj_subscribe';
 
	public function __construct() {
		global $wpdb;
		$this->wpdb = &$wpdb;
		$this->_subscribe_table = $wpdb->prefix . $this->_subscribe_table;
		$this->load = AT_Loader::get_instance();
		$this->core = AT_Core::get_instance();
		$this->registry = AT_Registry::get_instance();
	}

	public function get_data_for_options( $method, $option = null ){
		$return = array();
		if ( $method == 'get_all_users' ) {
			foreach ($this->$method($option) as $key => $value) {
				$return[$value['id']] = $value['name'] . ' : ' . $value['email'];
			}
		} else {
			foreach ($this->$method($option) as $key => $value) {
				$return[$value['id']] = $value['name'];
			}
		}
		return $return;
	}
}
