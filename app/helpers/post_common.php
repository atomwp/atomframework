<?php
if (!defined("AT_ROOT")) die('!!!');
class AT_Post_Common {

	public static function post_date_boxed ( $date = false, $icon = false, $link = false ) {
		// date
		if ( $date == false ) {
			$post_date = strtotime( get_the_date() );
		} elseif ( $date != false ) {
			$post_date = strtotime( $date );
		}
		// icon
		// if ( $icon == false ) {
		// 	$post_icon = str_replace('im-icon-pencil','fa fa-pencil', get_post_meta( get_the_ID(), '_post_icon', true ));
		// } elseif ( $icon != false ) {
		// 	$post_icon = str_replace('im-icon-pencil','fa fa-pencil', $icon);
		// } else {
		// 	$post_icon = 'fa fa-pencil';
		// }

		$format = get_post_format();
		$post_icon = 'fa fa-pencil';

		if ($format == 'gallery') {
			$post_icon = 'fa fa-camera';
		}

		if ($format == 'link') {
			$post_icon = 'fa fa-link';
		}


		// link
		if ( $link == false ) {
			$post_link = get_permalink();
		} elseif ( $link != false ) {
			$post_link = $link;
		}

		// out
		if ( isset( $post_date ) ) {
			$post_date = date( 'd M', $post_date );
			$post_date_separated = explode( ' ', $post_date );
			$out = '';
			$out .= '<div class="date_boxed-a">';
			$out .= '<i class="date_icon ' . $post_icon . '"></i>';
			$out .= ( !empty( $post_link ) ? '<a href="' . esc_url($post_link) . '" title="' . esc_attr( $post_date ) . '">' : '' );
			$out .= '<span class="date_day">' . $post_date_separated[ 0 ] . '</span>';
			$out .= '<span class="date_month">' . $post_date_separated[ 1 ] . '</span>';
			$out .= ( !empty( $post_link ) ? '</a>' : '' );

			if(atom_featured_post_class() != '') {
				$out .= '<div class="featured">';
					$out .= '<span class="icon_star"></span>';
				$out .= '</div>';
			}

			$out .= '</div><!-- /.date_boxed -->';
			return $out;
		}
	}

	public static function get_categories( $post_type = 'post' ) { 
		$args = array(
			'type' => $post_type,
			'hide_empty' => 0, 
			'orderby' => 'name', 
			'hierarchical' => true
		);
		$categories = array();
		foreach( get_categories( $args ) as $key=>$category ) {
			$categories[$category->term_id] = $category->name;
		}
		return $categories;
	}

	public static function get_terms( $terms_type ) { 
		$args = array(
		    'orderby'       => 'name', 
		    'order'         => 'ASC',
		    'hide_empty'    => false,
		    'parent' 		=> 0,
		    'fields'        => 'all',
		); 
		$terms = array();
		foreach( get_terms( $terms_type, $args ) as $key=>$term ) {
			if (is_object($term)) {
				$terms[$term->term_id] = $term->name;
			}
		}
		return $terms;
	}

	public static function get_parents_tree( $post_id ) {
		$post = get_post( $post_id );
		echo $post->post_parent;
	}

	public static function paged() {
		if (get_query_var('paged') ) {
			$paged = get_query_var('paged');
		} elseif ( get_query_var('page') ) {
			$paged = get_query_var('page');
		}
		else {
			$paged = 1;
		}

		return $paged;
	}

	public static function get_pages_by_template( $template, $with_default = false ) {
		$pages = get_posts(array(
		    'post_type' => 'page',
			'post_status' => 'publish',
			'meta_key' => '_wp_page_template',
			'meta_value' => $template
		));
		$return = array();
		if ( $with_default ) {
			$return['default'] =esc_html__( 'Default', 'atom' );
		}
		foreach($pages as $page){
			$return[$page->ID] = $page->post_title;
		}
		return $return;
	}
}
