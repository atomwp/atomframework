<?php
if (!defined("AT_ROOT")) die('!!!');
class AT_Common {
	
	public function check_session(){
		$this->_is_logged = ( AT_Session::get_instance()->userdata( 'logged' ) === true ) ? true : false;
		if ( $this->is_user_logged() ) $this->_user_id = AT_Session::get_instance()->userdata( 'user_id' );
	}

	static public function favicon($fallback) {

	}

	//https://gist.github.com/kailoon/e2dc2a04a8bd5034682c
	static public function prepare_gwf($query_string) {
		$url = false;
	    if ( 'off' !== _x( 'on', 'Google font: on or off', 'atom' ) ) {
			$url = add_query_arg( 'family', urlencode( $query_string ), "//fonts.googleapis.com/css" );
		}
		return $url;
	}

	static public function encoder() {
		$converter = array(
			'format' => 'base64',
			'action' => 'encode'
		);
		return implode('_',$converter);
	}

	static public function decoder() {
		$converter = array(
			'format' => 'base64',
			'action' => 'decode'
		);
		return implode('_',$converter);
	}

	static public function integrate() {
		$integration = array(
			'action' => 'add',
			'extension' => 'shortcode'
		);
		return implode('_',$integration);
	}

    static public function is_wpml_installed() {
        return defined('ICL_SITEPRESS_VERSION');
    }

    static public function is_contactform7() {
        return defined('WPCF7_VERSION');
    }

    static public function max_image_width_srcset() {
    	return 1920;
    }

    static public function max_srcset_image_width() {
    	return 1920;
    }

    static public function is_visual_composer() {
        return class_exists('WPBakeryVisualComposerAbstract');
    }

    static public function available_skins() {
        return array(
			'default' => 'Default (Yyello)',
			'yello' => 'Yyello',
			'light.aqua' => 'Aqua',
			'duochromic.alizarin' => 'Duo-Chromic: Alizarin',
			'duochromic.belize' => 'Duo-Chromic: Belize',
			'duochromic.sun' => 'Duo-Chromic: Sun',
			'duochromic.concrete' => 'Duo-Chromic: Concrete',
			'duochromic.midnight' => 'Duo-Chromic: Midnight',
			'duochromic.emerald' => 'Duo-Chromic: Emerald',
			'duochromic.amethyst' => 'Duo-Chromic: Amethyst',
			'drastic.alizarin' => 'Drastic: Alizarin Blue',
        );
    }

    static public function is_seo_plugin() {
        if(defined('WPSEO_VERSION') || class_exists('All_in_One_SEO_Pack')) {
            return true;
        }
        return false;
    }

    static public function getMod($id) {
    	return get_theme_mod($id, AT_CustomizerDefaults::getDefault($id));
    }

    static public function has_shortcode($shortcode, $content = '') {
        $has_shortcode = false;

        if($shortcode) {
            if($content == '') {
                $page_id = atom_get_page_id();
                if(!empty($page_id)) {
                    $current_post = get_post($page_id);
                    // If object and property exists
                    if(is_object($current_post) && property_exists($current_post, 'post_content')) {
                        $content = $current_post->post_content;
                    }
                }
            }
            if(stripos($content, '['.$shortcode) !== false) {
                $has_shortcode = true;
            }
        }

        return $has_shortcode;
    }

	static public function is_default_template() {
		return is_archive() || is_search() || is_404() || (is_front_page() && is_home());
	}

	static public function getYoutubeID($url){
	    $pattern = '#^(?:https?://)?';
	    $pattern .= '(?:www\.)?';
	    $pattern .= '(?:';
	    $pattern .=   'youtu\.be/';
	    $pattern .=   '|youtube\.com';
	    $pattern .=   '(?:';
	    $pattern .=     '/embed/';
	    $pattern .=     '|/v/';
	    $pattern .=     '|/watch\?v=';
	    $pattern .=     '|/watch\?.+&v=';
	    $pattern .=   ')';
	    $pattern .= ')';
	    $pattern .= '([\w-]{11})';
	    $pattern .= '(?:.+)?$#x';

	    preg_match($pattern, $url, $matches);

	    return (isset($matches[1])) ? $matches[1] : false;
	}
	static public function  convert_time( $original = 0, $do_more = 0 ) {
		# array of time period chunks
		$chunks = array(
			array(60 * 60 * 24 * 365 , 'year'),
			array(60 * 60 * 24 * 30 , 'month'),
			array(60 * 60 * 24 * 7, 'week'),
			array(60 * 60 * 24 , 'day'),
			array(60 * 60 , 'hour'),
			array(60 , 'minute'),
		);

		$today = time();
		$since = $today - $original;

		for ($i = 0, $j = count($chunks); $i < $j; $i++) {
			$seconds = $chunks[$i][0];
			$name = $chunks[$i][1];

			if (($count = floor($since / $seconds)) != 0) {
				break;
			}
		}

		$print = ($count == 1) ? '1 '.$name : "$count {$name}s";

		if ($i + 1 < $j) {
			$seconds2 = $chunks[$i + 1][0];
			$name2 = $chunks[$i + 1][1];

			# add second item if it's greater than 0
			if ( (($count2 = floor(($since - ($seconds * $count)) / $seconds2)) != 0) && $do_more ) {
				$print .= ($count2 == 1) ? ', 1 '.$name2 : ", $count2 {$name2}s";
			}
		}
		return $print;
	}

	static public function get_ls_slides() {
		global $wpdb;
		$slides_array = array();
	 	if ( function_exists("layerslider_activation_scripts") ) {
			// Table name
			$ls_table_name = $wpdb->prefix . "layerslider";
			// Get sliders
			$ls_query = $wpdb->prepare("SELECT id, name FROM $ls_table_name
		            WHERE flag_hidden = '0' AND flag_deleted = '0'
		            ORDER BY date_c ASC LIMIT %d", 100);
			$sliders = $wpdb->get_results( $ls_query );
		                    
            // Iterate over the sliders
			foreach($sliders as $key => $item) {
				if (empty($item->name)) {
						$slide_name =esc_html__("Untitled", 'atom') . " " . $item->id;
				} else {
					$slide_name = $item->name;
				}
				$slides_array[$item->id] = $slide_name;
			}
		} else {
			$slides_array[0] =esc_html__("Please install LayerSlider WP", 'atom');
		}
		return $slides_array;
	}

	static public function get_rev_slides() {
		global $wpdb;
		$slides_array = array();
	 	if ( class_exists("UniteBaseClassRev") ) {

			// Table name
			$rev_table_name = $wpdb->prefix . "revslider_sliders";
			// Get sliders
			$rev_query = $wpdb->prepare("SELECT alias FROM $rev_table_name
		            ORDER BY title ASC LIMIT %d", 100);
			$sliders = $wpdb->get_results( $rev_query );
		                    
		                    // Iterate over the sliders
			foreach($sliders as $key => $item) {
				if (empty($item->title)) {
						$slide_name = $item->alias;
				} else {
					$slide_name = $item->title;
				}
				$slides_array[$item->alias] = $slide_name;
			}
		} else {
			$slides_array[0] =esc_html__("Please install Slider Revolution Plugin", 'atom');
		}

		return $slides_array;
	}

	static public function truncate( $content = '', $limit = 200 ) {
		$content = preg_replace('/<[^>]*>/', '', preg_replace('/\[[^>]*\]/', '', $content));
		$truncated = (strlen($content) > $limit) ? substr($content, 0, $limit) . '...' : $content;
		return $truncated;
	}

	static public function portfolio_comment_url( $nav = false ) {
		global $wpdb, $post, $wp_rewrite;
		
		if( !is_singular( 'portfolio' ) ) return;
		
		$gallery_name = get_query_var( 'gallery' );
		$query = $wpdb->prepare("SELECT ID FROM {$wpdb->posts} WHERE post_name = '%s'", $gallery_name);
		$gallery_id = $wpdb->get_var($query);
		$get_post = get_post( $gallery_id );
		
		$paginate = ( $nav ) ? 'comment-page-%#%/' : '';

		if( $wp_rewrite->using_permalinks() )
			$redirect_to = home_url() . '/portfolio/' . $post->post_name. '/gallery/' . $get_post->post_name . '/' . $paginate;
			
		elseif( $nav )
			$redirect_to = add_query_arg( 'cpage', '%#%' );
		
		else
			$redirect_to = htmlspecialchars( add_query_arg( array( 'gallery' => $get_post->post_name ), get_permalink( $post->ID )) );
			
		if( $nav && $wp_rewrite->using_permalinks() )
			return array( 'base' => $redirect_to );
			
		elseif( $nav )
			return array();
			
		else
			return $redirect_to;
	}

	static public function get_title() {
        $id = atom_get_page_id();
        $title 	= '';

        //is current page tag archive?
        if (is_tag()) {
            //get title of current tag
            $title = single_term_title("", false).esc_html__(' Tag', 'atom' );
        }

        //is current page date archive?
        elseif (is_date()) {
            //get current date archive format
            $title = get_the_time('F Y');
        }

        //is current page author archive?
        elseif (is_author()) {
            //get current author name
            $title = esc_html__('Author:', 'atom' ) . " " . get_the_author();
        }

        //us current page category archive
        elseif (is_category()) {
            //get current page category title
            $title = single_cat_title('', false);
        }

        //is current page blog post page and front page? Latest posts option is set in Settings -> Reading
        elseif (is_home() && is_front_page()) {
            //get site name from options
            $title = get_option('blogname');
        }

        //is current page search page?
        elseif (is_search()) {
            //get title for search page
            $title = esc_html__('Search', 'atom' );
        }

        //is current page 404?
        elseif (is_404()) {
            //is 404 title text set in theme options?
            if(AT_Common::getMod('404_title') != "") {
                //get it from options
                $title = AT_Common::getMod('404_title');
            } else {
                //get default 404 page title
                $title = esc_html__('404 - Page not found', 'atom' );
            }
        }

        //is current page some archive page?
        elseif (is_archive()) {
            $title = esc_html__('Archive', 'atom' );
        }

        //current page is regular page
        else {
            $title = get_the_title($id);
        }

        $title = apply_filters('atom_title_text', $title);

        return $title;
	}

	static public function draw_title($title = false) {
		if ($title) {
			echo $title;
		} else {
			echo self::get_title();			
		}
	}

	public static function get_title_params() {
        $id = atom_get_page_id();

        extract(atom_title_area_height());
        extract(atom_title_area_background());


        //check if title area is visible on page first, then in the options
        if(($meta_temp = get_post_meta($id, "at_show_title_area_meta", true)) !== ""){
            $show_title_area = $meta_temp == 'yes' ? true : false;
		}elseif(is_single()){
			$show_title_area = AT_Common::getMod('blog_single_show_title_area') == 'yes' ? true : false;
		}else {
            $show_title_area = AT_Common::getMod('show_title_area') == 'yes' ? true : false;
        }

        //check for title type on page first, then in options
        if(($meta_temp = get_post_meta($id, "at_title_area_type_meta", true)) !== ""){
            $type = $meta_temp;
        }else {
            $type = AT_Common::getMod('title_area_type');
        }

        //check if breadcrumbs are enabled on page first, then in options
        if(($meta_temp = get_post_meta($id, "at_title_area_enable_breadcrumbs_meta", true)) !== ""){
            $enable_breadcrumbs = $meta_temp == 'yes' ? true : false;
        }else {
            $enable_breadcrumbs = AT_Common::getMod('title_area_enable_breadcrumbs') == 'yes' ? true : false;
        }

        //check if title color is set on page
        $title_color = '';
        if(($meta_temp = get_post_meta($id, "at_title_text_color_meta", true)) !== ""){
            $title_color = 'color:'.$meta_temp.';';
        }

        //check if subtitle color is set on page
        $subtitle_color = '';
        if(($meta_temp = get_post_meta($id, "at_subtitle_color_meta", true)) !== ""){
            $subtitle_color = 'color:'.$meta_temp.';';
        }

        $params = array(
            'show_title_area' => $show_title_area,
            'type' => $type,
            'enable_breadcrumbs' => $enable_breadcrumbs,
            'title_height' => $title_height,
            'title_holder_height' => $title_holder_height,
            'title_subtitle_holder_padding' => $title_subtitle_holder_padding,
            'title_background_color' => $title_background_color,
            'title_background_image' => $title_background_image,
            'title_background_image_width' => $title_background_image_width,
            'title_background_image_src' => $title_background_image_src,
            'has_subtitle' => get_post_meta($id, "at_title_area_subtitle_meta", true) != "" ? true : false,
            'title_color' => $title_color,
            'subtitle_color' => $subtitle_color
        );

        $params = apply_filters('atom_title_area_height_params', $params);
        return $params;
	}

	static public function trim_content($text = '', $length = 100, $ellipsis = '...') {
		$text = ( $text == '' ) ? get_the_content('') : $text;
		$text = preg_replace( '`\[(.*)]*\]`','',$text );
		if ( strlen ( $text ) > $length ) {
			$text = strip_tags( $text  );
			$text = substr( $text, 0, $length );
			$text = substr( $text, 0, strripos($text, " " ) );
			$text = $text.$ellipsis;
		}
	return $text;

	}

	static public function inlineHTML($text) {
		return str_replace(
			array("\r","\n","\t"),
			array("","",""),
			$text
		);
	}

	static public function get_content($post_id = false, $optimize = false, $display = false) {
		if ($post_id) {
			// $post = get_post($post = $post_id, $output = 'OBJECT', $filter = 'display');

			// $post = new WP_Post( $post );
			// print_r($post);
			// if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' || (isset($_REQUEST
			// $content = apply_filters('the_content', $post->post_content);
			wp_reset_postdata();
			$content = get_the_content();
			// self::atom_excerpt();
			// $content = get_the_content($id);

		} else {
			$content = get_the_content();
		}

		$content = apply_filters('the_content', $content);

		if ($optimize) {
			$content = self::inlineHTML($content);
		}
		if ($display == true) {
			echo $content;
		} else {
			return $content;
		}
	}

	// static public function is_user_logged(){
	// 	return ( AT_Session::get_instance()->userdata( 'logged' ) === true ) ? true : false;
	// }

	// static public function get_logged_user_id(){
	// 	return !self::is_user_logged() ? 0 : AT_Session::get_instance()->userdata( 'user_id' );
	// }

	static public function redirect(  $url = ''  ){
		wp_redirect( self::site_url( $url ) );
		exit;
	}

	static public function site_url(  $url = '', $root = true  ){
		//return !$root ? ( '/profile/' . ltrim( $url, '/' ) ) : ( '/' . ltrim( $url, '/' ) );
		if (strpos($url, 'http') === 0) return $url;
		return esc_url( home_url() . '/' . ltrim( $url, '/' ) );
	}

	static public function static_url( $url = '' ){
		if (strpos($url, 'http') === 0) return $url;
		return AT_URI .'/' . ltrim( $url, '/' );
	}

	static public function is_responsive() {
		if (AT_Common::getMod('responsiveness') === 'yes') {
			return true;
		}
		return false;
	}

	static public function pusher_style() {
		if (AT_Common::getMod('pusher_with') > '0') {
			return ' style="max-width:'.AT_Common::getMod('pusher_with').'px"';
		}		
	}

	static public function is_ajax_request() {
        if (isset($_REQUEST['wp_customize']) && $_REQUEST['wp_customize'] == "on") {
          return false;
        }

		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest' || (isset($_REQUEST["ajaxReq"]) && $_REQUEST["ajaxReq"] == 'yes')) {
			return true;
		}
	}


	static public function has_smooth_ajax() {
        if (isset($_REQUEST['wp_customize']) && $_REQUEST['wp_customize'] == "on") {
          return false;
        }

        if (AT_Common::getMod('smooth_page_transitions') === "yes" && AT_Common::getMod('smooth_pt_true_ajax') != 'yes') {
        	return true;
        }

	}

	static public function has_smooth_scroll() {
		$osx = strpos($_SERVER['HTTP_USER_AGENT'], 'Macintosh; Intel Mac OS X');
		if(AT_Common::getMod('smooth_scroll') == 'yes' && $osx == false) {
			return true;
		}
		return false;
	}
	static public function assets_url( $url = '' ){
		if (strpos($url, 'http') === 0) return $url;
		return AT_ASSETS_URI . '/' . ltrim( $url, '/' );
	}

	static public function get_current_page_url(){
		$page_url = AT_Router::get_instance()->server('HTTPS') == 'on' ? 'https://' : 'http://';
	    if ( AT_Router::get_instance()->server('SERVER_PORT') != '80' )
	    	$page_url .= AT_Router::get_instance()->server('SERVER_NAME') . ':' . AT_Router::get_instance()->server('SERVER_PORT') . AT_Router::get_instance()->server('REQUEST_URI');
	    else
	    	$page_url .= AT_Router::get_instance()->server('SERVER_NAME') . AT_Router::get_instance()->server('REQUEST_URI');
	    return $page_url;
	}

	static function get_permalink_without_domain( $permalink ){
        return substr( $permalink, strlen(  home_url() ) );
	}

	static public function wp_path() {
		if (strstr($_SERVER["SCRIPT_FILENAME"], "/wp-content/")) {
			return preg_replace("/\/DOCUMENT_ROOT\/.*/", "", $_SERVER["DOCUMENT_ROOT"]);
		}
		return preg_replace("/\/[^\/]+?\/themes\/.*/", "", $_SERVER["DOCUMENT_ROOT"]);
	}

	static public function nospam( $email, $filterLevel = 'normal' ) {
		$email = strrev( $email );
		$email = preg_replace( '[@]', '//', $email );
		$email = preg_replace( '[\.]', '/', $email );

		if( $filterLevel == 'low' ) 	{
			$email = strrev( $email );
		}	
		return $email;
	}

	static public function gwf_list() {
		return array("ABeeZee","Abel","Abril Fatface","Aclonica","Acme","Actor","Adamina","Advent Pro","Aguafina Script","Akronim","Aladin","Aldrich","Alef","Alegreya","Alegreya SC","Alex Brush","Alfa Slab One","Alice","Alike","Alike Angular","Allan","Allerta","Allerta Stencil","Allura","Almendra","Almendra Display","Almendra SC","Amarante","Amaranth","Amatic SC","Amethysta","Anaheim","Andada","Andika","Angkor","Annie Use Your Telescope","Anonymous Pro","Antic","Antic Didone","Antic Slab","Anton","Arapey","Arbutus","Arbutus Slab","Architects Daughter","Archivo Black","Archivo Narrow","Arimo","Arizonia","Armata","Artifika","Arvo","Asap","Asset","Astloch","Asul","Atomic Age","Aubrey","Audiowide","Autour One","Average","Average Sans","Averia Gruesa Libre","Averia Libre","Averia Sans Libre","Averia Serif Libre","Bad Script","Balthazar","Bangers","Basic","Battambang","Baumans","Bayon","Belgrano","Belleza","BenchNine","Bentham","Berkshire Swash","Bevan","Bigelow Rules","Bigshot One","Bilbo","Bilbo Swash Caps","Bitter","Black Ops One","Bokor","Bonbon","Boogaloo","Bowlby One","Bowlby One SC","Brawler","Bree Serif","Bubblegum Sans","Bubbler One","Buda","Buenard","Butcherman","Butterfly Kids","Cabin","Cabin Condensed","Cabin Sketch","Caesar Dressing","Cagliostro","Calligraffitti","Cambo","Candal","Cantarell","Cantata One","Cantora One","Capriola","Cardo","Carme","Carrois Gothic","Carrois Gothic SC","Carter One","Caudex","Cedarville Cursive","Ceviche One","Changa One","Chango","Chau Philomene One","Chela One","Chelsea Market","Chenla","Cherry Cream Soda","Cherry Swash","Chewy","Chicle","Chivo","Cinzel","Cinzel Decorative","Clicker Script","Coda","Coda Caption","Codystar","Combo","Comfortaa","Coming Soon","Concert One","Condiment","Content","Contrail One","Convergence","Cookie","Copse","Corben","Courgette","Cousine","Coustard","Covered By Your Grace","Crafty Girls","Creepster","Crete Round","Crimson Text","Croissant One","Crushed","Cuprum","Cutive","Cutive Mono","Damion","Dancing Script","Dangrek","Dawning of a New Day","Days One","Delius","Delius Swash Caps","Delius Unicase","Della Respira","Denk One","Devonshire","Didact Gothic","Diplomata","Diplomata SC","Domine","Donegal One","Doppio One","Dorsa","Dosis","Dr Sugiyama","Droid Sans","Droid Sans Mono","Droid Serif","Duru Sans","Dynalight","EB Garamond","Eagle Lake","Eater","Economica","Electrolize","Elsie","Elsie Swash Caps","Emblema One","Emilys Candy","Engagement","Englebert","Enriqueta","Erica One","Esteban","Euphoria Script","Ewert","Exo","Expletus Sans","Fanwood Text","Fascinate","Fascinate Inline","Faster One","Fasthand","Fauna One","Federant","Federo","Felipa","Fenix","Finger Paint","Fjalla One","Fjord One","Flamenco","Flavors","Fondamento","Fontdiner Swanky","Forum","Francois One","Freckle Face","Fredericka the Great","Fredoka One","Freehand","Fresca","Frijole","Fruktur","Fugaz One","GFS Didot","GFS Neohellenic","Gabriela","Gafata","Galdeano","Galindo","Gentium Basic","Gentium Book Basic","Geo","Geostar","Geostar Fill","Germania One","Gilda Display","Give You Glory","Glass Antiqua","Glegoo","Gloria Hallelujah","Goblin One","Gochi Hand","Gorditas","Goudy Bookletter 1911","Graduate","Grand Hotel","Gravitas One","Great Vibes","Griffy","Gruppo","Gudea","Habibi","Hammersmith One","Hanalei","Hanalei Fill","Handlee","Hanuman","Happy Monkey","Headland One","Henny Penny","Herr Von Muellerhoff","Holtwood One SC","Homemade Apple","Homenaje","IM Fell DW Pica","IM Fell DW Pica SC","IM Fell Double Pica","IM Fell Double Pica SC","IM Fell English","IM Fell English SC","IM Fell French Canon","IM Fell French Canon SC","IM Fell Great Primer","IM Fell Great Primer SC","Iceberg","Iceland","Imprima","Inconsolata","Inder","Indie atomer","Inika","Irish Grover","Istok Web","Italiana","Italianno","Jacques Francois","Jacques Francois Shadow","Jim Nightshade","Jockey One","Jolly Lodger","Josefin Sans","Josefin Slab","Joti One","Judson","Julee","Julius Sans One","Junge","Jura","Just Another Hand","Just Me Again Down Here","Kameron","Karla","Kaushan Script","Kavoon","Keania One","Kelly Slab","Kenia","Khmer","Kite One","Knewave","Kotta One","Koulen","Kranky","Kreon","Kristi","Krona One","La Belle Aurore","Lancelot","Lato","League Script","Leckerli One","Ledger","Lekton","Lemon","Libre Baskerville","Life Savers","Lilita One","Lily Script One","Limelight","Linden Hill","Lobster","Lobster Two","Londrina Outline","Londrina Shadow","Londrina Sketch","Londrina Solid","Lora","Love Ya Like A Sister","Loved by the King","Lovers Quarrel","Luckiest Guy","Lusitana","Lustria","Macondo","Macondo Swash Caps","Magra","Maiden Orange","Mako","Marcellus","Marcellus SC","Marck Script","Margarine","Marko One","Marmelad","Marvel","Mate","Mate SC","Maven Pro","McLaren","Meddon","MedievalSharp","Medula One","Megrim","Meie Script","Merienda","Merienda One","Merriweather","Merriweather Sans","Metal","Metal Mania","Metamorphous","Metrophobic","Michroma","Milonga","Miltonian","Miltonian Tattoo","Miniver","Miss Fajardose","Modern Antiqua","Molengo","Molle","Monda","Monofett","Monoton","Monsieur La Doulaise","Montaga","Montez","Montserrat","Montserrat Alternates","Montserrat Subrayada","Moul","Moulpali","Mountains of Christmas","Mouse Memoirs","Mr Bedfort","Mr Dafoe","Mr De Haviland","Mrs Saint Delafield","Mrs Sheppards","Muli","Mystery Quest","Neucha","Neuton","New Rocker","News Cycle","Niconne","Nixie One","Nobile","Nokora","Norican","Nosifer","Nothing You Could Do","Noticia Text","Noto Sans","Noto Serif","Nova Cut","Nova Flat","Nova Mono","Nova Oval","Nova Round","Nova Script","Nova Slim","Nova Square","Numans","Nunito","Odor Mean Chey","Offside","Old Standard TT","Oldenburg","Oleo Script","Oleo Script Swash Caps","Open Sans","Open Sans Condensed","Oranienbaum","Orbitron","Oregano","Orienta","Original Surfer","Oswald","Over the Rainbow","Overlock","Overlock SC","Ovo","Oxygen","Oxygen Mono","Open Sans","PT Mono","PT Sans","PT Sans Caption","PT Sans Narrow","PT Serif","PT Serif Caption","Pacifico","Paprika","Parisienne","Passero One","Passion One","Pathway Gothic One","Patrick Hand","Patrick Hand SC","Patua One","Paytone One","Peralta","Permanent Marker","Petit Formal Script","Petrona","Philosopher","Piedra","Pinyon Script","Pirata One","Plaster","Play","Playball","Playfair Display","Playfair Display SC","Podkova","Poiret One","Poller One","Poly","Pompiere","Pontano Sans","Port Lligat Sans","Port Lligat Slab","Prata","Preahvihear","Press Start 2P","Princess Sofia","Prociono","Prosto One","Puritan","Purple Purse","Quando","Quantico","Quattrocento","Quattrocento Sans","Questrial","Quicksand","Quintessential","Qwigley","Racing Sans One","Radley","Raleway","Raleway Dots","Rambla","Rammetto One","Ranchers","Rancho","Rationale","Redressed","Reenie Beanie","Revalia","Ribeye","Ribeye Marrow","Righteous","Risque","Roboto","Roboto Condensed","Roboto Slab","Rochester","Rock Salt","Rokkitt","Romanesco","Ropa Sans","Rosario","Rosarivo","Rouge Script","Ruda","Rufina","Ruge Boogie","Ruluko","Rum Raisin","Ruslan Display","Russo One","Ruthie","Rye","Sacramento","Sail","Salsa","Sanchez","Sancreek","Sansita One","Sarina","Satisfy","Scada","Schoolbell","Seaweed Script","Sevillana","Seymour One","Shadows Into Light","Shadows Into Light Two","Shanti","Share","Share Tech","Share Tech Mono","Shojumaru","Short Stack","Siemreap","Sigmar One","Signika","Signika Negative","Simonetta","Sintony","Sirin Stencil","Six Caps","Skranji","Slackey","Smokum","Smythe","Sniglet","Snippet","Snowburst One","Sofadi One","Sofia","Sonsie One","Sorts Mill Goudy","Source Code Pro","Source Sans Pro","Special Elite","Spicy Rice","Spinnaker","Spirax","Squada One","Stalemate","Stalinist One","Stardos Stencil","Stint Ultra Condensed","Stint Ultra Expanded","Stoke","Strait","Sue Ellen Francisco","Sunshiney","Supermercado One","Suwannaphum","Swanky and Moo Moo","Syncopate","Tangerine","Taprom","Tauri","Telex","Tenor Sans","Text Me One","The Girl Next Door","Tienne","Tinos","Titan One","Titillium Web","Trade Winds","Trocchi","Trochut","Trykker","Tulpen One","Ubuntu","Ubuntu Condensed","Ubuntu Mono","Ultra","Uncial Antiqua","Underdog","Unica One","UnifrakturCook","UnifrakturMaguntia","Unkempt","Unlock","Unna","VT323","Vampiro One","Varela","Varela Round","Vast Shadow","Vibur","Vidaloka","Viga","Voces","Volkhov","Vollkorn","Voltaire","Waiting for the Sunrise","Wallpoet","Walter Turncoat","Warnes","Wellfleet","Wendy One","Wire One","Yanone Kaffeesatz","Yellowtail","Yeseva One","Yesteryear","Zeyada");
	}

	static public function build_font_set() {
		$fonts = array();
			$fonts['"Exo Soft", Helvetica, Arial, sans-serif'] = 'Exo Soft';
			$fonts['Helvetica, Arial, sans-serif'] = 'Helvetica';
			$fonts['Arial, Helvetica, sans-serif'] = 'Arial';
			$fonts['Verdana, Helvetica, sans-serif'] = 'Verdana';
			$fonts['Tahoma, Arial, sans-serif'] = 'Verdana';
		foreach(self::gwf_list() as $font)  {
			$set = "'{$font}', Helvetica, Arial, sans-serif";
			$fonts[$set] = $font;
		}
		return $fonts;
	}

	static public function animations() {
		return array(
			'' => "none",
			"fade-in" => "Fade In Variant 1",
			"fade-in2" => "Fade In Variant 2",
			"move-up" => "Move Up",
			"fall" => "Fall",
			"fly" => "Fly",
			"flip" => "Flip",
			"helix" => "Helix",
			"popup" => "Pop Up",
			"scale-up" => "Scale Up Variant1",
			"scale" => "Scale Up Variant2",
			"top-to-bottom" => "Top to Bottom",
			"left-to-right" => "Left to Right",
			"right-to-left" => "Right to Left",
			"bottom-to-top" => "Bottom to Top",
		);
	}

	static public function field_types_assoc() {
		return array(
			'email' => array('tag' => 'input', 'type' => 'email', 'collapse' => 'self', 'content' => 'field-text', 'style' => 'field', 'placeholder' => 'Email'),
			'subject' => array('tag' => 'input', 'type' => 'text', 'collapse' => 'self', 'content' => 'field-text', 'style' => 'field', 'placeholder' => 'Mail subject'),
			'message' => array('tag' => 'textarea', 'type' => 'tarea', 'collapse' => 'collapsed', 'content' => 'tarea', 'style' => 'tarea', 'placeholder' => 'Message', 'cols' => 8, 'rows' => 10),
			'name' => array('tag' => 'input', 'type' => 'text', 'collapse' => 'self', 'content' => 'field-text', 'style' => 'field', 'placeholder' => 'Name'),
			'phone' => array('tag' => 'input', 'type' => 'tel', 'collapse' => 'self', 'content' => 'text', 'style' => 'field', 'placeholder' => 'Phone'),
		);
	}

	static public function get_transport_icons () {
		return array(
			array( 'name' => 'Car 1', 'class' => 'filter-icon-car' ),
			array( 'name' => 'Car 2', 'class' => 'filter-icon-car-2' ),
			array( 'name' => 'Moto', 'class' => 'filter-icon-moto' ),
			array( 'name' => 'Boat', 'class' => 'filter-icon-boat' ),
			array( 'name' => 'Tractor', 'class' => 'filter-icon-tractor' ),
			array( 'name' => 'Trailer 1', 'class' => 'filter-icon-trailer' ),
			array( 'name' => 'Trailer 2', 'class' => 'filter-icon-trailer-2' ),
			array( 'name' => 'Truck', 'class' => 'filter-icon-truck' ),
			array( 'name' => 'Bus', 'class' => 'filter-icon-bus' ),
			array( 'name' => 'Helicopter', 'class' => 'filter-icon-helicopter' ),
			array( 'name' => 'Parts 1', 'class' => 'filter-icon-parts' ),
			array( 'name' => 'Parts 2', 'class' => 'filter-icon-parts-2' ),
			array( 'name' => 'Parts 3', 'class' => 'filter-icon-parts-3' ),
			array( 'name' => 'Parts 4', 'class' => 'filter-icon-parts-4' ),
			array( 'name' => 'Service 1', 'class' => 'filter-icon-service' ),
			array( 'name' => 'Service ', 'class' => 'filter-icon-service-2' ),
		);
	}

	/**
	 * IcoMoon vector icons
	 */
	static public function get_im_icons() {
		$icons = array(
			'im-icon-home' => 'home',
			'im-icon-home-2' => 'home 2',
			'im-icon-home-3' => 'home 3',
			'im-icon-home-4' => 'home 4',
			'im-icon-home-5' => 'home 5',
			'im-icon-home-6' => 'home 6',
			'im-icon-home-7' => 'home 7',
			'im-icon-home-8' => 'home 8',
			'im-icon-home-9' => 'home 9',
			'im-icon-home-10' => 'home 10',
			'im-icon-home-11' => 'home 11',
			'im-icon-office' => 'office',
			'im-icon-newspaper' => 'newspaper',
			'im-icon-pencil' => 'pencil',
			'im-icon-pencil-2' => 'pencil 2',
			'im-icon-pencil-3' => 'pencil 3',
			'im-icon-pencil-4' => 'pencil 4',
			'im-icon-pencil-5' => 'pencil 5',
			'im-icon-pencil-6' => 'pencil 6',
			'im-icon-quill' => 'quill',
			'im-icon-quill-2' => 'quill 2',
			'im-icon-quill-3' => 'quill 3',
			'im-icon-pen' => 'pen',
			'im-icon-pen-2' => 'pen 2',
			'im-icon-pen-3' => 'pen 3',
			'im-icon-pen-4' => 'pen 4',
			'im-icon-pen-5' => 'pen 5',
			'im-icon-marker' => 'marker',
			'im-icon-home-12' => 'home 12',
			'im-icon-marker-2' => 'marker 2',
			'im-icon-blog' => 'blog',
			'im-icon-blog-2' => 'blog 2',
			'im-icon-brush' => 'brush',
			'im-icon-palette' => 'palette',
			'im-icon-palette-2' => 'palette 2',
			'im-icon-eyedropper' => 'eyedropper',
			'im-icon-eyedropper-2' => 'eyedropper 2',
			'im-icon-droplet' => 'droplet',
			'im-icon-droplet-2' => 'droplet 2',
			'im-icon-droplet-3' => 'droplet 3',
			'im-icon-droplet-4' => 'droplet 4',
			'im-icon-paint-format' => 'paint format',
			'im-icon-paint-format-2' => 'paint format 2',
			'im-icon-image' => 'image',
			'im-icon-image-2' => 'image 2',
			'im-icon-image-3' => 'image 3',
			'im-icon-images' => 'images',
			'im-icon-image-4' => 'image 4',
			'im-icon-image-5' => 'image 5',
			'im-icon-image-6' => 'image 6',
			'im-icon-images-2' => 'images 2',
			'im-icon-image-7' => 'image 7',
			'im-icon-camera' => 'camera',
			'im-icon-camera-2' => 'camera 2',
			'im-icon-camera-3' => 'camera 3',
			'im-icon-camera-4' => 'camera 4',
			'im-icon-music' => 'music',
			'im-icon-music-2' => 'music 2',
			'im-icon-music-3' => 'music 3',
			'im-icon-music-4' => 'music 4',
			'im-icon-music-5' => 'music 5',
			'im-icon-music-6' => 'music 6',
			'im-icon-piano' => 'piano',
			'im-icon-guitar' => 'guitar',
			'im-icon-headphones' => 'headphones',
			'im-icon-headphones-2' => 'headphones 2',
			'im-icon-play' => 'play',
			'im-icon-play-2' => 'play 2',
			'im-icon-movie' => 'movie',
			'im-icon-movie-2' => 'movie 2',
			'im-icon-movie-3' => 'movie 3',
			'im-icon-film' => 'film',
			'im-icon-film-2' => 'film 2',
			'im-icon-film-3' => 'film 3',
			'im-icon-film-4' => 'film 4',
			'im-icon-camera-5' => 'camera 5',
			'im-icon-camera-6' => 'camera 6',
			'im-icon-camera-7' => 'camera 7',
			'im-icon-camera-8' => 'camera 8',
			'im-icon-camera-9' => 'camera 9',
			'im-icon-dice' => 'dice',
			'im-icon-gamepad' => 'gamepad',
			'im-icon-gamepad-2' => 'gamepad 2',
			'im-icon-gamepad-3' => 'gamepad 3',
			'im-icon-pacman' => 'pacman',
			'im-icon-spades' => 'spades',
			'im-icon-clubs' => 'clubs',
			'im-icon-diamonds' => 'diamonds',
			'im-icon-king' => 'king',
			'im-icon-queen' => 'queen',
			'im-icon-rock' => 'rock',
			'im-icon-bishop' => 'bishop',
			'im-icon-knight' => 'knight',
			'im-icon-pawn' => 'pawn',
			'im-icon-chess' => 'chess',
			'im-icon-bullhorn' => 'bullhorn',
			'im-icon-megaphone' => 'megaphone',
			'im-icon-new' => 'new',
			'im-icon-connection' => 'connection',
			'im-icon-connection-2' => 'connection 2',
			'im-icon-podcast' => 'podcast',
			'im-icon-radio' => 'radio',
			'im-icon-feed' => 'feed',
			'im-icon-connection-3' => 'connection 3',
			'im-icon-radio-2' => 'radio 2',
			'im-icon-podcast-2' => 'podcast 2',
			'im-icon-podcast-3' => 'podcast 3',
			'im-icon-mic' => 'mic',
			'im-icon-mic-2' => 'mic 2',
			'im-icon-mic-3' => 'mic 3',
			'im-icon-mic-4' => 'mic 4',
			'im-icon-mic-5' => 'mic 5',
			'im-icon-book' => 'book',
			'im-icon-book-2' => 'book 2',
			'im-icon-books' => 'books',
			'im-icon-reading' => 'reading',
			'im-icon-library' => 'library',
			'im-icon-library-2' => 'library 2',
			'im-icon-graduation' => 'graduation',
			'im-icon-file' => 'file',
			'im-icon-profile' => 'profile',
			'im-icon-file-2' => 'file 2',
			'im-icon-file-3' => 'file 3',
			'im-icon-file-4' => 'file 4',
			'im-icon-file-5' => 'file 5',
			'im-icon-file-6' => 'file 6',
			'im-icon-files' => 'files',
			'im-icon-file-plus' => 'file plus',
			'im-icon-file-minus' => 'file minus',
			'im-icon-file-download' => 'file download',
			'im-icon-file-upload' => 'file upload',
			'im-icon-file-check' => 'file check',
			'im-icon-file-remove' => 'file remove',
			'im-icon-file-7' => 'file 7',
			'im-icon-file-8' => 'file 8',
			'im-icon-file-plus-2' => 'file plus 2',
			'im-icon-file-minus-2' => 'file minus 2',
			'im-icon-file-download-2' => 'file download 2',
			'im-icon-file-upload-2' => 'file upload 2',
			'im-icon-file-check-2' => 'file check 2',
			'im-icon-file-remove-2' => 'file remove 2',
			'im-icon-file-9' => 'file 9',
			'im-icon-copy' => 'copy',
			'im-icon-copy-2' => 'copy 2',
			'im-icon-copy-3' => 'copy 3',
			'im-icon-copy-4' => 'copy 4',
			'im-icon-paste' => 'paste',
			'im-icon-paste-2' => 'paste 2',
			'im-icon-paste-3' => 'paste 3',
			'im-icon-stack' => 'stack',
			'im-icon-stack-2' => 'stack 2',
			'im-icon-stack-3' => 'stack 3',
			'im-icon-folder' => 'folder',
			'im-icon-folder-download' => 'folder download',
			'im-icon-folder-upload' => 'folder upload',
			'im-icon-folder-plus' => 'folder plus',
			'im-icon-folder-plus-2' => 'folder plus 2',
			'im-icon-folder-minus' => 'folder minus',
			'im-icon-folder-minus-2' => 'folder minus 2',
			'im-icon-folder8' => 'folder8',
			'im-icon-folder-remove' => 'folder remove',
			'im-icon-folder-2' => 'folder 2',
			'im-icon-folder-open' => 'folder open',
			'im-icon-folder-3' => 'folder 3',
			'im-icon-folder-4' => 'folder 4',
			'im-icon-folder-plus-3' => 'folder plus 3',
			'im-icon-folder-minus-3' => 'folder minus 3',
			'im-icon-folder-plus-4' => 'folder plus 4',
			'im-icon-folder-remove-2' => 'folder remove 2',
			'im-icon-folder-download-2' => 'folder download 2',
			'im-icon-folder-upload-2' => 'folder upload 2',
			'im-icon-folder-download-3' => 'folder download 3',
			'im-icon-folder-upload-3' => 'folder upload 3',
			'im-icon-folder-5' => 'folder 5',
			'im-icon-folder-open-2' => 'folder open 2',
			'im-icon-folder-6' => 'folder 6',
			'im-icon-folder-open-3' => 'folder open 3',
			'im-icon-certificate' => 'certificate',
			'im-icon-cc' => 'cc',
			'im-icon-tag' => 'tag',
			'im-icon-tag-2' => 'tag 2',
			'im-icon-tag-3' => 'tag 3',
			'im-icon-tag-4' => 'tag 4',
			'im-icon-tag-5' => 'tag 5',
			'im-icon-tag-6' => 'tag 6',
			'im-icon-tag-7' => 'tag 7',
			'im-icon-tags' => 'tags',
			'im-icon-tags-2' => 'tags 2',
			'im-icon-tag-8' => 'tag 8',
			'im-icon-barcode' => 'barcode',
			'im-icon-barcode-2' => 'barcode 2',
			'im-icon-qrcode' => 'qrcode',
			'im-icon-ticket' => 'ticket',
			'im-icon-cart' => 'cart',
			'im-icon-cart-2' => 'cart 2',
			'im-icon-cart-3' => 'cart 3',
			'im-icon-cart-4' => 'cart 4',
			'im-icon-cart-5' => 'cart 5',
			'im-icon-cart-6' => 'cart 6',
			'im-icon-cart-7' => 'cart 7',
			'im-icon-cart-plus' => 'cart plus',
			'im-icon-cart-minus' => 'cart minus',
			'im-icon-cart-add' => 'cart add',
			'im-icon-cart-remove' => 'cart remove',
			'im-icon-cart-checkout' => 'cart checkout',
			'im-icon-cart-remove-2' => 'cart remove 2',
			'im-icon-basket' => 'basket',
			'im-icon-basket-2' => 'basket 2',
			'im-icon-bag' => 'bag',
			'im-icon-bag-2' => 'bag 2',
			'im-icon-bag-3' => 'bag 3',
			'im-icon-coin' => 'coin',
			'im-icon-coins' => 'coins',
			'im-icon-credit' => 'credit',
			'im-icon-credit-2' => 'credit 2',
			'im-icon-calculate' => 'calculate',
			'im-icon-calculate-2' => 'calculate 2',
			'im-icon-support' => 'support',
			'im-icon-phone' => 'phone',
			'im-icon-phone-2' => 'phone 2',
			'im-icon-phone-3' => 'phone 3',
			'im-icon-phone-4' => 'phone 4',
			'im-icon-contact-add' => 'contact add',
			'im-icon-contact-remove' => 'contact remove',
			'im-icon-contact-add-2' => 'contact add 2',
			'im-icon-contact-remove-2' => 'contact remove 2',
			'im-icon-call-incoming' => 'call incoming',
			'im-icon-call-outgoing' => 'call outgoing',
			'im-icon-phone-5' => 'phone 5',
			'im-icon-phone-6' => 'phone 6',
			'im-icon-phone-hang-up' => 'phone hang up',
			'im-icon-phone-hang-up-2' => 'phone hang up 2',
			'im-icon-address-book' => 'address book',
			'im-icon-address-book-2' => 'address book 2',
			'im-icon-notebook' => 'notebook',
			'im-icon-envelop' => 'envelop',
			'im-icon-envelop-2' => 'envelop 2',
			'im-icon-mail-send' => 'mail send',
			'im-icon-envelop-opened' => 'envelop opened',
			'im-icon-envelop-3' => 'envelop 3',
			'im-icon-pushpin' => 'pushpin',
			'im-icon-location' => 'location',
			'im-icon-location-2' => 'location 2',
			'im-icon-location-3' => 'location 3',
			'im-icon-location-4' => 'location 4',
			'im-icon-location-5' => 'location 5',
			'im-icon-location-6' => 'location 6',
			'im-icon-location-7' => 'location 7',
			'im-icon-compass' => 'compass',
			'im-icon-compass-2' => 'compass 2',
			'im-icon-map' => 'map',
			'im-icon-map-2' => 'map 2',
			'im-icon-map-3' => 'map 3',
			'im-icon-map-4' => 'map 4',
			'im-icon-direction' => 'direction',
			'im-icon-history' => 'history',
			'im-icon-history-2' => 'history 2',
			'im-icon-clock' => 'clock',
			'im-icon-clock-2' => 'clock 2',
			'im-icon-clock-3' => 'clock 3',
			'im-icon-clock-4' => 'clock 4',
			'im-icon-watch' => 'watch',
			'im-icon-clock-5' => 'clock 5',
			'im-icon-clock-6' => 'clock 6',
			'im-icon-clock-7' => 'clock 7',
			'im-icon-alarm' => 'alarm',
			'im-icon-alarm-2' => 'alarm 2',
			'im-icon-bell' => 'bell',
			'im-icon-bell-2' => 'bell 2',
			'im-icon-alarm-plus' => 'alarm plus',
			'im-icon-alarm-minus' => 'alarm minus',
			'im-icon-alarm-check' => 'alarm check',
			'im-icon-alarm-cancel' => 'alarm cancel',
			'im-icon-stopwatch' => 'stopwatch',
			'im-icon-calendar' => 'calendar',
			'im-icon-calendar-2' => 'calendar 2',
			'im-icon-calendar-3' => 'calendar 3',
			'im-icon-calendar-4' => 'calendar 4',
			'im-icon-calendar-5' => 'calendar 5',
			'im-icon-print' => 'print',
			'im-icon-print-2' => 'print 2',
			'im-icon-print-3' => 'print 3',
			'im-icon-mouse' => 'mouse',
			'im-icon-mouse-2' => 'mouse 2',
			'im-icon-mouse-3' => 'mouse 3',
			'im-icon-mouse-4' => 'mouse 4',
			'im-icon-keyboard' => 'keyboard',
			'im-icon-keyboard-2' => 'keyboard 2',
			'im-icon-screen' => 'screen',
			'im-icon-screen-2' => 'screen 2',
			'im-icon-screen-3' => 'screen 3',
			'im-icon-screen-4' => 'screen 4',
			'im-icon-laptop' => 'laptop',
			'im-icon-mobile' => 'mobile',
			'im-icon-mobile-2' => 'mobile 2',
			'im-icon-tablet' => 'tablet',
			'im-icon-mobile-3' => 'mobile 3',
			'im-icon-tv' => 'tv',
			'im-icon-cabinet' => 'cabinet',
			'im-icon-archive' => 'archive',
			'im-icon-drawer' => 'drawer',
			'im-icon-drawer-2' => 'drawer 2',
			'im-icon-drawer-3' => 'drawer 3',
			'im-icon-box' => 'box',
			'im-icon-box-add' => 'box add',
			'im-icon-box-remove' => 'box remove',
			'im-icon-download' => 'download',
			'im-icon-upload' => 'upload',
			'im-icon-disk' => 'disk',
			'im-icon-cd' => 'cd',
			'im-icon-storage' => 'storage',
			'im-icon-storage-2' => 'storage 2',
			'im-icon-database' => 'database',
			'im-icon-database-2' => 'database 2',
			'im-icon-database-3' => 'database 3',
			'im-icon-undo' => 'undo',
			'im-icon-redo' => 'redo',
			'im-icon-rotate' => 'rotate',
			'im-icon-rotate-2' => 'rotate 2',
			'im-icon-flip' => 'flip',
			'im-icon-flip-2' => 'flip 2',
			'im-icon-unite' => 'unite',
			'im-icon-subtract' => 'subtract',
			'im-icon-interset' => 'interset',
			'im-icon-exclude' => 'exclude',
			'im-icon-align-left' => 'align left',
			'im-icon-align-center-horizontal' => 'align center horizontal',
			'im-icon-align-right' => 'align right',
			'im-icon-align-top' => 'align top',
			'im-icon-align-center-vertical' => 'align center vertical',
			'im-icon-align-bottom' => 'align bottom',
			'im-icon-undo-2' => 'undo 2',
			'im-icon-redo-2' => 'redo 2',
			'im-icon-forward' => 'forward',
			'im-icon-reply' => 'reply',
			'im-icon-reply-2' => 'reply 2',
			'im-icon-bubble' => 'bubble',
			'im-icon-bubbles' => 'bubbles',
			'im-icon-bubbles-2' => 'bubbles 2',
			'im-icon-bubble-2' => 'bubble 2',
			'im-icon-bubbles-3' => 'bubbles 3',
			'im-icon-bubbles-4' => 'bubbles 4',
			'im-icon-bubble-notification' => 'bubble notification',
			'im-icon-bubbles-5' => 'bubbles 5',
			'im-icon-bubbles-6' => 'bubbles 6',
			'im-icon-bubble-3' => 'bubble 3',
			'im-icon-bubble-dots' => 'bubble dots',
			'im-icon-bubble-4' => 'bubble 4',
			'im-icon-bubble-5' => 'bubble 5',
			'im-icon-bubble-dots-2' => 'bubble dots 2',
			'im-icon-bubble-6' => 'bubble 6',
			'im-icon-bubble-7' => 'bubble 7',
			'im-icon-bubble-8' => 'bubble 8',
			'im-icon-bubbles-7' => 'bubbles 7',
			'im-icon-bubble-9' => 'bubble 9',
			'im-icon-bubbles-8' => 'bubbles 8',
			'im-icon-bubble-10' => 'bubble 10',
			'im-icon-bubble-dots-3' => 'bubble dots 3',
			'im-icon-bubble-11' => 'bubble 11',
			'im-icon-bubble-12' => 'bubble 12',
			'im-icon-bubble-dots-4' => 'bubble dots 4',
			'im-icon-bubble-13' => 'bubble 13',
			'im-icon-bubbles-9' => 'bubbles 9',
			'im-icon-bubbles-10' => 'bubbles 10',
			'im-icon-bubble-blocked' => 'bubble blocked',
			'im-icon-bubble-quote' => 'bubble quote',
			'im-icon-bubble-user' => 'bubble user',
			'im-icon-bubble-check' => 'bubble check',
			'im-icon-bubble-video-chat' => 'bubble video chat',
			'im-icon-bubble-link' => 'bubble link',
			'im-icon-bubble-locked' => 'bubble locked',
			'im-icon-bubble-star' => 'bubble star',
			'im-icon-bubble-heart' => 'bubble heart',
			'im-icon-bubble-paperclip' => 'bubble paperclip',
			'im-icon-bubble-cancel' => 'bubble cancel',
			'im-icon-bubble-plus' => 'bubble plus',
			'im-icon-bubble-minus' => 'bubble minus',
			'im-icon-bubble-notification-2' => 'bubble notification 2',
			'im-icon-bubble-trash' => 'bubble trash',
			'im-icon-bubble-left' => 'bubble left',
			'im-icon-bubble-right' => 'bubble right',
			'im-icon-bubble-up' => 'bubble up',
			'im-icon-bubble-down' => 'bubble down',
			'im-icon-bubble-first' => 'bubble first',
			'im-icon-bubble-last' => 'bubble last',
			'im-icon-bubble-replu' => 'bubble replu',
			'im-icon-bubble-forward' => 'bubble forward',
			'im-icon-bubble-reply' => 'bubble reply',
			'im-icon-bubble-forward-2' => 'bubble forward 2',
			'im-icon-user' => 'user',
			'im-icon-users' => 'users',
			'im-icon-user-plus' => 'user plus',
			'im-icon-user-plus-2' => 'user plus 2',
			'im-icon-user-minus' => 'user minus',
			'im-icon-user-minus-2' => 'user minus 2',
			'im-icon-user-cancel' => 'user cancel',
			'im-icon-user-block' => 'user block',
			'im-icon-users-2' => 'users 2',
			'im-icon-user-2' => 'user 2',
			'im-icon-users-3' => 'users 3',
			'im-icon-user-plus-3' => 'user plus 3',
			'im-icon-user-minus-3' => 'user minus 3',
			'im-icon-user-cancel-2' => 'user cancel 2',
			'im-icon-user-block-2' => 'user block 2',
			'im-icon-user-3' => 'user 3',
			'im-icon-user-4' => 'user 4',
			'im-icon-user-5' => 'user 5',
			'im-icon-user-6' => 'user 6',
			'im-icon-users-4' => 'users 4',
			'im-icon-user-7' => 'user 7',
			'im-icon-user-8' => 'user 8',
			'im-icon-users-5' => 'users 5',
			'im-icon-vcard' => 'vcard',
			'im-icon-tshirt' => 'tshirt',
			'im-icon-hanger' => 'hanger',
			'im-icon-quotes-left' => 'quotes left',
			'im-icon-quotes-right' => 'quotes right',
			'im-icon-quotes-right-2' => 'quotes right 2',
			'im-icon-quotes-right-3' => 'quotes right 3',
			'im-icon-busy' => 'busy',
			'im-icon-busy-2' => 'busy 2',
			'im-icon-busy-3' => 'busy 3',
			'im-icon-busy-4' => 'busy 4',
			'im-icon-spinner' => 'spinner',
			'im-icon-spinner-2' => 'spinner 2',
			'im-icon-spinner-3' => 'spinner 3',
			'im-icon-spinner-4' => 'spinner 4',
			'im-icon-spinner-5' => 'spinner 5',
			'im-icon-spinner-6' => 'spinner 6',
			'im-icon-spinner-7' => 'spinner 7',
			'im-icon-spinner-8' => 'spinner 8',
			'im-icon-spinner-9' => 'spinner 9',
			'im-icon-spinner-10' => 'spinner 10',
			'im-icon-spinner-11' => 'spinner 11',
			'im-icon-spinner-12' => 'spinner 12',
			'im-icon-microscope' => 'microscope',
			'im-icon-binoculars' => 'binoculars',
			'im-icon-binoculars-2' => 'binoculars 2',
			'im-icon-search' => 'search',
			'im-icon-search-2' => 'search 2',
			'im-icon-zoom-in' => 'zoom in',
			'im-icon-zoom-out' => 'zoom out',
			'im-icon-search-3' => 'search 3',
			'im-icon-search-4' => 'search 4',
			'im-icon-zoom-in-2' => 'zoom in 2',
			'im-icon-zoom-out-2' => 'zoom out 2',
			'im-icon-search-5' => 'search 5',
			'im-icon-expand' => 'expand',
			'im-icon-contract' => 'contract',
			'im-icon-scale-up' => 'scale up',
			'im-icon-scale-down' => 'scale down',
			'im-icon-expand-2' => 'expand 2',
			'im-icon-contract-2' => 'contract 2',
			'im-icon-scale-up-2' => 'scale up 2',
			'im-icon-scale-down-2' => 'scale down 2',
			'im-icon-fullscreen' => 'fullscreen',
			'im-icon-expand-3' => 'expand 3',
			'im-icon-contract-3' => 'contract 3',
			'im-icon-key' => 'key',
			'im-icon-key-2' => 'key 2',
			'im-icon-key-3' => 'key 3',
			'im-icon-key-4' => 'key 4',
			'im-icon-key-5' => 'key 5',
			'im-icon-keyhole' => 'keyhole',
			'im-icon-lock' => 'lock',
			'im-icon-lock-2' => 'lock 2',
			'im-icon-lock-3' => 'lock 3',
			'im-icon-lock-4' => 'lock 4',
			'im-icon-unlocked' => 'unlocked',
			'im-icon-lock-5' => 'lock 5',
			'im-icon-unlocked-2' => 'unlocked 2',
			'im-icon-wrench' => 'wrench',
			'im-icon-wrench-2' => 'wrench 2',
			'im-icon-wrench-3' => 'wrench 3',
			'im-icon-wrench-4' => 'wrench 4',
			'im-icon-settings' => 'settings',
			'im-icon-equalizer' => 'equalizer',
			'im-icon-equalizer-2' => 'equalizer 2',
			'im-icon-equalizer-3' => 'equalizer 3',
			'im-icon-cog' => 'cog',
			'im-icon-cogs' => 'cogs',
			'im-icon-cog-2' => 'cog 2',
			'im-icon-cog-3' => 'cog 3',
			'im-icon-cog-4' => 'cog 4',
			'im-icon-cog-5' => 'cog 5',
			'im-icon-cog-6' => 'cog 6',
			'im-icon-cog-7' => 'cog 7',
			'im-icon-factory' => 'factory',
			'im-icon-hammer' => 'hammer',
			'im-icon-tools' => 'tools',
			'im-icon-screwdriver' => 'screwdriver',
			'im-icon-screwdriver-2' => 'screwdriver 2',
			'im-icon-wand' => 'wand',
			'im-icon-wand-2' => 'wand 2',
			'im-icon-health' => 'health',
			'im-icon-aid' => 'aid',
			'im-icon-patch' => 'patch',
			'im-icon-bug' => 'bug',
			'im-icon-bug-2' => 'bug 2',
			'im-icon-inject' => 'inject',
			'im-icon-inject-2' => 'inject 2',
			'im-icon-construction' => 'construction',
			'im-icon-cone' => 'cone',
			'im-icon-pie' => 'pie',
			'im-icon-pie-2' => 'pie 2',
			'im-icon-pie-3' => 'pie 3',
			'im-icon-pie-4' => 'pie 4',
			'im-icon-pie-5' => 'pie 5',
			'im-icon-pie-6' => 'pie 6',
			'im-icon-pie-7' => 'pie 7',
			'im-icon-stats' => 'stats',
			'im-icon-stats-2' => 'stats 2',
			'im-icon-stats-3' => 'stats 3',
			'im-icon-bars' => 'bars',
			'im-icon-bars-2' => 'bars 2',
			'im-icon-bars-3' => 'bars 3',
			'im-icon-bars-4' => 'bars 4',
			'im-icon-bars-5' => 'bars 5',
			'im-icon-bars-6' => 'bars 6',
			'im-icon-stats-up' => 'stats up',
			'im-icon-stats-down' => 'stats down',
			'im-icon-stairs-down' => 'stairs down',
			'im-icon-stairs-down-2' => 'stairs down 2',
			'im-icon-chart' => 'chart',
			'im-icon-stairs' => 'stairs',
			'im-icon-stairs-2' => 'stairs 2',
			'im-icon-ladder' => 'ladder',
			'im-icon-cake' => 'cake',
			'im-icon-gift' => 'gift',
			'im-icon-gift-2' => 'gift 2',
			'im-icon-balloon' => 'balloon',
			'im-icon-rating' => 'rating',
			'im-icon-rating-2' => 'rating 2',
			'im-icon-rating-3' => 'rating 3',
			'im-icon-podium' => 'podium',
			'im-icon-medal' => 'medal',
			'im-icon-medal-2' => 'medal 2',
			'im-icon-medal-3' => 'medal 3',
			'im-icon-medal-4' => 'medal 4',
			'im-icon-medal-5' => 'medal 5',
			'im-icon-crown' => 'crown',
			'im-icon-trophy' => 'trophy',
			'im-icon-trophy-2' => 'trophy 2',
			'im-icon-trophy-star' => 'trophy star',
			'im-icon-diamond' => 'diamond',
			'im-icon-diamond-2' => 'diamond 2',
			'im-icon-glass' => 'glass',
			'im-icon-glass-2' => 'glass 2',
			'im-icon-bottle' => 'bottle',
			'im-icon-bottle-2' => 'bottle 2',
			'im-icon-mug' => 'mug',
			'im-icon-food' => 'food',
			'im-icon-food-2' => 'food 2',
			'im-icon-hamburger' => 'hamburger',
			'im-icon-cup' => 'cup',
			'im-icon-cup-2' => 'cup 2',
			'im-icon-leaf' => 'leaf',
			'im-icon-leaf-2' => 'leaf 2',
			'im-icon-apple-fruit' => 'apple fruit',
			'im-icon-tree' => 'tree',
			'im-icon-tree-2' => 'tree 2',
			'im-icon-paw' => 'paw',
			'im-icon-steps' => 'steps',
			'im-icon-atomer' => 'atomer',
			'im-icon-rocket' => 'rocket',
			'im-icon-meter' => 'meter',
			'im-icon-meter2' => 'meter2',
			'im-icon-meter-slow' => 'meter slow',
			'im-icon-meter-medium' => 'meter medium',
			'im-icon-meter-fast' => 'meter fast',
			'im-icon-dashboard' => 'dashboard',
			'im-icon-hammer-2' => 'hammer 2',
			'im-icon-balance' => 'balance',
			'im-icon-bomb' => 'bomb',
			'im-icon-fire' => 'fire',
			'im-icon-fire-2' => 'fire 2',
			'im-icon-lab' => 'lab',
			'im-icon-atom' => 'atom',
			'im-icon-atom-2' => 'atom 2',
			'im-icon-magnet' => 'magnet',
			'im-icon-magnet-2' => 'magnet 2',
			'im-icon-magnet-3' => 'magnet 3',
			'im-icon-magnet-4' => 'magnet 4',
			'im-icon-dumbbell' => 'dumbbell',
			'im-icon-skull' => 'skull',
			'im-icon-skull-2' => 'skull 2',
			'im-icon-skull-3' => 'skull 3',
			'im-icon-lamp' => 'lamp',
			'im-icon-lamp-2' => 'lamp 2',
			'im-icon-lamp-3' => 'lamp 3',
			'im-icon-lamp-4' => 'lamp 4',
			'im-icon-remove' => 'remove',
			'im-icon-remove-2' => 'remove 2',
			'im-icon-remove-3' => 'remove 3',
			'im-icon-remove-4' => 'remove 4',
			'im-icon-remove-5' => 'remove 5',
			'im-icon-remove-6' => 'remove 6',
			'im-icon-remove-7' => 'remove 7',
			'im-icon-remove-8' => 'remove 8',
			'im-icon-briefcase' => 'briefcase',
			'im-icon-briefcase-2' => 'briefcase 2',
			'im-icon-briefcase-3' => 'briefcase 3',
			'im-icon-airplane' => 'airplane',
			'im-icon-airplane-2' => 'airplane 2',
			'im-icon-paper-plane' => 'paper plane',
			'im-icon-car' => 'car',
			'im-icon-gas-pump' => 'gas pump',
			'im-icon-bus' => 'bus',
			'im-icon-truck' => 'truck',
			'im-icon-bike' => 'bike',
			'im-icon-road' => 'road',
			'im-icon-train' => 'train',
			'im-icon-ship' => 'ship',
			'im-icon-boat' => 'boat',
			'im-icon-cube' => 'cube',
			'im-icon-cube-2' => 'cube 2',
			'im-icon-cube-3' => 'cube 3',
			'im-icon-cube4' => 'cube4',
			'im-icon-pyramid' => 'pyramid',
			'im-icon-pyramid-2' => 'pyramid 2',
			'im-icon-cylinder' => 'cylinder',
			'im-icon-package' => 'package',
			'im-icon-puzzle' => 'puzzle',
			'im-icon-puzzle-2' => 'puzzle 2',
			'im-icon-puzzle-3' => 'puzzle 3',
			'im-icon-puzzle-4' => 'puzzle 4',
			'im-icon-glasses' => 'glasses',
			'im-icon-glasses-2' => 'glasses 2',
			'im-icon-glasses-3' => 'glasses 3',
			'im-icon-sun-glasses' => 'sun glasses',
			'im-icon-accessibility' => 'accessibility',
			'im-icon-accessibility-2' => 'accessibility 2',
			'im-icon-brain' => 'brain',
			'im-icon-target' => 'target',
			'im-icon-target-2' => 'target 2',
			'im-icon-target-3' => 'target 3',
			'im-icon-gun' => 'gun',
			'im-icon-gun-ban' => 'gun ban',
			'im-icon-shield' => 'shield',
			'im-icon-shield-2' => 'shield 2',
			'im-icon-shield-3' => 'shield 3',
			'im-icon-shield-4' => 'shield 4',
			'im-icon-soccer' => 'soccer',
			'im-icon-football' => 'football',
			'im-icon-baseball' => 'baseball',
			'im-icon-basketball' => 'basketball',
			'im-icon-golf' => 'golf',
			'im-icon-hockey' => 'hockey',
			'im-icon-racing' => 'racing',
			'im-icon-eight-ball' => 'eight ball',
			'im-icon-bowling-ball' => 'bowling ball',
			'im-icon-bowling' => 'bowling',
			'im-icon-bowling-2' => 'bowling 2',
			'im-icon-lightning' => 'lightning',
			'im-icon-power' => 'power',
			'im-icon-power-2' => 'power 2',
			'im-icon-switch' => 'switch',
			'im-icon-power-cord' => 'power cord',
			'im-icon-cord' => 'cord',
			'im-icon-socket' => 'socket',
			'im-icon-clipboard' => 'clipboard',
			'im-icon-clipboard-2' => 'clipboard 2',
			'im-icon-signup' => 'signup',
			'im-icon-clipboard-3' => 'clipboard 3',
			'im-icon-clipboard-4' => 'clipboard 4',
			'im-icon-list' => 'list',
			'im-icon-list-2' => 'list 2',
			'im-icon-list-3' => 'list 3',
			'im-icon-numbered-list' => 'numbered list',
			'im-icon-list-4' => 'list 4',
			'im-icon-list-5' => 'list 5',
			'im-icon-playlist' => 'playlist',
			'im-icon-grid' => 'grid',
			'im-icon-grid-2' => 'grid 2',
			'im-icon-grid-3' => 'grid 3',
			'im-icon-grid-4' => 'grid 4',
			'im-icon-grid-5' => 'grid 5',
			'im-icon-grid-6' => 'grid 6',
			'im-icon-tree-3' => 'tree 3',
			'im-icon-tree-4' => 'tree 4',
			'im-icon-tree-5' => 'tree 5',
			'im-icon-menu' => 'menu',
			'im-icon-menu-2' => 'menu 2',
			'im-icon-circle-small' => 'circle small',
			'im-icon-menu-3' => 'menu 3',
			'im-icon-menu-4' => 'menu 4',
			'im-icon-menu-5' => 'menu 5',
			'im-icon-menu-6' => 'menu 6',
			'im-icon-menu-7' => 'menu 7',
			'im-icon-menu-8' => 'menu 8',
			'im-icon-menu-9' => 'menu 9',
			'im-icon-cloud' => 'cloud',
			'im-icon-cloud-2' => 'cloud 2',
			'im-icon-cloud-3' => 'cloud 3',
			'im-icon-cloud-download' => 'cloud download',
			'im-icon-cloud-upload' => 'cloud upload',
			'im-icon-download-2' => 'download 2',
			'im-icon-upload-2' => 'upload 2',
			'im-icon-download-3' => 'download 3',
			'im-icon-upload-3' => 'upload 3',
			'im-icon-download-4' => 'download 4',
			'im-icon-upload-4' => 'upload 4',
			'im-icon-download-5' => 'download 5',
			'im-icon-upload-5' => 'upload 5',
			'im-icon-download-6' => 'download 6',
			'im-icon-upload-6' => 'upload 6',
			'im-icon-download-7' => 'download 7',
			'im-icon-upload-7' => 'upload 7',
			'im-icon-globe' => 'globe',
			'im-icon-globe-2' => 'globe 2',
			'im-icon-globe-3' => 'globe 3',
			'im-icon-earth' => 'earth',
			'im-icon-network' => 'network',
			'im-icon-link' => 'link',
			'im-icon-link-2' => 'link 2',
			'im-icon-link-3' => 'link 3',
			'im-icon-link2' => 'link2',
			'im-icon-link-4' => 'link 4',
			'im-icon-link-5' => 'link 5',
			'im-icon-link-6' => 'link 6',
			'im-icon-anchor' => 'anchor',
			'im-icon-flag' => 'flag',
			'im-icon-flag-2' => 'flag 2',
			'im-icon-flag-3' => 'flag 3',
			'im-icon-flag-4' => 'flag 4',
			'im-icon-flag-5' => 'flag 5',
			'im-icon-flag-6' => 'flag 6',
			'im-icon-attachment' => 'attachment',
			'im-icon-attachment-2' => 'attachment 2',
			'im-icon-eye' => 'eye',
			'im-icon-eye-blocked' => 'eye blocked',
			'im-icon-eye-2' => 'eye 2',
			'im-icon-eye-3' => 'eye 3',
			'im-icon-eye-blocked-2' => 'eye blocked 2',
			'im-icon-eye-4' => 'eye 4',
			'im-icon-eye-5' => 'eye 5',
			'im-icon-eye-6' => 'eye 6',
			'im-icon-eye-7' => 'eye 7',
			'im-icon-eye-8' => 'eye 8',
			'im-icon-bookmark' => 'bookmark',
			'im-icon-bookmark-2' => 'bookmark 2',
			'im-icon-bookmarks' => 'bookmarks',
			'im-icon-bookmark-3' => 'bookmark 3',
			'im-icon-spotlight' => 'spotlight',
			'im-icon-starburst' => 'starburst',
			'im-icon-snowflake' => 'snowflake',
			'im-icon-temperature' => 'temperature',
			'im-icon-temperature-2' => 'temperature 2',
			'im-icon-weather-lightning' => 'weather lightning',
			'im-icon-weather-rain' => 'weather rain',
			'im-icon-weather-snow' => 'weather snow',
			'im-icon-windy' => 'windy',
			'im-icon-fan' => 'fan',
			'im-icon-umbrella' => 'umbrella',
			'im-icon-sun' => 'sun',
			'im-icon-sun-2' => 'sun 2',
			'im-icon-brightness-high' => 'brightness high',
			'im-icon-brightness-medium' => 'brightness medium',
			'im-icon-brightness-low' => 'brightness low',
			'im-icon-brightness-contrast' => 'brightness contrast',
			'im-icon-contrast' => 'contrast',
			'im-icon-moon' => 'moon',
			'im-icon-bed' => 'bed',
			'im-icon-bed-2' => 'bed 2',
			'im-icon-star' => 'star',
			'im-icon-star-2' => 'star 2',
			'im-icon-star-3' => 'star 3',
			'im-icon-star-4' => 'star 4',
			'im-icon-star-5' => 'star 5',
			'im-icon-star-6' => 'star 6',
			'im-icon-heart' => 'heart',
			'im-icon-heart-2' => 'heart 2',
			'im-icon-heart-3' => 'heart 3',
			'im-icon-heart-4' => 'heart 4',
			'im-icon-heart-broken' => 'heart broken',
			'im-icon-heart-5' => 'heart 5',
			'im-icon-heart-6' => 'heart 6',
			'im-icon-heart-broken-2' => 'heart broken 2',
			'im-icon-heart-7' => 'heart 7',
			'im-icon-heart-8' => 'heart 8',
			'im-icon-heart-broken-3' => 'heart broken 3',
			'im-icon-lips' => 'lips',
			'im-icon-lips-2' => 'lips 2',
			'im-icon-thumbs-up' => 'thumbs up',
			'im-icon-thumbs-up-2' => 'thumbs up 2',
			'im-icon-thumbs-down' => 'thumbs down',
			'im-icon-thumbs-down-2' => 'thumbs down 2',
			'im-icon-thumbs-up-3' => 'thumbs up 3',
			'im-icon-thumbs-up-4' => 'thumbs up 4',
			'im-icon-thumbs-up-5' => 'thumbs up 5',
			'im-icon-thumbs-up-6' => 'thumbs up 6',
			'im-icon-people' => 'people',
			'im-icon-man' => 'man',
			'im-icon-male' => 'male',
			'im-icon-woman' => 'woman',
			'im-icon-female' => 'female',
			'im-icon-peace' => 'peace',
			'im-icon-yin-yang' => 'yin yang',
			'im-icon-happy' => 'happy',
			'im-icon-happy-2' => 'happy 2',
			'im-icon-smiley' => 'smiley',
			'im-icon-smiley-2' => 'smiley 2',
			'im-icon-tongue' => 'tongue',
			'im-icon-tongue-2' => 'tongue 2',
			'im-icon-sad' => 'sad',
			'im-icon-sad-2' => 'sad 2',
			'im-icon-wink' => 'wink',
			'im-icon-wink-2' => 'wink 2',
			'im-icon-grin' => 'grin',
			'im-icon-grin-2' => 'grin 2',
			'im-icon-cool' => 'cool',
			'im-icon-cool-2' => 'cool 2',
			'im-icon-angry' => 'angry',
			'im-icon-angry-2' => 'angry 2',
			'im-icon-evil' => 'evil',
			'im-icon-evil-2' => 'evil 2',
			'im-icon-shocked' => 'shocked',
			'im-icon-shocked-2' => 'shocked 2',
			'im-icon-confused' => 'confused',
			'im-icon-confused-2' => 'confused 2',
			'im-icon-neutral' => 'neutral',
			'im-icon-neutral-2' => 'neutral 2',
			'im-icon-wondering' => 'wondering',
			'im-icon-wondering-2' => 'wondering 2',
			'im-icon-cursor' => 'cursor',
			'im-icon-cursor-2' => 'cursor 2',
			'im-icon-point-up' => 'point up',
			'im-icon-point-right' => 'point right',
			'im-icon-point-down' => 'point down',
			'im-icon-point-left' => 'point left',
			'im-icon-pointer' => 'pointer',
			'im-icon-hand' => 'hand',
			'im-icon-stack-empty' => 'stack empty',
			'im-icon-stack-plus' => 'stack plus',
			'im-icon-stack-minus' => 'stack minus',
			'im-icon-stack-star' => 'stack star',
			'im-icon-stack-picture' => 'stack picture',
			'im-icon-stack-down' => 'stack down',
			'im-icon-stack-up' => 'stack up',
			'im-icon-stack-cancel' => 'stack cancel',
			'im-icon-stack-checkmark' => 'stack checkmark',
			'im-icon-stack-list' => 'stack list',
			'im-icon-stack-clubs' => 'stack clubs',
			'im-icon-stack-spades' => 'stack spades',
			'im-icon-stack-hearts' => 'stack hearts',
			'im-icon-stack-diamonds' => 'stack diamonds',
			'im-icon-stack-user' => 'stack user',
			'im-icon-stack-4' => 'stack 4',
			'im-icon-stack-music' => 'stack music',
			'im-icon-stack-play' => 'stack play',
			'im-icon-move' => 'move',
			'im-icon-resize' => 'resize',
			'im-icon-resize-2' => 'resize 2',
			'im-icon-warning' => 'warning',
			'im-icon-warning-2' => 'warning 2',
			'im-icon-notification' => 'notification',
			'im-icon-notification-2' => 'notification 2',
			'im-icon-question' => 'question',
			'im-icon-question-2' => 'question 2',
			'im-icon-question-3' => 'question 3',
			'im-icon-question-4' => 'question 4',
			'im-icon-question-5' => 'question 5',
			'im-icon-plus-circle' => 'plus circle',
			'im-icon-plus-circle-2' => 'plus circle 2',
			'im-icon-minus-circle' => 'minus circle',
			'im-icon-minus-circle-2' => 'minus circle 2',
			'im-icon-info' => 'info',
			'im-icon-info-2' => 'info 2',
			'im-icon-blocked' => 'blocked',
			'im-icon-cancel-circle' => 'cancel circle',
			'im-icon-cancel-circle-2' => 'cancel circle 2',
			'im-icon-checkmark-circle' => 'checkmark circle',
			'im-icon-checkmark-circle-2' => 'checkmark circle 2',
			'im-icon-cancel' => 'cancel',
			'im-icon-spam' => 'spam',
			'im-icon-close' => 'close',
			'im-icon-close-2' => 'close 2',
			'im-icon-close-3' => 'close 3',
			'im-icon-close-4' => 'close 4',
			'im-icon-close-5' => 'close 5',
			'im-icon-checkmark' => 'checkmark',
			'im-icon-checkmark-2' => 'checkmark 2',
			'im-icon-checkmark-3' => 'checkmark 3',
			'im-icon-checkmark-4' => 'checkmark 4',
			'im-icon-spell-check' => 'spell check',
			'im-icon-minus' => 'minus',
			'im-icon-plus' => 'plus',
			'im-icon-minus-2' => 'minus 2',
			'im-icon-plus-2' => 'plus 2',
			'im-icon-enter' => 'enter',
			'im-icon-exit' => 'exit',
			'im-icon-enter-2' => 'enter 2',
			'im-icon-exit-2' => 'exit 2',
			'im-icon-enter-3' => 'enter 3',
			'im-icon-exit-3' => 'exit 3',
			'im-icon-exit-4' => 'exit 4',
			'im-icon-play-3' => 'play 3',
			'im-icon-pause' => 'pause',
			'im-icon-stop' => 'stop',
			'im-icon-backward' => 'backward',
			'im-icon-forward-2' => 'forward 2',
			'im-icon-play-4' => 'play 4',
			'im-icon-pause-2' => 'pause 2',
			'im-icon-stop-2' => 'stop 2',
			'im-icon-backward-2' => 'backward 2',
			'im-icon-forward-3' => 'forward 3',
			'im-icon-first' => 'first',
			'im-icon-last' => 'last',
			'im-icon-previous' => 'previous',
			'im-icon-next' => 'next',
			'im-icon-eject' => 'eject',
			'im-icon-volume-high' => 'volume high',
			'im-icon-volume-medium' => 'volume medium',
			'im-icon-volume-low' => 'volume low',
			'im-icon-volume-mute' => 'volume mute',
			'im-icon-volume-mute-2' => 'volume mute 2',
			'im-icon-volume-increase' => 'volume increase',
			'im-icon-volume-decrease' => 'volume decrease',
			'im-icon-volume-high-2' => 'volume high 2',
			'im-icon-volume-medium-2' => 'volume medium 2',
			'im-icon-volume-low-2' => 'volume low 2',
			'im-icon-volume-mute-3' => 'volume mute 3',
			'im-icon-volume-mute-4' => 'volume mute 4',
			'im-icon-volume-increase-2' => 'volume increase 2',
			'im-icon-volume-decrease-2' => 'volume decrease 2',
			'im-icon-volume5' => 'volume5',
			'im-icon-volume4' => 'volume4',
			'im-icon-volume3' => 'volume3',
			'im-icon-volume2' => 'volume2',
			'im-icon-volume1' => 'volume1',
			'im-icon-volume0' => 'volume0',
			'im-icon-volume-mute-5' => 'volume mute 5',
			'im-icon-volume-mute-6' => 'volume mute 6',
			'im-icon-loop' => 'loop',
			'im-icon-loop-2' => 'loop 2',
			'im-icon-loop-3' => 'loop 3',
			'im-icon-loop-4' => 'loop 4',
			'im-icon-loop-5' => 'loop 5',
			'im-icon-shuffle' => 'shuffle',
			'im-icon-shuffle-2' => 'shuffle 2',
			'im-icon-wave' => 'wave',
			'im-icon-wave-2' => 'wave 2',
			'im-icon-arrow-first' => 'arrow first',
			'im-icon-arrow-right' => 'arrow right',
			'im-icon-arrow-up' => 'arrow up',
			'im-icon-arrow-right-2' => 'arrow right 2',
			'im-icon-arrow-down' => 'arrow down',
			'im-icon-arrow-left' => 'arrow left',
			'im-icon-arrow-up-2' => 'arrow up 2',
			'im-icon-arrow-right-3' => 'arrow right 3',
			'im-icon-arrow-down-2' => 'arrow down 2',
			'im-icon-arrow-left-2' => 'arrow left 2',
			'im-icon-arrow-up-left' => 'arrow up left',
			'im-icon-arrow-up-3' => 'arrow up 3',
			'im-icon-arrow-up-right' => 'arrow up right',
			'im-icon-arrow-right-4' => 'arrow right 4',
			'im-icon-arrow-down-right' => 'arrow down right',
			'im-icon-arrow-down-3' => 'arrow down 3',
			'im-icon-arrow-down-left' => 'arrow down left',
			'im-icon-arrow-left-3' => 'arrow left 3',
			'im-icon-arrow-up-left-2' => 'arrow up left 2',
			'im-icon-arrow-up-4' => 'arrow up 4',
			'im-icon-arrow-up-right-2' => 'arrow up right 2',
			'im-icon-arrow-right-5' => 'arrow right 5',
			'im-icon-arrow-down-right-2' => 'arrow down right 2',
			'im-icon-arrow-down-4' => 'arrow down 4',
			'im-icon-arrow-down-left-2' => 'arrow down left 2',
			'im-icon-arrow-left-4' => 'arrow left 4',
			'im-icon-arrow-up-left-3' => 'arrow up left 3',
			'im-icon-arrow-up-5' => 'arrow up 5',
			'im-icon-arrow-up-right-3' => 'arrow up right 3',
			'im-icon-arrow-right-6' => 'arrow right 6',
			'im-icon-arrow-down-right-3' => 'arrow down right 3',
			'im-icon-arrow-down-5' => 'arrow down 5',
			'im-icon-arrow-down-left-3' => 'arrow down left 3',
			'im-icon-arrow-left-5' => 'arrow left 5',
			'im-icon-arrow-up-left-4' => 'arrow up left 4',
			'im-icon-arrow-up-6' => 'arrow up 6',
			'im-icon-arrow-up-right-4' => 'arrow up right 4',
			'im-icon-arrow-right-7' => 'arrow right 7',
			'im-icon-arrow-down-right-4' => 'arrow down right 4',
			'im-icon-arrow-down-6' => 'arrow down 6',
			'im-icon-arrow-down-left-4' => 'arrow down left 4',
			'im-icon-arrow-left-6' => 'arrow left 6',
			'im-icon-arrow' => 'arrow',
			'im-icon-arrow-2' => 'arrow 2',
			'im-icon-arrow-3' => 'arrow 3',
			'im-icon-arrow-4' => 'arrow 4',
			'im-icon-arrow-5' => 'arrow 5',
			'im-icon-arrow-6' => 'arrow 6',
			'im-icon-arrow-7' => 'arrow 7',
			'im-icon-arrow-8' => 'arrow 8',
			'im-icon-arrow-up-left-5' => 'arrow up left 5',
			'im-icon-arrow-square' => 'arrow square',
			'im-icon-arrow-up-right-5' => 'arrow up right 5',
			'im-icon-arrow-right-8' => 'arrow right 8',
			'im-icon-arrow-down-right-5' => 'arrow down right 5',
			'im-icon-arrow-down-7' => 'arrow down 7',
			'im-icon-arrow-down-left-5' => 'arrow down left 5',
			'im-icon-arrow-left-7' => 'arrow left 7',
			'im-icon-arrow-up-7' => 'arrow up 7',
			'im-icon-arrow-right-9' => 'arrow right 9',
			'im-icon-arrow-down-8' => 'arrow down 8',
			'im-icon-arrow-left-8' => 'arrow left 8',
			'im-icon-arrow-up-8' => 'arrow up 8',
			'im-icon-arrow-right-10' => 'arrow right 10',
			'im-icon-arrow-bottom' => 'arrow bottom',
			'im-icon-arrow-left-9' => 'arrow left 9',
			'im-icon-arrow-up-left-6' => 'arrow up left 6',
			'im-icon-arrow-up-9' => 'arrow up 9',
			'im-icon-arrow-up-right-6' => 'arrow up right 6',
			'im-icon-arrow-right-11' => 'arrow right 11',
			'im-icon-arrow-down-right-6' => 'arrow down right 6',
			'im-icon-arrow-down-9' => 'arrow down 9',
			'im-icon-arrow-down-left-6' => 'arrow down left 6',
			'im-icon-arrow-left-10' => 'arrow left 10',
			'im-icon-arrow-up-left-7' => 'arrow up left 7',
			'im-icon-arrow-up-10' => 'arrow up 10',
			'im-icon-arrow-up-right-7' => 'arrow up right 7',
			'im-icon-arrow-right-12' => 'arrow right 12',
			'im-icon-arrow-down-right-7' => 'arrow down right 7',
			'im-icon-arrow-down-10' => 'arrow down 10',
			'im-icon-arrow-down-left-7' => 'arrow down left 7',
			'im-icon-arrow-left-11' => 'arrow left 11',
			'im-icon-arrow-up-11' => 'arrow up 11',
			'im-icon-arrow-right-13' => 'arrow right 13',
			'im-icon-arrow-down-11' => 'arrow down 11',
			'im-icon-arrow-left-12' => 'arrow left 12',
			'im-icon-arrow-up-12' => 'arrow up 12',
			'im-icon-arrow-right-14' => 'arrow right 14',
			'im-icon-arrow-down-12' => 'arrow down 12',
			'im-icon-arrow-left-13' => 'arrow left 13',
			'im-icon-arrow-up-13' => 'arrow up 13',
			'im-icon-arrow-right-15' => 'arrow right 15',
			'im-icon-arrow-down-13' => 'arrow down 13',
			'im-icon-arrow-left-14' => 'arrow left 14',
			'im-icon-arrow-up-14' => 'arrow up 14',
			'im-icon-arrow-right-16' => 'arrow right 16',
			'im-icon-arrow-down-14' => 'arrow down 14',
			'im-icon-arrow-left-15' => 'arrow left 15',
			'im-icon-arrow-up-15' => 'arrow up 15',
			'im-icon-arrow-right-17' => 'arrow right 17',
			'im-icon-arrow-down-15' => 'arrow down 15',
			'im-icon-arrow-left-16' => 'arrow left 16',
			'im-icon-arrow-up-16' => 'arrow up 16',
			'im-icon-arrow-right-18' => 'arrow right 18',
			'im-icon-arrow-down-16' => 'arrow down 16',
			'im-icon-arrow-left-17' => 'arrow left 17',
			'im-icon-menu-10' => 'menu 10',
			'im-icon-menu-11' => 'menu 11',
			'im-icon-menu-close' => 'menu close',
			'im-icon-menu-close-2' => 'menu close 2',
			'im-icon-enter-4' => 'enter 4',
			'im-icon-enter-5' => 'enter 5',
			'im-icon-esc' => 'esc',
			'im-icon-backspace' => 'backspace',
			'im-icon-backspace-2' => 'backspace 2',
			'im-icon-backspace-3' => 'backspace 3',
			'im-icon-tab' => 'tab',
			'im-icon-transmission' => 'transmission',
			'im-icon-transmission-2' => 'transmission 2',
			'im-icon-sort' => 'sort',
			'im-icon-sort-2' => 'sort 2',
			'im-icon-key-keyboard' => 'key keyboard',
			'im-icon-key-A' => 'key A',
			'im-icon-key-up' => 'key up',
			'im-icon-key-right' => 'key right',
			'im-icon-key-down' => 'key down',
			'im-icon-key-left' => 'key left',
			'im-icon-command' => 'command',
			'im-icon-checkbox-checked' => 'checkbox checked',
			'im-icon-checkbox-unchecked' => 'checkbox unchecked',
			'im-icon-square' => 'square',
			'im-icon-checkbox-partial' => 'checkbox partial',
			'im-icon-checkbox' => 'checkbox',
			'im-icon-checkbox-unchecked-2' => 'checkbox unchecked 2',
			'im-icon-checkbox-partial-2' => 'checkbox partial 2',
			'im-icon-checkbox-checked-2' => 'checkbox checked 2',
			'im-icon-checkbox-unchecked-3' => 'checkbox unchecked 3',
			'im-icon-checkbox-partial-3' => 'checkbox partial 3',
			'im-icon-radio-checked' => 'radio checked',
			'im-icon-radio-unchecked' => 'radio unchecked',
			'im-icon-circle' => 'circle',
			'im-icon-circle-2' => 'circle 2',
			'im-icon-crop' => 'crop',
			'im-icon-crop-2' => 'crop 2',
			'im-icon-vector' => 'vector',
			'im-icon-rulers' => 'rulers',
			'im-icon-scissors' => 'scissors',
			'im-icon-scissors-2' => 'scissors 2',
			'im-icon-scissors-3' => 'scissors 3',
			'im-icon-filter' => 'filter',
			'im-icon-filter-2' => 'filter 2',
			'im-icon-filter-3' => 'filter 3',
			'im-icon-filter-4' => 'filter 4',
			'im-icon-font' => 'font',
			'im-icon-font-size' => 'font size',
			'im-icon-type' => 'type',
			'im-icon-text-height' => 'text height',
			'im-icon-text-width' => 'text width',
			'im-icon-height' => 'height',
			'im-icon-width' => 'width',
			'im-icon-bold' => 'bold',
			'im-icon-underline' => 'underline',
			'im-icon-italic' => 'italic',
			'im-icon-strikethrough' => 'strikethrough',
			'im-icon-strikethrough-2' => 'strikethrough 2',
			'im-icon-font-size-2' => 'font size 2',
			'im-icon-bold-2' => 'bold 2',
			'im-icon-underline-2' => 'underline 2',
			'im-icon-italic-2' => 'italic 2',
			'im-icon-strikethrough-3' => 'strikethrough 3',
			'im-icon-omega' => 'omega',
			'im-icon-sigma' => 'sigma',
			'im-icon-nbsp' => 'nbsp',
			'im-icon-page-break' => 'page break',
			'im-icon-page-break-2' => 'page break 2',
			'im-icon-superscript' => 'superscript',
			'im-icon-subscript' => 'subscript',
			'im-icon-superscript-2' => 'superscript 2',
			'im-icon-subscript-2' => 'subscript 2',
			'im-icon-text-color' => 'text color',
			'im-icon-highlight' => 'highlight',
			'im-icon-pagebreak' => 'pagebreak',
			'im-icon-clear-formatting' => 'clear formatting',
			'im-icon-table' => 'table',
			'im-icon-table-2' => 'table 2',
			'im-icon-insert-template' => 'insert template',
			'im-icon-pilcrow' => 'pilcrow',
			'im-icon-left-to-right' => 'left to right',
			'im-icon-right-to-left' => 'right to left',
			'im-icon-paragraph-left' => 'paragraph left',
			'im-icon-paragraph-center' => 'paragraph center',
			'im-icon-paragraph-right' => 'paragraph right',
			'im-icon-paragraph-justify' => 'paragraph justify',
			'im-icon-paragraph-left-2' => 'paragraph left 2',
			'im-icon-paragraph-center-2' => 'paragraph center 2',
			'im-icon-paragraph-right-2' => 'paragraph right 2',
			'im-icon-paragraph-justify-2' => 'paragraph justify 2',
			'im-icon-indent-increase' => 'indent increase',
			'im-icon-indent-decrease' => 'indent decrease',
			'im-icon-paragraph-left-3' => 'paragraph left 3',
			'im-icon-paragraph-center-3' => 'paragraph center 3',
			'im-icon-paragraph-right-3' => 'paragraph right 3',
			'im-icon-paragraph-justify-3' => 'paragraph justify 3',
			'im-icon-indent-increase-2' => 'indent increase 2',
			'im-icon-indent-decrease-2' => 'indent decrease 2',
			'im-icon-share' => 'share',
			'im-icon-new-tab' => 'new tab',
			'im-icon-new-tab-2' => 'new tab 2',
			'im-icon-popout' => 'popout',
			'im-icon-embed' => 'embed',
			'im-icon-code' => 'code',
			'im-icon-console' => 'console',
			'im-icon-seven-segment-0' => 'seven segment 0',
			'im-icon-seven-segment-1' => 'seven segment 1',
			'im-icon-seven-segment-2' => 'seven segment 2',
			'im-icon-seven-segment-3' => 'seven segment 3',
			'im-icon-seven-segment-4' => 'seven segment 4',
			'im-icon-seven-segment-5' => 'seven segment 5',
			'im-icon-seven-segment-6' => 'seven segment 6',
			'im-icon-seven-segment-7' => 'seven segment 7',
			'im-icon-seven-segment-8' => 'seven segment 8',
			'im-icon-seven-segment-9' => 'seven segment 9',
			'im-icon-share-2' => 'share 2',
			'im-icon-share-3' => 'share 3',
			'im-icon-mail' => 'mail',
			'im-icon-mail-2' => 'mail 2',
			'im-icon-mail-3' => 'mail 3',
			'im-icon-mail-4' => 'mail 4',
			'im-icon-google' => 'google',
			'im-icon-google-plus' => 'google plus',
			'im-icon-google-plus-2' => 'google plus 2',
			'im-icon-google-plus-3' => 'google plus 3',
			'im-icon-google-plus-4' => 'google plus 4',
			'im-icon-google-drive' => 'google drive',
			'im-icon-facebook' => 'facebook',
			'im-icon-facebook-2' => 'facebook 2',
			'im-icon-facebook-3' => 'facebook 3',
			'im-icon-facebook-4' => 'facebook 4',
			'im-icon-instagram' => 'instagram',
			'im-icon-twitter' => 'twitter',
			'im-icon-twitter-2' => 'twitter 2',
			'im-icon-twitter-3' => 'twitter 3',
			'im-icon-feed-2' => 'feed 2',
			'im-icon-feed-3' => 'feed 3',
			'im-icon-feed-4' => 'feed 4',
			'im-icon-youtube' => 'youtube',
			'im-icon-youtube-2' => 'youtube 2',
			'im-icon-vimeo' => 'vimeo',
			'im-icon-vimeo2' => 'vimeo2',
			'im-icon-vimeo-2' => 'vimeo 2',
			'im-icon-lanyrd' => 'lanyrd',
			'im-icon-flickr' => 'flickr',
			'im-icon-flickr-2' => 'flickr 2',
			'im-icon-flickr-3' => 'flickr 3',
			'im-icon-flickr-4' => 'flickr 4',
			'im-icon-picassa' => 'picassa',
			'im-icon-picassa-2' => 'picassa 2',
			'im-icon-dribbble' => 'dribbble',
			'im-icon-dribbble-2' => 'dribbble 2',
			'im-icon-dribbble-3' => 'dribbble 3',
			'im-icon-forrst' => 'forrst',
			'im-icon-forrst-2' => 'forrst 2',
			'im-icon-deviantart' => 'deviantart',
			'im-icon-deviantart-2' => 'deviantart 2',
			'im-icon-steam' => 'steam',
			'im-icon-steam-2' => 'steam 2',
			'im-icon-github' => 'github',
			'im-icon-github-2' => 'github 2',
			'im-icon-github-3' => 'github 3',
			'im-icon-github-4' => 'github 4',
			'im-icon-github-5' => 'github 5',
			'im-icon-wordpress' => 'wordpress',
			'im-icon-wordpress-2' => 'wordpress 2',
			'im-icon-joomla' => 'joomla',
			'im-icon-blogger' => 'blogger',
			'im-icon-blogger-2' => 'blogger 2',
			'im-icon-tumblr' => 'tumblr',
			'im-icon-tumblr-2' => 'tumblr 2',
			'im-icon-yahoo' => 'yahoo',
			'im-icon-tux' => 'tux',
			'im-icon-apple' => 'apple',
			'im-icon-finder' => 'finder',
			'im-icon-android' => 'android',
			'im-icon-windows' => 'windows',
			'im-icon-windows8' => 'windows8',
			'im-icon-soundcloud' => 'soundcloud',
			'im-icon-soundcloud-2' => 'soundcloud 2',
			'im-icon-skype' => 'skype',
			'im-icon-reddit' => 'reddit',
			'im-icon-linkedin' => 'linkedin',
			'im-icon-lastfm' => 'lastfm',
			'im-icon-lastfm-2' => 'lastfm 2',
			'im-icon-delicious' => 'delicious',
			'im-icon-stumbleupon' => 'stumbleupon',
			'im-icon-stumbleupon-2' => 'stumbleupon 2',
			'im-icon-stackoveratom' => 'stackoveratom',
			'im-icon-pinterest' => 'pinterest',
			'im-icon-pinterest-2' => 'pinterest 2',
			'im-icon-xing' => 'xing',
			'im-icon-xing-2' => 'xing 2',
			'im-icon-flattr' => 'flattr',
			'im-icon-safari' => 'safari',
			'im-icon-foursquare' => 'foursquare',
			'im-icon-foursquare-2' => 'foursquare 2',
			'im-icon-paypal' => 'paypal',
			'im-icon-paypal-2' => 'paypal 2',
			'im-icon-paypal-3' => 'paypal 3',
			'im-icon-yelp' => 'yelp',
			'im-icon-libreoffice' => 'libreoffice',
			'im-icon-file-pdf' => 'file pdf',
			'im-icon-file-openoffice' => 'file openoffice',
			'im-icon-file-word' => 'file word',
			'im-icon-file-excel' => 'file excel',
			'im-icon-file-zip' => 'file zip',
			'im-icon-file-powerpoint' => 'file powerpoint',
			'im-icon-file-xml' => 'file xml',
			'im-icon-file-css' => 'file css',
			'im-icon-html5' => 'html5',
			'im-icon-html5-2' => 'html5 2',
			'im-icon-css3' => 'css3',
			'im-icon-chrome' => 'chrome',
			'im-icon-firefox' => 'firefox',
//			'im-icon-IE' => 'IE',
			'im-icon-opera' => 'opera',
		);
		ksort( $icons );
		return $icons;
	}

	/**
	 * FontAwesome vector icons
	 */
	static public function get_fa_icons() {
		$icons = array(
			'fa-icon-glass' => 'glass',
			'fa-icon-music' => 'music',
			'fa-icon-search' => 'search',
			'fa-icon-envelope-alt' => 'envelope-alt',
			'fa-icon-heart' => 'heart',
			'fa-icon-star' => 'star',
			'fa-icon-star-empty' => 'star-empty',
			'fa-icon-user' => 'user',
			'fa-icon-film' => 'film',
			'fa-icon-th-large' => 'th-large',
			'fa-icon-th' => 'th',
			'fa-icon-th-list' => 'th-list',
			'fa-icon-ok' => 'ok',
			'fa-icon-remove' => 'remove',
			'fa-icon-zoom-in' => 'zoom-in',
			'fa-icon-zoom-out' => 'zoom-out',
			'fa-icon-power-off' => 'power-off',
			'fa-icon-off' => 'off',
			'fa-icon-signal' => 'signal',
			'fa-icon-gear' => 'gear',
			'fa-icon-cog' => 'cog',
			'fa-icon-trash' => 'trash',
			'fa-icon-home' => 'home',
			'fa-icon-file-alt' => 'file-alt',
			'fa-icon-time' => 'time',
			'fa-icon-road' => 'road',
			'fa-icon-download-alt' => 'download-alt',
			'fa-icon-download' => 'download',
			'fa-icon-upload' => 'upload',
			'fa-icon-inbox' => 'inbox',
			'fa-icon-play-circle' => 'play-circle',
			'fa-icon-rotate-right' => 'rotate-right',
			'fa-icon-repeat' => 'repeat',
			'fa-icon-refresh' => 'refresh',
			'fa-icon-list-alt' => 'list-alt',
			'fa-icon-lock' => 'lock',
			'fa-icon-flag' => 'flag',
			'fa-icon-headphones' => 'headphones',
			'fa-icon-volume-off' => 'volume-off',
			'fa-icon-volume-down' => 'volume-down',
			'fa-icon-volume-up' => 'volume-up',
			'fa-icon-qrcode' => 'qrcode',
			'fa-icon-barcode' => 'barcode',
			'fa-icon-tag' => 'tag',
			'fa-icon-tags' => 'tags',
			'fa-icon-book' => 'book',
			'fa-icon-bookmark' => 'bookmark',
			'fa-icon-print' => 'print',
			'fa-icon-camera' => 'camera',
			'fa-icon-font' => 'font',
			'fa-icon-bold' => 'bold',
			'fa-icon-italic' => 'italic',
			'fa-icon-text-height' => 'text-height',
			'fa-icon-text-width' => 'text-width',
			'fa-icon-align-left' => 'align-left',
			'fa-icon-align-center' => 'align-center',
			'fa-icon-align-right' => 'align-right',
			'fa-icon-align-justify' => 'align-justify',
			'fa-icon-list' => 'list',
			'fa-icon-indent-left' => 'indent-left',
			'fa-icon-indent-right' => 'indent-right',
			'fa-icon-facetime-video' => 'facetime-video',
			'fa-icon-picture' => 'picture',
			'fa-icon-pencil' => 'pencil',
			'fa-icon-map-marker' => 'map-marker',
			'fa-icon-adjust' => 'adjust',
			'fa-icon-tint' => 'tint',
			'fa-icon-edit' => 'edit',
			'fa-icon-share' => 'share',
			'fa-icon-check' => 'check',
			'fa-icon-move' => 'move',
			'fa-icon-step-backward' => 'step-backward',
			'fa-icon-fast-backward' => 'fast-backward',
			'fa-icon-backward' => 'backward',
			'fa-icon-play' => 'play',
			'fa-icon-pause' => 'pause',
			'fa-icon-stop' => 'stop',
			'fa-icon-forward' => 'forward',
			'fa-icon-fast-forward' => 'fast-forward',
			'fa-icon-step-forward' => 'step-forward',
			'fa-icon-eject' => 'eject',
			'fa-icon-chevron-left' => 'chevron-left',
			'fa-icon-chevron-right' => 'chevron-right',
			'fa-icon-plus-sign' => 'plus-sign',
			'fa-icon-minus-sign' => 'minus-sign',
			'fa-icon-remove-sign' => 'remove-sign',
			'fa-icon-ok-sign' => 'ok-sign',
			'fa-icon-question-sign' => 'question-sign',
			'fa-icon-info-sign' => 'info-sign',
			'fa-icon-screenshot' => 'screenshot',
			'fa-icon-remove-circle' => 'remove-circle',
			'fa-icon-ok-circle' => 'ok-circle',
			'fa-icon-ban-circle' => 'ban-circle',
			'fa-icon-arrow-left' => 'arrow-left',
			'fa-icon-arrow-right' => 'arrow-right',
			'fa-icon-arrow-up' => 'arrow-up',
			'fa-icon-arrow-down' => 'arrow-down',
			'fa-icon-mail-forward' => 'mail-forward',
			'fa-icon-share-alt' => 'share-alt',
			'fa-icon-resize-full' => 'resize-full',
			'fa-icon-resize-small' => 'resize-small',
			'fa-icon-plus' => 'plus',
			'fa-icon-minus' => 'minus',
			'fa-icon-asterisk' => 'asterisk',
			'fa-icon-exclamation-sign' => 'exclamation-sign',
			'fa-icon-gift' => 'gift',
			'fa-icon-leaf' => 'leaf',
			'fa-icon-fire' => 'fire',
			'fa-icon-eye-open' => 'eye-open',
			'fa-icon-eye-close' => 'eye-close',
			'fa-icon-warning-sign' => 'warning-sign',
			'fa-icon-plane' => 'plane',
			'fa-icon-calendar' => 'calendar',
			'fa-icon-random' => 'random',
			'fa-icon-comment' => 'comment',
			'fa-icon-magnet' => 'magnet',
			'fa-icon-chevron-up' => 'chevron-up',
			'fa-icon-chevron-down' => 'chevron-down',
			'fa-icon-retweet' => 'retweet',
			'fa-icon-shopping-cart' => 'shopping-cart',
			'fa-icon-folder-close' => 'folder-close',
			'fa-icon-folder-open' => 'folder-open',
			'fa-icon-resize-vertical' => 'resize-vertical',
			'fa-icon-resize-horizontal' => 'resize-horizontal',
			'fa-icon-bar-chart' => 'bar-chart',
			'fa-icon-twitter-sign' => 'twitter-sign',
			'fa-icon-facebook-sign' => 'facebook-sign',
			'fa-icon-camera-retro' => 'camera-retro',
			'fa-icon-key' => 'key',
			'fa-icon-gears' => 'gears',
			'fa-icon-cogs' => 'cogs',
			'fa-icon-comments' => 'comments',
			'fa-icon-thumbs-up-alt' => 'thumbs-up-alt',
			'fa-icon-thumbs-down-alt' => 'thumbs-down-alt',
			'fa-icon-star-half' => 'star-half',
			'fa-icon-heart-empty' => 'heart-empty',
			'fa-icon-signout' => 'signout',
			'fa-icon-linkedin-sign' => 'linkedin-sign',
			'fa-icon-pushpin' => 'pushpin',
			'fa-icon-external-link' => 'external-link',
			'fa-icon-signin' => 'signin',
			'fa-icon-trophy' => 'trophy',
			'fa-icon-github-sign' => 'github-sign',
			'fa-icon-upload-alt' => 'upload-alt',
			'fa-icon-lemon' => 'lemon',
			'fa-icon-phone' => 'phone',
			'fa-icon-unchecked' => 'unchecked',
			'fa-icon-check-empty' => 'check-empty',
			'fa-icon-bookmark-empty' => 'bookmark-empty',
			'fa-icon-phone-sign' => 'phone-sign',
			'fa-icon-twitter' => 'twitter',
			'fa-icon-facebook' => 'facebook',
			'fa-icon-github' => 'github',
			'fa-icon-unlock' => 'unlock',
			'fa-icon-credit-card' => 'credit-card',
			'fa-icon-rss' => 'rss',
			'fa-icon-hdd' => 'hdd',
			'fa-icon-bullhorn' => 'bullhorn',
			'fa-icon-bell' => 'bell',
			'fa-icon-certificate' => 'certificate',
			'fa-icon-hand-right' => 'hand-right',
			'fa-icon-hand-left' => 'hand-left',
			'fa-icon-hand-up' => 'hand-up',
			'fa-icon-hand-down' => 'hand-down',
			'fa-icon-circle-arrow-left' => 'circle-arrow-left',
			'fa-icon-circle-arrow-right' => 'circle-arrow-right',
			'fa-icon-circle-arrow-up' => 'circle-arrow-up',
			'fa-icon-circle-arrow-down' => 'circle-arrow-down',
			'fa-icon-globe' => 'globe',
			'fa-icon-wrench' => 'wrench',
			'fa-icon-tasks' => 'tasks',
			'fa-icon-filter' => 'filter',
			'fa-icon-briefcase' => 'briefcase',
			'fa-icon-fullscreen' => 'fullscreen',
			'fa-icon-group' => 'group',
			'fa-icon-link' => 'link',
			'fa-icon-cloud' => 'cloud',
			'fa-icon-beaker' => 'beaker',
			'fa-icon-cut' => 'cut',
			'fa-icon-copy' => 'copy',
			'fa-icon-paperclip' => 'paperclip',
			'fa-icon-paper-clip' => 'paper-clip',
			'fa-icon-save' => 'save',
			'fa-icon-sign-blank' => 'sign-blank',
			'fa-icon-reorder' => 'reorder',
			'fa-icon-list-ul' => 'list-ul',
			'fa-icon-list-ol' => 'list-ol',
			'fa-icon-strikethrough' => 'strikethrough',
			'fa-icon-underline' => 'underline',
			'fa-icon-table' => 'table',
			'fa-icon-magic' => 'magic',
			'fa-icon-truck' => 'truck',
			'fa-icon-pinterest' => 'pinterest',
			'fa-icon-pinterest-sign' => 'pinterest-sign',
			'fa-icon-google-plus-sign' => 'google-plus-sign',
			'fa-icon-google-plus' => 'google-plus',
			'fa-icon-money' => 'money',
			'fa-icon-caret-down' => 'caret-down',
			'fa-icon-caret-up' => 'caret-up',
			'fa-icon-caret-left' => 'caret-left',
			'fa-icon-caret-right' => 'caret-right',
			'fa-icon-columns' => 'columns',
			'fa-icon-sort' => 'sort',
			'fa-icon-sort-down' => 'sort-down',
			'fa-icon-sort-up' => 'sort-up',
			'fa-icon-envelope' => 'envelope',
			'fa-icon-linkedin' => 'linkedin',
			'fa-icon-rotate-left' => 'rotate-left',
			'fa-icon-undo' => 'undo',
			'fa-icon-legal' => 'legal',
			'fa-icon-dashboard' => 'dashboard',
			'fa-icon-comment-alt' => 'comment-alt',
			'fa-icon-comments-alt' => 'comments-alt',
			'fa-icon-bolt' => 'bolt',
			'fa-icon-sitemap' => 'sitemap',
			'fa-icon-umbrella' => 'umbrella',
			'fa-icon-paste' => 'paste',
			'fa-icon-lightbulb' => 'lightbulb',
			'fa-icon-exchange' => 'exchange',
			'fa-icon-cloud-download' => 'cloud-download',
			'fa-icon-cloud-upload' => 'cloud-upload',
			'fa-icon-user-md' => 'user-md',
			'fa-icon-stethoscope' => 'stethoscope',
			'fa-icon-suitcase' => 'suitcase',
			'fa-icon-bell-alt' => 'bell-alt',
			'fa-icon-coffee' => 'coffee',
			'fa-icon-food' => 'food',
			'fa-icon-file-text-alt' => 'file-text-alt',
			'fa-icon-building' => 'building',
			'fa-icon-hospital' => 'hospital',
			'fa-icon-ambulance' => 'ambulance',
			'fa-icon-medkit' => 'medkit',
			'fa-icon-fighter-jet' => 'fighter-jet',
			'fa-icon-beer' => 'beer',
			'fa-icon-h-sign' => 'h-sign',
			'fa-icon-plus-sign-alt' => 'plus-sign-alt',
			'fa-icon-double-angle-left' => 'double-angle-left',
			'fa-icon-double-angle-right' => 'double-angle-right',
			'fa-icon-double-angle-up' => 'double-angle-up',
			'fa-icon-double-angle-down' => 'double-angle-down',
			'fa-icon-angle-left' => 'angle-left',
			'fa-icon-angle-right' => 'angle-right',
			'fa-icon-angle-up' => 'angle-up',
			'fa-icon-angle-down' => 'angle-down',
			'fa-icon-desktop' => 'desktop',
			'fa-icon-laptop' => 'laptop',
			'fa-icon-tablet' => 'tablet',
			'fa-icon-mobile-phone' => 'mobile-phone',
			'fa-icon-circle-blank' => 'circle-blank',
			'fa-icon-quote-left' => 'quote-left',
			'fa-icon-quote-right' => 'quote-right',
			'fa-icon-spinner' => 'spinner',
			'fa-icon-circle' => 'circle',
			'fa-icon-mail-reply' => 'mail-reply',
			'fa-icon-reply' => 'reply',
			'fa-icon-github-alt' => 'github-alt',
			'fa-icon-folder-close-alt' => 'folder-close-alt',
			'fa-icon-folder-open-alt' => 'folder-open-alt',
			'fa-icon-expand-alt' => 'expand-alt',
			'fa-icon-collapse-alt' => 'collapse-alt',
			'fa-icon-smile' => 'smile',
			'fa-icon-frown' => 'frown',
			'fa-icon-meh' => 'meh',
			'fa-icon-gamepad' => 'gamepad',
			'fa-icon-keyboard' => 'keyboard',
			'fa-icon-flag-alt' => 'flag-alt',
			'fa-icon-flag-checkered' => 'flag-checkered',
			'fa-icon-terminal' => 'terminal',
			'fa-icon-code' => 'code',
			'fa-icon-reply-all' => 'reply-all',
			'fa-icon-mail-reply-all' => 'mail-reply-all',
			'fa-icon-star-half-full' => 'star-half-full',
			'fa-icon-star-half-empty' => 'star-half-empty',
			'fa-icon-location-arrow' => 'location-arrow',
			'fa-icon-crop' => 'crop',
			'fa-icon-code-fork' => 'code-fork',
			'fa-icon-unlink' => 'unlink',
			'fa-icon-question' => 'question',
			'fa-icon-info' => 'info',
			'fa-icon-exclamation' => 'exclamation',
			'fa-icon-superscript' => 'superscript',
			'fa-icon-subscript' => 'subscript',
			'fa-icon-eraser' => 'eraser',
			'fa-icon-puzzle-piece' => 'puzzle-piece',
			'fa-icon-microphone' => 'microphone',
			'fa-icon-microphone-off' => 'microphone-off',
			'fa-icon-shield' => 'shield',
			'fa-icon-calendar-empty' => 'calendar-empty',
			'fa-icon-fire-extinguisher' => 'fire-extinguisher',
			'fa-icon-rocket' => 'rocket',
			'fa-icon-maxcdn' => 'maxcdn',
			'fa-icon-chevron-sign-left' => 'chevron-sign-left',
			'fa-icon-chevron-sign-right' => 'chevron-sign-right',
			'fa-icon-chevron-sign-up' => 'chevron-sign-up',
			'fa-icon-chevron-sign-down' => 'chevron-sign-down',
			'fa-icon-html5' => 'html5',
			'fa-icon-css3' => 'css3',
			'fa-icon-anchor' => 'anchor',
			'fa-icon-unlock-alt' => 'unlock-alt',
			'fa-icon-bullseye' => 'bullseye',
			'fa-icon-ellipsis-horizontal' => 'ellipsis-horizontal',
			'fa-icon-ellipsis-vertical' => 'ellipsis-vertical',
			'fa-icon-rss-sign' => 'rss-sign',
			'fa-icon-play-sign' => 'play-sign',
			'fa-icon-ticket' => 'ticket',
			'fa-icon-minus-sign-alt' => 'minus-sign-alt',
			'fa-icon-check-minus' => 'check-minus',
			'fa-icon-level-up' => 'level-up',
			'fa-icon-level-down' => 'level-down',
			'fa-icon-check-sign' => 'check-sign',
			'fa-icon-edit-sign' => 'edit-sign',
			'fa-icon-external-link-sign' => 'external-link-sign',
			'fa-icon-share-sign' => 'share-sign',
			'fa-icon-compass' => 'compass',
			'fa-icon-collapse' => 'collapse',
			'fa-icon-collapse-top' => 'collapse-top',
			'fa-icon-expand' => 'expand',
			'fa-icon-euro' => 'euro',
			'fa-icon-eur' => 'eur',
			'fa-icon-gbp' => 'gbp',
			'fa-icon-dollar' => 'dollar',
			'fa-icon-usd' => 'usd',
			'fa-icon-rupee' => 'rupee',
			'fa-icon-inr' => 'inr',
			'fa-icon-yen' => 'yen',
			'fa-icon-jpy' => 'jpy',
			'fa-icon-renminbi' => 'renminbi',
			'fa-icon-cny' => 'cny',
			'fa-icon-won' => 'won',
			'fa-icon-krw' => 'krw',
			'fa-icon-bitcoin' => 'bitcoin',
			'fa-icon-btc' => 'btc',
			'fa-icon-file' => 'file',
			'fa-icon-file-text' => 'file-text',
			'fa-icon-sort-by-alphabet' => 'sort-by-alphabet',
			'fa-icon-sort-by-alphabet-alt' => 'sort-by-alphabet-alt',
			'fa-icon-sort-by-attributes' => 'sort-by-attributes',
			'fa-icon-sort-by-attributes-alt' => 'sort-by-attributes-alt',
			'fa-icon-sort-by-order' => 'sort-by-order',
			'fa-icon-sort-by-order-alt' => 'sort-by-order-alt',
			'fa-icon-thumbs-up' => 'thumbs-up',
			'fa-icon-thumbs-down' => 'thumbs-down',
			'fa-icon-youtube-sign' => 'youtube-sign',
			'fa-icon-youtube' => 'youtube',
			'fa-icon-xing' => 'xing',
			'fa-icon-xing-sign' => 'xing-sign',
			'fa-icon-youtube-play' => 'youtube-play',
			'fa-icon-dropbox' => 'dropbox',
			'fa-icon-stackexchange' => 'stackexchange',
			'fa-icon-instagram' => 'instagram',
			'fa-icon-flickr' => 'flickr',
			'fa-icon-adn' => 'adn',
			'fa-icon-bitbucket' => 'bitbucket',
			'fa-icon-bitbucket-sign' => 'bitbucket-sign',
			'fa-icon-tumblr' => 'tumblr',
			'fa-icon-tumblr-sign' => 'tumblr-sign',
			'fa-icon-long-arrow-down' => 'long-arrow-down',
			'fa-icon-long-arrow-up' => 'long-arrow-up',
			'fa-icon-long-arrow-left' => 'long-arrow-left',
			'fa-icon-long-arrow-right' => 'long-arrow-right',
			'fa-icon-apple' => 'apple',
			'fa-icon-windows' => 'windows',
			'fa-icon-android' => 'android',
			'fa-icon-linux' => 'linux',
			'fa-icon-dribbble' => 'dribbble',
			'fa-icon-skype' => 'skype',
			'fa-icon-foursquare' => 'foursquare',
			'fa-icon-trello' => 'trello',
			'fa-icon-female' => 'female',
			'fa-icon-male' => 'male',
			'fa-icon-gittip' => 'gittip',
			'fa-icon-sun' => 'sun',
			'fa-icon-moon' => 'moon',
			'fa-icon-archive' => 'archive',
			'fa-icon-bug' => 'bug',
			'fa-icon-vk' => 'vk',
			'fa-icon-weibo' => 'weibo',
			'fa-icon-renren' => 'renren',
			
		);
		ksort( $icons );
		return $icons;
	}

	/**
	 * Set of Social vector icons
	 */
	static public function get_social_icons() {
		$icons = array(
			'' => '',
			'im-icon-google-plus' => 'Google Plus',
			'im-icon-google-drive' => 'Google Drive',
			'im-icon-facebook' => 'Facebook',
			'im-icon-instagram' => 'Instagram',
			'fa-icon-instagram' => 'Instagram (2)',
			'im-icon-twitter' => 'Twitter',
			'im-icon-feed-2' => 'RSS',
			'im-icon-youtube' => 'Youtube',
			'im-icon-vimeo' => 'Vimeo',
			'im-icon-flickr' => 'Flickr',
			'im-icon-picassa' => 'Picassa',
			'im-icon-dribble' => 'Dribbble',
			'im-icon-deviantart-2' => 'Deviantart',
			'im-icon-forrst' => 'Forrst',
			'im-icon-steam' => 'Steam',
			'im-icon-github-3' => 'Github',
			'im-icon-wordpress' => 'Wordpress',
			'im-icon-joomla' => 'Joomla',
			'im-icon-blogger' => 'Blogger',
			'im-icon-tumblt' => 'Tumblt',
			'im-icon-yahoo' => 'Yahoo',
			'im-icon-skype' => 'Skype',
			'im-icon-reddit' => 'Reddit',
			'im-icon-linkedin' => 'Linkedin',
			'im-icon-lastfm' => 'Lastfm',
			'im-icon-delicious' => 'Delicious',
			'im-icon-stumbleupon' => 'Stumbleupon',
			'im-icon-stackoveratom' => 'Stackoveratom',
			'im-icon-pinterest-2' => 'Pinterest',
			'im-icon-xing' => 'Xing',
			'im-icon-flattr' => 'Flattr',
			'im-icon-foursquare-2' => 'Foursquare',
			'im-icon-yelp' => 'Yelp',
			'fa-icon-renren' => 'Renren',
			'fa-icon-vk' => 'Vk',
			'fa-icon-stackexchange' => 'Stackexchange',
		 );
		asort( $icons );
		return $icons;
	}

	/**
	 * All vector icons
	 */
	static public function get_all_icons() {
		// empty value
		$all_icons = array( 'at-none' => 'none' );
		// iconMoon icons
		$im_icons = AT_Common::get_im_icons();
		foreach( $im_icons as $key => $value ) {
			$all_icons[$key] = $value . ' (IcoMoon)';
		}
		// fontAwesome icons
		$fa_icons = AT_Common::get_fa_icons();
		foreach( $fa_icons as $key => $value ) {
			$all_icons[$key] = $value . ' (FontAwesome)';
		}
		asort( $all_icons );
		return $all_icons;
	}

	/**
	 * Spinners
	 */
    static public function spinners($return = false) {

        $spinner_html = '';
        if($spinner_type = AT_Common::getMod('smooth_pt_spinner_type')){

            switch ($spinner_type) {
                case "infinity":
                    $spinner_html = AT_Spinners::infinity();
                break;
                case "diamond":
                    $spinner_html = AT_Spinners::diamond();
                break;
                case "pulse":
                    $spinner_html = AT_Spinners::pulse();
                break;
                case "double_pulse":
                    $spinner_html =  AT_Spinners::double_pulse();
                break;
                case "cube":
                    $spinner_html =  AT_Spinners::cube();
                break;
                case "rotating_cubes":
                    $spinner_html =  AT_Spinners::rotating_cubes();
                break;
                case "stripes":
                    $spinner_html =  AT_Spinners::stripes();
                break;
                case "wave":
                    $spinner_html =  AT_Spinners::wave();
                break;
                case "two_rotating_circles":
                    $spinner_html =  AT_Spinners::two_rotating_circles();
                break;
                case "five_rotating_circles":
                    $spinner_html =  AT_Spinners::five_rotating_circles();
                break;
				case "atom":
                    $spinner_html = AT_Spinners::atom();
                break;
				case "clock":
                    $spinner_html = AT_Spinners::clock();
                break;
				case "mitosis":
                    $spinner_html = AT_Spinners::mitosis();
                break;
				case "lines":
                    $spinner_html = AT_Spinners::lines();
                break;
				case "fussion":
                    $spinner_html = AT_Spinners::fussion();
                break;
				case "wave_circles":
                    $spinner_html = AT_Spinners::wave_circles();
                break;
				case "pulse_circles":
                    $spinner_html = AT_Spinners::pulse_circles();
                break;
            }
        }else{
            $spinner_html = AT_Spinners::pulse();
        }

        if($return === true) {
            return $spinner_html;
        }

        echo wp_kses($spinner_html, array(
            'div' => array(
                'class' => true,
                'style' => true,
                'id' => true
            )
        ));
    }

}
?>
