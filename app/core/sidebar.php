<?php
if (!defined("AT_ROOT")) die('!!!');
class AT_Sidebars {
	
	static public function register(){
		$sidebars = array(
			'primary' => array(
				'name' =>esc_html__( 'Primary Widget Area', 'atom' ),
				'desc' =>esc_html__( 'The primary widget area', 'atom' )
			),

			'blog_primary' => array(
				'name' =>esc_html__( 'Blog Primary Widget Area', 'atom' ),
				'desc' =>esc_html__( 'The blog primary widget area', 'atom' )
			),
			'testimonials_primary' => array(
				'name' =>esc_html__( 'Testimonials Primary Widget Area', 'atom' ),
				'desc' =>esc_html__( 'The testimonials primary widget area', 'atom' )
			),
			'staff_primary' => array(
				'name' =>esc_html__( 'Staff Primary Widget Area', 'atom' ),
				'desc' =>esc_html__( 'The staff primary widget area', 'atom' )
			),
			'portfolio_primary' => array(
				'name' =>esc_html__( 'Portfolio Primary Widget Area', 'atom' ),
				'desc' =>esc_html__( 'The portfolio primary widget area', 'atom' )
			),
			'partners_primary' => array(
				'name' =>esc_html__( 'Partners Primary Widget Area', 'atom' ),
				'desc' =>esc_html__( 'The partners primary widget area', 'atom' )
			),
			'news_primary' => array(
				'name' =>esc_html__( 'News Primary Widget Area', 'atom' ),
				'desc' =>esc_html__( 'The news primary widget area', 'atom' )
			),
			'price_tables_primary' => array(
				'name' =>esc_html__( 'Price Tables Primary Widget Area', 'atom' ),
				'desc' =>esc_html__( 'The price tables primary widget area', 'atom' )
			),

			'footer_1' => array(
				'name' =>esc_html__( 'First Footer Widget Area', 'atom' ),
				'desc' =>esc_html__( 'The first footer widget area', 'atom' )
			),
			'footer_2' => array(
				'name' =>esc_html__( 'Second Footer Widget Area', 'atom' ),
				'desc' =>esc_html__( 'The second footer widget area', 'atom' )
			),
			'footer_3' => array(
				'name' =>esc_html__( 'Third Footer Widget Area', 'atom' ),
				'desc' =>esc_html__( 'The third footer widget area', 'atom' )
			),
			'footer_4' => array(
				'name' =>esc_html__( 'Fourth Footer Widget Area', 'atom' ),
				'desc' =>esc_html__( 'The fourth footer widget area', 'atom' )
			),

		);

		foreach ( $sidebars as $type => $sidebar ){
			register_sidebar(array(
				'name' => $sidebar['name'],
				'id'=> $type,
				'description' => $sidebar['desc'],
				'before_widget' => '<div id="%1$s" class="%2$s">',
				'after_widget' => '</div>',
				'before_title' => '<h5>',
				'after_title' => '</h5>',
			));
		}
		$custom_sidebars = AT_Core::get_instance()->get_option('custom_sidebars', array());
		foreach ( $custom_sidebars as $key => $sidebar ){
			register_sidebar(array(
				'name' => $sidebar['name'],
				'id'=> 'at_custom_sidebar_' . $key,
				'description' => '',
				'before_widget' => '<div id="%1$s" class="%2$s">',
				'after_widget' => '</div>',
				'before_title' => '<h5>',
				'after_title' => '</h5>',
			));
		}
	}

	public static function get_custom_sidebars(){
		$data = AT_Core::get_instance()->get_option('custom_sidebars', array());
		$sidebars = array();
		foreach ($data as $key => $value) {
			$sidebars['at_custom_sidebar_' . $key] = $value['name'];
		}
		return $sidebars;
	}
}
