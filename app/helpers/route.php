<?php
if (!defined("AT_ROOT")) die('!!!');

class AT_Route {


	static function rewrite_archive( $key = false ){
		$rewrite = array(
			'portfolio_category' => array( 'portfolio', 'archive' ),
			'testimonials_category' => array( 'testimonials', 'archive' ),
			'staff_category' => array( 'staff', 'archive' ),
			'news_category' => array( 'news', 'archive' ),
			'partners_category' => array( 'partners', 'archive' ),
		);
		return ( $key ? $rewrite[$key] : $rewrite );
	}


	static function fronted(  $key = false  ){
		$rewrite = array(
			'comments' 	=> array( 'segment_start' => 0, 'regular_expressions' => array( '/([^/]*)', '/([^/]*)' ), 'without_index' => false ),
			'dynamic' 	=> array( 'segment_start' => 0, 'regular_expressions' => array( '/([^/]*)', '/([^/]*)/([^/]*)' ), 'without_index' => true ),
		);
		return ( $key ? $rewrite[$key] : $rewrite );
	}

	static function admin($page){
		$config = array();

		$config['at_theme_options_general'] = array(
			'page' => array('theme_options', 'general'),
		);

		$config['at_theme_options_header'] = array(
			'page' => array('theme_options', 'header'),
		);

		$config['at_theme_options_footer'] = array(
			'page' => array('theme_options', 'footer'),
		);

		$config['at_theme_options_sociable'] = array(
			'page' => array('theme_options', 'sociable'),
		);

		$config['at_theme_options_google_fonts'] = array(
			'page' => array('theme_options', 'google_fonts'),
		);

		$config['at_theme_options_styled'] = array(
			'page' => array('theme_options', 'styled'),
			'subpage' => array('skin_generator_cont'),
		);

		$config['at_theme_options_comments'] = array(
			'page' => array('theme_options', 'comments'),
		);

		$config['at_theme_options_blog'] = array(
			'page' => array('theme_options', 'blog'),
		);

		$config['at_theme_options_news'] = array(
			'page' => array('theme_options', 'news'),
		);

		$config['at_theme_options_testimonials'] = array(
			'page' => array('theme_options', 'testimonials'),
		);

		$config['at_theme_options_staff'] = array(
			'page' => array('theme_options', 'staff'),
		);

		$config['at_theme_options_portfolio'] = array(
			'page' => array('theme_options', 'portfolio'),
		);

		$config['at_theme_options_partners'] = array(
			'page' => array('theme_options', 'partners'),
		);

		$config['at_theme_options_sidebars'] = array(
			'page' => array('theme_options', 'sidebars'),
		);

		$config['at_theme_options_backup'] = array(
			'page' => array('theme_options', 'backup'),
		);

		$config['at_theme_options_support'] = array(
			'page' => array('theme_options', 'support'),
		);

		$config['at_theme_options_release'] = array(
			'page' => array('theme_options', 'release'),
		);

		$config['at_theme_options_troubleshooting'] = array(
			'page' => array('theme_options', 'troubleshooting'),
		);

		$config['at_site_options_maintenance'] = array(
			'page' => array('site_options', 'maintenance'),
		);

		$config['at_theme_install'] = array(
			'page' => array('install', 'index'),
		);

		$config['at_subscribe'] = array(
			'page' => array('subscribe', 'general'),
		);

		try {
			if ( isset($config[$page]) ) {
				if ( isset($_GET['page']) && isset($config[$page]['page']) ) {
					if (isset($_GET['subpage']) && isset($config[$page]['subpage'])) {
						if ( count($config[$page]['subpage']) == 1 ) {
							$config[$page]['subpage'][] = $_GET['subpage'];
						}
						throw new Exception( serialize( array_merge(array('admin'), $config[$page]['subpage'] ) ) );
					}
					throw new Exception( serialize( array_merge(array('admin'), $config[$page]['page'] ) ) );
				}
			}

			return false;
		} catch (Exception $e) {
			return unserialize( $e->getMessage() );
		}

		// return (isset($config[$page]) ? 
		// 		( isset($config[$page]['subpage']) ? array_merge(array('admin'), $config[$page]['subpage'] ) : array_merge(array('admin'), $config[$page] ) ) 
		// 		: false);
	}

}
