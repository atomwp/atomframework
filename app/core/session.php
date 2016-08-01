<?php
if (!defined("AT_ROOT")) die('!!!');
class AT_Session {

	private $_sess_expiration			= 7200;
	private $_sess_expire_on_close		= FALSE;
	private $_sess_match_ip				= FALSE;
	private $_sess_match_useragent		= TRUE;
	private $_sess_cookie_name			= 'at_session';
	private $_cookie_prefix				= '';
	private $_cookie_path				= '/';
	//private $_cookie_domain				= '.wp-test';
	private $_cookie_domain				= '';
	private $_cookie_secure				= FALSE;
	private $_sess_time_to_update		= 300;
	private $_encryption_key			= 'RePlAcE_ThIs_KeY!!';
	private $_userdata					= array();

	private $userdata = array();
	private static $_instance = null;
	
	private function __construct() {
		//session_settings
		$session_settings = AT_Core::get_instance()->get_option( 'session_settings', array() );
		if ( count($session_settings) > 0 ){
			
			if (isset($session_settings['sess_expiration']))
				$this->_sess_expiration = (int)$session_settings['sess_expiration'];
			
			if (isset($session_settings['sess_time_to_update']) && (int)$session_settings['sess_time_to_update'] > 0 ) 
				$this->_sess_time_to_update = (int)$session_settings['sess_time_to_update'];
			
			if (isset($session_settings['sess_expire_on_close']))
				$this->_sess_expire_on_close = $session_settings['sess_expire_on_close'];

			if (isset($session_settings['sess_match_ip']))
				$this->_sess_match_ip = $session_settings['sess_match_ip'];
			
			if (isset($session_settings['sess_match_useragent']))
				$this->_sess_match_useragent = $session_settings['sess_match_useragent'];
			
			if (!empty($session_settings['sess_cookie_name']))
				$this->_sess_cookie_name = $session_settings['sess_cookie_name'];

			if (!isset($session_settings['cookie_prefix']))
				$this->_cookie_prefix = $session_settings['cookie_prefix'];

			if (!empty($session_settings['cookie_path']))
				$this->_cookie_path = $session_settings['cookie_path'];

			if (!isset($session_settings['cookie_domain']))
				$this->_cookie_domain = $session_settings['cookie_domain'];

			if (!isset($session_settings['cookie_secure']))
				$this->_cookie_secure = $session_settings['cookie_secure'];

			if (!empty($session_settings['encryption_key']))
				$this->_encryption_key = $session_settings['encryption_key'];

		}
		if ($this->_sess_expiration == 0) {
			$this->_sess_expiration = (60*60*24*365*2);
		}

		$this->_sess_cookie_name = $this->_cookie_prefix.$this->_sess_cookie_name;

		if ( $this->sess_read() ) {
			$this->_sess_update();
		} else {
			$this->sess_create( array() );
		}
		/*else if( is_user_logged_in() && AT_Core::get_instance()->get_option( 'site_type', 'mode_soletrader' ) == 'mode_soletrader' ) {
			$data = array( 'logged' => true, 'user_id' => 1 );
			$this->sess_create( $data );
		}
		*/
	}

	private function _generate_session_id() {
		$sessid = '';
		while (strlen($sessid) < 32) {
			$sessid .= mt_rand(0, mt_getrandmax());
		}
		$sessid .=  AT_Router::get_instance()->server("REMOTE_ADDR");
		return md5( uniqid($sessid, TRUE) );
	}

	public function sess_read() {
		$session = isset( $_COOKIE[$this->_sess_cookie_name] ) ?  stripslashes( $_COOKIE[$this->_sess_cookie_name] ) : false;
		if ( $session === false ) {
			return false;
		}
		$session = call_user_func(AT_Common::decoder(),$session);
		$hash	 = substr( $session, strlen($session)-32 );
		$session = substr( $session, 0, strlen($session)-32 );
		if ( $hash !==  md5( $session . $this->_encryption_key ) ) {
			$this->sess_destroy();
			return false;
		}
		$session = unserialize( $session );
		if ( ( $this->_sess_match_ip && $session['ip_address'] != AT_Router::get_instance()->server("REMOTE_ADDR") ) || 
			$this->_sess_match_useragent && $session['user_agent'] != substr(AT_Router::get_instance()->server("HTTP_USER_AGENT"), 0, 120) ) {
			$this->sess_destroy();
			return false;
		}
		$this->_userdata = $session;
		return true;
	}

	public function sess_create( $data ) {
		$this->_userdata = array(
			'session_id'	=> $this->_generate_session_id(),
			'ip_address'	=> AT_Router::get_instance()->server("REMOTE_ADDR"),
			'user_agent'	=> substr(AT_Router::get_instance()->server("HTTP_USER_AGENT"), 0, 120),
			'last_activity'	=> time()
		);
		$this->_userdata = array_merge( $this->_userdata, $data );
		$this->_set_cookie();
	}

	private function _sess_write() {
		$this->_set_cookie();
		return;
	}

	private function _sess_update() {
		if ( ( $this->_userdata['last_activity'] + $this->_sess_time_to_update ) >= time() ) {
			return;
		}
		$new_sessid = $this->_generate_session_id();
		$this->_userdata['session_id'] = $new_sessid;
		$this->_userdata['last_activity'] = time();
		$this->_set_cookie();
	}

	public function sess_destroy() {
		setcookie(
			$this->_sess_cookie_name,
			addslashes( serialize( array() ) ),
			(time() - 31500000),
			$this->_cookie_path,
			$this->_cookie_domain,
			0
		);
		$this->_userdata = array();
	}

	private function _set_cookie( $cookie_data = NULL ) {
		if (is_null($cookie_data)) {
			$cookie_data = $this->_userdata;
		}
		$cookie_data = serialize( $cookie_data );
		$cookie_data = call_user_func(AT_Common::encoder(), $cookie_data . md5( $cookie_data . $this->_encryption_key ));
		$expire = ($this->_sess_expire_on_close === true) ? 0 : $this->_sess_expiration + time();
		setcookie(
			$this->_sess_cookie_name,
			$cookie_data,
			$expire,
			$this->_cookie_path,
			$this->_cookie_domain,
			$this->_cookie_secure
		);
	}

	public function set_userdata( $item, $value ) {
		$this->_userdata[$item] = $value;
		$this->_sess_write();
	}

	public function set_userdata_admin( $item, $value ) {
		$this->_userdata[$item] = $value;
		//$this->_sess_write();
	}

	public function userdata( $item ) {
		return ( !isset( $this->_userdata[$item] ) ) ? false : $this->_userdata[$item];
	}

	public function all_userdata() {
		return $this->_userdata;
	}

	public function unset_userdata( $item ) {
		if ( is_array($item) ) {
			foreach( $item as $_k => $record ) {
				$this->_userdata[$record] = null;
				unset($this->_userdata[$record]);
			}
		} else {
			unset($this->_userdata[$item]);
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
