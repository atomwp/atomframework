<?php
if (!defined("AT_ROOT")) die('!!!');
class AT_Loader {

	private $_models = array();
	private $_helpers = array();
	private $_libraries = array();
	private $_widgets = array();

	private static $_instance = null;
	
	private function __construct() {
	}

	public function model($model){
		$class_name = $model;
		$part = explode('/', $model);
		if (count($part) > 1) {
			$class_name = $part[1];
		}

		if (empty( $this->_models[$model] ) ) {
			if ( file_exists( AT_ROOT . '/models/' . strtolower( $model ) . '.php' ) )	{
				$this->_models[$model] = 'AT_' . $class_name;
				include AT_ROOT . '/models/' . strtolower( $model ) . '.php';
			} else {
				return null;
			}
		}
		$model = $this->_models[$model];
		return new $model();
	}

	public function library($library, $object = true, $params = array()){
		if ( empty( $this->_libraries[$library] ) ) {
			if ( file_exists( AT_ROOT . '/libraries/' . strtolower( $library ) . '.php' ) )	{
				$this->_libraries[$library] = 'AT_' . $library;
				include AT_ROOT . '/libraries/' . strtolower( $library ) . '.php';
			} else {
				return null;
			}
		}
		if ($object) {
			$library = 'AT_' . $library;
			return (!empty($params) ? new $library($params) : new $library());
		}
	}

	public function widget($widget, $params = array()){
		if ( empty( $this->_widgets[$widget] ) ) {
			if ( file_exists( AT_ROOT . '/widgets/' . strtolower( $widget ) . '.php' ) )	{
				$this->_widgets[$widget] = 'AT_' . $widget;
				include AT_ROOT . '/widgets/' . strtolower( $widget ) . '.php';
			} else {
				return null;
			}
		}
		$widget = 'AT_' . $widget;
		return new $widget($params);
	}

	public function helper($helper, $object = false){
		if ( empty( $this->_helpers[$helper] ) ) {
			if ( file_exists( AT_ROOT . '/helpers/' . strtolower( $helper ) . '.php' ) )	{
				$this->_helpers[$helper] = 'AT_' . $helper;	
				include AT_ROOT . '/helpers/' . strtolower( $helper ) . '.php';
			}
		}
		if ($object) {
			$helper = 'AT_' . $helper;
			return new $helper();
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
