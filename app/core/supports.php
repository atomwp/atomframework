<?php

class AT_Supports{

	static public function load() {
        //add support for feed links
        add_theme_support('automatic-feed-links');

        // customizer support
        add_theme_support( 'at_customizer_support' );

        add_theme_support( 'add_theme_support' );

        add_theme_support( 'custom-background' );

        add_theme_support( 'title-tag' );

        //add support for post formats
        add_theme_support('post-formats', array('gallery', 'link', 'quote', 'video', 'audio', 'status'));

        //add theme support for post thumbnails
        add_theme_support('post-thumbnails');

        //add theme support for title tag
        if(function_exists('_wp_render_title_tag')) {
            add_theme_support('title-tag');
        }

        // Default thumbnails
        add_image_size('atom_square', 550, 550, true);

		// Carousel
		add_image_size('atom_landscape', 600, 400, true);

        // Masonry Lists
        add_image_size('atom_portrait', 600, 730, true);

        // Splitscreen
		add_image_size('atom_split_column_landscape', 900, 600, true);
		
        load_theme_textdomain( 'atom', get_template_directory().'/languages' );
    }
}
