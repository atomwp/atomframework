<?php if (!defined("AT_ROOT")) die('!!!'); ?>

<?php if($this->get_option( 'custom_footer_code' ) != '' ) { ?>
	<?php echo stripslashes( $this->get_option( 'custom_footer_code' ) ); ?>
<?php } ?>
