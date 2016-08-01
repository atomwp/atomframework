<?php
if (!defined("AT_ROOT")) die('!!!');

class AT_Post extends AT_Controller{
	public function __construct() {
		parent::__construct();
	}

	public function single(){
		$post_id = get_the_ID();

		if ( $this->uri->is_ajax_request() ) {
			$this->view->add_json( array( 'html' => atom_get_single_html('yes')) )->display();
			// $this->view->add_json($response)->display();
			exit;
		}

		$layout = get_post_meta( $post_id, '_layout', true );
		if ( empty( $layout ) || !$this->view->check_layout( $layout ) ) $layout = $this->core->get_option( 'default_page_layout', 'content_right' );
		$page_title = get_the_title();
		$page_tagline = get_post_meta( $post_id, '_page_tagline', true );

		if( get_option( 'show_on_front' ) == 'page' ) { 
			$blog_url = get_permalink( get_option('page_for_posts' ) );
			$blog_title = get_the_title( get_option('page_for_posts' ) );
		} else {
			$blog_url = esc_url( home_url() ) ;
			$blog_title = $this->core->get_option('blog_title');
		}

		if ( !get_post_meta( $post_id, '_disable_breadcrumbs', true ) ) {
			$this->breadcrumbs->add_item( $blog_title, $blog_url );

			$categories = wp_get_post_categories( $post_id, array('fields' => 'ids') );
			if ( count( $categories ) > 0 ) {
				$this->breadcrumbs->add_item( get_cat_name( $categories[0] ), get_category_link( $categories[0] ) );
			}
			$this->breadcrumbs->add_item( $page_title . ' ' . $page_tagline, get_permalink( $post_id ) );
		}

		if ( !get_post_meta( $post_id, '_disable_page_title', true ) ) {
			$this->view->add_block('page_title', 'general/page_title', array( 'page_title' => $page_title ));
		}

		$this->view->use_layout('header_' . $layout . '_footer')
			->add_block('content', 'post/single', array( 'layout' => $layout ));
	}

	public function archive(){
		global $wp_query;
		$title =esc_html__( 'Archives', 'atom' );
		$page = get_query_var( 'paged' );
		
		$segment = 4;
		if( is_category() ) {
			$title = sprintf(esc_html__('Category Archive for: %1$s', 'atom' ), '&lsquo;' . single_cat_title('',false) . '&rsquo;');
			$catID = get_query_var('cat');
		} elseif ( is_tag () ) {
			$title = sprintf(esc_html__('All Posts Tagged Tag: %1$s', 'atom' ), '&lsquo;' . single_tag_title('',false) . '&rsquo;');
		} elseif ( is_day() ) {
			$title = sprintf(esc_html__('Daily Archive for: %1$s', 'atom' ), '&lsquo;' . get_the_time('F jS, Y') . '&rsquo;');
			$likedate = 'Y-m-d ';
		} elseif ( is_month() ) {
			$title = sprintf(esc_html__('Monthly Archive for: %1$s', 'atom' ), '&lsquo;' . get_the_time('F, Y') . '&rsquo;');
			$likedate = 'Y-m-';
		} elseif ( is_year() ) {
			$title = sprintf(esc_html__('Yearly Archive for: %1$s', 'atom' ), '&lsquo;' . get_the_time('Y') . '&rsquo;');
			$likedate = 'Y-';
		} elseif ( is_author() ) {
			global $author;
			$curauth = get_userdata( intval($author) );
			$title = sprintf(esc_html__('Author Archive for: %1$s', 'atom' ), '&lsquo;' . $curauth->nickname . '&rsquo;');
		} elseif ( is_tax() ) {
			$term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
			$title = sprintf(esc_html__('Archives for: %1$s', 'atom' ), '&lsquo;' . $term->name . '&rsquo;');
		}
		$count = $wp_query->found_posts;

		if ( is_author() ) {
			$this->view->add_block('page_title', 'general/page_title_author', array( 'page_title' => $title ));
		} else {
			$this->view->add_block('page_title', 'general/page_title', array( 'page_title' => $title ));
		}
		$layout = $this->core->get_option( 'default_page_layout', 'content_right' );

		$content_atts = array(
			'posttype' => 'post',
			'caption' => '',
			'loop_layout' => 'grid_bootstrap_author',
			'item_layout' => 'post_style_4',
			// 'loop_layout' => 'grid_tile',
			// 'item_layout' => 'post_expanding',
			'carousel_type' => '',
			'carousel_control' => '',
			'column' => 4,
			'showposts' => '',
			'animation' => '',
			'category_in' => '',
			'animation' => '',
			'sidebar' => 'default',
		);

		if ($content_atts['loop_layout'] == 'grid_tile') {
			$content_atts['type'] = 'expanding-tiles';
			$content_atts['classes'] = atom_get_blog_holder_classes($content_atts['type'], $content_atts['sidebar']);
		}

		$content = '';
		if( $wp_query->have_posts() ) {
			$sc_loop = new AT_ShortCode_Layout_Picker( $wp_query, $content_atts ); 
			$content = $sc_loop->get_output();
		}

		$paginator = $this->load->library('paginator');

		if ( get_option('permalink_structure') == '' ) {
			$paginator = $paginator->get_query_string( 'paged', $count, get_option('posts_per_page'));			
		} else {

			$segments = explode( '?', $_SERVER['REQUEST_URI'] );
			$segments = trim( $segments[0], '/' );
			$segments = explode( '/page/', $segments );
			$url = $segments[0];

			$paginator = $paginator->get( $segment, $count, get_option('posts_per_page'), 1, 2, $url . '/page/' . $page, $url . '/' );
		}

		$this->breadcrumbs->add_item( $title, '' );
		$this->view->use_layout('header_' . $layout . '_footer')
			->add_block( 'content', 'blog/loop', array( 'content' => $content, 'layout' => $layout ) )
			->add_block( 'content/pagination', 'general/pagination', $paginator );
	}

}
