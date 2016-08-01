<?php

class AT_Filters{

	static public function remove_media_tab( $strings ) {
		unset(
			$strings["insertFromUrlTitle"],
			$strings['createGalleryTitle']
		);
		return $strings;
	}

	static public function remove_medialibrary_tab( $tabs ) {
	    //if ( !current_user_can( 'update_core' ) ) {
		trigger_error('!');
	        unset($tabs['library']);
	        unset($tabs['library']);
	        return $tabs;
	    // }
	}

	static public function optimize_script_url( $src ){
		$segments = explode( '?ver', $src );
		return $segments[0];
	}

	static public function users_own_attachments( $wp_query_obj ) {

	    global $current_user, $pagenow;

	    if( !is_a( $current_user, 'WP_User') )
	        return;

	    if( 'upload.php' != $pagenow )
	        return;

	    // if( !current_user_can('delete_pages') )
	    //     $wp_query_obj->set('author', $current_user->id );

	    return;
	}

	static public function remove_per_page_from_query_string($query_string) {
		if ( isset( $query_string['post_type'] ) && in_array( $query_string['post_type'], AT_Posttypes::get_custom_post_types())) {
			$posts_per_page = (int)AT_Core::get_instance()->get_option( $query_string['post_type'] . '_per_page', get_option('posts_per_page') );
			if ( $posts_per_page == 0 ) {
				$posts_per_page = get_option('posts_per_page');
			}
			$query_string['posts_per_page'] = $posts_per_page;
		}
		return $query_string;
	}

	static public function add_body_class() {
		$classes = array();
		$classes[] = 'page';
		$classes[] = 'at-transform';
        if($content_width = AT_Common::getMod('initial_content_width')) {
            $classes[] = 'at-'.$content_width;
        }
        if ( is_page_template( 'blog-expanding-tiles.php' ) ) {
            $classes[] = 'at-ajax-search';
        }
        $classes[] = 'tpl-'.str_replace(array(AT_THEME_ROOT,'.php','.','/'),array('','','-',''), get_page_template());

        // if(AT_Common::getMod('boxed') == 'yes' && AT_Common::getMod('header_type') !== 'header-vertical') {
        if(AT_Common::getMod('boxed') == 'yes') {
            $classes[] = 'at-boxed';
        }

        $header_type = atom_get_meta_field_intersect('header_type', atom_get_page_id()
        );

        $classes[] = 'at-'.AT_Common::getMod('header_behaviour');


        $classes[] = 'at-'.$header_type;

        // if(AT_Common::getMod('smooth_scroll') == 'yes') {
        //     $classes[] = 'at-smooth-scroll';
        // }
        // echo atom_get_page_id();
		// print_r($classes);
		return $classes;
	}
}
?>
