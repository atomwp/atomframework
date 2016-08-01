<?php if (!defined("AT_ROOT")) die('!!!'); ?>
<?php
	global $wp_scripts, $wp_query;

    $global_variables = array();

    $global_variables['atAddForAdminBar'] = is_admin_bar_showing() ? 32 : 0;

    if ($element_appear_amount = AT_Common::getMod('element_appear_amount') !== '') {
      $global_variables['atElementAppearAmount'] = $element_appear_amount;
    } else {
      $global_variables['atElementAppearAmount'] = -150;
    }

    $global_variables['atFinishedMessage'] = esc_html__('No more posts', 'atom' );

    $global_variables['atMessage'] = esc_html__('Loading new posts...', 'atom' );

    $global_variables = apply_filters('atom_js_global_variables', $global_variables);

	$this->add_style( 'atom.app', AT_ASSETS_URI.'/css/theme.min.css' );

	if( ($this->get_option( 'active_skin_custom', array() ) != '' || $this->get_option( 'active_skin', '' ) != '') && $this->get_option( 'active_skin_rand', '' ) != '' ) {

		$upload_dir = wp_upload_dir();
		$css_file = md5(get_site_url());
		$this->add_style( 'skin', $upload_dir['baseurl'] . '/' . $css_file . '.css', array( 'atom.style' ), 'all', $this->get_option( 'active_skin_rand', '' )  );
	}

    if(atom_load_blog_assets() || is_singular('portfolio-item')) {
        $this->add_style('wp-mediaelement');
    }

    //define files afer which style dynamic needs to be included. It should be included last so it can override other files
    $style_dynamic_deps_array = array();

    //is responsive option turned on?
    if(AT_Common::is_responsive()) {
        //include proper styles
        if(file_exists(AT_THEME_ROOT.'/assets/css/style_dynamic_responsive.css') && atom_is_css_folder_writable() && !is_multisite()) {
            $this->add_style('atom_style_dynamic_responsive', AT_ASSETS_URI.'/css/style_dynamic_responsive.css', array(), filemtime(AT_THEME_ROOT.'/assets/css/style_dynamic_responsive.css'));
        } else {
            // $this->add_style('atom_style_dynamic_responsive', home_url('/').'/dynamic/responsive');
        }
    }

    //include Visual Composer styles
    if(class_exists('WPBakeryVisualComposerAbstract')) {
        $this->add_style('js_composer_front');
    }


	$this->add_localize_script('atom_app', 'atGlobalVars',array(
        'vars' => $global_variables
    ));

    //init theme core scripts
	$this->add_script($name = 'jquery', $footer = true);

	$this->add_script($name = 'wp-mediaelement', $footer = true);

	$this->add_script( 'atom_third_party', AT_ASSETS_URI . '/js/vendor-bundle.min.js', array( 'jquery' ));

	$this->add_script( 'atom_app', AT_ASSETS_URI.'/js/app.min.js', array('jquery'), true);

	$this->add_inline_script( 'var siteUri = "' .home_url().'";');

	$this->add_inline_script( 'var atLike = {'.
		'"ajaxurl": "' . admin_url('admin-ajax.php') . '"'.
	'};');

	$this->add_localize_script( 'at-uri', 'siteUri', home_url());

	$this->add_localize_script( 'at-like', 'atLike', array(
		'ajaxurl' => admin_url('admin-ajax.php')
	));

	if(AT_Common::has_smooth_scroll()) {
		// $this->add_script( 'atom_smooth_page_scroll', AT_ASSETS_URI . '/js/smoothPageScroll.js', array( 'jquery' ));
	}

    //include google map api script
    $this->add_script( 'google_map_api', 'https://maps.googleapis.com/maps/api/js');

    $this->add_style( 'atom_font_icons', AT_ASSETS_URI . '/css/icons.min.css');

    // if(atom_load_blog_assets()) {
    	$this->add_script( 'atom_blog', AT_ASSETS_URI.'/js/blog.min.js', array( 'atom_app' ));
    // }

    //include comment reply script
        wp_script_add_data('comment-reply', 'group', 1);
        $this->add_script( 'comment-reply');

    if(is_singular()) {
    }

    //include Visual Composer script
    if(class_exists('WPBakeryVisualComposerAbstract')) {
    	$this->add_script('wpb_composer_front_js');
        // wp_enqueue_script('wpb_composer_front_js');
    }

    if (AT_Core::get_instance()->get_option( 'demo_bar', '' ) == 'yes') {
		$this->add_style( 'demobar', AT_ASSETS_URI.'/demo/css/demo.css' );
		$this->add_script( 'demojs', AT_ASSETS_URI.'/demo/js/demo.js');
    }

	$test = false;

	get_header(); 
?>
<?php
if((!isset($_POST["ajaxReq"]) || $_POST["ajaxReq"] != 'yes') && AT_Common::getMod('smooth_page_transitions') == "yes") {
    $ajax_class = AT_Common::getMod('smooth_pt_true_ajax') === 'no' ? 'mimic-ajax' : 'ajax';
?>
<div class="at-smooth-transition-loader <?php echo esc_attr($ajax_class); ?>">
    <?php if(AT_Common::getMod('enable_preloader_logo') == "yes") { ?>
        <img class="at-normal-logo at-preloader-logo" src="<?php echo esc_url(atom_get_preloader_logo()); ?>" alt="<?php esc_html_e('Logo', 'atom' ); ?>"/>
    <?php } ?>
    <div class="at-st-loader">
        <div class="at-st-loader1">
            <?php AT_Common::spinners(); ?>
        </div>
    </div>
</div>
<?php } ?>

<div class="at-wrapper mp-pusher mp-pushers" id="mp-pusher"<?php echo AT_Common::pusher_style(); ?>>
    <div class="at-wrapper-inner">
        <?php
        if (!isset($_POST["ajaxReq"]) || $_POST["ajaxReq"] != 'yes') {
            atom_get_header();
        }
        ?>

        <div class="at-content" <?php atom_content_elem_style_attr(); ?>>
            <?php if(AT_Common::has_smooth_ajax()) { ?>
            <div class="at-meta">
                <?php do_action('atom_ajax_meta'); ?>
                <span id="at-page-id"><?php echo esc_html($wp_query->get_queried_object_id()); ?></span>
                <div class="at-body-classes"><?php echo esc_html(implode( ',', get_body_class())); ?></div>
            </div>
            <?php } ?>
            <div class="at-content-inner">



<?php if( !$is_only_static && $test == true) : ?>
	<!-- wrap  start -->
	<div id="wrap">
		<!-- header  start -->
		<header id="header">
			<!--  extra-header start -->
			<section class="extra-header">
				<div class="container holder">
					<div class="row">
						<div class="col-xs-12 col-sm-5 col-md-4">
							<?php $this->add_widget('menu_widget', array('name' => 'header-links' )); ?>
						</div>
						<div class="col-xs-12 col-sm-7 col-md-8">
							<address class="pull-right">
								<ul class="quick-contacts list-inline">
									<?php if (!empty($phone)) {?><li><span class="phone"><span class="fa-icon-phone pull-left"></span> <?php esc_html_e( 'Call', 'atom' ); ?> <a href="tel:<?php echo esc_attr($phone); ?>"><?php echo $phone; ?></a></span></li><?php } ?>
									<?php if (!empty($adress)) {?><li><span class="address-text"><span class="fa-icon-map-marker pull-left"> </span> <?php echo $adress; ?></span></li><?php } ?>
									<?php if (!empty($email)) {?><li><a class="email email_link_noreplace" href="#" rel="<?php echo esc_attr(AT_Common::nospam( $email )); ?>"><span class="im-icon-mail-3 pull-left"></span> <span class="email_link_replace"><?php echo AT_Common::nospam( $email );?></span></a></li><?php } ?>
								</ul>
							</address>
						</div>
					</div>
				</div>
			</section>
			<!--  extra-header end 
			      user-navigation start -->
			<section class="user-navigation">
				<div class="container">
					<div class="frame pull-right">
						<div class="form-search pull-right">
							<form action="<?php echo home_url( '/' ); ?>" role="search" method="get">
								<fieldset>
									<div class="field-search">
										<input type="text" class="form-control input-sm" name="s" placeholder="<?php esc_html_e( 'Search...', 'atom' );?>" data-inactive-label="<?php esc_html_e( 'Search...', 'atom' );?>" data-active-label="<?php esc_html_e( 'Start typing...', 'atom' );?>" value="<?php if ( !empty($_GET['s']) ) echo htmlspecialchars( strip_tags( $_GET['s'] ) );  ?>" />
										<button class="btn-search" type="submit"><span class="fa-icon-search"></span></button>
									</div>
								</fieldset>
							</form>
						</div>
						<?php if( $sociable_view && count($sociable) > 0 ) { ?>
						<div class="profiles-box pull-left">
							<a href="#" class="link-profiles pull-left"><?php esc_html_e( 'Profiles', 'atom' ); ?></a>
							<ul class="tools tools-middle pull-right">
								<?php
								foreach ($sociable as $key => $item) {
									$sociable_link = ( !empty( $item['link'] ) ) ? $item['link'] : '#';
									echo '<li><a href="' . esc_url( $sociable_link ) . '"><span class="' . esc_attr($item['icon']) . '"></span></a></li>';
								}
								?>
							</ul>
						</div>
						<?php } ?>
					</div>
				</div>
			</section>
			<!--  user-navigation end 
			      header start -->
			<section class="header-section">
				<div class="container">
					<div class="row">
						<div class="col-sm-4">
							<strong class="logo">
								<a href="<?php echo AT_Common::site_url( '/' ); ?>">
									<?php echo $logo; ?>
								</a>
							</strong>
						</div>
						<div class="col-sm-8">
							<!--  navbar start -->
							<nav class="navbar navbar-default navbar-business">
								<div class="navbar-header">
									<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
										<span class="sr-only"><?php esc_html_e('Toggle navigation', 'atom' ); ?></span>
										<?php esc_html_e('Navigation Menu', 'atom' ); ?>
										<span class="wrap-icon pull-left">
											<span class="icon-bar"></span>
											<span class="icon-bar"></span>
											<span class="icon-bar"></span>
										</span>
									</button>
								</div>
								<div class="collapse navbar-collapse nav-<?php echo esc_attr($nav_align);?>">
									<span class="nook">&nbsp;</span>
									<?php echo $this->add_widget('menu_widget'); ?>
									<!--  frame pull-right start -->
									<div class="frame">
										<div class="form-search pull-right">
											<form action="<?php echo home_url( '/' ); ?>" role="search" method="get">
												<fieldset>
													<div class="field-search">
														<input type="text" class="form-control input-sm" name="s" placeholder="<?php esc_html_e( 'Search', 'atom' );?>" value="<?php if ( !empty($_GET['s']) ) echo htmlspecialchars( strip_tags( $_GET['s'] ) );  ?>" />
														<button class="btn-search" type="submit"><span class="fa-icon-search"></span></button>
													</div>
												</fieldset>
											</form>
										</div>
										<?php if( $sociable_view && count($sociable) > 0 ) { ?>
										<div class="profiles-box pull-left">
											<ul class="tools tools-middle pull-right">
												<?php
												foreach ($sociable as $key => $item) {
													$sociable_link = ( !empty( $item['link'] ) ) ? $item['link'] : '#';
													echo '<li><a href="' . esc_url( $sociable_link ) . '"><span class="' . esc_attr($item['icon']) . '"></span></a></li>';
												}
												?>
											</ul>
											<a href="#" class="link-profiles pull-left"><?php esc_html_e( 'Profiles', 'atom' ); ?></a>
										</div>
										<?php } ?>
									</div>
									<!--  frame pull-right end -->
								</div>
							</nav>
							<!--  navbar end -->
						</div>
					</div>
				</div>
			</section>
			?>
			<!--  header end -->
		</header>

		<div id="main">
<?php endif; ?>
