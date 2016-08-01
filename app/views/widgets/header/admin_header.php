<?php if (!defined("AT_ROOT")) die('!!!'); ?>
<?php
	$this->add_script('jquery-ui-core');
	$this->add_script('jquery-ui-tabs');
	$this->add_script('jquery-ui-accordion');
    $this->add_script( 'theme-options', AT_ASSETS_URI.'/js/admin-ui.min.js', array('jquery'));
	$this->add_style( 'style.css', AT_ASSETS_URI . '/css/admin-ui.min.css');
?>
<h1>
    <div class="theme_header">
    	<div class="theme_logo">
    		<img src="<?php echo AT_ASSETS_URI . '/img/admin/admin-logo.png'; ?>"/>
    	</div>
    	<div class="theme_details">
            <p>
                <?php esc_html_e( 'Theme Version', 'atom' ); ?>
                <span class="version-number"><?php echo $version; ?></span><br />
            </p>
    	</div>
    </div>
</h1>
