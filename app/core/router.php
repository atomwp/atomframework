<?php
if (!defined("AT_ROOT")) die('!!!');
class AT_Router {
	private $_segments = array();
	private $_file = 'welcome';
	private $_controller = 'welcome';
	private $_method = 'index';
	private $_arguments = array();
	private $_init = false;
	private $_is_admin = false;

	private static $_instance = null;
	
	private function __construct( $segments ) {

		$this->_init = true;
		$this->_segments = $segments;

		if (!empty($segments[0])) {
			if ($segments[0] == 'admin' && is_admin()) {
				$this->_is_admin = true;
				array_splice($segments,0, 1);
			}
			$this->_controller = $segments[0];
			if (!empty($segments[1])) {
				$this->_method = $segments[1];
				array_splice($segments,0, 2);
				$this->_arguments = $segments;
			} else {
				$this->set_segment(1, $this->_method);
			}
		} else {
			$this->set_segment(0, $this->_controller);
			$this->set_segment(1, $this->_method);
		}

		$this->_method = strtolower($this->_method);
		$this->_controller = $this->_file = strtolower($this->_controller);

		$t = substr($this->_controller, 0, 1);
		$t = strtoupper($t);
		$this->_controller = 'AT_' . substr_replace($this->_controller, $t, 0, 1);
	}

	public function get_controller() {
		return $this->_controller;
	}

	public function set_controller( $controller ) {
		$this->_controller = $controller;
	}

	public function get_method() {
		return $this->_method;
	}

	public function set_method( $method ) {
		$this->_method = $method;
	}

	public function set_segment($segment, $value){
		$this->_segments[$segment] = $value;
	}

	public function segments( $segment = false ){
		return ( $segment === false ) ? $this->_segments : ( !empty( $this->_segments[$segment] ) ? $this->_segments[$segment] : false );
	}

	public function run(){
		if($this->_method != '__construct' && $this->_method != '__clone' && file_exists(AT_ROOT . '/controllers/' . ($this->_is_admin ? 'admin/' : ''). $this->_file . '.php') ) {
			include_once AT_ROOT . '/controllers/' . ($this->_is_admin ? 'admin/' : '') . $this->_file . '.php';
			if(method_exists($this->_controller, $this->_method)) {
	            $controllerObj = new $this->_controller();
	            call_user_func_array(array($controllerObj, $this->_method), $this->_arguments);
	            unset($controllerObj);
	        } else {
	        	AT_Core::get_instance()->show_404();
	        }
        } else {
        	AT_Core::get_instance()->show_404();
        }
	}

	public function ruri_string(){
		return trim($this->_controller . '/' . $this->_method . implode('/', $this->_arguments), '/');
	}

	public function server( $env ) {
		if ( getenv( $env ) ) {
			return getenv( $env );
		} else if ( isset( $_SERVER[$env] ) ) {
			return $_SERVER[$env];
		} else {
			return false;
		}
	}

	public function is_ajax_request() {
        if (isset($_REQUEST['wp_customize']) && $_REQUEST['wp_customize'] == "on") {
          return false;
        }

		return ($this->server('HTTP_X_REQUESTED_WITH') === 'XMLHttpRequest');
	}

	static public function get_instance( $segments = array() ) {
		if(is_null(self::$_instance)) {
			self::$_instance = new self( $segments );
		}
		return self::$_instance;
	}
	
	protected function __clone() {
	}

	//////////////////////////////////////////////////////////////////////////////////////////////
	// Static Methods
	//////////////////////////////////////////////////////////////////////////////////////////////

	//////////////////////////////////////////////////////////////////////////////////////////////
	// Add Rewrite rule
	//////////////////////////////////////////////////////////////////////////////////////////////
	static public function add_rewrite_rules(){
	    global $wp,$wp_rewrite; 
	    $wp->add_query_var( 'profile' );

	    foreach( AT_Route::fronted() as $key=>$params ){
			$wp_rewrite->add_rule('^' .$key, 'index.php?profile=true', 'top');
			foreach ( $params['regular_expressions'] as $key => $expression ) {
				$wp_rewrite->add_rule('^' .$key . $expression, 'index.php?profile=true', 'top');
			}

	    }

	    $wp_rewrite->flush_rules();
	}

	static private function find_page_segment( $segments ) {
		try {
			for ($i=0; $i < count( $segments )-1; $i++) { 
				if ($segments[$i] == 'page' && isset( $segments[$i+1] ) && is_numeric( $segments[$i+1] ) ) {
					throw new Exception( $segments[$i+1] );
				}
			}
			$response = false;
		} catch (Exception $e) {
			$response = $e->getMessage();	
		}
		return $response;
	} 

	static public function route() {
		$coreProfile = AT_Core::get_instance();
// global $wp_rewrite;
// $wp_rewrite->flush_rules();
		try {
			if ( is_admin() ){
				// init admin panel
				if (isset($_GET['page'])){
					if($segments = AT_Route::admin($_GET['page'])) {
						// if (isset($_GET['tab']))
						// 	$segments[2] = $_GET['tab'];
						throw new Exception( serialize($segments) );
					}
				} else if ( isset( $_GET['activated'] ) ){
					global $pagenow;
					if( $pagenow === 'themes.php' ) {
						throw new Exception( serialize( array( 'admin', 'install', 'redirect' ) ) );
					}
				}
			} else {	
				////////////////////////////////////////////////////
				// view inited URI
				////////////////////////////////////////////////////
				$segments =  explode( '/', ltrim( trim($_SERVER['REQUEST_URI'], '/' ), 'index.php' ) );


				$host = explode( rtrim( $_SERVER['HTTP_HOST'], '/' ), rtrim( esc_url( home_url() ), '/' ) );
				$request_uri = trim($_SERVER['REQUEST_URI'], '/' );
				if( !empty( $host[1] ) ) {
					$request_uri = substr_replace($request_uri, '', 0, strlen($host[1]));
					$request_uri = trim($request_uri, '/' );
				}
				
				$request_uri = explode( '?', $request_uri );
				$segments =  explode( '/', trim($request_uri[0], '/' ) );


				if ( !empty( $segments[0] ) && array_key_exists( $segments[0], AT_Route::fronted() ) ){
					$param_segment = AT_Route::fronted( $segments[0] );
					array_splice( $segments, 0, $param_segment['segment_start'] );
					if (!isset($segments[0])) {
						$segments[0] = 'vehicles';	
					}
					throw new Exception( serialize($segments) );
				}

				////////////////////////////////////////////////////
				// view inited single post type
				////////////////////////////////////////////////////

				if (is_single() && in_array( get_post_type(), AT_Posttypes::get_post_types() ) ){
					$segments = array(get_post_type(), 'single' );
					throw new Exception( serialize($segments) );
				}

				if( is_archive() && is_tax() ) {
					$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
					if( array_key_exists( $term->taxonomy, AT_Route::rewrite_archive() ) ) {
						$r_segments = AT_Route::rewrite_archive( $term->taxonomy );
						if( isset($segments[3]) && is_numeric($segments[3]) ){
							$r_segments[] = $segments[3];
						}
						throw new Exception( serialize( $r_segments ) );
					}
				}

				if( is_archive() && ( is_category() || is_tag() || is_day() || is_month() || is_year() || is_author() || is_tax() ) ) {
						throw new Exception( serialize( array( 'post', 'archive' ) ) );
				}
				////////////////////////////////////////////////////
				// view inited archive custom post type
				// news, reviews
				////////////////////////////////////////////////////
				if ( is_archive() && in_array( get_post_type(), AT_Posttypes::get_post_types() ) ){
					if( isset($segments[1]) && $segments[1] == 'page' && isset($segments[2]) && is_numeric($segments[2]) )
						throw new Exception( serialize( array( get_post_type(), 'archive', $segments[2] ) ) );
					else
						throw new Exception( serialize( array( get_post_type(), 'archive' ) ) );
					//$segments = array(get_post_type(), 'archive' );
					throw new Exception( serialize($segments) );
				}

				////////////////////////////////////////////////////
				// blog view
				////////////////////////////////////////////////////
				if ( $segments[0] == 'blog' ) {


					// if ( in_array( $post_type, AT_Posttypes::get_post_types() ) ){
						if( self::find_page_segment( $segments ) !== false ) {
							throw new Exception( serialize( array( 'blog', 'index', self::find_page_segment( $segments ) ) ) );
						} else {
							throw new Exception( serialize( array( 'blog', 'index' ) ) );
						}
						//throw new Exception( serialize($segments) );
					// }

					// $template_slug = get_page_template_slug( get_the_ID() );
					// if ( !empty( $template_slug ) ) {
					// 	$template = explode( '/', $template_slug );
					// 	if ( isset( $template[1] ) ) {
					// 		$template = explode( '_', $template[1] );
					// 		if ( $template[0] == 'page' ) {
					// 			$post_type = str_replace( '.php', '', $template[1] );
					// 			if ( in_array( $post_type, AT_Posttypes::get_post_types() ) ){
					// 				if( self::find_page_segment( $segments ) !== false ) //isset($segments[1]) && $segments[1] == 'page' && isset($segments[2]) && is_numeric($segments[2]) )
					// 					throw new Exception( serialize( array( $post_type == 'post' ? 'blog' : $post_type, $post_type == 'post' ? 'index' : 'archive', self::find_page_segment( $segments ) ) ) );
					// 				else
					// 					throw new Exception( serialize( array( $post_type == 'post' ? 'blog' : $post_type, $post_type == 'post' ? 'index' : 'archive' ) ) );
					// 				//throw new Exception( serialize($segments) );
					// 			}
					// 		}
					// 	}
					// }
					// $segments = array('page', 'index' );
					// throw new Exception( serialize($segments) );
				}
				////////////////////////////////////////////////////
				// page view
				////////////////////////////////////////////////////
				if ( is_page() ) {
					$template_slug = get_page_template_slug( get_the_ID() );
					if ( !empty( $template_slug ) && $template_slug != 'default' ) {
						/*
						$template = explode( '/', $template_slug );
						if ( isset( $template[1] ) ) {
							$template = explode( '_', $template[1] );
							if ( $template[0] == 'page' ) {
								$post_type = str_replace( '.php', '', $template[1] );
								if ( in_array( $post_type, AT_Posttypes::get_post_types() ) ){
									if( self::find_page_segment( $segments ) !== false ) //isset($segments[1]) && $segments[1] == 'page' && isset($segments[2]) && is_numeric($segments[2]) )
										throw new Exception( serialize( array( $post_type == 'post' ? 'blog' : $post_type, $post_type == 'post' ? 'index' : 'archive', self::find_page_segment( $segments ) ) ) );
									else
										throw new Exception( serialize( array( $post_type == 'post' ? 'blog' : $post_type, $post_type == 'post' ? 'index' : 'archive' ) ) );
									//throw new Exception( serialize($segments) );
								}
							}
						}
						*/
						// throw new Exception( serialize( array() ) );
						AT_Templates::load($template_slug);
					}

					$segments = array('page', 'index' );
					throw new Exception( serialize($segments) );

				}

				////////////////////////////////////////////////////
				// category view
				////////////////////////////////////////////////////
				if (is_category()) {

					throw new Exception( serialize( array( 'post', 'archive' ) ) );	
				}

				////////////////////////////////////////////////////
				// author view
				////////////////////////////////////////////////////
				if (is_author()) {

					throw new Exception( serialize( array( 'post', 'archive' ) ) );	
				}

				////////////////////////////////////////////////////
				// front page view
				////////////////////////////////////////////////////
				if (is_front_page()) {
					// throw new Exception( serialize( array( 'blog', 'index' ) ) );
					if( isset($segments[1]) && $segments[1] == 'page' && isset($segments[2]) && is_numeric($segments[2]) ){
						throw new Exception( serialize( array( 'blog', 'index', self::find_page_segment( $segments ) ) ) );
					} else {
						throw new Exception( serialize( array( 'blog', 'index' ) ) );
					}
				}

				////////////////////////////////////////////////////
				// blog view
				////////////////////////////////////////////////////
				if (is_home()) {
					if( isset($segments[1]) && $segments[1] == 'page' && isset($segments[2]) && is_numeric($segments[2]) ){
						throw new Exception( serialize( array( 'blog', 'index', $segments[2] ) ) );
					} else {
						throw new Exception( serialize( array( 'blog', 'index' ) ) );
					}
				}
				
				////////////////////////////////////////////////////
				// search view
				////////////////////////////////////////////////////
				if (is_search()) {
					throw new Exception( serialize( array( 'search', 'index' ) ) );
				}

				////////////////////////////////////////////////////
				// 404 view
				////////////////////////////////////////////////////
				if (is_404()) {
					throw new Exception( serialize( array( 'errors', 'show_404' ) ) );
				}

				if (is_attachment()) {
					throw new Exception( serialize( array( 'post', 'single' ) ) );
				}

				if (is_single()) {
					throw new Exception( serialize( array( 'post', 'single' ) ) );
				}

				if ( is_feed() ) {
					throw new Exception('');
				}

				// other pages && post types
				throw new Exception( serialize( array( 'errors', 'show_404' ) ) );
				//echo "don't find";
				die();
			}

		} catch (Exception $e) {
			if ( $e->getMessage() != '' ){
				$route = AT_Router::get_instance( unserialize($e->getMessage()) );
				$route->run();
			}
		}
	}
}
?>
